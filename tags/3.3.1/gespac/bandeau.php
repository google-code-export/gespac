<?PHP  
 
	// fichier contenant le bandeau du site, du cg ...

	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;
	$db_gespac      = & MDB2::factory($dsn_gespac);

	$login = $_SESSION ['login'];
	$grade = $db_gespac->queryOne ( "SELECT grade_nom FROM users, grades WHERE users.grade_id=grades.grade_id AND user_logon='$login' " );

	$db_gespac->disconnect();

?>

<div id=bandeau-logo></div>

<div id=bandeau-identity>
	<p>
		Bienvenue, <b><?PHP echo $login; ?></b>
		<br>
		<small>Grade : <b><?PHP echo $grade; ?></b></small>
		<p>
		<a href="gestion_authentification/logout.php" title="Cliquer ici pour se déconnecter"><b>Se déconnecter</b></a>
	</p>
</div>
