<?PHP
	require_once("commonlib.php");
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	
	$ligne = $_GET['page'];
	
		$handle = fopen("../../dump/flux.txt", "r");

		$row = 0;
		
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			$line[$row][0] = $data[0];	
			$line[$row][1] = $data[1];	
			
			$row++;
		}

		$url = $line[$ligne][1];
	
		fclose ($handle);

	
	$aContext = array(
		'http' => array(
			'proxy' => 'gespac:5865', // This needs to be the server and the port of the NTLM Authentication Proxy Server.
			'request_fulluri' => True,
			),
		);
		
	$cxContext = stream_context_create($aContext);

	$page = file_get_contents($url, false, $cxContext);

	
	if ($page == true) {
		// On stocke le fichier en local
		$handle = fopen("../../dump/flux_courant.xml", "w+");	
		fwrite($handle, $page);
		fclose ($handle);
		
		// On affiche le flux mais converti en Latin
		echo $output = iconv( "UTF-8", "CP1252", Common_Display("../../dump/flux_courant.xml", 25, true, true, true) );
	}
	else
		echo "Pas de contenu affichable.";

	?>