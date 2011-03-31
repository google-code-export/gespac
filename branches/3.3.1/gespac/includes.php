		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
<?PHP

	// utiliser pe $_SERVER['HTTP_HOST']; pour avoir la racine du site ...

	include_once ('../config/databases.php');					// fichiers de configuration des bases de données
	include_once ('../fonctions.php');							// fichier contenant les fonctions utilisées dans le reste des scripts
	include_once ('../config/pear.php');							// fichiers de configuration des lib PEAR (setinclude + packages)
	include_once ('../gestion_authentification/controle.php');	// fichier de vérification de login des utilisateurs
	
	
	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères
?>
