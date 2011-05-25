<?PHP

include_once ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...


	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_hardware = $db_gespac->queryAll ( "SELECT mat_mac, mat_nom FROM materiels;" );



$fp = fopen('../dump/gespac_vers_fog.csv', 'w+');

foreach ($liste_hardware as $record) {
	$mac 	= $record[0];
	$name 	= $record[1];
    
	// On ne garde que les machines avec adresses MAC (sinon on va récupérer aussi les écrans, imprimantes ...)
	if ( $mac <> "" )
		fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
	}

fclose($fp);

$db_gespac->disconnect();


?>

<center><h1><a href="./dump/gespac_vers_fog.csv">Fichier CSV GESPAC pour FOG</a></h1></center>