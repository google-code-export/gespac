<?PHP	header("Content-Type:text/html; charset=iso-8859-15" ); 	// r�gle le probl�me d'encodage des caract�res	?>

<div id="target"></div>

<?PHP

	include_once ('config/databases.php');	// fichiers de configuration des bases de donn�es
	include_once ('fonctions.php');			// fichier contenant les fonctions utilis�es dans le reste des scripts
	include_once ('config/pear.php');		// fichiers de configuration des lib PEAR (setinclude + packages)

	session_start();
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac      = & MDB2::factory($dsn_gespac);

	$user = $_SESSION ['login'];
	$page_accueil = $db_gespac->queryOne ( "SELECT user_accueil FROM users WHERE user_logon='$user' " );

	if ($page_accueil == "") $page_accueil = "bienvenue.php";
	

?>


<script type="text/javascript">	
	AffichePage('conteneur', '<?PHP echo $page_accueil;?>');
</script>
	
<a href="../index.php">Retour au portail</a>
