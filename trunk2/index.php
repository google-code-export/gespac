<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?PHP session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
		<!--	CHROME FRAME	-->
		<meta http-equiv="X-UA-Compatible" content="chrome=1" />
		
		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=utf-8" /> 
		
		<!--	FAVICON	-->
		<link rel="SHORTCUT ICON" href="./gespac/img/favicon.ico"/>
				
		<!--	CSS	-->
		<link rel="stylesheet" href="./gespac/css/style.css" type="text/css" media="screen" />
		
		<!--Script de détection pour savoir si il y a un popup killer-->
		<script type="text/JavaScript" language="javascript">
			var mine = window.open('','','width=1,height=1,left=0,top=0,scrollbars=no');
			if(mine)
				var popUpsBlocked = false
			else
				var popUpsBlocked = true
			mine.close()
		</script>	

</head>

<body>

	<script type="text/javascript" src="gespac/js/CFInstall.min.js"> </script>
	 
	<div id="placeholder"></div>

	<script>
	 CFInstall.check({
		mode: "inline",
		node: "placeholder",
		destination: "cf:http://localhost/GESPAC3/"
	  });
	</script>


	<div id=portail-menu>
	
	<h3>
	
	
	<?php

		// lib
		require_once ('gespac/config/databases.php');
		require_once ('gespac/fonctions.php');
		include_once ('class/Sql.class.php');	
		include ('version');
		
	
		// on vérifie la connectivité avec le serveur avant d'aller plus loin
		if(!mysql_connect($host, $user, $pass)) {
			echo 'Merci de renseigner le fichier "config.php" se trouvant dans le dossier include.<br>';
			exit();
		}

		// on vérifie la connectivité avec la base avant d'aller plus loin	
		if(!mysql_select_db($gespac)) {
			echo '<img src="./gespac/img/icons/info.png"> Vous devez installer au préalable la base de données en cliquant <a href="install_mysql/installation.php">ICI</a>';
			exit();
		}	

		// on test la version de GESPAC
		$version_gespac = EXEC('apt-cache policy gespac | grep Inst | cut -d \: -f 2'); //on prend la variable installée
	    $version_gespacup = EXEC('apt-cache policy gespac | grep Cand | cut -d \: -f 2'); //on prend la variable candidate
	    if (trim($version_gespac) == trim($version_gespacup)) {
				$gespacversion='';
				} else { 
				$gespacversion = '<img src="gespac/img/update.gif">';
			}
		
		
		
		// on vérifie si l'utilisateur est identifié
		if (!isset( $_SESSION['login'])) {
			// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifié -> On redirige sur la page permettant de s'authentifier
			echo '<img src="./gespac/img/gespac.png" height=48> '.$version.''.$gespacversion;
			include 'login.php';
			exit();	// on arrête l'exécution

		} else {

			//$display_icon = ( $_SESSION['grade'] < 2 ) ? "" : "none" ;

			// si le grade du compte est root, on donne automatiquement les droits d'accés aux icones. Sinon, on teste si le compte a accés aux icones sinon.
			
				
			echo "<div class=portail-menu-item><a href='./gespac'> 
				<img src='./gespac/img/gespac.png' height=48><br>GESPAC </a></div>";
			
			// On récupère adresse du serveur pour le menu DELL https port 1311 et webmin sur port 10000
			$adresse = $_SERVER['SERVER_ADDR'];

	
			// Connexion à la base de données GESPAC
			$con_gespac = new Sql ( $host, $user, $pass, $gespac );

			// stockage des lignes retournées par sql dans un tableau nommÃ© liste_des_materiels
			$liste_des_icones = $con_gespac->QueryAll ( "SELECT mp_id, mp_nom, mp_url, mp_icone FROM menu_portail ORDER BY mp_nom" );	
			
				
			foreach ( $liste_des_icones as $record ) {
			
				$mp_id 		= $record['mp_id'];
				$mp_nom 	= $record['mp_nom'];
				$mp_url 	= $record['mp_url'];
				$mp_icone 	= $record['mp_icone'];
				
				$affiche_item = ($_SESSION['grade'] == 'root') ? true : preg_match ("#item$mp_id#", $_SESSION['menu_portail']);
				

				//On change l'adresse de l'url pour l'application dell et webmin qui utilise du https avec des ports on se base sur le nom du menu portail
				if ($mp_nom == 'dell') {$mp_url = 'https://'.$adresse.':1311';} elseif ($mp_nom == 'webmin') {$mp_url = 'https://'.$adresse.':10000';}

				//On reprend l'affichage du menu
				if ( $affiche_item ) {
					if ( file_exists("./gespac/img/$mp_icone") ) $icon_path = "./gespac/img/$mp_icone";
					else $icon_path = "./gespac/img/application.png";
					
					echo "<div class=portail-menu-item><a href='$mp_url' target=_blank> <img src='$icon_path' height=48><br>$mp_nom</a> </div>";
				}

			}	
			
			echo "<div style='float:right;' class=portail-menu-item><a href='logout.php'> 
				<img src='./gespac/img/cancel.png' height=48><br>Déconnexion </a></div>";
				
			echo "<div style='clear:both;'></div>";			
		}
	?>
	
	<!--On lance la détection du popup killer -->
	<script type="text/JavaScript" language="JavaScript">
		if ( popUpsBlocked ) alert('POPUP KILLERS :\nPrêter ou rendre un portable génère un popup pour les conventions.\nLe popup killer bloquera l`affichage de ces conventions.\nPensez à autoriser les popups pour GESPAC.');
	</script>
	
	</h3>
	</div>
	
	<br/>
	
	<div id=portail-conteneur>
		
		<center>
			<div style="font-size:60px;">GESPAC</div>
			<div style="font-size:12px;">Gestion de Parc en Collectivité</div>
		</center>
		
		<hr>
		
		<div style="font-size:14px;">
			<b>SITE OFFICIEL : </b> <a href="http://gespac.free.fr" target=_blank>Cliquez ici</a> <br><br>
			<b>LES PROCEDURES : </b> <a href="http://gespac.free.fr/doku/doku.php?id=gespacweb" target=_blank>Cliquez ici</a> <br><br>
			<b>LES SOURCES : </b> <a href="http://code.google.com/p/gespac/" target=_blank>Cliquez ICI</a> <br><br>
			<b>DECLARER UN BUG : </b> <a href="http://code.google.com/p/gespac/issues/list" target=_blank>Cliquez ICI</a> <br><br>
		</div>
		
		<div style="font-size:14px;">
			<b>COMPATIBILITE : </b>Gespac est compatible avec la plupart des navigateurs modernes comme Firefox, Chrome, Opéra et Safari. Internet Explorer présente quelques problèmes de présentation et de fonctionnalités (si vous utilisez IE, Gespac vous proposera d'installer <a href="http://code.google.com/intl/fr-FR/chrome/chromeframe/" target=_blank>Google Chrome Frame</a>).<br><br>
		</div>
		
		<div style="font-size:14px;">
			<b>LICENCE : </b>GESPAC est régi par la licence CeCILL V2 soumise au droit français et respectant les principes de diffusion des logiciels libres. Vous pouvez utiliser, modifier et/ou redistribuer ce programme sous les conditions de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA  sur le site <a href="http://www.cecill.info" target=_blank>http://www.cecill.info</a>.<br><br>
		</div>
		


	</div>



</body>
</html>

