<?php	
	/*Zeby byly widoczne zmienne z tablicy asocjacyjnej*/
	session_start();
	
	if(isset($_POST['email'])){
		//Udana walidacja, zakladamy ze tak
		$wszystko_OK = true;
		
		/*Sprawdz poprawnosc nickanem*/
		$nick = $_POST['nick'];
		
		//Sprawdzenie dlugosci nickanem*/
		if((strlen($nick) < 3) || (strlen($nick) > 20)){
			$wszystko_OK = false;
			$_SESSION['e_nick'] = 'Nick musi posiadac od 3 do 20 znakow';
		}
		
		//Sprawdzanie czy znaki w lancuchu sa alfanumeryczne, preg_match??
		if(ctype_alnum($nick) == false){
			
			$wszystko_OK = false;
			$_SESSION['e_nick'] = 'Nick moze skladac sie tylko z liter i cyfr';
		}
		
		//Sprawdz poprawnosc adresu email
		//Sanityzacja kodu - wyczyszenie zrodla z potencjalnie groznych zapisow
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL); //usuwa polskie znaki z adresu email michał -> michal
		
		//Sprawdzamy czy email zostal dobrze podany i czy zadne znaki nie zostaly wyciete
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)){
			$wszystko_OK = false;
			$_SESSION['e_email'] = "Podaj poprawny adres email";
		}
		
		//Sprawdzamy poprawnosc hasla
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		
		if((strlen($haslo1) < 8) || (strlen($haslo1) > 20)){
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = 'Haslo musi posiadac od 8 do 20 znakow';
		}
		
		if($haslo1 != $haslo2){
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = 'Podane hasla nie sa identyczne';
		}
		
		//Hashowanie hasla, zamienianie hasla na ciag znakow, aby bylo ono w32api_deftype
		//bazie danych w formie niejawnej, dodatkowo serwer dodaje tzw sol czy kilka liczb 
		//na poczatku hasla wygenerowanych losowo, aby bylo wieksze bezpieczenstwo
		//Ponadto dla danego hasla zawsze bedzie generowalo jednakowy ciag znakow
		//PASSWORD_DEFAULT okresla, aby uzyc obecnie najslisniejszego hashowania, czyli algorytmu bcrypt
		//Do przechowywania zahasowanych hasel nalezy uzyc komorki, ktora pomiesi 255 znakow
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		//Czy zaakceptowano regulamin? sprawdzanie czy check box zostal zaznaczony
		//jesli tak to istnieje zmienna regulamin jesli nie to nie istnieje
		if(!isset($_POST['regulamin'])){
			$wszystko_OK = false;
			$_SESSION['e_regulamin'] = "Potwierdz akceptacje regulaminu";
		}
		
		//Sprawdzanie captcha i bota
		//sekret posiada ukryty klucz ze strony googla, ktory identyfikuje nasza strone
		//gdyz ten klucz jest wygnerowany dla tej domeny
		$sekret = "6LfY0hcUAAAAAAvojHzqwH03cTeT7ICIGD78Ie5r";
		//Pobierz zawartosc pliku do zmiennej
		//Laczymy sie z zserwer googla bo on dekoduje captche
		//wysylamy mu nasz secret key oraz wpisane przez uzytkowanika captche response='.$_POST['g-recaptcha-response']
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		//Odpowiedz z serwera googlea bedzie zakodowana w formacie JSON i trzeba ja zdekodowac (jest to lekki format do przyslania danych komputerowych)
		//oparty na java scirpcie, ale mozna go uzywac w kazdym jezyku
		$odpowiedz = json_decode($sprawdz);
		
		if($odpowiedz->success == false){
			$wszystko_OK = false;
			$_SESSION['e_bot'] = "Potwierdz, ze nie jestes botem";
		}
		
		/*Dolaczamy plik connect.php*/
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);//ustawienie raportowania bledow
		try{
			/*nowa instancja polaczenie na stercie new*/
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno != 0){/*sprawdzanie czy ostatnia proba polaczenie byla sukcesem*/
				throw new Exception(mysqli_connect_errno()); //rzucanie wyjatkiem, aby instr. catch go zlapala i wypisala
			}
			else{
				//Czy email istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows; //zwraca ile taich maili istnieje juz w bazie
				if($ile_takich_maili > 0){
					$wszystko_OK = false;
					$_SESSION['e_email'] = "Istnieje juz takie konto email";
				}
				
				//Czy nick jest zarezerwowany?
				$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_nickow = $rezultat->num_rows; //zwraca ile taich nickow istnieje juz w bazie
				if($ile_takich_nickow > 0){
					$wszystko_OK = false;
					$_SESSION['e_nick'] = "Istnieje juz gracz o takim nicku";
				}
				
				if($wszystko_OK == true){
					//Wszystko zaliczone dodajemy gracza do bazy
					//NULL bo klucz jest autoinkrementowany, a liczby na koncu to domyslne wartosci zboza itd
					if($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email', 
					100, 100, 100, 14)")){
						//jesli udalo sie dodac uzytkownika do bazy ustawiamy zmienna sesyjna i przekierowujemy go 
						//do strony witamy.php
						$_SESSION['udanarejestracja'] = true;
						header('Location: witamy.php');
					}
					else{
						throw new Exception($polaczenie->error);
					}
					echo "Udana walidacja"; exit();
				}
				$polaczenie->close();
			}
		}
		catch(Exception $e){
			//Zlapanie wyjatku i wyswietlenie czerwona czcionka bledu
			echo '<span style = "color:red;">Blad serwera! Przepraszamy za niedogodnosci i prosimy o rejestracje
			w innym terminie!</span>';
			echo '<br/>Informacja developerska:'.$e;
		}
		
		
		
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">

