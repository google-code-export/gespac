		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
		<!--	CSS	-->
		<link rel="stylesheet" href="css/smoothbox.css" type="text/css" media="screen" />

		<!--	JS	-->
		<script type="text/javascript" src="js/smoothbox.js"></script> 
	
		<!-- 	AJAX	-->
		<script type="text/javascript" src="server.php?client=all"></script>


<?PHP

	// utiliser pe $_SERVER['HTTP_HOST']; pour avoir la racine du site ...

	include ('../config/databases.php');					// fichiers de configuration des bases de données
	include ('../fonctions.php');							// fichier contenant les fonctions utilisées dans le reste des scripts
	include ('../config/pear.php');							// fichiers de configuration des lib PEAR (setinclude + packages)
	include ('../gestion_authentification/controle.php');	// fichier de vérification de login des utilisateurs
	
	
	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères
?>