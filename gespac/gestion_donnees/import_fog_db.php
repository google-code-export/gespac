PAS D'iMPORT POUR LE MOMENT

<?PHP

/*

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	
	// adresse de connexion à la base de données
	$dsn_ocs 	= 'mysql://'. $user .':' . $pass . '@localhost/ocsweb';
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données OCS
	$db_ocs 	= & MDB2::factory($dsn_ocs);


	
	// stockage des lignes retournées par sql dans un tableau
	$liste_marques  = $db_ocs->queryAll ( "SELECT DISTINCT smanufacturer, smodel FROM bios;" );
	$liste_hardware = $db_ocs->queryAll ( "SELECT id, name, smanufacturer, smodel, ssn from hardware, bios where id=hardware_id;" );

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	
	//************************************************************
	//											
	//		IMPORT DES MARQUES DANS LA TABLE MARQUES	
	//											
	//***********************************************************
	


	
	// init du nb de marques differentes importées
	$nb_marques_importees = 0;
	
	// On insert pour chaque ligne de la liste des marques un enregistrement dans la table marques de la base GESPAC
	foreach ($liste_marques as $record ) {
		
		$marque = $record[0];
		$model 	= $record[1];
		
		// On change certains noms par qqchose de plus explicite
		switch ($record[1]) {
			case "8189M7G" 							: $model = "Think Centre"; 	break;
			case "8307LG9" 							: $model = "NetVista"; 		break;
			case "HP Compaq 6715b (GU475EC#ABF)" 	: $model = "6715b";
			default 		: 		break;
		}
		
		$req_insert_marques = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model ) VALUES ('PC', 'NA', '$marque', '$model' )";
		$result = $db_gespac->exec ( $req_insert_marques );
		
		if ( $result ==1 ) $nb_marques_importees++;
		
	}
	
	// donne le nombre de marques differentes importées
	echo "<small>$nb_marques_importees sur " . count($liste_marques) . " marques différentes importées ...</small>";
	
	
	
	
	//************************************************************
	//											
	//		IMPORT DES MACHINES DANS LA TABLE MARQUES MATERIELS
	//											
	//***********************************************************
	
	
	
	
	// On range dans un array la liste des marques et modeles de la base GESPAC
	$liste_marques = $db_gespac->queryAll ( "SELECT marque_id, CONCAT(marque_marque, ' ', marque_model) FROM marques;" );
	
	// On insert pour chaque ligne de la liste hardware OCS un enregistrement dans la table materiels de la base GESPAC en collant la bonne FK de la table des marques
	foreach ($liste_hardware as $record ) {
			
		$id 			= $record[0];
		$name 			= $record[1];
		$marque_model 	= $record[2] . " " . $record[3];
		$ssn 			= $record[4];
		
		// On génère un ssn aléatoire pour éviter les ssn vides (ssn est une clé unique. Si on se retrouve avec 12 materiels sans serial comme les tulipes, on ne pourra importer que le premier)
		if ( $ssn == "" ) $ssn = "RAND" . rand(0, 999);
			
		// On change certains noms par qqchose de plus explicite
		switch ($record[3]) {
			case "8189M7G" 	: $marque_model = "IBM Think Centre"; 	break;
			case "8307LG9" 	: $marque_model = "IBM NetVista"; 		break;
			case "HP Compaq 6715b (GU475EC#ABF)" 	: $marque_model = "Hewlett-Packard 6715b";
			default 		: 		break;
		}
			
		// on cherche l'index de la concaténation manufacturer + model dans le tableau des marques
		$marque_index = find_marque_id($marque_model, $liste_marques);

		// On execute la requete d'insertion
		$req_insert_marques = "INSERT INTO materiels (mat_nom, mat_serial, marque_id) VALUES ('$name', '$ssn', $marque_index)";
		$result = $db_gespac->exec ( $req_insert_marques );
		
	}

	//Nombre de matériel dans la base gespac
	$rq_nb_materiels = $db_gespac->queryAll ( "SELECT COUNT(*) FROM materiels" );
	$nb_materiels = $rq_nb_materiels[0][0];
	
	// donne le nombre de machine importées
	echo "<br><small>$nb_materiels sur " . count($liste_hardware) . " machines importées avec un serial unique...</small>";

	
	
	//Insertion d'un log
	
	$log_texte = "Import OCS vers GESPAC : $nb_marques_importees marques et $nb_materiels machines importées. ";
		
	$req_log_import_ocs_gespac = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import OCS', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_import_ocs_gespac );
	
	
	// On se déconnecte de la db
	$db_ocs->disconnect();
	$db_gespac->disconnect();
	
*/	
	
?>
