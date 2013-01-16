<?PHP  
 
	// fichier contenant le bandeau du site, du cg ...

	// lib
	require_once ('fonctions.php');
	include_once ('config/databases.php');
	include_once ('../class/Sql.class.php');
	
	// Connexion à la base de données GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	$login = $_SESSION ['login'];
	$grade = $con_gespac->QueryOne ( "SELECT grade_nom FROM users, grades WHERE users.grade_id=grades.grade_id AND user_logon='$login' " );

	$con_gespac->Close();

?>

<div id=bandeau-logo></div>

<div id=bandeau-identity>
	<p>
		Bienvenue, <b><?PHP echo $login; ?></b>
		<br>
		<small>Grade : <b><?PHP echo $grade; ?></b></small>
		<p>
		<a href="../logout.php" title="Cliquer ici pour se déconnecter"><b>Se déconnecter</b></a>
	</p>
</div>
