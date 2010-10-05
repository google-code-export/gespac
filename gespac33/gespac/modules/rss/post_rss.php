<?PHP

$action = $_GET['action'];



	if ( $action == "add" ) {
	
		$nom = $_POST ['nom'];
		$url = $_POST ['url'];
		
		$fp = fopen('../../dump/flux.txt', 'a+');
		fwrite($fp, '"' . $nom . '";"' . $url . '"' . "\n");			
		fclose ($fp);
	}
	
	
	
	
	if ( $action == "suppr") {
	
		$id = $_GET ['id'];
		$fichier = "";
		
		
		$fp = fopen('../../dump/flux.txt', 'r');
		
		$row = 0;
		
		while (!feof($fp)) {
			
			$buffer = fgets($fp);
						
			if ($row <> $id) {
				$fichier .=  $buffer;
			}

			$row++;
		}
		
		fclose ($fp);
			
		// Maintenant on recrache la variable fichier dans le fichier flux.txt
		$fp = fopen('../../dump/flux.txt', 'w+');
		fwrite ($fp, $fichier);
		fclose ($fp);
	
	}
?>

	<script>					
		// on recharge la page de rss
		//HTML_AJAX.replace('conteneur', 'modules/rss/rss.php');
	</script>