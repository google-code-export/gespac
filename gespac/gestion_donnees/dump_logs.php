<?PHP

include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...


	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données OCS
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_logs = $db_gespac->queryAll ( "SELECT * FROM logs;" );


$dump_logs = 'dump_logs.csv';

$fp = fopen('../dump/'.$dump_logs, 'a+');
//$out = fopen('php://output', 'w');
foreach ($liste_logs as $record) {
	$log_id 	= $record[0];
	$log_type 	= $record[1];
	$log_date 	= $record[2];
    $log_texte 	= $record[3];
	
	fputcsv($fp, array($log_date, $log_type, $log_texte), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
	//fputcsv( $out, array("" . $mac . "", '"' . $name . '"'), ',');
}

fclose($fp);
//fclose($out);



echo "<center><h1><a href=./dump/$dump_logs>Fichier CSV des LOGS</a></h1></center>";

	//Insertion d'un log
	$log_texte = "Le fichier $dump_logs a été créé ou mis à jour";
		
	$req_log_dump_logs = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Dump LOGS', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_dump_logs );
	
	$db_gespac->disconnect();
?>