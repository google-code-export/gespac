<?PHP

include_once ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...


	// adresse de connexion � la base de donn�es
	$dsn_ocs 	= 'mysql://'. $user .':' . $pass . '@localhost/' . $ocsweb;

	// cnx � la base de donn�es OCS
	$db_ocs 	= & MDB2::factory($dsn_ocs);

	// stockage des lignes retourn�es par sql dans un tableau (je ne r�cup�re que le matos associ� � une marque)
	$liste_hardware = $db_ocs->queryAll ( "SELECT macaddr, name FROM hardware, networks WHERE hardware.id = networks.hardware_id;" );



$fp = fopen('../dump/ocs_vers_fog.csv', 'w+');
//$out = fopen('php://output', 'w');
foreach ($liste_hardware as $record) {
	$mac 	= $record[0];
	$name 	= $record[1];
    
	fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
	//fputcsv( $out, array("" . $mac . "", '"' . $name . '"'), ',');
	}

fclose($fp);
//fclose($out);

$db_ocs->disconnect();


?>

<center><h1><a href="./dump/ocs_vers_fog.csv">Fichier CSV OCS pour FOG</a></h1></center>