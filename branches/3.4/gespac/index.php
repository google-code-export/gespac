<?php
	session_start();
	
	
	/****************************************************************
	*	Changer le nom du skin avec le nom du fichier CSS à utiliser
	****************************************************************/
	
	$skin_name = $_SESSION['skin'];
	
	
	// on vérifie si l'utilisateur est identifié
	if (!isset( $_SESSION['login'])) {

		// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifié
		// On redirige sur la page permettant de s'authentifier
		header("Location: ../index.php");
			
		// on arrête l'exécution
		exit();
		
	} else {
?> 
<HTML>
	<head>
		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
		<!--	FAVICON	-->
		<link rel="SHORTCUT ICON" href="img/favicon.ico"/>
		
		<!--	CSS	-->
		<link rel="stylesheet" href="css/sexylightbox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/chart.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/dropdown/dropdown.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/dropdown/default.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/style_ff.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="skins/<?PHP echo $skin_name;?>/param.css" type="text/css" />
		
		<!--	JS	-->
		<script type="text/javascript" src="js/mootools-1.2.3-core-yc.js"></script>	
		<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
		<script type="text/javascript" src="js/sexylightbox.js"></script> 
		<script type="text/javascript" src="js/main.js"></script>


		<title>GESPAC -> GEStion du PArc des Collèges</title>
	
	</head>

	
	<BODY onload="resizeContent();" onResize="resizeContent();">

		<?PHP	// Includes
			include_once ('config/databases.php');	// fichiers de configuration des bases de données
			include_once ('fonctions.php');			// fichier contenant les fonctions utilisées dans le reste des scripts
			include_once ('config/pear.php');		// fichiers de configuration des lib PEAR (setinclude + packages)
		?>

		<DIV id="principal">
		
			<DIV id="bandeau">		<?PHP 	include ('bandeau.php');	// fichier contenant le bandeau à afficher	?>		</DIV>
			
			<DIV id="main_menu">	<?PHP	include ("menu.php");	// fichier contenant les menus du site	?>			</DIV>

			<DIV id="conteneur">
			
				<?PHP // Vérification de l'existence d'enregistrement dans la table COLLEGE
				
					// adresse de connexion à la base de données
					$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

					// cnx à la base de données OCS
					$db_gespac 	= & MDB2::factory($dsn_gespac);

					// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
					$req_college = $db_gespac->queryAll ( "SELECT * FROM college;" );

					$college_existe = $req_college[0][0];	// La table college n'est pas vide si on a au moins un enregistrement (sans blague)
					
					// On se déconnecte de la db
					$db_gespac->disconnect();

				?>
				
			
				<?PHP	// Inclusion des pages
				
				if ( $college_existe <> "" ) {	// la base de donnée contient des données sur le collège
					echo "<script>AffichePage ('conteneur', './accueil.php');</script>";		// Pas d'include car les headers sont déjà postés			
				} else {
					echo "<script>AffichePage ('conteneur', 'gestion_college/form_college.php');</script>";		// Pas d'include car les headers sont déjà postés
				}
			}	
				?>
<div style="clear: both"></div>
			</DIV>	<!--	Fin du div "conteneur"	-->

		</DIV>	<!--	Fin du div "principal"	-->

	</BODY>
	
</HTML>