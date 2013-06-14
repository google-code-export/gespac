<?PHP

	/* fichier de creation / modif / suppr des items du portail */
	

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../../dump/log_sql.sql', 'a+');
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	
	/*********************************************
	*
	*		ACTIONS SUR LES ITEMS
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'del' ) {
		
		$id	= $_POST['id'];
		
		//On récupère les valeurs de l'item en fonction de son id
	    $row = $con_gespac->QueryRow ( "SELECT mp_nom, mp_icone FROM menu_portail WHERE mp_id=$id" );

		$mp_nom = $row['mp_nom'];
		$mp_icone = $row['mp_icone'];

	    $log_texte = "L'item $mp_nom a été supprimé.";
	    $req_log_suppr_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression item', '$log_texte');";
	    $result = $con_gespac->Execute ( $req_log_suppr_grade );
		
		
		// Suppression du grade de la base
		$req_suppr_item = "DELETE FROM menu_portail WHERE mp_id=$id;";
		$result = $con_gespac->Execute ( $req_suppr_item );
		
		// On supprime ce point de menu pour tous les utilisateurs
		$menus = $con_gespac->QueryAll("SELECT grade_id, grade_menu_portail FROM grades");
		
		foreach ($menus as $menu) {

			$gradeid = $menu["grade_id"];
			$old = $menu["grade_menu_portail"];
			$new = preg_replace("/\"item$id\":\"on\",?/", "", $old);
			
			$rq_grade_menu = "UPDATE grades SET grade_menu_portail='$new' WHERE grade_id=$gradeid;";
			$con_gespac->Execute ( $rq_grade_menu );
			
			fwrite($fp, date("Ymd His") . " " . $rq_grade_menu."\n");
			
		}
		
		
		//Suppression de l'icone
		unlink('../../img/' . $mp_icone);
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_item."\n");
		
		echo "L'item <b>$mp_nom</b> a été supprimé.";

	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$id	= $_POST['id'];
		$mp_nom 	= $_POST ['mp_nom'];
		$mp_url 	= $_POST ['mp_url'];
		
		$req_modif_item = "UPDATE menu_portail SET mp_nom='$mp_nom', mp_url='$mp_url' WHERE mp_id=$id";
		$result = $con_gespac->Execute ( $req_modif_item );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_item."\n");
		
		// insert dans la table log
		$log_texte = "L'item $mp_nom a été modifié";
	    $req_log_modif_item = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $con_gespac->Execute ( $req_log_modif_item );
		
		echo "L'item <b>$mp_nom</b> a été modifié...";
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
			if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier)) {
				
				$req_add_item = "INSERT INTO menu_portail (mp_nom, mp_url, mp_icone) VALUES ('$mp_nom', '$mp_url', '$fichier' );";
				$result = $con_gespac->Execute ( $req_add_item );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_add_item."\n");

				//Insertion d'un log
				$log_texte = "Ajout de l'item <b>$mp_nom</b> dans le menu du portail";
				$req_log_add_item = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Ajout item', '$log_texte' )";
				$result = $con_gespac->Execute ( $req_log_add_item );

				// On se déconnecte de la db
				$con_gespac->Close();	
				
				echo "Le raccourci <b>$mp_nom</b> a été ajouté.";

			}
			else	// En cas d'échec d'upload
				echo 'Echec de l\'upload du fichier <b>' . $fichier . '</b> dans le dossier <b>' . $dossier . '</b>';
				  
		} else	// En cas d'erreur dans l'extension
			 echo $erreur;


	}
	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


