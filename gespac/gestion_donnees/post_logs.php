<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');
	

	// cnx à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);


	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_logs = $con_gespac->QueryAll ( "SELECT log_id, log_type, log_date, log_texte FROM logs;" );


	// On colle les logs dans un fichier
	$dump_logs = 'dump_logs.csv';

	$fp = fopen('../dump/'.$dump_logs, 'a+');

	foreach ($liste_logs as $record) {
		$log_id 	= $record['log_id'];
		$log_type 	= $record['log_type'];
		$log_date 	= $record['log_date'];
		$log_texte 	= $record['log_texte'];
		
		fputcsv($fp, array($log_date, $log_type, $log_texte), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
	}


	//Suppression de tous les logs dans la db
		
	$req_suppr_logs = "DELETE FROM logs";
	$result = $con_gespac->Execute ( $req_suppr_logs );
	
	echo "Suppression des logs ...";


	//Insertion d'un log
	
	$log_texte = urlencode("Le fichier <a href='./dump/$dump_logs'>$dump_logs</a> a été créé ou mis à jour");
		
	$req_log_dump_logs = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Dump LOGS', '$log_texte' );";
	$result = $con_gespac->Execute ( $req_log_dump_logs );

	fclose($fp);
	$con_gespac->Close();
?>
