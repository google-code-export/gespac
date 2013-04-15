<?PHP


	/* fichier de creation / modif / suppr des grades
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une création
	
	*/
	
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');		
	include_once ('../../class/Sql.class.php');		

	
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	
	/*********************************************
	*
	*		ACTIONS SUR GRADES
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {

        //Insertion d'un log
		
		//On récupère le nom du grade en fonction de son id
	    $grade_nom = $con_gespac->QueryOne ( "SELECT grade_nom FROM grades WHERE grade_id=$id" );

	    $log_texte = "Le grade $grade_nom a été supprimé et les utilisateurs affectés sont désormais du grade \"invité\"";

	    $req_log_suppr_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression grade', '$log_texte');";
	    $con_gespac->Execute ( $req_log_suppr_grade );
		//On log la requête SQL
		$log->Insert( $req_log_suppr_grade );
		
		// On test si le grade invité existe
		$grade_id_invite = $con_gespac->QueryOne ( "SELECT grade_id FROM grades WHERE grade_nom LIKE 'invit%'" );
		
		// Si il n'existe pas, on le créé
		if ( $grade_id_invite == "" ) {
			$req_insert_grade_invite = "INSERT INTO grades ( grade_nom, grade_menu, est_modifiable ) VALUES ( 'invité', '', '0');";
			$con_gespac->Execute ( $req_insert_grade_invite );
			$log->Insert( $req_insert_grade_invite );
		}
			
		// On récupère le grade_id du grade "invité"
		$grade_id_invite = $con_gespac->QueryOne ( "SELECT grade_id FROM grades WHERE grade_nom='invité'" );
		
		// On colle tous les utilisateurs du grade dans le grade générique "invité"
		$req_maj_users = "UPDATE users SET grade_id=$grade_id_invite WHERE grade_id=$id;";
		$con_gespac->Execute ( $req_maj_users );
		
		// On log la requête SQL
		$log->Insert( $req_maj_users );
		
		// Suppression du grade de la base
		$req_suppr_grade = "DELETE FROM grades WHERE grade_id=$id;";
		$con_gespac->Execute ( $req_suppr_grade );
		
		// On log la requête SQL
		$log->Insert( $req_suppr_grade );
		
		echo "Le grade <b>$grade_nom</b> a été supprimé et les utilisateurs affectés sont désormais du grade \"invité\"";
	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$id     	= $_POST ['id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));

		$req_modif_grade = "UPDATE grades SET grade_nom='$nom' WHERE grade_id=$id";
		$con_gespac->Execute ( $req_modif_grade );
		
		// On log la requête SQL
		$log->Insert( $req_modif_grade );
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le grade $nom a été modifié";
		
	    $req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $con_gespac->Execute ( $req_log_modif_grade );
		$log->Insert( $req_log_modif_grade );
		
		echo "Le grade <b>$nom</b> a été modifié...";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		
		$req_add_grade = "INSERT INTO grades ( grade_nom, grade_menu) VALUES ( '$nom', '' )";
		$con_gespac->Execute ( $req_add_grade );
		
		// On log la requête SQL
		$log->Insert( $req_add_grade );
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le grade $nom a été créé";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création compte', '$log_texte' );";
	    $con_gespac->Execute ( $req_log_creation_user );
	    $log->Insert( $req_log_creation_user );
		
		echo "Le grade <b>$nom</b> a été ajouté...";
	}

?>


