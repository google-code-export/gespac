<?PHP


	/* fichier de creation / modif / suppr des marques
	
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
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	$id 		= $_GET['id'];
	
	
	
	/*********************************************
	*
	*		ACTIONS SUR MARQUES
	*
	**********************************************/
	
	/**************** ACTION => AJOUT A PARTIR DE LA TABLE DE CORRESPONDANCES ********************/
	
	if ( $action == 'add_corr' ) {
	
		$corr_id = $_GET['corr_id'];
		
		$marque_a_inserer = $db_gespac->queryRow("SELECT corr_type, corr_stype, corr_marque, corr_modele FROM correspondances WHERE corr_id=$corr_id;");
		
		$famille 	= $marque_a_inserer[0];
		$sfamille 	= $marque_a_inserer[1];
		$marque 	= $marque_a_inserer[2];
		$modele 	= $marque_a_inserer[3];
		
		$test_existence_dans_table_marques = $db_gespac->queryRow("SELECT * FROM marques WHERE marque_model='$modele' AND marque_type='$famille' AND marque_stype='$sfamille' AND marque_marque='$marque'");
		
		if ( $test_existence_dans_table_marques ) {
			echo "La marque <b>$marque $modele</b> existe déjà.";
			echo "<script>alert('La marque $marque $modele existe déjà.');</script>";
		}
		else {
		
			echo "Insertion de <b>$marque $modele</b> à partir de la table des correspondances";
		
			//Insertion d'un log
			$log_texte = "$marque $modele ajouté à partir de la table des correspondances.";
			$req_log_ajout_marque = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_ajout_marque );
			
			// ajout de la marque à partir de la corrspondance
			$req_add_marque = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model) VALUES ( '$famille', '$sfamille', '$marque', '$modele' )";
			$result = $db_gespac->exec ( $req_add_marque );
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_add_marque."\n");
		}
	}

	
	/**************** ACTION => SUPPRESSION ********************/
	
	if ( $action == 'suppr' ) {
	
		//Insertion d'un log
		//On récupère le nom de la marque en fonction du marque_id avant sa suppression
		$liste_marques = $db_gespac->queryAll ( "SELECT marque_model FROM marques WHERE marque_id = $id" );
		$marque_model = $liste_marques [0][0];

		$log_texte = "Le modèle $marque_model a été supprimé";

		$req_log_suppr_marque = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression marque', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_suppr_marque );
		
		
		/**************** SUPPRESSION ********************/
		
		$req_suppr_marque = "DELETE FROM marques WHERE marque_id=$id;";
		$result = $db_gespac->exec ( $req_suppr_marque );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_marque."\n");
	}
		
	/**************** ACTION => MODIFICATION DE LA MARQUE ********************/
	
	if ( $action == 'mod' ) {
	
		$id     = $_POST ['marqueid'];
		
		$select_type = addslashes(utf8_decode($_POST ['select_type']));
		$select_stype = addslashes(utf8_decode($_POST ['select_stype']));
		$select_marque = addslashes(utf8_decode($_POST ['select_marque']));
		$select_modele = addslashes(utf8_decode($_POST ['select_modele']));
		
		$text_type = addslashes(utf8_decode($_POST ['text_type']));
		$text_stype = addslashes(utf8_decode($_POST ['text_stype']));
		$text_marque = addslashes(utf8_decode($_POST ['text_marque']));
		$text_modele = addslashes(utf8_decode($_POST ['text_modele']));
		
		// Si le champ texte est vide on prend la valeur du select
		$type = $text_type == "" ? $type = $select_type : $type = $text_type;
		$stype = $text_stype == "" ? $stype = $select_stype : $stype = $text_stype;
		$marque = $text_marque == "" ? $marque = $select_marque : $marque = $text_marque;
		$modele = $text_modele == "" ? $modele = $select_modele : $modele = $text_modele;
		
		//On récupère le nom de la marque avant modification en fonction du marque_id
		$liste_marques = $db_gespac->queryAll ( "SELECT marque_model, marque_type, marque_stype, marque_marque FROM marques WHERE marque_id = $id" );
		$marque_modele_old 	= $liste_marques [0][0];
		$marque_type_old   	= $liste_marques [0][1];
		$marque_stype_old   = $liste_marques [0][2];
		$marque_marque_old  = $liste_marques [0][3];
		
		/**************** MODIFICATION ********************/
		
		$req_verifie_existence_marque = $db_gespac->queryAll("SELECT * FROM marques WHERE marque_type = '$type' AND marque_stype = '$stype' AND marque_marque = '$marque' AND marque_model = '$modele'; ");
		
		if ( $req_verifie_existence_marque[0][0] ) { // alors la marque existe
			echo "<script>alert('La marque existe déjà !');</script>";
			
			//Insertion d'un log
			$log_texte = "La marque $marque $modele de type $type $stype existe déjà !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
		}
		else {	
				
			$req_modif_marque = "UPDATE marques SET marque_type='$type', marque_stype='$stype', marque_marque='$marque', marque_model='$modele' WHERE marque_id='$id'";
			$result = $db_gespac->exec ( $req_modif_marque );
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_modif_marque."\n");
			
			//Insertion d'un log
			$log_texte = "La marque <b>$marque_marque_old $marque_modele_old</b> de type <b>$marque_type_old $marque_stype_old</b> a été modifiée en <b>$marque $modele</b> de type <b>$type $stype</b>";
			$req_log_modif_marque = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_modif_marque );
				
			echo "<small>Modification du modèle <b>$modele</b>.</small>";
		}
	}
	
	/**************** ACTION => MODIFICATION A PARTIR DE LA TABLE DE CORRESPONDANCES ********************/
	
	if ( $action == 'modif_corr' ) {
	
		$corr_id = $_GET['corr_id'];
		$marque_id = $_GET['marque_id'];
		
		$marque_a_inserer = $db_gespac->queryRow("SELECT corr_type, corr_stype, corr_marque, corr_modele FROM correspondances WHERE corr_id=$corr_id;");
		
		$famille 	= $marque_a_inserer[0];
		$sfamille 	= $marque_a_inserer[1];
		$marque 	= $marque_a_inserer[2];
		$modele 	= $marque_a_inserer[3];
		
		$test_existence_dans_table_marques = $db_gespac->queryRow("SELECT * FROM marques WHERE marque_model='$modele' AND marque_type='$famille' AND marque_stype='$sfamille' AND marque_marque='$marque'");
		
		if ( $test_existence_dans_table_marques ) {
			echo "La marque <b>$marque $modele</b> existe déjà.";
			echo "<script>alert('La marque $marque $modele existe déjà.');</script>";
		}
		else {
		
			echo "Modification de <b>$marque $modele</b> à partir de la table des correspondances";
		
			//Insertion d'un log
			$log_texte = "$marque $modele modifié à partir de la table des correspondances.";
			$req_log_ajout_marque = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_ajout_marque );
			
			// ajout de la marque à partir de la corrspondance
			$req_modif_marque = "UPDATE marques SET marque_type='$famille', marque_stype='$sfamille', marque_marque='$marque', marque_model='$modele' WHERE marque_id=$marque_id";
			$result = $db_gespac->exec ( $req_modif_marque );
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_modif_marque."\n");
		}
	}

	
	/**************** INSERTION DE LA MARQUE ********************/
	
	if ( $action == 'add' ) {
	
		$select_type = addslashes(utf8_decode($_POST ['select_type']));
		$select_stype = addslashes(utf8_decode($_POST ['select_stype']));
		$select_marque = addslashes(utf8_decode($_POST ['select_marque']));
		$select_modele = addslashes(utf8_decode($_POST ['select_modele']));
		
		$text_type = addslashes(utf8_decode($_POST ['text_type']));
		$text_stype = addslashes(utf8_decode($_POST ['text_stype']));
		$text_marque = addslashes(utf8_decode($_POST ['text_marque']));
		$text_modele = addslashes(utf8_decode($_POST ['text_modele']));
		
		// Si le champ texte est vide on prend la valeur du select
		$type = $text_type == "" ? $type = $select_type : $type = $text_type;
		$stype = $text_stype == "" ? $stype = $select_stype : $stype = $text_stype;
		$marque = $text_marque == "" ? $marque = $select_marque : $marque = $text_marque;
		$modele = $text_modele == "" ? $modele = $select_modele : $modele = $text_modele;
		

		$req_verifie_existence_marque = $db_gespac->queryAll("SELECT * FROM marques WHERE marque_type = '$type' AND marque_stype = '$stype' AND marque_marque = '$marque' AND marque_model = '$modele'; ");
		
		if ( $req_verifie_existence_marque[0][0] ) { // alors la marque existe
			echo "<script>alert('La marque existe déjà !');</script>";
			
			//Insertion d'un log
			$log_texte = "La marque $marque $modele de type $type $stype existe déjà !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
		}
		else {	
			$req_add_marque = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model) VALUES ( '$type', '$stype', '$marque', '$modele' )";
			$result = $db_gespac->exec ( $req_add_marque );
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_add_marque."\n");
				
			echo "<small>Ajout de la marque <b>$marque $modele</b> de type <b>$stype / $stype</b>.</small>";
				
			//Insertion d'un log
			$log_texte = "La marque $marque $modele de type $type $stype a été créée";
			$req_log_insert_marque = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création marque', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_insert_marque );
		}
	}
	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
	
?>