

<?php
	
	include ('../config/databases.php');		// fichiers de configuration des bases de données
	include ('../fonctions.php');				// fichier contenant les fonctions utilisées dans le reste des scripts
	include ('../config/pear.php');				// fichiers de configuration des lib PEAR (setinclude + packages)
	
	session_start();

	// adresse de connexion à la base de données
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	
	// options facultatives de cnx à la db
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL,);


	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);
	
	$message = 'En attente de connexion...';
	
	if (!empty($_POST['login']) AND !empty($_POST['passwd'])) {
		
	   $login  = htmlentities($_POST['login']);
	   $_SESSION['login'] = $login;
	   
	   $passwd = htmlentities($_POST['passwd']);
	   $_SESSION['passwd'] = $passwd;
	   
	   $req_select_comptes  = "SELECT * FROM users WHERE user_logon='$login' AND user_password='$passwd'";
	   $result 				= $db_gespac->queryAll($req_select_comptes);
	  
		if (!$result) {
		
	       $message = 'Vérifiez la saisie de votre login et/ou password !';
		} else {
		
			header ("Location: ./../index.php");
			break;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
		<!--	CSS	-->
		<link rel="stylesheet" href="../css/style_ff.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="../css/menu.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="../css/smoothbox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="../css/chart.css" type="text/css" media="screen" />
			
		<!--	JS	-->

		<script type="text/javascript" src="js/mootools-1.2.3-core-yc.js"></script>	
		<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
		<script type="text/javascript" src="js/smoothbox.js"></script> 
		<script type="text/javascript" src="js/dropdown.js"></script>		
		<script type="text/javascript" src="js/sortableTable.js"></script>		
	
		<!-- 	AJAX	-->
	
	
		<title>GESPAC -> GEStion du PArc des Collèges</title>
	
	</head>

	<DIV id="principal">
			
	<body>
		<center><h3>Authentification</h3></center>
		<form action="<?PHP echo $_SERVER['PHP_SELF']; ?>" method="post">
			<p><?PHP echo $message;?></p>
			
			<p align="center"><strong>Login : </strong>
			<input type="text" name="login" size="20" maxlength="20" />
			
			<strong>Mot de passe : </strong>
			<input type="password" name="passwd" size="20" maxlength="20" /></p>
			
			<p align="center"><input type="submit" value="> Se connecter <" /></p>
		</form>
		<?php
			$db_gespac->disconnect();
		?>
		
	</body>
	</DIV>
</html>


