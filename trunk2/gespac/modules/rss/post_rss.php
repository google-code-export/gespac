<?PHP

	$action = $_GET['action'];

	if ( $action == "add" ) {
	
		$nom = $_POST ['nom'];
		$url = $_POST ['url'];
		
		$fp = fopen('../../dump/flux.txt', 'a+');
		fwrite($fp, '"' . $nom . '";"' . $url . '"' . "\n");			
		fclose ($fp);
		
		echo "Le flux <b>$nom</b> est ajouté à la liste";
	}
	
	
	
	
	if ( $action == "suppr") {
	
		$id = $_GET ['id'];
		$fichier = "";
		
		// On lit le fichier et on colle tout dans une variable sauf la ligne à supprimer
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
		
		echo "Le flux est supprimé de la liste";
	
	}
?>
