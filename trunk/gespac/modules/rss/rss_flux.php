<?PHP

	require_once ("commonlib.php");

	$ligne = $_GET['flux'];
	
	if (!$ligne) $ligne=0;
		
	$handle = fopen("dump/flux.txt", "r");

	$row = 0;
		
	while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		$line[$row][0] = $data[0];	
		$line[$row][1] = $data[1];	
		
		$row++;
	}

	$url = $line[$ligne][1];
	
	echo "<small><b>" . $line[$ligne][0] . "</b> sur " . $url . "</small>";
	
	
	fclose ($handle);
		
	$page = file_get_contents($url, false);

	if ($page == true) {
		// On stocke le fichier en local
		$handle = fopen("dump/flux_courant.xml", "w+");	
		fwrite($handle, $page);
		fclose ($handle);
		
		echo $output = Common_Display("dump/flux_courant.xml", 25, true, true, true);
	}
	else
		echo "<br><br><h2>Pas de contenu affichable.</h2>";
	
	
?>
