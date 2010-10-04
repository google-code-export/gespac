<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
		<!--	CHROME FRAME	-->
		<meta http-equiv="X-UA-Compatible" content="chrome=1" />
		
		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
		<!--	FAVICON	-->
		<link rel="SHORTCUT ICON" href="./gespac/img/favicon.ico"/>
		
		<!--	JS	-->
		<script type="text/javascript" src="./gespac/js/mootools-1.2.3-core-yc.js"></script>	
		<script type="text/javascript" src="./gespac/js/mootools-1.2.3.1-more.js"></script>
		<script type="text/javascript" src="./gespac/js/smoothbox.js"></script> 
		<!--<script type="text/javascript" src="./gespac/js/dropdown.js"></script>		-->
		<script type="text/javascript" src="./gespac/js/main.js"></script>	
		
		<!--	CSS	-->
		<link rel="stylesheet" href="./gespac/css/smoothbox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="./gespac/css/style_ff.css" type="text/css" media="screen" />
		
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

	<script type="text/javascript" 
		src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"> </script>
	 
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

	//installation de la base GESPAC
	
	require_once ('./include/config.php');

	//Analyse des versions OCS et FOG
	$file_ocs = '/usr/share/ocsinventory-reports/ocsreports/preferences.php';//fichier ou est stocké la version du GUI d'OCS
	if (file_exists($file_ocs)) {
		require_once ('/usr/share/ocsinventory-reports/ocsreports/preferences.php');
		$version_ocs = GUI_VER;//récupère la version du GUI d'OCS
	}
	else {$version_ocs = "Il semblerait qu'OCS ne soit pas installé";}//le fichier n'est pas trouvé
	
	$file_fog = '/var/www/fog/commons/config.php';//fichier ou est stocké la version de FOG
	if (file_exists($file_fog)) {
		require_once ('/var/www/fog/commons/config.php'); 
		$version_fog = FOG_VERSION;//récupère la version de FOG
	}
	else {$version_fog = "Il semblerait que FOG ne soit pas installé";}//Fichier config FOG non trouvé
	
		//Les commandes sous linux
		if (!EXEC('uname -r')) {//on vérifie le système avec une commande sh
			$version_gespac = 'Non déterminé car le système ne semble pas être un Linux';
			$version_sqlgespac = 'Non déterminé car le système ne semble pas être un Linux';
			$version_linux = 'Horreur votre système est un WIN32';//Message un peu dur, mais salutaire!!!
		}
		else {
			$version_gespac = EXEC('apt-show-versions gespac');
			$version_sqlgespac = EXEC('apt-show-versions sql-gespac');
			$version_linux = EXEC('uname -r');
		}
	
		// on vérifie la connectivité avec le serveur avant d'aller plus loin
		if(!mysql_connect($hostname_gespac, $username_gespac, $password_gespac)) {
			echo 'Merci de renseigner le fichier "config.php" se trouvant dans le dossier include.<br>';
			exit();
		}

		// on vérifie la connectivité avec la base avant d'aller plus loin	
		if(!mysql_select_db($database_gespac)) {
			echo '<img src="./gespac/img/info.png"><br>
			vous devez installer au préalable la base de données en cliquant <a href="install_mysql/installation.php">ici</a>';
			exit();
		}	

		
		
		session_start();
		
		// on vérifie si l'utilisateur est identifié
		if (!isset( $_SESSION['login'])) {
			// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifié -> On redirige sur la page permettant de s'authentifier
			echo '<img src="./gespac/img/gespac.png" height=48> version développement';
			include 'login.php';
			exit();	// on arrête l'exécution

		} else {

			$display_icon = ( $_SESSION['grade'] < 2 ) ? "" : "none" ;
				
			echo "<div id=portail-menu-item><a href='./gespac'> 
				<img src='./gespac/img/gespac.png' height=48><br>GESPAC </a></div>";
			
			if (file_exists($file_fog)) { //fichier présent active le lien vers interface FOG
				echo "<div id=portail-menu-item style='display : $display_icon;'><a href='../fog/management/index.php' target=_blank> 
					<img src='./gespac/img/fog.png' height=48><br>FOG </a></div>";
			}	
			
			if (file_exists($file_ocs)) {	//fichier présent active le lien vers interface OCS
				echo "<div id=portail-menu-item style='display : $display_icon;'><a href='../ocsreports' target=_blank> 
					<img src='./gespac/img/ocs.png' height=48><br>OCS </a></div>";
			}

			echo "<div id=portail-menu-item style='display : $display_icon;'><a href='./gespac/gestion_donnees/form_upload_restauration.php?height=200&width=640' class='smoothbox' title='Restauration des bases de données'>
				<img src='./gespac/img/database.png' height=48><br>RESTAURATION </a></div>";	
			
			echo "<div style='float:right;' id=portail-menu-item><a href='./gespac/gestion_authentification/logout.php'> 
				<img src='./gespac/img/cancel.png' height=48><br>Déconnexion </a></div>";

			echo "<div class='spacer'> </div>";
	
			
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
		<b>GESPAC : </b><?php echo $version_gespac;?><br/>
		<b>SQL-GESPAC : </b><?php echo $version_sqlgespac;?><br/>
		<b>Linux kernel : </b><?php echo $version_linux;?><br/>
		<b><?php echo $_SERVER['SERVER_SIGNATURE'];?></b>
		<b>PHP : </b><?php echo  phpversion();?><b/>r>
		<b>Zend engine version :</b> <?php echo zend_version(); ?><br/>
		<b>Version GUI OCS : </b><?php echo $version_ocs;?><br/>
		<b>Version FOG :</b> <?php echo $version_fog;?><br/>
		<b>Navigateur utilisé : </b><?php echo $_SERVER["HTTP_USER_AGENT"];?><br/><br/><br/>
				<b>SITE OFFICIEL : </b><br/>
					<a href="http://gespac13.free.fr" target=_blank>GESPAC13</a> (Les procédures et manuels validés)<br/><br/>
		<b>NAVIGATEURS : </b><br/>
			- Gespac marche mieux avec Firefox 3.5.x, Firefox 3.6, Chrome et Safari (globalement si le navigateur gère le css3, pas de problème)<br/>
			- Il marche avec pratiquement tous les autres navigateurs, mais c'est moins joli (par exemple Opera 10.5 ne gère pas les fonctions css3 utilisées, donc c'est carré) <br/>
			- Il ne marche pas avec Internet Explorer mais si vous voulez quand même l'utiliser, Gespac tentera d'installer Google Frame (vous aurez donc l'interface ie avec le moteur webkit de chrome).<br/>
	</div>


</body>
</html>
