<?PHP
	session_start();
	
	/* fichier de creation / modification d'un dossier	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	
	
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	

	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$type 			= $_POST ['type'];
		$commentaire 	= $_POST ['commentaire'];
		$liste_mat		= $_POST ['liste_mat'];
		$current_user	= $_SESSION['login'];
		
		// On créé le dossier
		$con_gespac->Execute("INSERT INTO dossiers (dossier_type, dossier_mat) VALUES ('$type', '$liste_mat');");
	
		// On récupère l'id du dernier dossier créé
		$last_id = $con_gespac->GetLastID();
		
		// On créé une page dans le dossier
		$con_gespac->Execute("INSERT INTO dossiers (dossier_id, txt_user, txt_texte, txt_etat) VALUES ($last_id, '$current_user', '$commentaire', 'ouverture');");
	}
	


?>
