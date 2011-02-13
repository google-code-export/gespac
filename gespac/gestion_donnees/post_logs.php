<?PHP

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion à la base de données	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	//Suppression
		
	$req_suppr_logs = "DELETE FROM logs";
	$result = $db_gespac->exec ( $req_suppr_logs );
	
	echo "Logs supprimés ...";

?>