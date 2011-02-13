<?PHP  
 
	// fichier contenant le bandeau du site, du cg ...

	$niveau =  $_SESSION['grade'];
	
	switch ($niveau) {
		case 0 : $niveau = "ROOT";			break;
		case 1 : $niveau = "ATI";			break;
		case 2 : $niveau = "TICE";			break;
		case 3 : $niveau = "Professeur";	break;
		case 9 : $niveau = "Autre";			break;
	}
?>

<div id=bandeau-logo></div>

<div id=bandeau-identity>
	<p>
		Bienvenue, <b><?PHP echo $_SESSION['login']; ?></b>
		<br>
		<small>Grade : <b><?PHP echo $niveau; ?></b></small>
		<p>
		<a href="gestion_authentification/logout.php" title="Cliquer ici pour se déconnecter"><b>Se déconnecter</b></a>
	</p>
</div>
