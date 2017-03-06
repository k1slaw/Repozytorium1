<?php	
	/*Zeby byly widoczne zmienne z tablicy asocjacyjnej*/
	session_start();
	
	session_unset();//zamykanie sesji, usuwanie wszytkich zmienncy
	header('Location: index.php');
?>
