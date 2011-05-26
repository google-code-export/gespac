<?PHP
	session_start();
	
	/* fichier de creation / modification d'un dossier	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		
	
	
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	

	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$type 			= addslashes($_POST ['type']);
		$commentaire 	= addslashes($_POST ['commentaire']);
		$liste_mat		= $_POST ['liste_mat'];
		$add_inter		= $_POST ['add_inter'];
		$current_user	= $con_gespac->QueryOne("SELECT user_id FROM users WHERE user_logon = '" . $_SESSION['login'] . "'");
	
		
			
		// On créé le dossier
		$rq = "INSERT INTO dossiers (dossier_type, dossier_mat) VALUES ('$type', '$liste_mat');";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
		
	
		// On récupère l'id du dernier dossier créé
		$last_id = $con_gespac->GetLastID();
		
		// Si la case créer l'intervention est cochée
		$etat = $add_inter == "on" ? "intervention" : "ouverture";
		
		// On créé une page dans le dossier
		$rq = "INSERT INTO dossiers_textes (dossier_id, txt_user, txt_texte, txt_etat) VALUES ($last_id, '$current_user', '$commentaire', '$etat');";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
		
		echo utf8_decode("le dossier $last_id a été créé.");
		
	}
	

	if ( $action == 'modif' ) {
	
		$dossierid		= $_POST['dossierid'];
		$etat 			= addslashes($_POST ['etat']);
		$commentaire 	= addslashes($_POST ['commentaire']);
		$current_user	= $con_gespac->QueryOne("SELECT user_id FROM users WHERE user_logon = '" . $_SESSION['login'] . "'");
		
		// On créé une page dans le dossier
		$rq = "INSERT INTO dossiers_textes (dossier_id, txt_user, txt_texte, txt_etat) VALUES ($dossierid, '$current_user', '$commentaire', '$etat');";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
	
	
	}


?>
