<?PHP


	/* fichier de creation / modif / du college
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une cr�ation
	
	reste � coder pour la suppression
	
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);
	
		
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
	
	
		$req_add_college = "INSERT INTO college VALUES ( '$clg_uai', '$clg_nom', '$clg_ati', '$clg_ati_mail', '$clg_adresse', '$clg_cp', '$clg_ville', '$clg_tel', '$clg_fax', '$clg_web', '$clg_grr')";
		$result = $db_gespac->exec ( $req_add_college );
		
		echo "<br><small>Cr�ation du coll�ge <b>$clg_nom</b> !</small><br>";
		
		$req_add_salle_stock = "INSERT INTO salles (salle_id, salle_nom, clg_uai) VALUES (1, 'STOCK', '$clg_uai' )";
		$result = $db_gespac->exec ( $req_add_salle_stock );
		
		echo "<small>Cr�ation de la salle <b>Stock</b> !</small><br>";
		
		$req_add_salle_d3e = "INSERT INTO salles (salle_id, salle_nom, clg_uai) VALUES (2, 'D3E', '$clg_uai' )";
		$result = $db_gespac->exec ( $req_add_salle_d3e );
		
		echo "<small>Cr�ation de la salle <b>D3E</b> !</small><br>";
		
		$req_add_salle_pret = "INSERT INTO salles (salle_id, salle_nom, clg_uai) VALUES (3, 'PRETS', '$clg_uai' )";
		$result = $db_gespac->exec ( $req_add_salle_pret );
		
		echo "<small>Cr�ation de la salle <b>PRETS</b> !</small><br>";

		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_college."\n");
		fwrite($fp, date("Ymd His") . " " . $req_add_salle_stock."\n");
		fwrite($fp, date("Ymd His") . " " . $req_add_salle_d3e."\n");
		fwrite($fp, date("Ymd His") . " " . $req_add_salle_pret."\n");
		
		//Insertion d'un log
		
		$log_texte = "La fiche informative a �t� cr��e. Nom actuel : <b>$clg_nom</b>";
		
		$req_log_creer_college = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation coll�ge', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_creer_college );
	
		// Apr�s la cr�ation du coll�ge dans la base, on affiche la barre de menu
		echo "<script> $('main_menu').style.display = ''; </script>";

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
		$result = $db_gespac->exec ( $req_modif_college );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_college."\n");
		
		//Insertion d'un log
		
		$log_texte = "La fiche informative a �t� modifi�e. Nom actuel : <b>$clg_nom</b>";
		
		$req_log_modif_college = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification coll�ge', '$log_texte');";
		$result = $db_gespac->exec ( $req_log_modif_college );
		
		$clg_nom = stripslashes($clg_nom);
		
		echo "<small>Modification du coll�ge <b>$clg_nom</b>.</small>";
	
	}	
	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
?>