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
	
	//R�cup�ration du mail du compte ati (root)
	$req_mail_root = $db_gespac->queryAll("SELECT user_mail FROM users WHERE user_id=1");
	$mail_root = $req_mail_root[0][0];
	
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
		
		
		// on r�cup�re le num�ro d'id du user qui fait la demande, son nom et son mail 
		$req_id_user = $db_gespac->queryAll ( "SELECT user_id, user_nom, user_mail FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0][0];
		$user_nom 	=  $req_id_user[0][1];
		$user_mail 	=  $req_id_user[0][2];
		
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
		

		/*************************
				MAILING
		**************************/
		
		//R�cup�ration des comptes qui ont le grade ATI
		$req_count_ati 	 = $db_gespac->queryAll("SELECT COUNT(*) FROM users WHERE user_niveau=1"); //on compte le nombre d'utilisateurs qui ont le grade ATI
		$req_comptes_ati = $db_gespac->queryAll("SELECT user_nom, user_mail FROM users WHERE user_niveau=1"); //on r�cup�re les noms et mails de ces comptes avec le grade ATI
		
		
		//On r�cup�re les identifiants de l'intervenant et du demandeur en fonction du num�ro de dossier
		$req_id_intervenant_demandeur = $db_gespac->queryAll("SELECT user_intervenant_id, user_demandeur_id FROM demandes WHERE dem_id='$dossier'");
		$id_intervenant 			  = $req_id_intervenant_demandeur[0][0];
		$id_demandeur	 			  = $req_id_intervenant_demandeur[0][1];
		
		//on r�cup�re les mails de l'intervenant et du demandeur
		$req_mail_intervenant = $db_gespac->queryAll("SELECT user_mail, user_nom FROM users WHERE user_id='$id_intervenant'");
		$mail_intervenant	  = $req_mail_intervenant[0][0];
		$nom_intervenant	  = $req_mail_intervenant[0][1];
		
		$req_mail_demandeur	= $db_gespac->queryAll("SELECT user_mail, user_nom FROM users WHERE user_id='$id_demandeur'");
		$mail_demandeur     = $req_mail_demandeur[0][0];
		$nom_demandeur      = $req_mail_demandeur[0][1];
		
		echo $nom_demandeur.'<br>';
		
		// CORPS DU MAIL
		$corps_mail = "Le dossier <b>$dossier</b> a chang� d'�tat. Vous pouvez le suivre en cliquant sur le lien suivant : http://localhost/developpement/gespac33/gespac/gestion_demandes/voir_dossier.php?height=480&width=640&id=$dossier<br><br>";
		$corps_mail .= "L'�tat du dossier est actuellement : <b>'$etat'<br><br></b>";
		$corps_mail .= "Commentaire de l'utilisateur : <i>'$reponse'</i><br><br>";
		$corps_mail .= "<i>Ce mail est envoy� automatiquement. Inutile d'y r�pondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter � <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
		$corps_mail .= "L'�quipe GESPAC";
		
		$message = '<html><head><title>'.$sujet_mail.'</title></head><body>'.$corps_mail.'</body></html>'; 
		
		
		// boucle qui va r�cup�rer la liste des mails et des noms
		foreach ( $req_comptes_ati as $record ) {
			$mail_nom	= $record[0].', ';
			$mail_copie = $record[1];
			
			if (empty($mail_copie)) {
				$mail_copie .= '';
				
			} else {
				$mail_copie .= ', ';
			}
			
			$mail_copie = trim($mail_copie); //supprime les espaces en d�but et fin de chaine
			echo $mail_copie;
		}	
		
		echo '<br><br><b>USER MAIL : '.$user_mail.'</b><br>';
		echo '<b>MAIL PROFESSEUR : '.$mail_demandeur.'</b><br>';
		
		//test le mail de la personne qui a modifi� la demande par rapport aux mails intervenant et demandeur
		foreach ( $req_comptes_ati as $record ) {
			
			$mail_nom = $record[0];
			$mail_ati = $record[1];
			echo '<br><b>MAILS DE L\'ATI '.$mail_nom. ' : '.$mail_ati.'</b><br>';
			
			if ($user_mail == $mail_ati) { //le mail de l'utilisateur connect� est l'un de ceux du grade ATI
				
				$user_mail = $mail_demandeur;
				$sujet_mail = '[GESPAC]Modification de l\'�tat du dossier n�'.$dossier.' par l\'utilisateur '.$nom_intervenant;
				
			}
			
			if (empty($mail_ati)) {
				$mail_ati .= '';
			} else {
				$mail_ati .= ', ';
			}
			$headers ='From: '.$mail_root."\n"; //c'est toujours le compte root qui envoie le mail
			$headers .='To: '.$mail_demandeur."\n"; 
			$headers .='Reply-To: '.$mail_root."\n"; 
			$headers .='Cc: '.$mail_ati."\n"; //on met toutes les adresses des comptes ATI en copie
			$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
			$headers .='Content-Transfer-Encoding: 8bit'; 
			
			if(mail($sujet_mail, $message, $headers)) { 
				echo 'Le mail a bien �t� envoy� � <b>'. $mail_nom.'</b> � l\'adresse '.$mail_ati.' !<br>'; 
				echo 'Le mail a bien �t� envoy� � <b>'. $mail_demandeur.'</b> � l\'adresse '.$mail_demandeur.' !<br>'; 
			} else { 
				echo 'Le mail n\'a pas �t� envoy�...<br>'; 
			}
		}
		
		
		
/*
		if ($user_mail == $mail_demandeur) { // la personne qui a modifi� la demande est le demandeur initial de la demande. On envoie un mail � l'intervenant pour qu'il soit au courant et on met en copie la personne qui a modifi� la demande
				
			$headers ='From: '.$mail_root."\n"; //c'est toujours le compte root qui envoie le mail
			$headers .='Reply-To: '.$mail_root."\n"; 
			$headers .='Cc: '.$mail_demandeur."\n"; 
			$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
			$headers .='Content-Transfer-Encoding: 8bit'; 
				
			$user_mail = $mail_copie;
			$sujet_mail = '[GESPAC]Modification de l\'�tat du dossier n�'.$dossier.' par l\'utilisateur '.$nom_demandeur;
			
			if(mail($user_mail, $sujet_mail, $message, $headers)) { 
				echo 'Le mail a bien �t� envoy� � <b>'. $mail_nom.'</b> !<br>'; 
			} else { 
				echo 'Le mail n\'a pas �t� envoy�...<br>'; 
			}
		}*/
	}
	
	
	/**************** CREATION ********************/
	
	if ( $action == 'add' ) {
		
		$type 		= $_POST ['type_demande'];
		$login		= $_SESSION['login'];
			
		// on r�cup�re le num�ro d'id du user qui fait la demande
		$req_id_user = $db_gespac->queryAll ( "SELECT user_id, user_nom, user_mail FROM users WHERE user_logon='$login'" );
		$user_id 	=  $req_id_user[0][0];
		$user_nom 	=  $req_id_user[0][1];
		$user_mail 	=  $req_id_user[0][2];
		$user_intervenant_id = 1;
		
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

			$req_add_demande = "INSERT INTO demandes ( dem_text, dem_etat, user_demandeur_id, user_intervenant_id, salle_id, mat_id, dem_type ) VALUES ( '$texte', '$etat', $user_id, $user_intervenant_id, $salle, $pc, '$type' )";
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
			
			$texte 			= addslashes(utf8_decode( $_POST ['texte_demande'] ));
			$creat_inter	= $_POST ['creat_inter'];
			
			if ( $creat_inter == 'on')	$etat = "intervention";
			else $etat = "attente";
			
			$req_add_demande = "INSERT INTO demandes ( dem_text, dem_etat, user_demandeur_id, user_intervenant_id, dem_type ) VALUES ( '$texte', '$etat', $user_id, $user_intervenant_id, '$type' )";
			$result = $db_gespac->exec ( $req_add_demande );		
		}

		echo "Votre demande a �t� prise en compte...";
		
		//Insertion d'un log
		$log_texte = "La demande de <b>$type</b> a �t� cr��e par <b>$user_nom</b>";
		
		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation demande', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_mat );
		
		
		/*************************
				MAILING
		**************************/
		
		//on a besoin de l'id du dossier pour son suivi (m�me technique que pour la cr�ation de l'intervention A DISCUTER
		$req_dossier_id = $db_gespac->queryAll ( "SELECT max(dem_id) FROM demandes" );
		$dossier 	=  $req_dossier_id[0][0];
		
		//On r�cup�re les identifiants de l'intervenant et du demandeur en fonction du num�ro de dossier
		$req_id_intervenant_demandeur = $db_gespac->queryAll("SELECT user_intervenant_id, user_demandeur_id FROM demandes WHERE dem_id='$dossier'");
		$id_intervenant 			  = $req_id_intervenant_demandeur[0][0];
		$id_demandeur	 			  = $req_id_intervenant_demandeur[0][1];
		
		
		
		//on r�cup�re les mails de l'intervenant et du demandeur
		$req_mail_intervenant = $db_gespac->queryAll("SELECT user_mail, user_nom FROM users WHERE user_id='$id_intervenant'");
		$mail_intervenant	  = $req_mail_intervenant[0][0];
		$nom_intervenant	  = $req_mail_intervenant[0][1];
		
		$req_mail_demandeur	= $db_gespac->queryAll("SELECT user_mail, user_nom FROM users WHERE user_id='$id_demandeur'");
		$mail_demandeur     = $req_mail_demandeur[0][0];
		$nom_demandeur      = $req_mail_demandeur[0][1];
		
		// CORPS DU MAIL
		$corps_mail = "Le dossier <b>$dossier</b> a �t� cr��. Vous pouvez le suivre en cliquant sur le lien suivant : http://localhost/developpement/gespac33/gespac/gestion_demandes/voir_dossier.php?height=480&width=640&id=$dossier<br><br>";
		$corps_mail .= "L'�tat du dossier est actuellement : <b>'$etat'<br><br></b>";
		$corps_mail .= "Commentaire de l'utilisateur : <i>'$texte'</i><br><br>";
		$corps_mail .= "<i>Ce mail est envoy� automatiquement. Inutile d'y r�pondre, vous ne recevrez aucun mail en retour. Pour tout suivi du dossier, merci de vous connecter � <a href='http://gespac/gespac'>votre interface GESPAC.</a></i><br><br>";
		$corps_mail .= "L'�quipe GESPAC";
		
		$message = '<html><head><title>'.$sujet_mail.'</title></head><body>'.$corps_mail.'</body></html>'; 
		
		//on envoie un mail en copie au cr�ateur du dossier. Le destinataire du mail sera l'intervenant
		
		$headers ='From: '.$mail_demandeur."\n"; 
		$headers .='Reply-To: '.$mail_demandeur."\n"; 
		$headers .='Cc: '.$mail_demandeur."\n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
		$headers .='Content-Transfer-Encoding: 8bit'; 
			
		$sujet_mail = '[GESPAC]Cr�ation d\'un nouveau dossier par l\'utilisateur '.$nom_demandeur;
		
		if(mail($mail_intervenant, $sujet_mail, $message, $headers)) { 
			echo 'Le mail a bien �t� envoy� !'; 
		} else { 
			echo 'Le mail n\'a pas �t� envoy�...'; 
		}
	}
	
?>


