<?PHP


	/* fichier de creation / modif / suppr des items du portail */
	

	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
	
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	
	/*********************************************
	*
	*		ACTIONS SUR LES ITEMS
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {

        //Insertion d'un log

		//On récupère les valeurs de l'item en fonction de son id
	    $row = $db_gespac->queryRow ( "SELECT mp_nom, mp_icone FROM menu_portail WHERE mp_id=$id" );

		$mp_nom = $row[0];
		$mp_icone = $row[1];

	    $log_texte = "L'item $mp_nom a été supprimé.";
	    $req_log_suppr_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression item', '$log_texte');";
	    $result = $db_gespac->exec ( $req_log_suppr_grade );
		
		
		// Suppression du grade de la base
		$req_suppr_item = "DELETE FROM menu_portail WHERE mp_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_item );
		
		//Suppression de l'icone
		//chmod('./img/' . $mp_icone, 0777);
		unlink('./img/' . $mp_icone);
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_item."\n");
		
		echo "<br><small>L'item <b>$mp_nom</b> a été supprimé.</small>";

	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$mp_nom 	= $_POST ['mp_nom'];
		$mp_url 	= $_POST ['mp_url'];

		$req_modif_item = "UPDATE menu_portail SET mp_nom='$mp_nom', mp_url='$mp_url' WHERE mp_id=$id";
		$result = $db_gespac->exec ( $req_modif_item );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_item."\n");
		
		// insert dans la table log
		$log_texte = "L'item $mp_nom a été modifié";
	    $req_log_modif_item = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_item );
		
		echo "<br><small>L'item <b>$mp_nom</b> a été modifié...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
			
		$dossier = '../../img/'; 		// dossier où sera déplacé le fichier
		
		$fichier 	= basename($_FILES['myfile']['name']);
		$extensions = array('.png', '.jpg');
		$extension 	= strrchr($_FILES['myfile']['name'], '.'); 
		$size 		= $_FILES['myfile']['size']; 
		
		$mp_nom 	= $_POST ['mp_nom'];
		$mp_url		= $_POST ['mp_url']; 
		
		//Si l'extension n'est pas dans le tableau
		if ( !in_array($extension, $extensions) )
			 $erreur = 'Vous devez uploader un fichier png ou jpg...';
			 
		//Si le poids excede 300Ko
		if ( $size > 300000 )
			 $erreur = "L'image est trop grosse...";

		if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload, on créé la marque ...
		
			//On formate le nom du fichier ici...
			$fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
			 
			//On upload et on teste si la fonction renvoie TRUE
			if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
				echo $fichier . " envoyé avec succès !";

				$req_add_item = "INSERT INTO menu_portail (mp_nom, mp_url, mp_icone) VALUES ('$mp_nom', '$mp_url', '$fichier' );";
				$result = $db_gespac->exec ( $req_add_item );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_add_item."\n");

				//Insertion d'un log
				$log_texte = "Ajout de l'item <b>$mp_nom</b> dans le menu du portail";
				$req_log_add_item = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Ajout item', '$log_texte' )";
				$result = $db_gespac->exec ( $req_log_add_item );

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


	}
	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


