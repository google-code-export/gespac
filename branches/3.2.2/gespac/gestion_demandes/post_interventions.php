<?PHP
session_start();

	/* 
	
		fichier de creation / modif / suppr des interventions
	
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion à la base de données	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	
	
	/*********************************************
	*
	*	ACTIONS SUR INTERVENTIONS
	*
	**********************************************/
	
	
	/**************** MODIFICATION ********************/
		
	if ( $action == 'mod' ) {
	
		$reponse 	= addslashes(utf8_decode( $_POST ['reponse'] ));
		$etat 		= "clos";
		$dossier	= $_POST ['dossier'];
		$login		= $_SESSION['login'];
		$date_clot	= date( 'Y-m-d H:i:s', time() );
		$inter		= $_POST ['inter'];
		
		
		// on récupére le numéro d'id du user qui fait la demande
		$req_id_user = $db_gespac->queryRow ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0];
		$user_nom 	=  $req_id_user[1];
			
				
		// On met à jour l'intervention
		$req_change_etat = "UPDATE interventions SET interv_cloture='$date_clot', interv_text='$reponse', user_id=$user_id WHERE interv_id=$inter";
		$result = $db_gespac->exec ( $req_change_etat );
		
		// On ferme le dossier
		$req_change_etat_demande = "UPDATE demandes SET dem_etat='clos' WHERE dem_id=$dossier";
		$result = $db_gespac->exec ( $req_change_etat_demande );
		
		// On met un dernier commentaire de cloture dans le dossier
		$req_cloture_demande = "INSERT INTO demandes_textes ( txt_texte, dem_id, user_id, txt_etat ) VALUES ( '$reponse', $dossier, $user_id, '$etat');";
		$result = $db_gespac->exec ( $req_cloture_demande );

		//Insertion d'un log
		$log_texte = "Modification de <b>$user_nom</b> sur le dossier <b>$dossier</b>. Le dossier est passé à l`état : <b>$etat</b> ";
		$req_log_modif_dem = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Etat demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_dem );
	}
	
	
	/**************** CREATION ********************/
	
	if ( $action == 'add' ) {
		
		$type 		= $_POST ['type_intervention'];
		$login		= $_SESSION['login'];
			
		// on récupére le numéro d'id du user qui fait l'inter
		$req_id_user = $db_gespac->queryRow ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0];
		$user_nom 	=  $req_id_user[1];
	
		if ( $type == "installation" || $type == "reparation" ) {	
		
			$salle 		= $_POST ['salle_intervention'];
			$pc 		= $_POST ['pc_intervention'];
			$texte 		= addslashes(utf8_decode( $_POST ['texte_intervention'] ));
			$date_inter = date("Y-m-d");

			$req_add_inter = "INSERT INTO interventions ( interv_text, interv_date, interv_type, salle_id, mat_id, dem_id, user_id ) VALUES ( '$texte', '$date_inter', '$type', $salle, $pc, 0, $user_id )";
			$result = $db_gespac->exec ( $req_add_inter );

		} else {	// si on a pas affaire à une installation ou une reparation, pas la peine de renseigner la salle et le pc
			
			$texte 		= addslashes(utf8_decode( $_POST ['texte_intervention'] ));
			
			$req_add_inter = "INSERT INTO interventions ( interv_text, user_id, interv_type ) VALUES ( '$texte', $user_id, '$type' )";
			$result = $db_gespac->exec ( $req_add_inter );		
		}
		

		echo "Votre intervention a été prise en compte...";
		
		//Insertion d'un log
		$log_texte = "L'intervention de <b>$type</b> a été créée par <b>$user_nom</b>";
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création inter', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_mat );
	}

	

?>


