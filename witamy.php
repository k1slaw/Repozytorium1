<?php	
	/*Zeby byly widoczne zmienne z tablicy asocjacyjnej*/
	session_start();
	/*To sie daje zeby z palca niemozna bylo wejsc na te strone*/
	/*Sprawdzamy czy zmienna w ogole istnieje, jesli nie to przekierowujemy go do index.php*/
	if(!isset($_SESSION['udanarejestracja'])){
		
		header('Location: index.php');
		exit();/*Musi byc exit po header, gdyz inaczej wszystkie linie kodu by sie wykonaly do konca*/
	}
	else{
		//Jesli ktos tu przyszedl z formularza rejestracji mozna skasowac ta zmienna
		unset($_SESSION['udanarejestracja']);
	}
	
	//Usuwanie zmiennych, ktore sluzyly to zapamietania wartosci w razie nieudanejgo 
	if(isset($_SESSION['fr_nick'])) unset($_SESSION['fr_nick']);
	if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if(isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if(isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	if(isset($_SESSION['fr_regulamin'])) unset($_SESSION['fr_regulamin']);
	
	//Ususwanie bledow rejestracji
	if(isset($_SESSION['e_nick'])) unset($_SESSION['e_nick']);
	if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if(isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if(isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
	if(isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
?>

<!DOCTYPE HTML>
<html lang="pl">

<head>
	<meta charset = "utf-8"/>
	<meta http-equiv = "X-UA-Compatibile" content = "IE = edge, chrome = 1"/>
	<title>Osadnicy - gra przegladarkowa</title>
</head>

<body>
	Dziekujemy za rejestracje</br></br>
	<a href="index.php">Zaloguj sie na swoje konto!</a>
	</br></br>

</body>

</html>