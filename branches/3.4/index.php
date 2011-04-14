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
         $version_foggespac = EXEC('apt-show-versions fog-gespac');
			$version_serveurgespac = EXEC('apt-show-versions serveur-gespac');
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

			//$display_icon = ( $_SESSION['grade'] < 2 ) ? "" : "none" ;

			// si le grade du compte est root, on donne automatiquement les droits d'accès aux icones. Sinon, on teste si le compte a accès aux icones sinon.
			
				
			echo "<div class=portail-menu-item><a href='./gespac'> 
				<img src='./gespac/img/gespac.png' height=48><br>GESPAC </a></div>";
			
			include ('gespac/config/databases.php');
			include ('gespac/config/pear.php');
				
			// adresse de connexion à la base de données
			$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

			// cnx à la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);

			// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
			$liste_des_icones = $db_gespac->queryAll ( "SELECT mp_id, mp_nom, mp_url, mp_icone FROM menu_portail ORDER BY mp_nom" );	
			
				
			foreach ( $liste_des_icones as $record ) {
			
				$mp_id 		= $record[0];
				$mp_nom 	= $record[1];
				$mp_url 	= $record[2];
				$mp_icone 	= $record[3];
				
				$affiche_item = ($_SESSION['grade'] == 'root') ? true : preg_match ("#item$mp_id#", $_SESSION['menu_portail']);
				
				if ( $affiche_item )
					echo "<div class=portail-menu-item><a href='$mp_url' target=_blank> <img src='./gespac/img/$mp_icone' height=48><br>$mp_nom</a> </div>";

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
		<b>GESPAC : </b><?php echo $version_gespac;?><br/>
		<b>SQL-GESPAC : </b><?php echo $version_sqlgespac;?><br/>
		<b>FOG-GESPAC : </b><?php echo $version_foggespac;?><br/>
		<b>SERVEUR-GESPAC : </b><?php echo $version_serveurgespac;?><br/>
      <b>Linux kernel : </b><?php echo $version_linux;?><br/>
		<b><?php echo $_SERVER['SERVER_SIGNATURE'];?></b>
     <b>PHP : </b><?php echo  phpversion();?><br/>
		<b>Zend engine version :</b> <?php echo zend_version(); ?><br/>
		<b>Version GUI OCS : </b><?php echo $version_ocs;?><br/>
		<b>Version FOG :</b> <?php echo $version_fog;?><br/>
		
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
