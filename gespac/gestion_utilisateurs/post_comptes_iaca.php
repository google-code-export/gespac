<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import des comptes IACA
	*
	********************************************************/
		
	$dossier = '..\\dump\\'; 		// dossier où sera déplacé le fichier
	
	$fichier = basename($_FILES['myfile']['name']);
	$extensions = array('.txt', '.csv');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload

		//On formate le nom du fichier ici...
		$fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
			echo $fichier . " envoyé avec succès !";
			
			
			// ************ Traitement du fichier uploadé *****************
	
			include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	
	
			// adresse de connexion à la base de données
			$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

			// cnx à la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	
			$chemin_import = $dossier . $fichier;
			
			$handle = fopen($chemin_import, "r");

			$row = 0;	// [AMELIORATION] penser à virer l'entête

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];			
				$line[$row][2] = $data[2];

				if ($line[$row][0] <> "NOMCOMPL" )	{
					$req_import_comptes = "INSERT INTO users (user_nom, user_logon, user_password) VALUES ('" . $line[$row][0] . "', '" . $line[$row][1] ."', '" . $line[$row][2] . "' );";
					$result = $db_gespac->exec ( $req_import_comptes );
				}

				$row++;
			}

			//Insertion d'un log

			$log_texte = "Import des comptes IACA vers GESPAC";

			$req_log_import_iaca_gespac = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import IACA', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_import_iaca_gespac );


			// On se déconnecte de la db
			$db_gespac->disconnect();	
			?>
			
			<script>window.close();</script>
			
			<?PHP
		}
		else	// En cas d'échec d'upload
			echo 'Echec de l\'upload !';
			  
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;


?>