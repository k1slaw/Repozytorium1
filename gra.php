<?php	
	/*Zeby byly widoczne zmienne z tablicy asocjacyjnej*/
	session_start();
	
	/*To po to, aby niezalogowany uzytkownik nie mogl wejsc na ta strone*/
	if(!isset($_SESSION['zalogowany'])){
		header('Location: index.php');
		exit();
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
<?php
		echo "<p>Witaj ".$_SESSION['user'].'![<a href = "logout.php">Wyloguj sie</a>]</p>';
		echo "<p><b>Drewno</b>".$_SESSION['drewno'];
		echo " | <b>Kamien</b>".$_SESSION['kamien'];
		echo " | <b>Zboze</b>".$_SESSION['zboze']."</p>";
		echo "<p><b>E-mail: </b>".$_SESSION['email'];
		echo "<br/><b>Dni premium: </b>".$_SESSION['dnipremium']."</p>";
?>
</body>

</html>

