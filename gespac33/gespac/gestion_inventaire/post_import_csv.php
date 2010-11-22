<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import du fichier import csv
	*
	********************************************************/
		
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);	
		
		
	$dossier = '../dump/'; 		// dossier où sera déplacé le fichier
	
	$fichier 	= basename($_FILES['myfile']['name']);
	$extensions = array('.txt', '.csv');
	$extension 	= strrchr($_FILES['myfile']['name'], '.'); 
	
	$origine 	= $_POST ['origine'];
	$etat 		= $_POST ['etat']; 
	$marque_id 	= $_POST ['marque_id']; 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload, on créé la marque ...
	
		//On formate le nom du fichier ici...
		$fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
			echo $fichier . " envoyé avec succès !";
			
			
			// ************ Traitement du fichier uploadé *****************
		
			$chemin_import = $dossier . $fichier;
			
			$handle = fopen($chemin_import, "r");

			$row = 0;	// [AMELIORATION] penser à virer l'entête

			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];			
				$line[$row][2] = $data[2];			


				echo $req_import_csv = "INSERT INTO materiels (mat_nom, mat_serial, mat_dsit, mat_etat, mat_origine, salle_id, user_id, marque_id, mat_suppr) VALUES ('" . $line[$row][0] . "', '" . $line[$row][1] ."', '" . $line[$row][2] . "', '$etat', '$origine', 1, 1, $marque_id, 0 );";
				$result = $db_gespac->exec ( $req_import_csv );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_import_csv."\n");

				$row++;
			}

			//Insertion d'un log
			$log_texte = "Import fichier CSV pour la marque <b>$marque $modele</b>";

			echo $req_log_import_csv = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import CSV', '$log_texte' )";
			$result = $db_gespac->exec ( $req_log_import_csv );


			// On se déconnecte de la db
			$db_gespac->disconnect();	
			?>
			
			<script>window.close();</script>
			
			<?PHP
		}
		else	// En cas d'échec d'upload
			echo 'Echec de l\'upload du fichier ' . $fichier;
			  
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;

		 
	// Je ferme le fichier  de log sql
	fclose($fp);
?>
