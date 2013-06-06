<?PHP

	/*******************************************************
	*
	*		Requêtes pour Import des comptes IACA
	*
	********************************************************/
		
	$dossier = '../dump/'; 		// dossier où sera déplacé le fichier
	
	$extensions = array('.txt', '.csv');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( !in_array($extension, $extensions) )
		 $erreur = 'Vous devez uploader un fichier de txt ou csv...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload
		 
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . "import_iaca.csv") ) {
					
			// ************ Traitement du fichier uploadé *****************
	
			// Libs
			require_once ('../fonctions.php');
			include_once ('../config/databases.php');
			include_once ('../../class/Sql.class.php');
			include_once ('../../class/Log.class.php');
			
			// connexion à la base de données GESPAC
			$con_gespac = new Sql($host, $user, $pass, $gespac);
			
			//Log SQL
			$log = new Log ("../dump/log_sql.sql");
	
			$chemin_import = $dossier . "import_iaca.csv";
			
			$handle = fopen($chemin_import, "r");

			$row = 0;	// [AMELIORATION] penser à virer l'entête
			
			$grade = $con_gespac->QueryOne ( "SELECT grade_id FROM grades WHERE grade_nom='professeur';" );	// Le grade par défaut dans lequel nous allons ranger tous les utilisateurs.

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];			
				$line[$row][2] = $data[2];

				$existe = $con_gespac->QueryOne ( "SELECT * FROM users WHERE user_logon='" . $line[$row][1] . "';" );	// Le grade par défaut dans lequel nous allons ranger tous les utilisateurs.

				if (!$existe) {
					$req_import_comptes = "INSERT INTO users (user_nom, user_logon, user_password) VALUES ('" . $line[$row][0] . "', '" . $line[$row][1] ."', '" . $line[$row][2] . "' );";
					$con_gespac->Execute ( $req_import_comptes );
					$log->Insert ( $req_import_comptes );
					
					echo "Import de <b>". $line[$row][0] . "</b><br>";
				}
				else
					echo "Le login <b>" . $line[$row][1]	. "</b> est déjà dans la base <br>";


				$row++;
			}

			//Insertion d'un log
			$log_texte = "Import des comptes IACA vers GESPAC";

			$req_log_import_iaca_gespac = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import IACA', '$log_texte' );";
			$con_gespac->Execute ( $req_log_import_iaca_gespac );
			$log->Insert( $req_log_import_iaca_gespac );

			// On se déconnecte de la db
			$con_gespac->Close();	

		} else	// En cas d'échec d'upload
			echo "Echec de l'upload !";
			  
	} else // En cas d'erreur
		 echo $erreur;

?>
