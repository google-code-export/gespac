<?PHP
session_start();

	/* 
	
		fichier de creation / modif / suppr des demandes
	
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on r�cup�re les param�tres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	
	
	/*********************************************
	*
	*		ACTIONS SUR DEMANDES
	*
	**********************************************/
	
	
	/**************** MODIFICATION ********************/
		
	if ( $action == 'mod' ) {
	
		$dossier    = $_POST ['dossier'];
		$reponse 	= addslashes(utf8_decode( $_POST ['reponse'] ));
		$etat 		= $_POST ['etat'];
		$salle_id 	= $_POST ['salle'];
		$mat_id 	= $_POST ['mat'];
		$login		= $_SESSION['login'];
		
		
		// on r�cup�re le num�ro d'id du user qui fait la demande
		$req_id_user = $db_gespac->queryAll ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0][0];
		$user_nom 	=  $req_id_user[0][1];
		
		// insert d'un texte associ� � la demande
		$req_insert_txt = "INSERT INTO demandes_textes ( txt_texte, dem_id, user_id, txt_etat ) VALUES ( '$reponse', $dossier, $user_id, '$etat');";
		$result = $db_gespac->exec ( $req_insert_txt );
		
		// on change l'�tat du dossier
		$req_change_etat = "UPDATE demandes SET dem_etat='$etat' WHERE dem_id=$dossier";
		$result = $db_gespac->exec ( $req_change_etat );
		
		//Insertion d'un log
		$log_texte = "Modification de <b>$user_nom</b> sur le dossier <b>$dossier</b>. Le dossier est pass� � l'�tat : <b>$etat</b> ";
		$req_log_modif_dem = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Etat demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_dem );
		
		// Si l'�tat est "intervention" on cr�� en m�me temps une inter dans la table des inter
		if ( $etat == "intervention" ) {
			$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id ) VALUES ( $dossier, $salle_id, $mat_id);";
			$result = $db_gespac->exec ( $req_create_inter );
		}
		
		// Si l'�tat est "clos" on ferme aussi l'inter
		if ( $etat == "clos" ) {
			$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id ) VALUES ( $dossier, $salle_id, $mat_id);";
			$result = $db_gespac->exec ( $req_create_inter );
		}
	}
	
	
	/**************** CREATION ********************/
	
	if ( $action == 'add' ) {
		
		$type 		= $_POST ['type_demande'];
		$login		= $_SESSION['login'];
			
		// on r�cup�re le num�ro d'id du user qui fait la demande
		$req_id_user = $db_gespac->queryAll ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0][0];
		$user_nom 	=  $req_id_user[0][1];
	
		if ( $type == "installation" || $type == "reparation" ) {	
		
			$salle 			= $_POST ['salle_demande'];
			$pc 			= $_POST ['pc_demande'];
			$creat_inter	= $_POST ['creat_inter'];
			$texte 			= addslashes(utf8_decode( $_POST ['texte_demande'] ));
			
			if ( $creat_inter == 'on') {
				$etat = "intervention";	
			} else {
				$etat = "attente";
			}

			$req_add_demande = "INSERT INTO demandes ( dem_text, dem_etat, user_demandeur_id, salle_id, mat_id, dem_type ) VALUES ( '$texte', '$etat', $user_id, $salle, $pc, '$type' )";
			$result = $db_gespac->exec ( $req_add_demande );
			
			// Si l'�tat est "intervention" on cr�� en m�me temps une inter dans la table des inter
			if ( $creat_inter == 'on' ) {
				// On r�cup�re le demande_id le plus �lev� : c'est tr�s probablement le dernier cr�� donc le dossier en cours de cr�ation
				$req_dossier_id = $db_gespac->queryAll ( "SELECT max(dem_id) FROM demandes" );
				$dossier 	=  $req_dossier_id[0][0];
			
				$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id, interv_text ) VALUES ( $dossier, $salle, $pc, '$texte' );";
				$result = $db_gespac->exec ( $req_create_inter );
			}
			

		} else {	// si on a pas affaire � une installation ou une reparation, pas la peine de renseigner la salle et le pc
			
			$texte 		= addslashes(utf8_decode( $_POST ['texte_demande'] ));
			$creat_inter	= $_POST ['creat_inter'];
			
			if ( $creat_inter == 'on')	$etat = "intervention";
			else $etat = "attente";
			
			$req_add_demande = "INSERT INTO demandes ( dem_text, dem_etat, user_demandeur_id, dem_type ) VALUES ( '$texte', '$etat', $user_id, '$type' )";
			$result = $db_gespac->exec ( $req_add_demande );		
		}
		

		

		echo "Votre demande a �t� prise en compte...";
		
		//Insertion d'un log
		$log_texte = "La demande de <b>$type</b> a �t� cr��e par <b>$user_nom</b>";
		
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_mat );
	}

	

?>