<head>
	<meta charset = "utf-8"/>
	<meta http-equiv = "X-UA-Compatibile" content = "IE = edge, chrome = 1"/>
	<title>Osadnicy - zaloz darmowe konto</title>
	<!--Dodanie captchy-->
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
	<style>
		.error{
			color: red;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	</style>
	
</head>

<body>
	 <!--ten sam plik dostanie postem dane z formularza -->
	<form method="post">
		Nickname: <br / ><input type="text" name="nick"/><br/>
		
		<!--Wyswietlenie informacji o bledzie w nicku -->
		<?php
			if(isset($_SESSION['e_nick'])){
				echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
				unset($_SESSION['e_nick']);
			}
		?>
		
		E-mail: <br / ><input type="text" name="email"/><br/>
		
		<!--Wyswietlenie informacji o bledzie w emailu -->
		<?php
			if(isset($_SESSION['e_email'])){
				echo '<div class="error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
		?>
		
		Haslo: <br / ><input type="password" name="haslo1"/><br/>
		
		<!--Wyswietlenie informacji o bledzie w hasle -->
		<?php
			if(isset($_SESSION['e_haslo'])){
				echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
				unset($_SESSION['e_haslo']);
			}
		?>
		
		Powtorz haslo: <br / ><input type="password" name="haslo2"/><br/>
		Zamykamy wszystko w labelu, zeby likajac na napis tez sie zaznaczal chck box
		<label>
			<input type="checkbox" name="regulamin"/>Akceptuje regulamin
		</label>
		<!--Wyswietlenie informacji o bledzie w regulaminie -->
		<?php
			if(isset($_SESSION['e_regulamin'])){
				echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
				unset($_SESSION['e_regulamin']);
			}
		?>
		<!--Dodanie captchy ze strony googla -->
		<div class="g-recaptcha" data-sitekey="6LfY0hcUAAAAAMsAT_TcijcdoHNOJwOhrTTJNJDp"></div>
		<!--Wyswietlenie informacji o bledzie w captchy -->
		<?php
			if(isset($_SESSION['e_bot'])){
				echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
				unset($_SESSION['e_bot']);
			}
		?>
		</br>
		<input type="submit" value="Zarejestruj sie"/>
	
	</form>
</body>

</html>