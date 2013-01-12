<?PHP  
 
 ///////////////////////////////////////////////////////
 //
 //		affichage du login, grade et propose la
 //		déconnexion
 //
 //
 ///////////////////////////////////////////////////////
 
 
 
 

	
	// Connexion à la base de données GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	$login = $_SESSION ['login'];
	$grade = $con_gespac->QueryOne ( "SELECT grade_nom FROM users, grades WHERE users.grade_id=grades.grade_id AND user_logon='$login' " );

	$con_gespac->Close();

?>



<div id="bandeau">
		<img src="img/icons/user.png">
		<b><?PHP echo "$login ($grade)"; ?></b><br>
		<a href="../logout.php" title="Cliquer ici pour se déconnecter"><img src="img/icons/quit.png"><b>quitter</b></a>
</div>