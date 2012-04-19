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
		<script type="text/javascript" src="./gespac/js/main.js"></script>
		
		<!--	CSS	-->
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
		
		require_once ('gespac/config/databases.php');
		include ('gespac/config/pear.php');
		include ('version');
		
	
		// on vérifie la connectivité avec le serveur avant d'aller plus loin
		if(!mysql_connect($host, $user, $pass)) {
			echo 'Merci de renseigner le fichier "config.php" se trouvant dans le dossier include.<br>';
			exit();
		}

		// on vérifie la connectivité avec la base avant d'aller plus loin	
		if(!mysql_select_db($gespac)) {
			echo '<img src="./gespac/img/info.png"><br>
			vous devez installer au prÃ©alable la base de donnÃ©es en cliquant <a href="install_mysql/installation.php">ici</a>';
			exit();
		}	

		
		
		session_start();
		
		// on vérifie si l'utilisateur est identifié
		if (!isset( $_SESSION['login'])) {
			// la variable de session n'existe pas, donc l'utilisateur n'est pas authentifié -> On redirige sur la page permettant de s'authentifier
			echo '<img src="./gespac/img/gespac.png" height=48> '.$version;
			include 'login.php';
			exit();	// on arrête l'exécution

		} else {

			//$display_icon = ( $_SESSION['grade'] < 2 ) ? "" : "none" ;

			// si le grade du compte est root, on donne automatiquement les droits d'accés aux icones. Sinon, on teste si le compte a accés aux icones sinon.
			
				
			echo "<div class=portail-menu-item><a href='./gespac'> 
				<img src='./gespac/img/gespac.png' height=48><br>GESPAC </a></div>";
			
			// On récupère adresse du serveur pour le menu DELL https port 1311 et webmin sur port 10000
			$adresse = $_SERVER['SERVER_ADDR'];

	
			// adresse de connexion Ã  la base de donnÃ©es
			$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

			// cnx Ã  la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);

			// stockage des lignes retournées par sql dans un tableau nommÃ© liste_des_materiels
			$liste_des_icones = $db_gespac->queryAll ( "SELECT mp_id, mp_nom, mp_url, mp_icone FROM menu_portail ORDER BY mp_nom" );	
			
				
			foreach ( $liste_des_icones as $record ) {
			
				$mp_id 		= $record[0];
				$mp_nom 	= utf8_decode($record[1]);
				$mp_url 	= $record[2];
				$mp_icone 	= $record[3];
				
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
			
			echo "<div style='float:right;' class=portail-menu-item><a href='./gespac/gestion_authentification/logout.php'> 
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
		
		
      <br/><br/>
		<b><h3><center>GESPAC est régi par la licence CeCILL V2 soumise au droit français et respectant les principes de diffusion des logiciels libres.<br>
								Vous pouvez utiliser, modifier et/ou redistribuer ce programme sous les conditions de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA  sur le site "http://www.cecill.info".</b></h3></center><br><br>
				<b>SITE OFFICIEL : </b><br/>
					<a href="http://gespac.free.fr" target=_blank>GESPAC</a> (Les procédures et manuels validés)<br/><br/>
		<b>NAVIGATEURS : </b><br/>
			- Gespac marche mieux avec les navigateurs respectant la norme W3C (globalement si le navigateur gère le css3, pas de problème)<br/>
        - Dans le cas ou vous utilisez Internet Explorer, Gespac tentera d'installer <a href="http://code.google.com/intl/fr-FR/chrome/chromeframe/" target=_blank>Google Frame</a><br/>
	</div>



</body>
</html>
