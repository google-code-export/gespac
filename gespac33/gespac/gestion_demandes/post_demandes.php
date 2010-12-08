<?PHP
session_start();

	/* 
	
		fichier de creation / modif / suppr des demandes
	
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
	$mail_root = $db_gespac->queryOne("SELECT user_mail FROM users WHERE user_id=1");
	
	// PARAMETRAGE DU SMTP 
	ini_set('SMTP','smtp.intranet.cg13.oleane.fr'); //Mettre l'adresse SMTP dans le fichier de config
	ini_set('sendmail_from', $mail_root);
	
	
	/*********************************************
	*
	*		ACTIONS SUR DEMANDES
	*
	**********************************************/
	
	
	/**************** MODIFICATION ********************/
		
	if ( $action == 'mod' ) {
	
		$dossier    		= $_POST ['dossier'];
		$reponse 			= addslashes(utf8_decode( $_POST ['reponse'] ));
		$etat 				= $_POST ['etat'];
		$salle_id 			= $_POST ['salle'];
		$mat_id 			= $_POST ['mat'];
		$login				= $_SESSION['login'];
		
		
		
		
		//on récupére le type de la demande
		$type_demande = $db_gespac->queryOne("SELECT dem_type FROM demandes WHERE dem_id=$dossier");
		
		// on récupére le numéro d'id et le nom du user qui fait la demande
		$req_id_user = $db_gespac->queryRow ( "SELECT user_id, user_nom FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0];
		$user_nom 	=  $req_id_user[1];
		
		
		//Insertion d'un log
		$log_texte = "Modification de <b>$user_nom</b> sur le dossier <b>$dossier</b>. Le dossier est passé à l`état : <b>$etat</b> ";
		$req_log_modif_dem = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Etat demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_dem );
		
		
		// insert d'un texte associé à la demande
		$req_insert_txt = "INSERT INTO demandes_textes ( txt_texte, dem_id, user_id, txt_etat ) VALUES ( '$reponse', $dossier, $user_id, '$etat');";
		$result = $db_gespac->exec ( $req_insert_txt );
		
		// on change l'état du dossier
		$req_change_etat = "UPDATE demandes SET dem_etat='$etat' WHERE dem_id=$dossier";
		$result = $db_gespac->exec ( $req_change_etat );
		
		if ($type_demande == "installation" || $type_demande == "reparation") {
			$salle_id = $_POST['salle'];
			$mat_id = $_POST['mat'];
		} else { 
			//dans le cas d'une formation, d'un usage ou autre, on affecte une valeur 0 aux mat_id et salle_id afin de pouvoir remplir la requête avec une valeur
			$salle_id = 0;
			$mat_id   = 0;
		}
		
		// Si l'état est "intervention" on créé en même temps une inter dans la table des inter
		if ( $etat == "intervention" ) {
			$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id ) VALUES ( $dossier, $salle_id, $mat_id);";
			$result = $db_gespac->exec ( $req_create_inter );
		}
		

		/*************************
				MAILING
		**************************/
		
		// on récupére le numéro d'id du user qui fait la demande. Attention on ne récupére les infos de mail que si le compte est actif.
		$user_mail = $db_gespac->queryOne ( "SELECT user_mail FROM users WHERE user_logon='$login' AND user_mailing=1" );
		
		//Récupération des comptes qui ont le grade ATI
		$req_comptes_ati = $db_gespac->queryAll("SELECT user_nom, user_mail FROM users WHERE grade_id=2 AND user_mailing=1");
		
		//On récupére les identifiants de l'intervenant et du demandeur en fonction du numéro de dossier
		$id_demandeur = $db_gespac->queryOne("SELECT user_demandeur_id FROM demandes WHERE dem_id='$dossier'");
		
		//on récupère uniquement les comptes qui sont actifs !
		$req_mail_demandeur	= $db_gespac->queryRow("SELECT user_mail, user_nom FROM users WHERE user_id='$id_demandeur' AND user_mailing=1");
		$mail_demandeur     = $req_mail_demandeur[0];
		$nom_demandeur      = $req_mail_demandeur[1];
		
		// CORPS DU MAIL
		$corps_mail = "Le dossier <b>$dossier</b> a changé d'état. Vous pouvez le suivre en affichant la liste de vos dossiers par le lien suivant : http://localhost/developpement/gespac33/gespac/gestion_demandes/voir_demandes.php<br><br>";
		$corps_mail .= "L'état du dossier est actuellement : <b>'$etat'<br><br></b>";
		$corps_mail .= "Commentaire de l'utilisateur : <i>'$reponse'</i><br><br>";
		$corps_mail .= "<i>Ce mail est envoyé automatiquement. Inutile d'y répondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter à <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
		$corps_mail .= "L'équipe GESPAC";
		
		$sujet_mail = '[GESPAC]Modification de l\'état du dossier n°'.$dossier.' par l\'utilisateur '.$user_nom;
		
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
		
		$type 		= $_POST ['type_demande'];
		$login		= $_SESSION['login'];
			
		// on récupére le numéro d'id du user qui fait la demande ainsique son nom et son grade
		$req_id_user 	= $db_gespac->queryRow ( "SELECT user_id, user_nom, grade_id FROM users WHERE user_logon='$login'" );
		$user_id 		=  $req_id_user[0];
		$user_nom 		=  $req_id_user[1];
		$grade_id	 	=  $req_id_user[2];
		
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
			
			// Si l'état est "intervention" on créé en même temps une inter dans la table des inter
			if ( $creat_inter == 'on' ) {
				// On récupère le demande_id le plus élevé : c'est très probablement le dernier créé donc le dossier en cours de création
				$req_dossier_id = $db_gespac->queryAll ( "SELECT max(dem_id) FROM demandes" );
				$dossier 	=  $req_dossier_id[0][0];
			
				$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id, interv_text ) VALUES ( $dossier, $salle, $pc, '$texte' );";
				$result = $db_gespac->exec ( $req_create_inter );
				
			}
			

		} else {	// si on a pas affaire à une installation ou une reparation, on donne un id de PC et de salle bidon (0) 
			
			$pc 			= 0;
			$salle 			= 0;
			$texte 			= addslashes(utf8_decode( $_POST ['texte_demande'] ));
			$creat_inter	= $_POST ['creat_inter'];
			
		
			
			if ( $creat_inter == 'on')	$etat = "intervention";
			else $etat = "attente";
			
			$req_add_demande = "INSERT INTO demandes ( dem_text, dem_etat, user_demandeur_id, salle_id, mat_id, dem_type ) VALUES ( '$texte', '$etat', $user_id, $salle, $pc, '$type' )";
			$result = $db_gespac->exec ( $req_add_demande );
			
			// Si l'état est "intervention" on créé en même temps une inter dans la table des inter
			if ( $creat_inter == 'on' ) {
				// On récupère le demande_id le plus élevé : c'est très probablement le dernier créé donc le dossier en cours de création
				$req_dossier_id = $db_gespac->queryAll ( "SELECT max(dem_id) FROM demandes" );
				$dossier 	=  $req_dossier_id[0][0];
			
				$req_create_inter = "INSERT INTO interventions ( dem_id, salle_id, mat_id, interv_text ) VALUES ( $dossier, $salle, $pc, '$texte' );";
				$result = $db_gespac->exec ( $req_create_inter );
			}
		}

		echo "Votre demande a été prise en compte...";
		
		//Insertion d'un log
		$log_texte = "La demande de <b>$type</b> a été créée par <b>$user_nom</b>";
		
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_mat );
		
		
		/*************************
				MAILING
		**************************/
		
		/*************************************************
			Fait uniquement pour les grades prof et ATI
		**************************************************/
		
		// on récupére le mail du user qui fait la demande. Attention on ne récupére ces infos que si le compte est actif.
		$user_mail = $db_gespac->queryOne ( "SELECT user_mail FROM users WHERE user_logon='$login' AND user_mailing=1" );
		
		//on récupére les noms et mails de ces comptes avec le grade ATI
		$req_comptes_ati = $db_gespac->queryAll("SELECT user_logon, user_mail FROM users WHERE grade_id=2 AND user_mailing=1"); 
		
		
		// CORPS DU MAIL
		$corps_mail = "Un nouveau dossier a été créé. Vous pouvez le suivre en affichant la liste de vos dossiers par le lien suivant : http://localhost/developpement/gespac33/gespac/gestion_demandes/voir_demandes.php<br><br>";
		$corps_mail .= "L'état du dossier est actuellement : <b>'$etat'<br><br></b>";
		$corps_mail .= "Commentaire de l'utilisateur : <i>'$reponse'</i><br><br>";
		$corps_mail .= "<i>Ce mail est envoyé automatiquement. Inutile d'y répondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter à <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
		$corps_mail .= "L'équipe GESPAC";
		
		$sujet_mail = '[GESPAC]Création d\'un nouveau dossier par l\'utilisateur '.$user_nom;
		
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
		
		
		//ON TESTE SI LE GRADE DU CREATEUR DU DOSSIER EST CELUI D'UN PROF (OU PAS)
		if ($grade_id == 4) { //le grade_id correspond à celui d'un professeur
			//Il faut donc envoyer un mail au professeur ainsiqu'à tous les ATI dont le compte est actif
			$mail_destinataire = $liste_mail_ati.$user_mail;
		
		} else {
			//le grade n'est pas celui d'un professeur. On considère pour l'instant que c'est un ATI qui a créé le dossier.
			$mail_destinataire = $liste_mail_ati;
		}
		
		
		/******************************
			Vérification des doublons
		*******************************/
		
		// on transforme la chaine $mail_prof en un tableau
		$verif_doublon_mail_destinataire = explode(', ', $mail_destinataire); 
		// Cette fonction va nous retourner un tableau dédoublonné.
		$verif_doublon_mail_destinataire = array_unique($verif_doublon_mail_destinataire);
		// On reconstruit notre string à partir du tableau dédoublonné
		$mail_destinataire = implode(", ", $verif_doublon_mail_destinataire);
		
		
		
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
	
	
?>


