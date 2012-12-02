<?PHP	
	session_start();
	
	// Connexion à la base de données GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	$user = $_SESSION ['login'];
	$page_accueil = $con_gespac->QueryOne ( "SELECT user_accueil FROM users WHERE user_logon='$user' " );

	if ($page_accueil == "") $page_accueil = "bienvenue.php";
	

?>


<script type="text/javascript">	
	AffichePage('conteneur', '<?PHP echo $page_accueil;?>');
</script>
	
<a href="../index.php">Retour au portail</a>
