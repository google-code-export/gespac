<?PHP

	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);


	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_logs = $db_gespac->queryAll ( "SELECT * FROM logs;" );


	// On colle les logs dans un fichier
	$dump_logs = 'dump_logs.csv';

	$fp = fopen('../dump/'.$dump_logs, 'a+');

	foreach ($liste_logs as $record) {
		$log_id 	= $record[0];
		$log_type 	= $record[1];
		$log_date 	= $record[2];
		$log_texte 	= $record[3];
		
		fputcsv($fp, array($log_date, $log_type, $log_texte), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis

	}


	//Suppression de tous les logs dans la db
		
	$req_suppr_logs = "DELETE FROM logs;";
	$result = $db_gespac->exec ( $req_suppr_logs );
	
	echo "Suppression des logs ...";


	//Insertion d'un log
	
	$log_texte = urlencode("Le fichier <a href='./dump/$dump_logs'>$dump_logs</a> a été créé ou mis à jour");
		
	$req_log_dump_logs = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Dump LOGS', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_dump_logs );

	fclose($fp);
	$db_gespac->disconnect();
?>
