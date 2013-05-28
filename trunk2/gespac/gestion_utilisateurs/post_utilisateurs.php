<?PHP


	/* fichier de creation / modif / suppr d'un utilisateur
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une création
	
	
	*/
	
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');		
	
	
	
	// Cnx Ã  la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");
	
	
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
		

	/**************** SUPPRESSION ********************/
	
	if ( $action == 'del' ) {
		
		$id = $_POST['id'];

        //Insertion d'un log
		
		//On récupère le nom de l'utilisateur en fonction du user_id
	    $user_nom = $con_gespac->QueryOne ( "SELECT user_nom FROM users WHERE user_id=$id" );
	    
	    // On teste si un matériel est prêté Ã  l'utilisateur
	    $pret_existe = $con_gespac->QueryOne ( "SELECT mat_id FROM materiels WHERE user_id=$id" );
	    
	    if ( $pret_existe )	{
			echo "Le compte ne peut être supprimé, un matériel est prêté.";
		}
		
		else {
			
			$log_texte = "Le compte <b>$user_nom</b> a été supprimé.";

			$req_log_suppr_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression compte', '$log_texte');";
			$con_gespac->Execute ( $req_log_suppr_user );
					
			//on log la requête
			$log->Insert( $req_log_suppr_user );
			
			// Suppression de l'utilisateur de la base
			$req_suppr_user = "DELETE FROM users WHERE user_id=$id;";
			$con_gespac->Execute ( $req_suppr_user );
			
			// On log la requête SQL
			$log->Insert( $req_suppr_user );
			
			echo "L'utilisateur <b>$user_nom</b> a été supprimé !";				
		}    
	    


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

		// si on modifie le grade, la page de démarrage devient la page de bienvenue
		$ancien_grade = $con_gespac->QueryOne("SELECT grade_id FROM users WHERE user_id=$id");
		
		if ($grade <> $ancien_grade) $page="bienvenue.php";


		// on récupére les anciennes valeurs du compte pour les logs
		$req_infos_compte_old = $con_gespac->QueryRow("SELECT user_nom FROM users WHERE user_id=$id");
		$nom_old = $req_infos_compte_old[0];
		
		
		$req_modif_user = "UPDATE users SET user_nom='$nom', user_logon='$login', user_password='$password', grade_id=$grade, user_mail='$mail', user_skin='$skin', user_accueil='$page', user_mailing=$mailing WHERE user_id=$id";
		$con_gespac->Execute ( $req_modif_user );
		
		// On log la requête SQL
		$log->Insert( $req_modif_user );
		
		//Insertion d'un log
		$log_texte = "Le compte (anciennement <b>$nom_old</b>) a été modifié en <b>$nom</b>.";
		
	    $req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	    $con_gespac->Execute ( $req_log_modif_user );
		
		// On log la requête SQL
		$log->Insert( $req_log_modif_user );
		
		echo "L'utilisateur <b>$nom</b> a bien été modifié...";
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
		$con_gespac->Execute ( $req_add_user );
		
		// On log la requête SQL
		$log->Insert( $req_add_user );
		
		//Insertion d'un log
		$log_texte = "Le compte <b>$nom</b> a été créé.";

	    $req_log_creation_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création compte', '$log_texte' );";
	    $con_gespac->Execute ( $req_log_creation_user );
		
		// On log la requête SQL
		$log->Insert( $req_log_creation_user );
		
		echo "L'utilisateur <b>$nom</b> a bien été ajouté...";
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
			
				// si on modifie le grade, la page de démarrage devient la page de bienvenue
				$ancien_grade = $con_gespac->QueryOne("SELECT grade_id FROM users WHERE user_id=$item");
		
				if ($grade <> $ancien_grade) 
					$page="bienvenue.php";
				else
					$page = $con_gespac->QueryOne("SELECT user_accueil FROM users WHERE user_id=$item");
				
				
				//$skin est le 1er champ Ã  UPDATER (ou pas)
				$sql_skin = $skin == "" ? "" : " user_skin='$skin' ";
				
				
				//$grade est le 2eme champ à UPDATER (ou pas)
				if ( $grade <> "" ) {
					
					// met on ou non la virgule avant en fonction de l'existence de la variable précédente (oula, dur à comprendre là ...). Si $sql_skin est vide, Ca signifie qu'on ne modifie pas cette valeur donc pas de virgule avant $sql_grade
					$sql_grade = $sql_skin == "" ? " grade_id=$grade " : ", grade_id=$grade " ;
					
				} else { $sql_grade = ""; }
				
				
				//$mailing est le 3eme champ à  UPDATER (ou pas)
				if ( $mailing <> 2 ) {

					// dans la rq sql, met on ou non la virgule avant en fonction de l'existence de la variable précédente (oula, dure Ã  comprendre Ã§a ...)					
					if ( $sql_skin == "" && $sql_grade == "" ) $sql_mailing = " user_mailing=$mailing";
					else $sql_mailing = " , user_mailing=$mailing" ;

				} else { $sql_mailing = ""; }
				
				

				
				$req_modif_user = "UPDATE users SET " . $sql_skin . $sql_grade . $sql_mailing . ", user_accueil='$page' WHERE user_id=$item ;";
				$con_gespac->Execute ( $req_modif_user );
				
				//on récupérer le nom et l'id du user de chaque item pour les logs
				$req_nom_id_user = $con_gespac->QueryRow ("SELECT user_nom, user_id FROM users WHERE user_id=$item");
				$liste_noms_id   .=  '<b>'.$req_nom_id_user[0].' (</b>serial : <b>'.$req_nom_id_user[1].')</b>, ';
				
				// On log la requête SQL
				$log->Insert( $req_modif_user );
			}

		}
	
		//Insertion d'un log
		//on supprime les caractères en fin de chaine
		$liste_noms_id = trim ($liste_noms_id, ", ");
		$log_texte = "Les utilisateurs $liste_noms_id ont été modifiés.";

		echo "Le lot a été modifié.";

		$req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
		$con_gespac->Execute ( $req_log_modif_user );
		
		// On log la requête SQL
		$log->Insert( $req_log_modif_user );
	}

?>


