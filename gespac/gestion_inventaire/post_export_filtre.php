<?PHP

/*	CREATION DU FICHIER D'EXPORT DU FILTRE	*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	$filtre = $_GET['filtre'];
	$filter_explode_exclusion = @explode ("/", $filtre);
	$value_like_tab 	= $filter_explode_exclusion[0];	// Pour le filtre d'inclusion
	$value_notlike_tab 	= $filter_explode_exclusion[1]; // Pour le filtre d'exclusion


	/**************************
	* 	PARTIE INCLUSION
	***************************/
	
	$filter_explode_like = @explode ("+", $value_like_tab);
	$curseur_like = 1;
	
	
	foreach ( $filter_explode_like as $value_like) {
	
		// Si la valeur du champ est renseignée on l'intègre à la requête
		if ( $value_like <> "" ) {
			
			$value_like_explode = @explode (":", $value_like);
			$value_inc 			= trim($value_like_explode[0]);
			$champ_inc 			= trim($value_like_explode[1]);
			
			
			// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
			if ( !isset($champ_inc)  || $champ_inc == "") $champ_inc = -1;
			
			switch ($champ_inc) {
				case 0 :	$like .= "(mat_nom LIKE '%$value_inc%' OR user_nom LIKE '%$value_inc%' OR mat_dsit LIKE '%$value_inc%' OR mat_serial LIKE '%$value_inc%' OR mat_origine LIKE '%$value_inc%' OR mat_etat LIKE '%$value_inc%' OR marque_type LIKE '%$value_inc%' OR marque_stype LIKE '%$value_inc%' OR marque_marque LIKE '%$value_inc%' OR marque_model LIKE '%$value_inc%' OR salle_nom LIKE '%$value_inc%')";	break;
				case 1 :	$like .= "mat_nom LIKE '%$value_inc%'";			break;
				case 2 :	$like .= "user_nom LIKE '%$value_inc%'";		break;
				case 3 :	$like .= "mat_dsit LIKE '%$value_inc%'";		break;
				case 4 :	$like .= "mat_serial LIKE '%$value_inc%'";		break;
				case 5 :	$like .= "mat_etat LIKE '%$value_inc%'";		break;
				case 6 :	$like .= "marque_type LIKE '%$value_inc%'";		break;
				case 7 :	$like .= "marque_stype LIKE '%$value_inc%'";	break;
				case 8 :	$like .= "marque_marque LIKE '%$value_inc%'";	break;
				case 9 :	$like .= "marque_model LIKE '%$value_inc%'";	break;
				case 10 :	$like .= "salle_nom LIKE '%$value_inc%'";		break;
				case 11 :	$like .= "mat_origine LIKE '%$value_inc%'";		break;
				default :	$like .= "mat_nom LIKE '%$value_inc%'";			break;
			}
			
			// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
			if ( $curseur_like <> count($filter_explode_like) ) {
				$like .= " AND ";
			}
			
			$curseur_like++;
		}
			
	}
	
	
	/**************************
	* 	PARTIE EXCLUSION
	***************************/
	
	$filter_explode_notlike = @explode ("+", $value_notlike_tab);
	$curseur_notlike = 1;
		
		
	foreach ( $filter_explode_notlike as $value_notlike) {
	
		// Si la valeur du champ est renseignée on l'intègre à la requête
		if ( $value_notlike <> "" ) {
			
			$value_notlike_explode = @explode (":", $value_notlike);
			$value_exc 			= trim($value_notlike_explode[0]);
			$champ_exc 			= trim($value_notlike_explode[1]);
			
			
			// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
			if ( !isset($champ_exc)  || $champ_exc == "") $champ_exc = -1;
			
			switch ($champ_exc) {
				case 0 :	$notlike .= "(mat_nom NOT LIKE '%$value_exc%' OR user_nom NOT LIKE '%$value_exc%' OR mat_dsit NOT LIKE '%$value_exc%' OR mat_serial NOT LIKE '%$value_exc%' OR mat_origine NOT LIKE '%$value_exc%' OR mat_etat NOT LIKE '%$value_exc%' OR marque_type NOT LIKE '%$value_exc%' OR marque_stype NOT LIKE '%$value_exc%' OR marque_marque NOT LIKE '%$value_exc%' OR marque_model NOT LIKE '%$value_exc%' OR salle_nom NOT LIKE '%$value_exc%')";	break;
				case 1 :	$notlike .= "mat_nom NOT LIKE '%$value_exc%'";			break;
				case 2 :	$notlike .= "user_nom NOT LIKE '%$value_exc%'";			break;
				case 3 :	$notlike .= "mat_dsit NOT LIKE '%$value_exc%'";			break;
				case 4 :	$notlike .= "mat_serial NOT LIKE '%$value_exc%'";		break;
				case 5 :	$notlike .= "mat_etat NOT LIKE '%$value_exc%'";			break;
				case 6 :	$notlike .= "marque_type NOT LIKE '%$value_exc%'";		break;
				case 7 :	$notlike .= "marque_stype NOT LIKE '%$value_exc%'";		break;
				case 8 :	$notlike .= "marque_marque NOT LIKE '%$value_exc%'";	break;
				case 9 :	$notlike .= "marque_model NOT LIKE '%$value_exc%'";		break;
				case 10 :	$notlike .= "salle_nom NOT LIKE '%$value_exc%'";		break;
				case 11 :	$notlike .= "mat_origine NOT LIKE '%$value_exc%'";		break;
				default :	$notlike .= "mat_nom NOT LIKE '%$value_exc%'";			break;
			}
		}
		
		// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
		if ( $curseur_notlike <> count($filter_explode_notlike) ) {
			$notlike .= " AND ";
		}
		
		$curseur_notlike++;
			
	}
	
	
	/**************************
	* 		PARTIE JONCTION
	***************************/
	
	// permet de mettre la particule "AND" dans le cas ou le filtre d'exclusion existe
	if ( $value_like_tab <> "" && $value_notlike_tab <> "" ) $jonction = " AND ";

	
	//echo $like . $jonction . $notlike;
	
		
	if ( $filtre <> '' ) {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND $like $jonction $notlike)" );
	}
	else {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id)" );
	}
	

$fp = fopen('../dump/export_filtre.csv', 'w+');

// ENTETES
fputcsv($fp, array('NOM', 'DSIT', 'SSN', 'ETAT', 'MARQUE', 'MODELE', 'FAMILLE', 'SFAMILLE', 'SALLE', 'ORIGINE', 'UTILISATEUR'), ';' );

foreach ($liste_des_materiels as $record) {

	$mat_nom 		= $record['mat_nom'];
	$mat_dsit 		= $record['mat_dsit'];
	$mat_serial 	= $record['mat_serial'];
	$mat_etat 		= $record['mat_etat'];
	$marque_marque 	= $record['marque_marque'];
	$marque_model 	= $record['marque_model'];
	$marque_type 	= $record['marque_type'];
	$marque_stype 	= $record['marque_stype'];
	$salle_nom 		= $record['salle_nom'];
	$mat_origine	= $record['mat_origine'];
	$user_nom 		= $record['user_nom'] == 'ati' ? '' : $record['user_nom'];

    
	fputcsv($fp, array($mat_nom, $mat_dsit, $mat_serial, $mat_etat, $marque_marque, $marque_model, $marque_type, $marque_stype, $salle_nom, $mat_origine, $user_nom), ';');
	
}

fclose($fp);

$con_gespac->Close();

?>

<script>window.open('dump/export_filtre.csv', 'export du filtre');</script>
