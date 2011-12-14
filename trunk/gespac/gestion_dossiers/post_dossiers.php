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
		$liste_mat 		= preg_replace("[^;]", "", $_POST ['liste_mat']); // On vire le premier ; dans la liste du matériel
		$add_inter		= $_POST ['add_inter'];
		$active_mailing	= $_POST ['active_mailing']; $mailing = $active_mailing <> "" ? 1 : 0 ;
		$mat_hs			= $_POST ['mat_hs'];
		$current_user	= $con_gespac->QueryOne("SELECT user_id FROM users WHERE user_logon = '" . $_SESSION['login'] . "'");
		
		// On créé le dossier
		$rq = "INSERT INTO dossiers (dossier_type, dossier_mat, dossier_mailing) VALUES ('$type', '$liste_mat', $mailing);";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
		
	
		// On récupère l'id du dernier dossier créé
		$dossier = $con_gespac->GetLastID();
		
		// Si la case créer l'intervention est cochée
		$etat = $add_inter == "on" ? "intervention" : "ouverture";
		
		// On créé une page dans le dossier
		$rq = "INSERT INTO dossiers_textes (dossier_id, txt_user, txt_texte, txt_etat) VALUES ($dossier, '$current_user', '$commentaire', '$etat');";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
		
		echo "le dossier $dossier a été créé.";
		
		
		// Si on veut changer l'état du matériel
		if ( $mat_hs == "on" ) {
			$tab_liste_mat = explode(";", $liste_mat);
			
			$etat = addslashes($_POST ['CB_etats']);
			
			if ($etat <> "") {
				
				$gign = $_POST ['gign'];
				
				foreach ($tab_liste_mat as $mat) {
					
					if ($gign <> "") $espace = " ";
					
					$rq = "UPDATE materiels SET mat_etat='$etat $espace $gign' WHERE mat_id=$mat";
					$con_gespac->Execute($rq);
					$log->Insert($rq);
					
				}
			}
		}
		
		
		// Si on active le mailing
		if ( $mailing == 1) {
			
			//Récupération du mail du compte ati (root)
			$mail_root = $con_gespac->QueryOne("SELECT clg_ati_mail FROM college");
	
			// PARAMETRAGE DU SMTP 
			//ini_set('SMTP','smtp.intranet.cg13.oleane.fr'); //Mettre l'adresse SMTP dans le fichier de config
			//ini_set('sendmail_from', $mail_root);
			

			//Récupération des comptes qui ont le grade ATI
			$req_comptes_ati = $con_gespac->QueryAll("SELECT user_nom, user_mail FROM users, grades WHERE grade_nom='ATI' AND users.grade_id = grades.grade_id AND user_mailing=1");
			
			
			//on récupère le mail et le nom du créateur de l'intervention (si le mailing est activé)
			$req_mail_demandeur	= $con_gespac->QueryRow("SELECT user_mail, user_nom FROM users WHERE user_id=$current_user AND user_mailing=1");
			$mail_demandeur     = $req_mail_demandeur["user_mail"];
			$nom_demandeur      = $req_mail_demandeur["user_nom"];
			
			// CORPS DU MAIL
			$corps_mail = "Le dossier <b>$dossier</b> a été créé. Vous pouvez le suivre en consultant la liste de vos dossiers sur votre interface GESPAC.<br><br>";
			$corps_mail .= "Le type du dossier est : <b>'$type'<br><br></b>";
			$corps_mail .= "L'état du dossier est actuellement : <b>'$etat'<br><br></b>";
			$corps_mail .= "Commentaire de l'utilisateur : <i>'$commentaire'</i><br><br>";
			$corps_mail .= "<i>Ce mail est envoyé automatiquement. Inutile d'y répondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter à <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
			$corps_mail .= "L'équipe GESPAC";
			
			$sujet_mail = '[GESPAC]Création du dossier n°'.$dossier.' par l\'utilisateur '.$nom_demandeur;
			
			$message = '<html><head><title>'.$sujet_mail.'</title></head><body>'.$corps_mail.'</body></html>'; 

			//Boucle pour récupérer la liste des mails des ATIs
			foreach ( $req_comptes_ati as $record ) {
					
				$mail_nom = $record["user_nom"];
				$mail_ati = $record["user_mail"];
					
				if (empty($mail_ati)) { //le champ $mail_ati est vide
					$liste_mail_ati .= ''; //on ne concatène rien dans la variable $liste_mail_ati
				} else { // si ce champ n'est pas vide
					$liste_mail_ati .= $mail_ati.','; //on colle à la variable la valeur de $mail_ati suivi d'une virgule
				}
			}
			
			// on concatène les mails des ati avec le mail du destinataire. L'envoi en Cc nous met une erreur.
			echo $mail_destinataire = $liste_mail_ati.$mail_demandeur;
			//on cherche si il y a une virgule après le séparateur ','. Si c'est le cas, on remplace cette virgule par une seule virgule.
			$mail_destinataire = str_replace (",,", ",", $mail_destinataire);


			// Vérification des doublons
			
			// on transforme la chaine $mail_prof en un tableau
			$verif_doublon_mail_destinataire = explode(',', $mail_destinataire); 
			// Cette fonction va nous retourner un tableau complètement dédoublonné !! Magique !
			$verif_doublon_mail_destinataire = array_unique($verif_doublon_mail_destinataire);
			// On reconstruit notre string à partir du tableau dédoublonné
			$mail_destinataire = implode(",", $verif_doublon_mail_destinataire);
			
			echo $mail_destinataire;
			
			$headers ='From: '.$mail_root."\n"; //c'est toujours le compte root qui envoie le mail
			$headers .='Reply-To: '.$mail_root."\n"; 
			$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
			$headers .='Content-Transfer-Encoding: 8bit'; 
					
			if (mail($mail_destinataire, $sujet_mail, $message, $headers)) 
				echo 'Le(s) mail(s) a (ont) bien été envoyé(s) !<br>'; 
			else 
				echo 'Le(s) mail(s) n\'a (ont) pas été envoyé(s) !<br>'; 
		
		}
	}
	
	/**************** MODIFICATION ********************/
	if ( $action == 'modif' ) {
		
		$dossierid		= $_POST['dossierid'];
		$etat 			= addslashes($_POST ['etat']);
		$commentaire 	= addslashes($_POST ['commentaire']);
		$mailing		= $con_gespac->QueryOne("SELECT dossier_mailing FROM dossiers WHERE dossier_id = $dossierid");
		$current_user	= $con_gespac->QueryOne("SELECT user_id FROM users WHERE user_logon = '" . $_SESSION['login'] . "'");
		
		// On créé une page dans le dossier
		$rq = "INSERT INTO dossiers_textes (dossier_id, txt_user, txt_texte, txt_etat) VALUES ($dossierid, '$current_user', '$commentaire', '$etat');";
		$con_gespac->Execute($rq);
		$log->Insert($rq);
		
		
		// Si on active le mailing
		if ( $mailing == 1) {
			
			//Récupération du mail du compte ati (root)
			$mail_root = $con_gespac->QueryOne("SELECT clg_ati_mail FROM college");
	
			//Récupération des comptes qui ont le grade ATI et dont le mailing est actif au niveau utilisateur
			$req_comptes_ati = $con_gespac->QueryAll("SELECT user_nom, user_mail FROM users, grades WHERE grade_nom='ati' AND users.grade_id = grades.grade_id AND user_mailing=1");
			
			//Récupération des noms et des mails des personnes qui ont participé au dossier et dont le mailing est actif au niveau utilisateur
			$req_comptes_participants = $con_gespac->QueryAll("SELECT user_nom, user_mail FROM users, dossiers_textes WHERE user_id = txt_user AND dossier_id=$dossierid AND user_mailing=1");
			
			//on récupère le mail et le nom du créateur de l'intervention (si le mailing est activé)
			/*$req_mail_demandeur	= $con_gespac->QueryRow("SELECT user_mail, user_nom FROM users WHERE user_id=$current_user AND user_mailing=1");
			$mail_demandeur     = $req_mail_demandeur["user_mail"];
			$nom_demandeur      = $req_mail_demandeur["user_nom"];*/
			
			// CORPS DU MAIL
			$corps_mail = "Le dossier <b>$dossierid</b> a été modifié. Vous pouvez le suivre en consultant la liste de vos dossiers sur votre interface GESPAC.<br><br>";
			$corps_mail .= "L'état du dossier est actuellement : <b>'$etat'<br><br></b>";
			$corps_mail .= "Commentaire de l'utilisateur : <i>'$commentaire'</i><br><br>";
			$corps_mail .= "<i>Ce mail est envoyé automatiquement. Inutile d'y répondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter à <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
			$corps_mail .= "L'équipe GESPAC";
			
			$sujet_mail = '[GESPAC]Modification du dossier n°'.$dossierid.' par l\'utilisateur '.$_SESSION['login'];
			
			$message = '<html><head><title>'.$sujet_mail.'</title></head><body>'.$corps_mail.'</body></html>'; 

			//Boucle pour récupérer la liste des mails des ATIs
			foreach ( $req_comptes_ati as $record ) {
					
				$mail_nom = $record["user_nom"];
				$mail_ati = $record["user_mail"];
					
				if (empty($mail_ati)) { //le champ $mail_ati est vide
					$liste_mail_ati .= ''; //on ne concatène rien dans la variable $liste_mail_ati
				} else { // si ce champ n'est pas vide
					$liste_mail_ati .= $mail_ati.','; //on colle à la variable la valeur de $mail_ati suivi d'une virgule
				}
			}
			
			//Boucle pour récupérer la liste des mails des participants au dossier
			foreach ( $req_comptes_participants as $record ) {
				
				$nom_participant  = $record["user_nom"];
				$mail_participant = $record["user_mail"];
				
				if (empty($mail_participant)) { //le champ $mail_ati est vide
					$liste_mail_participants .= ''; //on ne concatène rien dans la variable $liste_mail_ati
				} else { // si ce champ n'est pas vide
					$liste_mail_participants .= $mail_participant.','; //on colle à la variable la valeur de $mail_ati suivi d'une virgule
				}
			}
				
			// on concatène les mails des ati avec le mail du destinataire. L'envoi en Cc nous met une erreur.
			$mail_destinataire = $liste_mail_ati.$liste_mail_participants;
			//on cherche si il y a une virgule après le séparateur ','. Si c'est le cas, on remplace cette virgule par une seule virgule.
			$mail_destinataire = str_replace (",,", ",", $mail_destinataire);
			

			// Vérification des doublons
			
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
					
			if (mail($mail_destinataire, $sujet_mail, $message, $headers)) 
				echo 'Le(s) mail(s) a (ont) bien été envoyé(s) !<br>'; 
			else 
				echo 'Le(s) mail(s) n\'a (ont) pas été envoyé(s) !<br>'; 
			
		}
	
	}


?>
