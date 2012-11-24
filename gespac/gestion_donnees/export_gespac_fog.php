<?PHP

	// lib
	require_once ('../config/databases.php');
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');	

	// Connexion � la base de donn�es GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retourn�es par sql dans un tableau (je ne r�cup�re que le matos associ� � une marque)
	$liste_hardware = $con_gespac->QueryAll ( "SELECT mat_mac, mat_nom FROM materiels;" );

	$fp = fopen('../dump/gespac_vers_fog.csv', 'w+');

		foreach ($liste_hardware as $record) {
			$mac 	= $record['mat_mac'];
			$name 	= $record['mat_nom'];
			
			// On ne garde que les machines avec adresses MAC (sinon on va r�cup�rer aussi les �crans, imprimantes ...)
			if ( $mac <> "" )
				fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
		}

	fclose($fp);
	$con_gespac->Close();


?>

<center><h1><a href="./dump/gespac_vers_fog.csv">Fichier CSV GESPAC pour FOG</a></h1></center>