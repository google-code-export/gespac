<?PHP


	/* fichier de creation / modif / suppr du matériel
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une création
	
	reste à coder pour la suppression
	
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
	*		ACTIONS SUR UTILISATEUR
	*
	**********************************************/
	
	
	
	/**************** SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {

        //Insertion d'un log
		
		//On récupère le nom de l'utilisateur en fonction du user_id
	    $liste_noms = $db_gespac->queryAll ( "SELECT user_nom FROM users WHERE user_id=$id" );
	    $user_nom = $liste_noms [0][0];

	    $log_texte = "Le compte $user_nom a été supprimé";

	    $req_log_suppr_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression compte', '$log_texte');";
	    $result = $db_gespac->exec ( $req_log_suppr_user );
	
		// Suppression de l'utilisateur de la base
		$req_suppr_user = "DELETE FROM users WHERE user_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_user );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_user."\n");
		
		echo "<br><small>L'utilisateur <b>$user_nom</b> a été supprimé !</small>";
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

		// [BUG] pour le niveau : la valeur peut être null mais pas vide. On devrait pe mettre le niveau avec une valeur 0 pour non affecté par défaut
		$req_modif_user = "UPDATE users SET user_nom='$nom', user_logon='$login', user_password='$password', user_niveau=$niveau, user_mail='$mail', user_skin='$skin', user_accueil='$page' WHERE user_id=$id";
		$result = $db_gespac->exec ( $req_modif_user );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le compte $nom a été modifié";
		
	    $req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien été modifié...</small>";
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
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_user."\n");
		
		// [BUG=>la requête est nok] Insertion d'un log
		$log_texte = "Le compte $nom a été créé";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien été ajouté...</small>";
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


