<?PHP

// Connexion à la base de données GESPAC
$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	if ($con_gespac->Exists()) {
		// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
		$liste_hardware = $con_gespac->QueryAll ( "SELECT mat_mac, mat_nom FROM materiels;" );

		$fp = fopen('dump/gespac_vers_fog.csv', 'w+');

			foreach ($liste_hardware as $record) {
				$mac 	= $record['mat_mac'];
				$name 	= $record['mat_nom'];
				
				// On ne garde que les machines avec adresses MAC (sinon on va récupérer aussi les écrans, imprimantes ...)
				if ( $mac <> "" )
					fputcsv($fp, array($mac, $name), ',' );	// les delimiters et "encloseurs" par defaut ne marchent pas ? tant pis
			}

		fclose($fp);
		$con_gespac->Close();


	?>

	<center><h1><a href="dump/gespac_vers_fog.csv">Fichier CSV GESPAC pour FOG</a></h1></center>
<?PHP
	}
	else {
		echo "<center><h2 style='color:red;'>La base GESPAC ne semble pas joignable...</h2></center>";
	}
?>
