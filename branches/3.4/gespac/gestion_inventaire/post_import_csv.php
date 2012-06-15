<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import du fichier import csv
	*
	********************************************************/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		

	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
		
		
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

				if ( $line[$row][0] <> "" && $line[$row][1] <> "") {	// On ne prend le champ que si le nom et le ssn sont non nuls
					$req_import_csv = "INSERT INTO materiels (mat_nom, mat_serial, mat_dsit, mat_etat, mat_origine, salle_id, user_id, marque_id, mat_suppr) VALUES ('" . trim($line[$row][0]) . "', '" . trim($line[$row][1]) ."', '" . trim($line[$row][2]) . "', '$etat', '$origine', 1, 1, $marque_id, 0 );";
					$con_gespac->Execute ( $req_import_csv );
					
					// On log la requête SQL
					$log->Insert ( $req_import_csv );
				}
				$row++;
			}

			//Insertion d'un log
			$log_texte = "Import fichier CSV pour la marque <b>$marque $modele</b>";

			$req_log_import_csv = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import CSV', '$log_texte' )";
			$con_gespac->Execute ( $req_log_import_csv );


			// On se déconnecte de la db
			$con_gespac->Close();	
			?>
			
			<script>window.close();</script>
			
			<?PHP
		}
		else	// En cas d'échec d'upload
			echo 'Echec de l\'upload du fichier ' . $fichier;
			  
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;

?>
