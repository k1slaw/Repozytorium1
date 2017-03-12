<?php	
	/*Zeby byly widoczne zmienne z tablicy asocjacyjnej*/
	session_start();
	
	/*Sprawdzamy czy zmienna w ogole istnieje i czy ma wartosc true*/
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany'] == true)){
		
		header('Location: gra.php');
		exit();/*Musi byc exit po header, gdyz inaczej wszystkie linie kodu by sie wykonaly do konca*/
	}
?>

<!DOCTYPE HTML>
<html lang="pl">

<head>
	<meta charset = "utf-8"/>
	<meta http-equiv = "X-UA-Compatibile" content = "IE = edge, chrome = 1"/>
	<title>Osadnicy - gra przegladarkowa</title>
</head>

<body>
	Tylko martwi ujrzeli koniec wojny - Platon </br></br>
	<a href="rejestracja.php">Rejestracja - załóż darmowe konto</a>
	</br></br>
	
	<form action = "zaloguj.php" method = "post">
		Login: </br> <input type = "text" name = "login"/> </br>
		Haslo: </br> <input type = "password" name = "haslo"/> </br></br>	//pole do wpisywania
		<input type = "submit" value = "Zaloguj sie">	//przycisk

	</form>
	
<?php
	/*Sprawdzanie czy zmienna blad istneije, jesli tak to znaczy, ze nie udalo sie zalogowac*/
	if(isset($_SESSION['blad'])) echo $_SESSION['blad'];
	
?>
</body>

</html>