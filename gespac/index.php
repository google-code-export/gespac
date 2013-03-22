<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	session_start();
	
	
	/****************************************************************
	*	Changer le nom du skin avec le nom du fichier CSS à utiliser
	****************************************************************/
	
	$skin_name = $_SESSION['skin'];
	
	
	// on vérifie si l'utilisateur est identifié
	if (!isset( $_SESSION['login'])) {

		// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifié -> On redirige sur la page permettant de s'authentifier
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
		<!--<link rel="stylesheet" href="skins/<?PHP echo $skin_name;?>/param.css" type="text/css" />-->
		
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
			
			<DIV id="toggle-menu"><img src="img/icons/menu.png"></DIV>
			
			<DIV id="menu">
				<?PHP	
					include ("bandeau.php");
					include ("menu.php");	
				?>
			</DIV>
			
		
			
			<DIV id="contenu">
		
				<?PHP 
					
					$con_gespac = new Sql ( $host, $user, $pass, $gespac );
					
					$page=@$_GET["page"];
					
				
					// Vérification de l'existence d'enregistrement dans la table COLLEGE
					$college_existe = $con_gespac->QueryOne ( "SELECT clg_uai FROM college;" );
					
					// on charge la bonne page d'accueil
					$page_accueil = $con_gespac->QueryOne ( "SELECT user_accueil FROM users WHERE user_logon='" . $_SESSION ['login'] . "' " );
					if ($page_accueil == "") $page_accueil = "bienvenue.php";
					
					if ( !$college_existe ) $page='form_college';	// si pas de college, on charge la page du formulaire de creation d'un college

					switch ($page) {
						case "accueil" :	include ($page_accueil);	break;					
						
						case "materiels" :	include ("gestion_inventaire/voir_materiels.php");	break;
						case "marques" :	include ("gestion_inventaire/voir_marques.php");	break;
						case "salles" :		include ("gestion_inventaire/voir_salles.php");	break;
						
						case "dossiers" :	include ("gestion_dossiers/voir_dossiers.php");	break;
						
						case "prets" :	include ("gestion_prets/voir_prets.php");	break;
						
						case "utilisateurs" :	include ("gestion_utilisateurs/voir_utilisateurs.php");	break;
						case "grades" :	include ("gestion_utilisateurs/voir_grades.php");	break;
						case "importiaca" :	include ("gestion_utilisateurs/form_comptes_iaca.php");	break;
						case "moncompte" :	include ("gestion_utilisateurs/form_utilisateur_personnel.php");	break;
						
						case "recapfog" :	include ("modules/fog/recap_fog.php");	break;
						case "wol" :	include ("modules/wol/voir_liste_wol.php");	break;
						case "exportsperso" :	include ("modules/export/export_perso.php");	break;
						case "taginventaire" :	include ("modules/ssn_dsit/form_import_csv.php");	break;
						case "imagefog" :	include ("modules/image_fog/voir_liste.php");	break;
						case "modportail" :	include ("modules/menu_portail/voir_menu_portail.php");	break;
						case "gestfichiers" :	include ("modules/gestion_fichiers/voir_fichiers.php");	break;
						case "migfog" :	include ("modules/migration_fog/voir_migration.php");	break;
						case "migdossiers" :	include ("modules/migration_dossiers/migration_dossiers.php");	break;
						case "geninventaire" :	include ("modules/generate_inv/voir_generate.php");	break;
						case "migusers" :	include ("modules/migration_users/form_extract.php");	break;
						case "migusers2" :	include ("modules/migration_users/form_migration_users.php");	break;
						case "aic" :	include ("modules/snapin_aic/voir_snapin_aic.php");	break;
						
						case "college" :	include ("gestion_college/voir_college.php");	break;
						case "rss" :	include ("modules/rss/rss.php");	break;
						case "statcam" :	include ("modules/stats/camembert.php");	break;
						case "statbat" :	include ("modules/stats/csschart.php");	break;
						case "statparc" :	include ("modules/stats/utilisation_parc.php");	break;
						case "infoserveur" :	include ("modules/infoserveur/infoserveur.php");	break;
						
						case "importocs" :	include ("gestion_donnees/voir_ocs_db.php");	break;
						case "exports" :	include ("gestion_donnees/exports.php");	break;
						case "dumpgespac" :	include ("gestion_donnees/dump_db_gespac.php");	break;
						case "dumpocs" :	include ("gestion_donnees/dump_db_ocs.php");	break;
						case "logs" :	include ("gestion_donnees/voir_logs.php");	break;
						case "importcsv" :	include ("gestion_inventaire/form_import_csv.php");	break;
						
						
						case "form_college" :	include ("gestion_college/form_college.php");	break;
						
						default : include ($page_accueil); break;
					}
				
				?>
			</DIV> <!-- end contenu -->
		</DIV> <!-- end page -->
		
	</BODY>
	
</HTML>

<?PHP } ?>
