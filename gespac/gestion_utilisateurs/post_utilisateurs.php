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
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
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

	    $log_texte = "Le compte $user_nom a �t� supprim�";

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
		$niveau   	= $_POST ['niveau'];
		$mail  		= $_POST ['mail'];
		$skin  		= $_POST ['skin'];
		$page   	= $_POST ['page'];

		// [BUG] pour le niveau : la valeur peut �tre null mais pas vide. On devrait pe mettre le niveau avec une valeur 0 pour non affect� par d�faut
		$req_modif_user = "UPDATE users SET user_nom='$nom', user_logon='$login', user_password='$password', user_niveau=$niveau, user_mail='$mail', user_skin='$skin', user_accueil='$page' WHERE user_id=$id";
		$result = $db_gespac->exec ( $req_modif_user );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
		
		// [BUG=>la requ�te est nok] Insertion d'un log
		$log_texte = "Le compte $nom a �t� modifi�";
		
	    $req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien �t� modifi�...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$login 		= $_POST ['login'];
		$password	= $_POST ['password'];
		$niveau   	= $_POST ['niveau'];
		$mail   	= $_POST ['mail'];
		$skin   	= $_POST ['skin'];
		$page   	= $_POST ['page'];
		
		$req_add_user = "INSERT INTO users ( user_nom, user_logon, user_password, user_niveau, user_mail, user_skin, user_accueil) VALUES ( '$nom', '$login', '$password', $niveau, '$mail', '$skin', '$page')";
		$result = $db_gespac->exec ( $req_add_user );
		
		// On log la requ�te SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_user."\n");
		
		// [BUG=>la requ�te est nok] Insertion d'un log
		$log_texte = "Le compte $nom a �t� cr��";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien �t� ajout�...</small>";
	}

	/********** AFFECTATION D'UN GRADE ***********/	
	
	//	PAS UTILISE POUR LE MOMENT
	
	
	if ( $action == 'affect' ) {
	/*
		$mat_ids 	= $_POST['materiel_a_poster'];
		$salle_id 	= $_POST['salle_select'];
		
		$mat_ids_array = explode (";", $mat_ids);
		
		$mat_ids_unique = array_unique ($mat_ids_array);
		
		foreach ($mat_ids_unique as $id) {
			if ( $id <> "") {
				$req_modif_apreter = "UPDATE materiels SET salle_id = $salle_id WHERE mat_id = $id";
				$result = $db_gespac->exec ( $req_modif_apreter );
			}
		}

	*/
	}	
	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


