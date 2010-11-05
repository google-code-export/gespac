<?PHP


	/* fichier de creation / modif / suppr des grades
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une création
	
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
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
		
		//On récupère le nom du grade en fonction de son id
	    $grade_nom = $db_gespac->queryOne ( "SELECT grade_nom FROM grades WHERE grade_id=$id" );

	    $log_texte = "Le grade $grade_nom a été supprimé";

	    $req_log_suppr_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression grade', '$log_texte');";
	    $result = $db_gespac->exec ( $req_log_suppr_grade );
	
		// Suppression de l'utilisateur de la base
		$req_suppr_grade = "DELETE FROM grades WHERE grade_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_grade );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_grade."\n");
		
		echo "<br><small>L'utilisateur <b>$grade_nom</b> a été supprimé !</small>";
	}

	/**************** MODIFICATION ********************/	
	if ( $action == 'mod' ) {
	
		$id     	= $_POST ['id'];
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$niveau   	= $_POST ['niveau'];

		$req_modif_grade = "UPDATE grades SET grade_nom='$nom', grade_niveau=$niveau WHERE grade_id=$id";
		$result = $db_gespac->exec ( $req_modif_grade );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_grade."\n");
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le grade $nom a été modifié";
		
	    $req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_grade );
		
		echo "<br><small>Le grade <b>$nom</b> a été modifié...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$niveau   	= $_POST ['niveau'];
		
		$req_add_grade = "INSERT INTO grades ( grade_nom, grade_niveau, grade_menu) VALUES ( '$nom', $niveau, '' )";
		$result = $db_gespac->exec ( $req_add_grade );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_grade."\n");
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le grade $nom a été créé";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>Le grade <b>$nom</b> a été ajouté...</small>";
	}
	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


