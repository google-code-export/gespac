<?PHP


	/* fichier de creation / modif / suppr du mat�riel
	
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
	$id 		= $_GET['id'];
	
	
	/*********************************************
	*
	*		ACTIONS SUR UTILISATEUR
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {

        //Insertion d'un log
		
		//On r�cup�re le nom de l'utilisateur en fonction du user_id
	    $liste_noms = $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id=$id" );
	    $user_nom = $liste_noms [0][0];

	    $log_texte = "Le compte <b>$user_nom</b> a �t� supprim�.";

	    $req_log_suppr_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression compte', '$log_texte');";
	    $result = $db_gespac->exec ( $req_log_suppr_user );
	
		// Suppression de l'utilisateur de la base
		$req_suppr_user = "DELETE FROM users WHERE user_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_user );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_user."\n");
		
		echo "<br><small>L'utilisateur <b>$user_nom</b> a �t� supprim� !</small>";
	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$id     	= $_POST ['id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$login 		= $_POST ['login'];
		$password	= $_POST ['password'];
		$grade   	= $_POST ['grade'];
		$mail  		= $_POST ['mail'];
		$skin  		= $_POST ['skin'];
		$page   	= $_POST ['page'];
		$mailing   	= $_POST ['mailing'];
		
		$mailing = $mailing == "on" ? 1 : 0 ;

		// on r�cup�re les anciennes valeurs du compte pour les logs
		$req_infos_compte_old = $db_gespac->queryRow("SELECT user_nom FROM users WHERE user_id=$id");
		$nom_old = $req_infos_compte_old[0];
		
		
		$req_modif_user = "UPDATE users SET user_nom='$nom', user_logon='$login', user_password='$password', grade_id=$grade, user_mail='$mail', user_skin='$skin', user_accueil='$page', user_mailing=$mailing WHERE user_id=$id";
		$result = $db_gespac->exec ( $req_modif_user );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
		
		//Insertion d'un log
		$log_texte = "Le compte (anciennement <b>$nom_old</b>) a �t� modifi� en <b>$nom</b>.";
		
	    $req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien �t� modifi�...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$login 		= $_POST ['login'];
		$password	= $_POST ['password'];
		$grade   	= $_POST ['grade'];
		$mail   	= $_POST ['mail'];
		$skin   	= $_POST ['skin'];
		$page   	= $_POST ['page'];
		$mailing	= $_POST ['mailing'];

		$mailing = $mailing == "on" ? 1 : 0 ;
		
		$req_add_user = "INSERT INTO users ( user_nom, user_logon, user_password, grade_id, user_mail, user_skin, user_accueil, user_mailing) VALUES ( '$nom', '$login', '$password', $grade, '$mail', '$skin', '$page', $mailing)";
		$result = $db_gespac->exec ( $req_add_user );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_user."\n");
		
		//Insertion d'un log
		$log_texte = "Le compte <b>$nom</b> a �t� cr��.";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien �t� ajout�...</small>";
	}
	
	
	/**************** MODIFICATION D'UN LOT ********************/
		
	if ( $action == 'modlot' ) {
		
		$lot		= addslashes(utf8_decode(urldecode($_POST ['lot_users'])));
		$mailing	= $_POST ['mailing'];
		$grade		= addslashes(utf8_decode(urldecode($_POST ['grade'])));
		$skin		= addslashes(utf8_decode(urldecode($_POST ['skin'])));
		$page		= addslashes(utf8_decode(urldecode($_POST ['page'])));
		
		$lot_array = explode(";", $lot);
		
		var_dump($_POST);
		
		foreach ($lot_array as $item) {
			if ( $item <> "" ) {	// permet de virer les �l�ments vides
				
				// Si l'�tat est modifi� on fait un update sur ce champ
				$sql_mailing = $mailing == "" ? "" : " mat_etat='$etat' ";
				
				if ( $origine <> "" ) {
					// met on ou non la virgule avant en fonction de l'existence de la variable pr�c�dente (oula, dure � comprendre �a ...)
					$sql_origine = $sql_etat == "" ? " mat_origine='$origine' " : ", mat_origine='$origine' " ;
					
				} else { $sql_origine = ""; }
				
				
				if ( $salle <> "" ) {
					// on r�cup�re le num�ro d'id de salle que l'on veut modifier dans la table materiels avec comme clause WHERE le nom de salle post�
					$req_id_salle_par_nom = $db_gespac->queryAll ( "SELECT salle_id FROM salles WHERE salle_nom='$salle'" );
					$salle_id =  $req_id_salle_par_nom[0][0];

					// dans la rq sql, met on ou non la virgule avant en fonction de l'existence de la variable pr�c�dente (oula, dure � comprendre �a ...)
					
					if ( $sql_origine == "" && $sql_etat == "" ) $sql_salle = " salle_id=$salle_id ";
					else $sql_salle = ", salle_id=$salle_id " ;

				} else { $sql_salle = ""; }
				
				if ( $type <> "" ) {
					// on r�cup�re le num�ro d'id de marque que l'on veut modifier dans la table materiels avec comme clause WHERE le type, le sous type, la marque et le modele de marque
					$req_id_marque_par_type = $db_gespac->queryAll ( "SELECT marque_id FROM marques WHERE marque_type='$type' AND marque_stype='$stype' AND marque_marque='$marque' AND marque_model='$modele'" );
					$marque_id =  $req_id_marque_par_type[0][0];
					
					if ( $sql_origine == "" && $sql_etat == "" && $sql_salle == "" ) $sql_marque = " mat_salle=$marque_id";
					else $sql_marque = " , marque_id=$marque_id" ;
					
				} else { $sql_marque = ""; }
				
				$req_modif_materiel = "UPDATE materiels SET " . $sql_etat . $sql_origine . $sql_salle . $sql_marque . " WHERE mat_id=$item ;";
				//$result = $db_gespac->exec ( $req_modif_materiel );
				
				//on r�cup�rer le nom et le serial de chaque item
				$req_nom_serial_materiel = $db_gespac->queryRow ("SELECT mat_nom, mat_serial FROM materiels WHERE mat_id=$item");
				$liste_noms_serial   .=  '<b>'.$req_nom_serial_materiel[0].' (</b>serial : <b>'.$req_nom_serial_materiel[1].')</b>, ';
				
				// On log la requ�te SQL
				fwrite($fp, date("Ymd His") . " " . $req_modif_materiel."\n");
			}

		}
	
		//Insertion d'un log
		//on supprime les caract�res en fin de chaine
		$liste_noms_serial = trim ($liste_noms_serial, ", ");
		$log_texte = "Les materiels $liste_noms_serial ont �t� modifi�s.";

		$req_log_modif_mat = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification mat�riel', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_modif_mat );

	}

	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


