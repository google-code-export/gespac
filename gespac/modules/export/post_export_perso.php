<?PHP

	$rq_export = $_POST['rqsql'];


	/*	CREATION DU FICHIER D'EXPORT INVENTAIRE	*/

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	// stockage des lignes retournées par sql dans un tableau
	$liste_export = $con_gespac->QueryAll ( $rq_export );

	$filename = "export_perso.csv";

	$fp = fopen('../../dump/' .$filename, 'w+');

	foreach ($liste_export as $record) {

		$my_line = array();

		foreach ($record as $field)
			array_push($my_line, $field);
			
		fputcsv($fp, $my_line, ',');
	}

	fclose($fp);

	echo "<center><a href='./dump/$filename' onclick=\"document.location.href='index.php?page=exportsperso'\">Fichier CSV Export Perso</a></center>";

?>
