<?PHP

	/*******************************************************
	*
	*		Requ�tes pour Import du fichier import csv
	*
	********************************************************/
		
	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');

	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);	
		
		
	$dossier = '../../dump/'; 		// dossier o� sera d�plac� le fichier

	$fichier 	= basename($_FILES['myfile']['name']);
	$extensions = array('.txt', '.csv');
	$extension 	= strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload, on cr�� la marque ...
	
		//On formate le nom du fichier ici...
		$fichier = strtr($fichier, '����������������������������������������������������', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
			echo $fichier . " envoy� avec succ�s !";
			
			
			// ************ Traitement du fichier upload� *****************
		
			$chemin_import = $dossier . $fichier;
			
			$handle = fopen($chemin_import, "r");

			$row = 0;	// [AMELIORATION] penser � virer l'ent�te

			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];					

				$req_MAJ_csv =  "UPDATE materiels SET mat_dsit='" . $line[$row][1] . "' WHERE mat_serial= '" . $line[$row][0] . "';";
				$result = $db_gespac->exec ( $req_MAJ_csv );
				
				// On log la requ�te SQL
				fwrite($fp, date("Ymd His") . " " . $req_MAJ_csv."\n");

				$row++;
			}

			//Insertion d'un log
			$log_texte = "Mise � jour des tags DSIT par fichier CSV.";

			$req_log_import_csv = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import CSV', '$log_texte' )";
			$result = $db_gespac->exec ( $req_log_import_csv );


			// On se d�connecte de la db
			$db_gespac->disconnect();	
			?>
			
			<script>window.close();</script>
			
			<?PHP
		}
		else	// En cas d'�chec d'upload
			echo 'Echec de l\'upload du fichier ' . $fichier;
			  
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;

		 
	// Je ferme le fichier  de log sql
	fclose($fp);
	
	
?>
