<?PHP

	// Connexion à la base de données GESPAC
	$con_ocs = new Sql ($host, $user, $pass, $ocsweb);
	

	if ($con_ocs->Exists()) {
		// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
		$liste_hardware = $con_ocs->QueryAll ( "SELECT macaddr, name FROM hardware, networks WHERE hardware.id = networks.hardware_id;" );

		$fp = fopen('dump/ocs_vers_fog.csv', 'w+');

		foreach ($liste_hardware as $record) {
			$mac 	= $record['macaddr'];
			$name 	= $record['name'];
			
			fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
		}

		fclose($fp);
		$con_ocs->Close();


		?>

		<center><h1><a href="dump/ocs_vers_fog.csv">Fichier CSV OCS pour FOG</a></h1></center>

<?PHP
	}
	else {
		echo "<center><h2 style='color:red;'>La base OCS ne semble pas joignable. Impossible de créer le fichier d'export pour FOG.</h2></center>";
	}
?>
