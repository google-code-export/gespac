<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import du fichier import csv
	*
	********************************************************/
		
	$dossier = '../../dump/'; 		// dossier où sera déplacé le fichier
	
	$extensions = array('.txt', '.csv');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . "maj_tag.csv") ) {
					
			// ************ Traitement du fichier uploadé *****************
	
			// Libs
			require_once ('../../fonctions.php');
			include_once ('../../config/databases.php');
			include_once ('../../../class/Sql.class.php');
			include_once ('../../../class/Log.class.php');
			
			// connexion à la base de données GESPAC
			$con_gespac = new Sql($host, $user, $pass, $gespac);
			
			//Log SQL
			$log = new Log ("../../dump/log_sql.sql");
	
			$chemin_import = $dossier . "maj_tag.csv";
			
			$handle = fopen($chemin_import, "r");

			$row = 0;	// [AMELIORATION] penser à virer l'entête

			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];					

				$req_MAJ_csv =  "UPDATE materiels SET mat_dsit='" . $line[$row][1] . "' WHERE mat_serial= '" . $line[$row][0] . "';";
				$result = $con_gespac->Execute ( $req_MAJ_csv );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_MAJ_csv."\n");

				$row++;
			}

			//Insertion d'un log
			echo $log_texte = "Mise à jour des tags DSIT par fichier CSV.";

			$req_log_import_csv = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import CSV', '$log_texte' )";
			$result = $con_gespac->Execute ( $req_log_import_csv );


			// On se déconnecte de la db
			$con_gespac->Close();	

		} else	// En cas d'échec d'upload
			echo "Echec de l'upload !";
			  
	} else // En cas d'erreur
		 echo $erreur;

?>

