		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		
<?PHP

	// utiliser pe $_SERVER['HTTP_HOST']; pour avoir la racine du site ...

	include ('../config/databases.php');					// fichiers de configuration des bases de donn�es
	include ('../fonctions.php');							// fichier contenant les fonctions utilis�es dans le reste des scripts
	include ('../config/pear.php');							// fichiers de configuration des lib PEAR (setinclude + packages)
	include ('../gestion_authentification/controle.php');	// fichier de v�rification de login des utilisateurs
	include_once ('../../class/Sql.class.php');				
	include_once ('../../class/Log.class.php');		
	
	
	header("Content-Type:text/html; charset=iso-8859-15" ); 	// r�gle le probl�me d'encodage des caract�res
?>
