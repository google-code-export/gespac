<?PHP

	/*******************************************************
	*
	*		Requ�tes pour Import du fichier import csv
	*
	********************************************************/
		
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);	
		
		
	$dossier = '../dump/'; 		// dossier o� sera d�plac� le fichier
	
	$fichier = basename($_FILES['myfile']['name']);
	$extensions = array('.txt', '.csv');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	$origine = $_POST ['origine'];
	$etat = $_POST ['etat']; 
	$corr_id = $_POST ['corr_id']; 
	
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload, on cr�� la marque ...
	
	
		// Cr�ation de la marque ou affectation � la marque si elle existe
		
		// On r�cup�re les champs dans la table des correspondances
		$marque_dans_corr = $db_gespac->queryRow ("SELECT corr_type, corr_stype, corr_marque, corr_modele FROM correspondances WHERE corr_id=$corr_id;");
		
		$famille 	= $marque_dans_corr [0];
		$sfamille 	= $marque_dans_corr [1];
		$marque 	= $marque_dans_corr [2];
		$modele 	= $marque_dans_corr [3];
		
		// On v�rifie si le quadruplet existe dans la table des marques
		$marque_id = $db_gespac->queryOne ("SELECT marque_id FROM marques WHERE marque_marque='$marque' AND marque_model='$modele' AND marque_type='$famille' AND marque_stype='$sfamille' ;");
		
		if ( $marque_id <> "" ) {
			//echo "La marque existe, son id est $marque_id";
		}
		else {
			//echo "La marque n'existe pas, on la cr��e : ";
		
			// Insertion d'une nouvelle marque avec les param�tres de la correspondance :
			$req_add_marque = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model) VALUES ( '$famille', '$sfamille', '$marque', '$modele' )";
			$result = $db_gespac->exec ( $req_add_marque );
			
			// On log la requ�te SQL
			fwrite($fp, date("Ymd His") . " " . $req_add_marque."\n");
			
			// On r�cup�re le id de la marque nouvellement cr��e
			$marque_id = $db_gespac->queryOne ("SELECT marque_id FROM marques WHERE marque_marque='$marque' AND marque_model='$modele' AND marque_type='$famille' AND marque_stype='$sfamille' ;");
		}
	

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
				$line[$row][2] = $data[2];			


				$req_import_csv = "INSERT INTO materiels (mat_nom, mat_serial, mat_dsit, mat_etat, mat_origine, salle_id, user_id, marque_id, mat_suppr) VALUES ('" . $line[$row][0] . "', '" . $line[$row][1] ."', '" . $line[$row][2] . "', '$etat', '$origine', 1, 1, $marque_id, 0 );";
				$result = $db_gespac->exec ( $req_import_csv );
				
				// On log la requ�te SQL
				fwrite($fp, date("Ymd His") . " " . $req_import_csv."\n");


				$row++;
			}

			//Insertion d'un log
			$log_texte = "Import fichier CSV pour la marque <b>$marque $modele</b>";

			echo $req_log_import_csv = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import CSV', '$log_texte' )";
			$result = $db_gespac->exec ( $req_log_import_csv );


			// On se d�connecte de la db
			$db_gespac->disconnect();	
			?>
			
			<script>window.close();</script>
			
			<?PHP
		}
		else	// En cas d'�chec d'upload
			echo 'Echec de l\'upload !';
			  
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;

		 
	// Je ferme le fichier  de log sql
	fclose($fp);
?>
