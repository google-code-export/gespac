<?PHP


	/* fichier de creation / modif / suppr des grades
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une cr�ation
	
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
	*		ACTIONS SUR GRADES
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {

        //Insertion d'un log
		
		//On r�cup�re le nom du grade en fonction de son id
	    $grade_nom = $db_gespac->queryOne ( "SELECT grade_nom FROM grades WHERE grade_id=$id" );

	    $log_texte = "Le grade $grade_nom a �t� supprim� et les utilisateurs affect�s sont d�sormais du grade \"invit�\"";

	    $req_log_suppr_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression grade', '$log_texte');";
	    $result = $db_gespac->exec ( $req_log_suppr_grade );
		
		// On test si le grade invit� existe
		$grade_id_invite = $db_gespac->queryOne ( "SELECT grade_id FROM grades WHERE grade_nom LIKE 'invit%'" );
		
		// Si il n'existe pas, on le cr��
		if ( $grade_id_invite == "" ) {
			$req_insert_grade_invite = "INSERT INTO grades ( grade_nom, grade_menu ) VALUES ( 'invit�', '');";
			$result = $db_gespac->exec ( $req_insert_grade_invite );
			fwrite($fp, date("Ymd His") . " " . $req_insert_grade_invite."\n");
		}
			
		// On r�cup�re le grade_id du grade "invit�"
		$grade_id_invite = $db_gespac->queryOne ( "SELECT grade_id FROM grades WHERE grade_nom='invit�'" );
		
		// On colle tous les utilisateurs du grade dans le grade g�n�rique "invit�"
		$req_maj_users = "UPDATE users SET grade_id=$grade_id_invite WHERE grade_id=$id;";
		$result = $db_gespac->exec ( $req_maj_users );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_maj_users."\n");
		
		// Suppression du grade de la base
		$req_suppr_grade = "DELETE FROM grades WHERE grade_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_grade );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_grade."\n");
		
		echo "<br><small>Le grade <b>$grade_nom</b> a �t� supprim� et les utilisateurs affect�s sont d�sormais du grade \"invit�\"</small>";
	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$id     	= $_POST ['id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));

		$req_modif_grade = "UPDATE grades SET grade_nom='$nom' WHERE grade_id=$id";
		$result = $db_gespac->exec ( $req_modif_grade );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_grade."\n");
		
		// [BUG=>la requ�te est nok] Insertion d'un log
		$log_texte = "Le grade $nom a �t� modifi�";
		
	    $req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_grade );
		
		echo "<br><small>Le grade <b>$nom</b> a �t� modifi�...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		
		$req_add_grade = "INSERT INTO grades ( grade_nom, grade_menu) VALUES ( '$nom', '' )";
		$result = $db_gespac->exec ( $req_add_grade );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_grade."\n");
		
		// [BUG=>la requ�te est nok] Insertion d'un log
		$log_texte = "Le grade $nom a �t� cr��";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>Le grade <b>$nom</b> a �t� ajout�...</small>";
	}
	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


