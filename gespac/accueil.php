<?PHP	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères	?>

<?PHP

	include_once ('config/databases.php');	// fichiers de configuration des bases de données
	include_once ('fonctions.php');			// fichier contenant les fonctions utilisées dans le reste des scripts
	include_once ('config/pear.php');		// fichiers de configuration des lib PEAR (setinclude + packages)

	session_start();
	
	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac      = & MDB2::factory($dsn_gespac);

	$user = $_SESSION ['login'];
	$rq_accueil_user = $db_gespac->queryAll ( "SELECT user_accueil FROM users WHERE user_logon='$user' " );
	$page_accueil = $rq_accueil_user[0][0];
	

?>


<script type="text/javascript">	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
	
	OuvrirPage ('<?PHP echo $page_accueil;?>');
</script>
	
<a href="../index.php">Retour au portail</a>