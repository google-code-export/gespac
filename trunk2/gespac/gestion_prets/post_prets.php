<?PHP

	/* 
	
	fichier pour preter ou rendre du matériel
	
	*/

	
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');	

	// Connexion à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	
	
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	
	
	/*********************************************
	*
	*			ACTIONS SUR PRETS
	*
	**********************************************/
	
	
	
	/**************** PRETER ********************/
	
	if ( $action == 'preter' ) {
	
		$matid 		= $_POST['mat_id'];
		$userid		= $_POST['user_id'];
			
		$req_preter_materiel = "UPDATE materiels SET user_id = $userid WHERE mat_id =$matid ;";
		$con_gespac->Execute ( $req_preter_materiel );
		
		//on log la requête SQL
		$log->Insert($req_preter_materiel);
		
		//On récupère le nom d'utilisateur en fonction du user_id
		$user_nom = $con_gespac->QueryOne ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		
		//On récupère le nom de matériel en fonction du mat_id
		$liste_materiel = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel[0];
		$mat_serial = $liste_materiel[1];
		
		echo $log_texte = "Le matériel $mat_nom (Numéro de série : <b>$mat_serial</b>) a été prêté à $user_nom";
				
		// On insère une ligne dans les logs pour tracer tout ça
		$req_log_preter_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Prêté', '$log_texte' );";
		$con_gespac->Execute ( $req_log_preter_materiel );
		
		//on log la requête SQL
		$log->Insert($req_log_preter_materiel);
				
		// On ouvre une autre fenêtre pour la convention de pret
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_pret.php?matid=$matid&userid=$userid\", 'CONVENTION DE PRET');</script>";
		
		
	}

	
	/**************** RENDRE ********************/	
	if ( $action == 'rendre' ) {
	
		$matid 		= $_POST['mat_id'];
		$userid		= $_POST['user_id'];
				
		// On récupère le nom de l'utilisateur en fonction du user_id
		$user_nom = $con_gespac->QueryOne ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		
		
		// On récupère le nom du matériel en fonction du mat_id
		$liste_materiel = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0];
		$mat_serial = $liste_materiel [1];
		
		$log_texte = urlencode("<a href='./gestion_prets/convention_retour.php?matid=$matid&userid=$userid&id_conv=0' target=_blank>$user_nom a rendu le matériel $mat_nom (Numéro de série : <b>$mat_serial</b>)</a>");
		
		// On insère une ligne dans les logs pour tracer tout ça
		$req_log_rendu_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Rendu', '$log_texte' );";
		$con_gespac->Execute ( $req_log_rendu_materiel );
		
		//on log la requête SQL
		$log->Insert($req_log_rendu_materiel);

		echo "Le matériel est rendu !";
				
		// On ouvre une autre fenêtre pour la convention de retour
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_retour.php?matid=$matid&userid=$userid&id_conv=1\", 'CONVENTION DE RETOUR');</script>";
		
	
		// On ne rend pas ici le matériel mais dans le fichier convention_retour.php car la mise à jour de la table est trop rapide et les données n'existent plus lors de la création de la convention (donc convention vierge)
						

	}

?>
