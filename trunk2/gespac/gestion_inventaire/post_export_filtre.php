<?PHP

/*	CREATION DU FICHIER D'EXPORT DU FILTRE	*/

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	$filtre = $_GET['filter'];
	$segments = @explode ("&&", $filtre);
	$curseur = 1;	
	$where = " ";
	
	foreach ($segments as $segment) {
		
		// On explose les segments par les '='
		$couple = explode ('=', $segment);
		
		// Pour la partie champ
		$key = $couple[0];
		
		// pour la partie valeur
		if ($couple[1]) {
			$value = $couple[1];	
			
			if (substr($key, -1) == '!') {	// forme négative ?
				$not = " NOT ";
				$key = substr ($key, 0, -1);
			} else $not = " ";
		}
		else {
			$value=$couple[0];
			
			if (substr($value, 0, 1) == '!') { // forme négative ?	
				$not = " NOT ";
				$value = substr ($value,1);
			} else $not = "";		
		}

	
		
		switch ($key) {
			case "t" :	break;
			case "n" :	$champ = "mat_nom";			break;
			case "p" :	$champ = "user_nom";		break;
			case "d" :	$champ = "mat_dsit";		break;
			case "s" :	$champ = "mat_serial";		break;
			case "e" :	$champ = "mat_etat";		break;
			case "f" :	$champ = "marque_type";		break;
			case "sf" :	$champ = "marque_stype";	break;
			case "m" :	$champ = "marque_marque";	break;
			case "mo" :	$champ = "marque_model";	break;
			case "sa" :	$champ = "salle_nom";		break;
			case "o" :	$champ = "mat_origine";		break;
			default :	$champ = "mat_nom";			break;
		}
	
		if ($key == "t")  $where .= "(mat_nom LIKE '%$value%' OR user_nom LIKE '%$value%' OR mat_dsit LIKE '%$value%' OR mat_serial LIKE '%$value%' OR mat_origine LIKE '%$value%' OR mat_etat LIKE '%$value%' OR marque_type LIKE '%$value%' OR marque_stype LIKE '%$value%' OR marque_marque LIKE '%$value%' OR marque_model LIKE '%$value%' OR salle_nom LIKE '%$value%')";
		else $where .= " $champ $not LIKE '%" . $value . "%'";
	
		// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
		if ( $curseur <> count($segments) ) $where .= " AND ";
	
		$curseur++;
				
	}


	if ( $_GET['filter'] <> '' ) {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND $where)" );
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

<div id='downloadcsv'>
	<a href='dump/export_filtre.csv'><center><h2>Télécharger le fichier</h2><br><img src='img/icons/csv.png'></center></a>
</div>

<script>
	$('#downloadcsv').dialog({title:'export du filtre',width:'320',height:'250'}); 
</script>
