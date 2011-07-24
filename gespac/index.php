<?php
	session_start();
	
	
	/****************************************************************
	*	Changer le nom du skin avec le nom du fichier CSS � utiliser
	****************************************************************/
	
	$skin_name = $_SESSION['skin'];
	
	
	// on v�rifie si l'utilisateur est identifi�
	if (!isset( $_SESSION['login'])) {

		// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifi�
		// On redirige sur la page permettant de s'authentifier
		header("Location: ../index.php");
			
		// on arr�te l'ex�cution
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


		<title>GESPAC -> GEStion du PArc des Coll�ges</title>
	
	</head>

	
	<BODY onload="resizeContent();" onResize="resizeContent();">

		<?PHP	// Includes
			include_once ('config/databases.php');	// fichiers de configuration des bases de donn�es
			include_once ('fonctions.php');			// fichier contenant les fonctions utilis�es dans le reste des scripts
			include_once ('config/pear.php');		// fichiers de configuration des lib PEAR (setinclude + packages)
		?>

		<DIV id="principal">
		
			<DIV id="bandeau">		<?PHP 	include ('bandeau.php');	// fichier contenant le bandeau � afficher	?>		</DIV>
			
			<DIV id="main_menu">	<?PHP	include ("menu.php");	// fichier contenant les menus du site	?>			</DIV>

			<DIV id="conteneur">
			
				<?PHP // V�rification de l'existence d'enregistrement dans la table COLLEGE
				
					// adresse de connexion � la base de donn�es
					$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

					// cnx � la base de donn�es OCS
					$db_gespac 	= & MDB2::factory($dsn_gespac);

					// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
					$req_college = $db_gespac->queryAll ( "SELECT * FROM college;" );

					$college_existe = $req_college[0][0];	// La table college n'est pas vide si on a au moins un enregistrement (sans blague)
					
					// On se d�connecte de la db
					$db_gespac->disconnect();

				?>
				
			
				<?PHP	// Inclusion des pages
				
				if ( $college_existe <> "" ) {	// la base de donn�e contient des donn�es sur le coll�ge
					echo "<script>AffichePage ('conteneur', './accueil.php');</script>";		// Pas d'include car les headers sont d�j� post�s			
				} else {
					echo "<script>AffichePage ('conteneur', 'gestion_college/form_college.php');</script>";		// Pas d'include car les headers sont d�j� post�s
				}
			}	
				?>
<div style="clear: both"></div>
			</DIV>	<!--	Fin du div "conteneur"	-->

		</DIV>	<!--	Fin du div "principal"	-->

	</BODY>
	
</HTML>