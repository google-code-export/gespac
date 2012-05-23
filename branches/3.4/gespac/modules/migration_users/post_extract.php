<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import des comptes IACA
	*
	********************************************************/
			
	$fichier = basename($_FILES['myfile']['name']);
	$extensions = array('.txt', '.csv');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], "../../dump/migration_users_ad2008.csv") ) {
			echo $fichier . " envoyé avec succès !";
			
	?>					
		<script>window.close();</script>
	
	<?PHP
						
		} else	// En cas d'échec d'upload
			echo 'Echec de l\'upload !';
			  
	} else // En cas d'erreur
		 echo $erreur;

?>
