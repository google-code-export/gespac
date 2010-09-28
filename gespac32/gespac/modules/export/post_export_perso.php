<?PHP

	$rq_export = $_POST['rqsql'];


	/*	CREATION DU FICHIER D'EXPORT INVENTAIRE	*/

	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');


	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données OCS
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau
	$liste_export = $db_gespac->queryAll ( $rq_export );

	$filename = "export_perso.csv";

	$fp = fopen('../../dump/' .$filename, 'w+');

	foreach ($liste_export as $record) {

		$my_line = array();

		foreach ($record as $field)
			array_push($my_line, $field);
			
		fputcsv($fp, $my_line, ',');
	}

	fclose($fp);

	$db_gespac->disconnect();


	echo "<center><h1><a href='./dump/$filename'>Fichier CSV Export Perso</a></h1></center>";

?>
