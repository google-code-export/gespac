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
		<meta http-equiv=Content-Type content="text/html; charset=utf-8" /> 
		
		<!--	FAVICON	-->
		<link rel="SHORTCUT ICON" href="img/favicon.ico"/>
		
		<!--	CSS	-->
		<link rel="stylesheet" href="css/sexylightbox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/chart.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/dropdown/dropdown.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/dropdown/default.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="skins/<?PHP echo $skin_name;?>/param.css" type="text/css" />
		
		<!--	JS	-->
		<script type="text/javascript" src="js/mootools-1.2.3-core-yc.js"></script>	
		<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
		<script type="text/javascript" src="js/sexylightbox.js"></script> 
		<script type="text/javascript" src="js/main.js"></script>


		<title>GESPAC -> GEStion du PArc des Collèges</title>
	
	</head>

	
	<BODY>
	
	<!--	DIV target pour Ajax	-->
	<div id="target"></div>

		<?PHP
			// lib
			include_once ('fonctions.php');
			include_once ('config/databases.php');
			include_once ('../class/Sql.class.php');
		?>
	
		<DIV id="page">
			<DIV id="menu">
				<?PHP	
					include ("bandeau.php");
					include ("menu.php");	
				?>
			</DIV>
			
			<DIV id="contenu">
		
				<?PHP 
					$page=@$_GET["page"];
					
				
					// Vérification de l'existence d'enregistrement dans la table COLLEGE
				
					$con_gespac = new Sql ( $host, $user, $pass, $gespac );
					$college_existe = $con_gespac->QueryOne ( "SELECT clg_uai FROM college;" );
					
					if ( !$college_existe ) $page='form_college';	// si pas de college, on charge la page du formulaire de creation d'un college

					switch ($page) {
						case "materiels" :	include ("gestion_inventaire/voir_materiels.php");	break;
						case "salles" :	include ("gestion_inventaire/voir_salles.php");	break;
						case "form_college" :	include ("gestion_college/form_college.php");	break;
						default : include ("accueil.php"); break;
					}
				
				?>
			</DIV> <!-- end contenu -->
		</DIV> <!-- end page -->
		
	</BODY>
	
</HTML>

<?PHP } ?>