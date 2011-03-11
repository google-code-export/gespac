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
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
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

	    $log_texte = "Le compte <b>$user_nom</b> a été supprimé.";

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
		$grade   	= $_POST ['grade'];
		$mail  		= $_POST ['mail'];
		$skin  		= $_POST ['skin'];
		$page   	= $_POST ['page'];
		$mailing   	= $_POST ['mailing'];
		
		$mailing = $mailing == "on" ? 1 : 0 ;

		// on récupére les anciennes valeurs du compte pour les logs
		$req_infos_compte_old = $db_gespac->queryRow("SELECT user_nom FROM users WHERE user_id=$id");
		$nom_old = $req_infos_compte_old[0];
		
		
		$req_modif_user = "UPDATE users SET user_nom='$nom', user_logon='$login', user_password='$password', grade_id=$grade, user_mail='$mail', user_skin='$skin', user_accueil='$page', user_mailing=$mailing WHERE user_id=$id";
		$result = $db_gespac->exec ( $req_modif_user );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
		
		//Insertion d'un log
		$log_texte = "Le compte (anciennement <b>$nom_old</b>) a été modifié en <b>$nom</b>.";
		
	    $req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_modif_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien été modifié...</small>";
	}
	
	/**************** INSERTION ********************/
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
		$login 		= $_POST ['login'];
		$password	= $_POST ['password'];
		$grade   	= $_POST ['grade'];
		$mail   	= $_POST ['mail'];
		$skin   	= $_POST ['skin'];
		$page   	= "bienvenue.php";
		$mailing	= $_POST ['mailing'];

		$mailing = $mailing == "on" ? 1 : 0 ;
		
		$req_add_user = "INSERT INTO users ( user_nom, user_logon, user_password, grade_id, user_mail, user_skin, user_accueil, user_mailing) VALUES ( '$nom', '$login', '$password', $grade, '$mail', '$skin', '$page', $mailing)";
		$result = $db_gespac->exec ( $req_add_user );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_add_user."\n");
		
		//Insertion d'un log
		$log_texte = "Le compte <b>$nom</b> a été créé.";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création compte', '$log_texte' );";
	    $result = $db_gespac->exec ( $req_log_creation_user );
		
		echo "<br><small>L'utilisateur <b>$nom</b> a bien été ajouté...</small>";
	}
	
	
	/**************** MODIFICATION D'UN LOT ********************/
		
	if ( $action == 'modlot' ) {
		
		$lot		= $_POST ['lot_users'];
		$mailing	= $_POST ['mailing'];
		$grade		= $_POST ['grade'];
		$skin		= addslashes(utf8_decode(urldecode($_POST ['skin'])));
		
		$lot_array = explode(";", $lot);
		
		foreach ($lot_array as $item) {
			if ( $item <> "" ) {	// permet de virer les éléments vides
				
				
				//$skin est le 1er champ à UPDATER (ou pas)
				$sql_skin = $skin == "" ? "" : " user_skin='$skin' ";
				
				
				//$grade est le 2eme champ à UPDATER (ou pas)
				if ( $grade <> "" ) {
					
					// met on ou non la virgule avant en fonction de l'existence de la variable précédente (oula, dure à comprendre ça ...). Si $sql_skin est vide, ça signifie qu'on ne modifie pas cette valeur donc pas de virgule avant $sql_grade
					$sql_grade = $sql_skin == "" ? " grade_id=$grade " : ", grade_id=$grade " ;
					
				} else { $sql_grade = ""; }
				
				
				//$mailing est le 3eme champ à UPDATER (ou pas)
				if ( $mailing <> 2 ) {

					// dans la rq sql, met on ou non la virgule avant en fonction de l'existence de la variable précédente (oula, dure à comprendre ça ...)					
					if ( $sql_skin == "" && $sql_grade == "" ) $sql_mailing = " user_mailing=$mailing";
					else $sql_mailing = " , user_mailing=$mailing" ;

				} else { $sql_mailing = ""; }
				
				

				
				$req_modif_user = "UPDATE users SET " . $sql_skin . $sql_grade . $sql_mailing . " WHERE user_id=$item ;";
				$result = $db_gespac->exec ( $req_modif_user );
				
				//on récupérer le nom et l'id du user de chaque item pour les logs
				$req_nom_id_user = $db_gespac->queryRow ("SELECT user_nom, user_id FROM users WHERE user_id=$item");
				$liste_noms_id   .=  '<b>'.$req_nom_id_user[0].' (</b>serial : <b>'.$req_nom_id_user[1].')</b>, ';
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
			}

		}
	
		//Insertion d'un log
		//on supprime les caractères en fin de chaine
		$liste_noms_id = trim ($liste_noms_id, ", ");
		$log_texte = "Les utilisateurs $liste_noms_id ont été modifiés.";

		$req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_modif_user );

	}

	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>


