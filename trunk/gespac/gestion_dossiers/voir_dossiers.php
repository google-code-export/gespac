<?PHP
	
	session_start();

/*

	Page de visualisation des dossiers
	

*/


	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');




//	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
//	$resultat = $con_gespac->QueryAll ('Select * from marques;');

// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-03-03#", $_SESSION['droits']);
	
	

?>

<h3>Visualisation des dossiers</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<?PHP 
	if ( $E_chk ) echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_dossiers/form_dossiers.php?id=-1');\"> <img src='img/add.png'>Créer un dossier </a>"; 
?>
