<?PHP

	/* 
	
	fichier pour preter ou rendre du matériel
	
	*/

	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion à la base de données	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// options facultatives de cnx à la db
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL,);

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);
	
	
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	
	
	/*********************************************
	*
	*			ACTIONS SUR PRETS
	*
	**********************************************/
	
	
	
	/**************** PRETER ********************/
	
	if ( $action == 'preter' ) {
	
		$matid 		= $_GET['matid'];
		$userid		= $_GET['userid'];
			
		$req_preter_materiel = "UPDATE materiels SET user_id = $userid WHERE mat_id =$matid ;";
		$result = $db_gespac->exec ( $req_preter_materiel );
		
		//On récupère le nom d'utilisateur en fonction du user_id
		$liste_user 	= $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		$user_nom = $liste_user [0][0];
		
		//On récupère le nom de matériel en fonction du mat_id
		$liste_materiel = $db_gespac->queryAll ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0][0];
		$mat_serial = $liste_materiel [0][1];
		
		$log_texte = "Le matériel $mat_nom (Numéro de série : <b>$mat_serial</b>) a été prêté à $user_nom";
		
		// On insère une ligne dans les logs pour tracer tout ça
		$req_log_preter_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Prêté', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_preter_materiel );
		
		// On ouvre une autre fenêtre pour la convention de pret
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_pret.php?matid=$matid&userid=$userid\", 'CONVENTION DE PRET');</script>";
		
		
	}

	
	/**************** RENDRE ********************/	
	if ( $action == 'rendre' ) {
	
		$matid 		= $_GET['matid'];
		$userid		= $_GET['userid'];
		
		// On récupère le nom de l'utilisateur en fonction du user_id
		$liste_user = $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		$user_nom = $liste_user [0][0];
		
		// On récupère le nom du matériel en fonction du mat_id
		$liste_materiel = $db_gespac->queryAll ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0][0];
		$mat_serial = $liste_materiel [0][1];
		
		$log_texte = "$user_nom a rendu le matériel $mat_nom (Numéro de série : <b>$mat_serial</b>)";
		
		// On insère une ligne dans les logs pour tracer tout ça
		$req_log_rendu_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Rendu', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_rendu_materiel );
				
		// On ouvre une autre fenêtre pour la convention de retour
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_retour.php?matid=$matid&userid=$userid\", 'CONVENTION DE RETOUR');</script>";
		
	
		// On ne rend pas ici le matériel mais dans le fichier convention_retour.php car la mise à jour de la table est trop rapide et les données n'existent plus lors de la création de la convention (donc convention vierge)
						

	}

?>