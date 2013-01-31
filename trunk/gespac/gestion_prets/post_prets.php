<?PHP

	/* 
	
	fichier pour preter ou rendre du mat�riel
	
	*/

	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	// Connexion � la base de donn�es GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	
	
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
		$con_gespac->Execute ( $req_preter_materiel );
		
		//on log la requ�te SQL
		$log->Insert($req_preter_materiel);
		
		//On r�cup�re le nom d'utilisateur en fonction du user_id
		$user_nom = $con_gespac->QueryOne ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		
		//On r�cup�re le nom de mat�riel en fonction du mat_id
		$liste_materiel = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel[0];
		$mat_serial = $liste_materiel[1];
		
		echo $log_texte = "Le mat�riel $mat_nom (Num�ro de s�rie : <b>$mat_serial</b>) a �t� pr�t� � $user_nom";
				
		// On ins�re une ligne dans les logs pour tracer tout �a
		$req_log_preter_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Pr�t�', '$log_texte' );";
		$con_gespac->Execute ( $req_log_preter_materiel );
		
		//on log la requ�te SQL
		$log->Insert($req_log_preter_materiel);
				
		// On ouvre une autre fen�tre pour la convention de pret
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_pret.php?matid=$matid&userid=$userid\", 'CONVENTION DE PRET');</script>";
		
		
	}

	
	/**************** RENDRE ********************/	
	if ( $action == 'rendre' ) {
	
		$matid 		= $_GET['matid'];
		$userid		= $_GET['userid'];
	
		
		// On r�cup�re le nom de l'utilisateur en fonction du user_id
		$user_nom = $con_gespac->QueryOne ( "SELECT user_nom FROM users WHERE user_id = $userid" );
		
		
		// On r�cup�re le nom du mat�riel en fonction du mat_id
		$liste_materiel = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial FROM materiels WHERE mat_id = $matid" );
		$mat_nom = $liste_materiel [0];
		$mat_serial = $liste_materiel [1];
		
		$log_texte = urlencode("<a href='./gestion_prets/convention_retour.php?matid=$matid&userid=$userid&id_conv=0' target=_blank>$user_nom a rendu le mat�riel $mat_nom (Num�ro de s�rie : <b>$mat_serial</b>)</a>");
		
		// On ins�re une ligne dans les logs pour tracer tout �a
		$req_log_rendu_materiel = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Rendu', '$log_texte' );";
		$con_gespac->Execute ( $req_log_rendu_materiel );
		
		//on log la requ�te SQL
		$log->Insert($req_log_rendu_materiel);

		echo "Le mat�riel est rendu !";
				
		// On ouvre une autre fen�tre pour la convention de retour
		echo "<script type='text/javascript'>window.open(\"gestion_prets/convention_retour.php?matid=$matid&userid=$userid&id_conv=1\", 'CONVENTION DE RETOUR');</script>";
		
	
		// On ne rend pas ici le mat�riel mais dans le fichier convention_retour.php car la mise � jour de la table est trop rapide et les donn�es n'existent plus lors de la cr�ation de la convention (donc convention vierge)
						

	}

?>
