<?PHP	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères	?>

<div id="target"></div>

<?PHP

	include_once ('config/databases.php');	// fichiers de configuration des bases de données
	include_once ('fonctions.php');			// fichier contenant les fonctions utilisées dans le reste des scripts
	include_once ('config/pear.php');		// fichiers de configuration des lib PEAR (setinclude + packages)

	session_start();
	
	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac      = & MDB2::factory($dsn_gespac);

	$user = $_SESSION ['login'];
	$page_accueil = $db_gespac->queryOne ( "SELECT user_accueil FROM users WHERE user_logon='$user' " );

	if ($page_accueil == "") $page_accueil = "bienvenue.php";
	

?>


<script type="text/javascript">	
	AffichePage('conteneur', '<?PHP echo $page_accueil;?>');
</script>
	
<a href="../index.php">Retour au portail</a>
