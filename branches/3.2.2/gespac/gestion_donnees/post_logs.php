<?PHP

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	//Suppression
		
	$req_suppr_logs = "DELETE FROM logs";
	$result = $db_gespac->exec ( $req_suppr_logs );
	
	echo "Logs supprim�s ...";

?>