<?PHP


	/* fichier de creation / modif / du college
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une cr�ation
	
	reste � coder pour la suppression
	
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	// lib
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// cnx � la base de donn�es GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	//Log des requ�tes SQL
	$log = new Log ("../dump/log_sql.sql");
	
		
	// on r�cup�re les param�tres de l'url	
	$action 	= $_GET['action'];
	

	
	
	
	#*********************************************
	#
	#		ACTIONS SUR COLLEGE
	#
	#*********************************************
	
	
	
	#**************** CREATION ********************#
	
	if ( $action == 'creat' ) {
		
		//la fonction addslashes() va permettre de rajouter un slash avant une apostrophe dans un string. Pas besoin d'utiliser cette fonction pour les champs uniquement num�riques comme le t�l�phone, fax...
		$clg_uai 		= $_POST['clg_uai'];
		$clg_nom 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_nom']))));
		$clg_ati 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_ati']))));
		$clg_ati_mail 	= addslashes(utf8_decode(urldecode($_POST['clg_ati_mail'])));
		$clg_adresse 	= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_adresse']))));
		$clg_cp 		= $_POST['clg_cp'];
		$clg_ville 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_ville']))));
		$clg_tel 		= $_POST['clg_tel'];
		$clg_fax 		= $_POST['clg_fax'];
		$clg_web 		= addslashes(utf8_decode(urldecode($_POST['clg_web'])));
		$clg_grr 		= addslashes(utf8_decode(urldecode($_POST['clg_grr'])));
	
		// cr�ation du coll�ge
		$req_add_college = "INSERT INTO college VALUES ( '$clg_uai', '$clg_nom', '$clg_ati', '$clg_ati_mail', '$clg_adresse', '$clg_cp', '$clg_ville', '$clg_tel', '$clg_fax', '$clg_web', '$clg_grr')";
		$con_gespac->Execute ( $req_add_college );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_college );
		
		echo "<br><small>Cr�ation du coll�ge <b>$clg_nom</b> !</small><br>";
		
		// Mise � jour de l'adresse mail dans le compte ati
		$req_maj_mail_ati = "UPDATE users SET user_mail = '$clg_ati_mail' WHERE user_nom='ati'";
		$con_gespac->Execute ( $req_maj_mail_ati );
		
		// On log la requ�te SQL
		$log->Insert( $req_maj_mail_ati );
		
		echo "<br><small>Mise � jour du mail du compte ATI !</small><br>";
		
		// Cr�ation de diverses salles
		$req_add_salle_stock = "INSERT INTO salles (salle_id, salle_nom, clg_uai, est_modifiable) VALUES (1, 'STOCK', '$clg_uai', 0 )";
		$con_gespac->Execute ( $req_add_salle_stock );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_salle_stock );
		
		echo "<small>Cr�ation de la salle <b>Stock</b> !</small><br>";
		
		$req_add_salle_d3e = "INSERT INTO salles (salle_id, salle_nom, clg_uai, est_modifiable) VALUES (2, 'D3E', '$clg_uai', 0 )";
		$con_gespac->Execute ( $req_add_salle_d3e );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_salle_d3e );
		
		echo "<small>Cr�ation de la salle <b>D3E</b> !</small><br>";
		
		$req_add_salle_pret = "INSERT INTO salles (salle_id, salle_nom, clg_uai, est_modifiable) VALUES (3, 'PRETS', '$clg_uai', 0 )";
		$con_gespac->Execute ( $req_add_salle_pret );
		
		// On log la requ�te SQL
		$log->Insert( $req_add_salle_pret );
		
		echo "<small>Cr�ation de la salle <b>PRETS</b> !</small><br>";

		//Insertion d'un log
		
		$log_texte = "La fiche informative a �t� cr��e. Nom actuel : <b>$clg_nom</b>";
		
		$req_log_creer_college = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation coll�ge', '$log_texte');";
		$con_gespac->Execute ( $req_log_creer_college );
		
		// On log la requ�te SQL
		$log->Insert( $req_log_creer_college );
		
	}
	
	
		

	#**************** MODIFICATION ********************#	
	
	if ( $action == 'mod' ) {
		
		//la fonction addslashes() va permettre de rajouter un slash avant une apostrophe dans un string. Pas besoin d'utiliser cette fonction pour les champs uniquement num�riques comme le t�l�phone, fax...
		$old_uai		= $_POST['old_uai'];
		$clg_uai 		= $_POST['clg_uai'];
		$clg_nom 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_nom']))));
		$clg_ati 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_ati']))));
		$clg_ati_mail 	= addslashes(utf8_decode(urldecode($_POST['clg_ati_mail'])));
		$clg_adresse 	= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_adresse']))));
		$clg_cp 		= $_POST['clg_cp'];
		$clg_ville 		= strtoupper(addslashes(utf8_decode(urldecode($_POST['clg_ville']))));
		$clg_tel 		= $_POST['clg_tel'];
		$clg_fax 		= $_POST['clg_fax'];
		$clg_web 		= addslashes(utf8_decode(urldecode($_POST['clg_web'])));
		$clg_grr 		= addslashes(utf8_decode(urldecode($_POST['clg_grr'])));
			
		$req_modif_college = "UPDATE college SET clg_uai = '$clg_uai', clg_nom = '$clg_nom', clg_ati = '$clg_ati', clg_ati_mail = '$clg_ati_mail', 
							  clg_adresse = '$clg_adresse',	clg_cp = '$clg_cp', clg_ville = '$clg_ville', clg_tel = '$clg_tel', clg_fax = '$clg_fax', 
							  clg_site_web = '$clg_web', clg_site_grr = '$clg_grr' WHERE clg_uai='$old_uai'";
		$con_gespac->Execute ( $req_modif_college );
		
		// On log la requ�te SQL
		$log->Insert( $req_modif_college );
		
		// Mise � jour de l'adresse mail dans le compte ati
		$req_maj_mail_ati = "UPDATE users SET user_mail = '$clg_ati_mail' WHERE user_nom='ati'";
		$con_gespac->Execute ( $req_maj_mail_ati );
		
		// On log la requ�te SQL
		$log->Insert( $req_maj_mail_ati );
		
		echo "<br><small>Mise � jour du mail du compte ATI !</small><br>";
		
		
		//Insertion d'un log
		
		$log_texte = "La fiche informative a �t� modifi�e. Nom actuel : <b>$clg_nom</b>";
		
		$req_log_modif_college = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification coll�ge', '$log_texte');";
		$con_gespac->Execute ( $req_log_modif_college );
		
		// On log la requ�te SQL
		$log->Insert( $req_log_modif_college );
		
		$clg_nom = stripslashes($clg_nom);
		
		echo "<small>Modification du coll�ge <b>$clg_nom</b>.</small>";
	
	}
	
?>
