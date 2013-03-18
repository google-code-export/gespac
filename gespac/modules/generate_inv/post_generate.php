<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Log.class.php');
	include_once ('../../../class/Sql.class.php');

	$lot 		= $_POST ['pc_a_poster'];
	$lot_array 	= explode(";", $lot);

	// cnx à la db gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// Log des requêtes SQL
	$log = new Log ("../../dump/log_sql.sql");
	
	$uai = $con_gespac->QueryOne("SELECT clg_uai FROM college;");
	
	// On commence à générer le numéro d'inventaire
	$inventaire = "C" . substr($uai, 3, 4);
	
	// Liste des mat_id libres dans la base
	$free_mat_id = $con_gespac->QueryAll("SELECT mat_id+1 FROM materiels WHERE (mat_id + 1) NOT IN (SELECT mat_id FROM materiels) ORDER BY mat_id;");
	
	
	foreach ($lot_array as $record) {
		if ( $record <> "" ) {
			$materiel = $con_gespac->QueryRow ("SELECT mat_id, mat_nom, mat_dsit, mat_serial, marque_type, marque_stype FROM materiels, marques WHERE materiels.marque_id=marques.marque_id AND mat_id=$record");
			
			$mat_id	= $materiel[0];
			$nom 	= $materiel[1];
			$dsit 	= $materiel[2];
			$serial	= $materiel[3];
			$type	= $materiel[4];
			$stype	= $materiel[5];
			
			// J'initialise le type à X. comme xorro ;p
			$id_type = "X";
			
			if ( $type == "PC" && $stype == "DESKTOP") $id_type = "C";
			if ( $type == "PC" && $stype == "PORTABLE") $id_type = "P";
			if ( $type == "IMPRIMANTE") $id_type = "I";
			if ( $type == "TBI") $id_type = "V";
			if ( $type == "ECRAN") $id_type = "E";
			
		
			// On limite le id à 3 digits
			if ( $mat_id > 999 ) {
				// On change le mat_id avec le premier id libre dans la table materiels.

				$my_id = $free_mat_id[0]["mat_id+1"];
				
				// Je vire un élément du tableau des free_id
				$free_mat_id = array_slice($free_mat_id, 1);
			
				// bourrage de zero de l'index sur 3 digits
				$num_unique = sprintf("%1$03d", $my_id);
			}
			else {
				// bourrage de zero de l'index sur 3 digits
				$num_unique = sprintf("%1$03d", $mat_id);
			}
		
			
			$numinventaire = $inventaire . $id_type . $num_unique;
		
			$maj_dsit = "UPDATE materiels SET mat_dsit = '$numinventaire' WHERE mat_id=$mat_id;";
		
			$con_gespac->Execute($maj_dsit);
			
			$log->Insert($maj_dsit);
				
		}
	}
	
	echo "Mise à jour des numéros d'inventaire effectuée.";

?>
