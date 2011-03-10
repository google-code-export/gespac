<?PHP

	/*
		Fichier post de restauration de base de donn�es
		permet de restaurer une base qui a �t� dump�e auparavant,
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include_once ('../config/databases.php');

$dossier = '../dump/'; 		// dossier o� sera d�plac� le fichier
	
	$fichier = basename($_FILES['myfile']['name']);
	$extensions = array('.sql');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) ) {
		 $erreur = 'Vous devez choisir un fichier SQL...';
		 echo "<br><script>window.alert('Vous devez choisir un fichier SQL ! Cliquez pour �tre redirig� sur votre page d\'accueil');</script><br>";
	}
	
	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload

		//On formate le nom du fichier ici...
		$fichier = strtr($fichier, '����������������������������������������������������', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
			$chemin = "../dump/$fichier";
		
			exec("mysql -h $host -u $user -p$pass < $chemin");
			
			echo "<br><script>window.alert('La restauration de la base a r�ussi !');</script><br>";
		}
	}


?>
<script>
	
	window.close();
</script>
