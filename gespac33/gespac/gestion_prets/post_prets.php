<?PHP

	/* 
	
	fichier pour preter ou rendre du mat�riel
	
	*/

	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// options facultatives de cnx � la db
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL,);

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);
	
	
	// on r�cup�re les param�tres de l'url	
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
		
		//On r�cup�re le nom d'utilisateur en fonction du user_id
		$liste_user 	= $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		$user_nom = $liste_user [0][0];
		
		//On r�cup�re le nom de mat�riel en fonction du mat_id
		$liste_materiel = $db_gespac->queryAll ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0][0];
		$mat_serial = $liste_materiel [0][1];
		
		$log_texte = "Le mat�riel $mat_nom (Num�ro de s�rie : <b>$mat_serial</b>) a �t� pr�t� � $user_nom";
		
		// On ins�re une ligne dans les logs pour tracer tout �a
		$req_log_preter_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Pr�t�', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_preter_materiel );
		
		// On ouvre une autre fen�tre pour la convention de pret
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_pret.php?matid=$matid&userid=$userid\", 'CONVENTION DE PRET');</script>";
		
		
	}

	
	/**************** RENDRE ********************/	
	if ( $action == 'rendre' ) {
	
		$matid 		= $_GET['matid'];
		$userid		= $_GET['userid'];
		
		// On r�cup�re le nom de l'utilisateur en fonction du user_id
		$liste_user = $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		$user_nom = $liste_user [0][0];
		
		// On r�cup�re le nom du mat�riel en fonction du mat_id
		$liste_materiel = $db_gespac->queryAll ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0][0];
		$mat_serial = $liste_materiel [0][1];
		
		$log_texte = "$user_nom a rendu le mat�riel $mat_nom (Num�ro de s�rie : <b>$mat_serial</b>)";
		
		// On ins�re une ligne dans les logs pour tracer tout �a
		$req_log_rendu_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Rendu', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_rendu_materiel );
				
		// On ouvre une autre fen�tre pour la convention de retour
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_retour.php?matid=$matid&userid=$userid\", 'CONVENTION DE RETOUR');</script>";
		
	
		// On ne rend pas ici le mat�riel mais dans le fichier convention_retour.php car la mise � jour de la table est trop rapide et les donn�es n'existent plus lors de la cr�ation de la convention (donc convention vierge)
						

	}

?>