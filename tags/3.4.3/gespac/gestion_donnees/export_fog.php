<?PHP

	// lib
	require_once ('../config/databases.php');
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');	

	// Connexion � la base de donn�es GESPAC
	$con_ocs = new Sql ( $host, $user, $pass, $ocsweb );

	// stockage des lignes retourn�es par sql dans un tableau (je ne r�cup�re que le matos associ� � une marque)
	$liste_hardware = $con_ocs->queryAll ( "SELECT macaddr, name FROM hardware, networks WHERE hardware.id = networks.hardware_id;" );

	$fp = fopen('../dump/ocs_vers_fog.csv', 'w+');

	foreach ($liste_hardware as $record) {
		$mac 	= $record['macaddr'];
		$name 	= $record['name'];
		
		fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
	}

	fclose($fp);
	$con_ocs->Close();


?>

<center><h1><a href="./dump/ocs_vers_fog.csv">Fichier CSV OCS pour FOG</a></h1></center>