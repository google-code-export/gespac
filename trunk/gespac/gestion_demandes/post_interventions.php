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
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	//Récupération du mail du compte ati (root)
	$mail_root = $db_gespac->queryOne("SELECT clg_ati_mail FROM college");
	
	// PARAMETRAGE DU SMTP 
	//ini_set('SMTP','smtp.intranet.cg13.oleane.fr'); //Mettre l'adresse SMTP dans le fichier de config
	ini_set('sendmail_from', $mail_root);
	
	
	
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
		
		
		// on récupére le numéro d'id du user qui fait la clôture du dossier
		$req_id_user = $db_gespac->queryRow ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0];
		$user_nom 	=  $req_id_user[1];
			
				
		// On met à jour l'intervention
		$req_change_etat = "UPDATE interventions SET interv_cloture='$date_clot', interv_text='$reponse', user_id=$user_id WHERE interv_id=$inter";
		$result = $db_gespac->exec ( $req_change_etat );
		
		// On ferme le dossier
		$req_change_etat_demande = "UPDATE demandes SET dem_etat='$etat' WHERE dem_id=$dossier";
		$result = $db_gespac->exec ( $req_change_etat_demande );
		
		// On met un dernier commentaire de cloture dans le dossier
		$req_cloture_demande = "INSERT INTO demandes_textes ( txt_texte, dem_id, user_id, txt_etat ) VALUES ( '$reponse', $dossier, $user_id, '$etat');";
		$result = $db_gespac->exec ( $req_cloture_demande );

		//Insertion d'un log
		$log_texte = "Modification de <b>$user_nom</b> sur le dossier <b>$dossier</b>. Le dossier est passé à l`état : <b>$etat</b> ";
		$req_log_modif_dem = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Etat demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_dem );
		
		
		/*************************
				MAILING
		**************************/
		
		// on récupére le numéro d'id du user qui fait la demande. Attention on ne récupére les infos de mail que si le compte est actif.
		$user_mail = $db_gespac->queryOne ( "SELECT user_mail FROM users WHERE user_logon='$login' AND user_mailing=1" );
		
		//Récupération des comptes qui ont le grade ATI
		$req_comptes_ati = $db_gespac->queryAll("SELECT user_nom, user_mail FROM users, grades WHERE grade_nom='ati' AND users.grade_id = grades.grade_id AND user_mailing=1");
		
		//On récupére les identifiants du demandeur en fonction du numéro de dossier
		$id_demandeur = $db_gespac->queryOne("SELECT user_demandeur_id FROM demandes WHERE dem_id='$dossier'");
		
		//on récupère uniquement les comptes qui sont actifs ! Un mail est envoyé au demandeur pour l'avertir que son dossier a été clôturé
		$req_mail_demandeur	= $db_gespac->queryRow("SELECT user_mail, user_nom FROM users WHERE user_id='$id_demandeur' AND user_mailing=1");
		$mail_demandeur     = $req_mail_demandeur[0];
		$nom_demandeur      = $req_mail_demandeur[1];
		
		// CORPS DU MAIL
		$corps_mail = "L'intervention (n° : <b>$inter</b>) concernant le dossier n°<b>$dossier</b> a été clôturée le <b>$date_clot</b>. Vous pouvez le suivre en consultant la liste de vos interventions sur votre interface GESPAC.<br><br>";
		$corps_mail .= "L'état du dossier est actuellement : <b>'$etat'<br><br></b>";
		$commentaire = ($reponse == '') ? "Pas de commentaire." : $commentaire = $reponse;
		$corps_mail .= "Commentaire de l'utilisateur : <i>'$commentaire'</i><br><br>";
		$corps_mail .= "<i>Ce mail est envoyé automatiquement. Inutile d'y répondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter à <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
		$corps_mail .= "L'équipe GESPAC";
		
		$sujet_mail = '[GESPAC]Clôture du dossier n°'.$dossier.' par l\'utilisateur '.$user_nom;
		
		$message = '<html><head><title>'.$sujet_mail.'</title></head><body>'.$corps_mail.'</body></html>'; 
		
		//Boucle pour récupérer la liste des mails des ATIs
		foreach ( $req_comptes_ati as $record ) {
				
			$mail_nom = $record[0];
			$mail_ati = $record[1];
				
			if (empty($mail_ati)) { //le champ $mail_ati est vide
				$liste_mail_ati .= ''; //on ne concatène rien dans la variable $liste_mail_ati
			} else { // si ce champ n'est pas vide
				$liste_mail_ati .= $mail_ati.','; //on colle à la variable la valeur de $mail_ati suivi d'une virgule et d'un espace
			}
		}
		
		// on concatène les mails des ati avec le mail du destinataire. L'envoi en Cc nous met une erreur.
		$mail_destinataire = $liste_mail_ati.$mail_demandeur.','.$user_mail;
		//on cherche si il y a une virgule après le séparateur ','. Si c'est le cas, on remplace cette virgule par une seule virgule.
		$mail_destinataire = str_replace (",,", ",", $mail_destinataire);
		
		/******************************
			Vérification des doublons
		*******************************/
		
		// on transforme la chaine $mail_prof en un tableau
		$verif_doublon_mail_destinataire = explode(',', $mail_destinataire); 
		// Cette fonction va nous retourner un tableau complètement dédoublonné !! Magique !
		$verif_doublon_mail_destinataire = array_unique($verif_doublon_mail_destinataire);
		// On reconstruit notre string à partir du tableau dédoublonné
		$mail_destinataire = implode(",", $verif_doublon_mail_destinataire);
		
		
		$headers ='From: '.$mail_root."\n"; //c'est toujours le compte root qui envoie le mail
		$headers .='Reply-To: '.$mail_root."\n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
		$headers .='Content-Transfer-Encoding: 8bit'; 
		
		if(mail($mail_destinataire, $sujet_mail, $message, $headers)) { 
			echo 'Le(s) mail(s) a (ont) bien été envoyé(s) !<br>'; 
		} else { 
			echo 'Le(s) mail(s) n\'a (ont) pas été envoyé(s) !<br>'; 
		}
		
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

		} else {	// si on a pas affaire à une installation ou une reparation, l'id de la salle et des pc est à 0 (id fictif)
			
			$pc 			= 0;
			$salle 			= 0;
			$texte 		= addslashes(utf8_decode( $_POST ['texte_intervention'] ));
			
			$req_add_inter = "INSERT INTO interventions ( interv_text, user_id, interv_type, salle_id, mat_id ) VALUES ( '$texte', $user_id, '$type', $salle, $pc )";
			$result = $db_gespac->exec ( $req_add_inter );		
		}
		

		echo "Votre intervention a été prise en compte...";
		
		//Insertion d'un log
		$log_texte = "L'intervention de <b>$type</b> a été créée par <b>$user_nom</b>";
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création inter', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_mat );
	}

	

?>