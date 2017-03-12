<?php	

	session_start();	/*Otwarcie sesji do tablocu asocjacyjnej*/
	/*Polaczenie z baza danych i sprawdzenie uzytkownika*/
	/*Potrzeba: adres serwera MySQL, Login do MySQL, Haslo do MySQL, nazwe bazy danych*/
	/*Te dane zapisuje sie w osobnym pliku PHP aby inne pliki tez mialy dostep do bazy, ale
		nazwe konkretnej tabeli juz tutaj*/
	/*Inkludoawnie pliku z danymi bazy*/
	/*4 mozliwosci include, inlclude_once, require, require_once*/
	/*require w momencie braku pliku wygeneruje blad krytyczny a include tylko ostrzezenie*/
	/*once czyli ze doda tylko raz ten plik tak samo jak pragma once*/
	
	if((!isset($_POST['login']))  || (!isset($_POST['haslo']))){
		header('Location: index.php');
		exit();
	}
	require_once "connect.php";
	/*@ powoduje ze wyrazenie przed ktorym to postawiono nie wygeneruje osstrzezenia podcza bledu*/
	/*nowa instancja polaczenie na stercie new*/
	//$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	try{
		$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
		if($polaczenie->connect_errno !=0){
			throw new Exception(mysqli_connect_errno());
		}
		else{
			$login = $_POST['login'];	/*'login' nazwa pola w php*/
			$haslo = $_POST['haslo'];
			/*Ta funkcja zamienia na encje, czyli na znaki znaczace to samo, ale nie rozpoznawalne przez html*/
			/*Np znak " zostanie zamieniony na &quot; */
			/*ENT_QUOTES mowi zeby zamieniac na encje cudzyslowia i apostorfy*/
			$login = htmlentities($login, ENT_QUOTES, "UTF-8");
			//$haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8"); to usuwamy, bo teraz bedziemy uzywac zahashowanego hasla
			
			
			/*Cale zapytanie zapisujemy w cudzyslowiach, a zmienne PHP bedace lancuchami w apostrofach*/
			//$sql = "SELECT * FROM uzytkownicy WHERE user='$login' AND pass = '$haslo'";
			
			
			/*Wysylanie zapytania*/
			/*mysqli_real_escape_string powoduje ze wpisany ciag znakow jest odporny na wstrzykiwanie sqla*/
			if($rezultat = @$polaczenie->query(
			sprintf("SELECT * FROM uzytkownicy WHERE user='%s'", 
			mysqli_real_escape_string($polaczenie, $login)))){
				
				/*ile rekordow zwrocila baza*/
				$ilu_userow = $rezultat->num_rows;
				if($ilu_userow > 0){
					$wiersz = $rezultat->fetch_assoc();	///pobierz tablice asocjacyjna nazwa kolummy jest indeksem
					/*Wyciaganie nazwy usera*/
					/*Pisanie do globalnej tablicy asocjacyjnej na serwerze*/
					/*Pomocnicza zmienna ktora bedzie mowila czy ktos jest zalogowany*/
					
					//weryfikacja hasha
					if(password_verify($haslo, $wiersz['pass'])){
							
						$_SESSION['zalogowany'] = true;
						

						$_SESSION['id'] = $wiersz['id'];
						$_SESSION['user'] = $wiersz['user'];
						$_SESSION['drewno'] = $wiersz['drewno'];
						$_SESSION['kamien'] = $wiersz['kamien'];
						$_SESSION['zboze'] = $wiersz['zboze'];
						$_SESSION['email'] = $wiersz['email'];
						$_SESSION['dnipremium'] = $wiersz['dnipremium'];
						
						/*Usuwanie z sesji zmienna blad jesli udalo nam sie zalogowac*/
						unset($_SESSION['blad']);
						/*Zwalanianie pamieci RAM*/
						$rezultat->free();
						
						/*Przekierowanie pliku do nowej stroni*/
						header('Location: gra.php');
					}
					else{
						/*Stworzenie zmiennej blad jesli nie udalo nam sie zalogowac*/
						$_SESSION['blad'] = '<span style = "color:red">Nieprawidlowy login lub haslo!</span>';
						/*Powrot do formularza logowania*/
						header('Location: index.php');
					}
				}
				else{
					/*Stworzenie zmiennej blad jesli nie udalo nam sie zalogowac*/
					$_SESSION['blad'] = '<span style = "color:red">Nieprawidlowy login lub haslo!</span>';
					/*Powrot do formularza logowania*/
					header('Location: index.php');
					
				}
			}
			$polaczenie->close();
		}
	}
	catch(Exception $e){
		//Zlapanie wyjatku i wyswietlenie czerwona czcionka bledu
		echo '<span style = "color:red;">Blad serwera! Przepraszamy za niedogodnosci i prosimy o logowanie
		w innym terminie!</span>';
		echo '<br/>Informacja developerska:'.$e;
	}
	

?>