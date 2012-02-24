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
			
			// bourrage de zero de l'index sur 3 digits
			$num_unique = sprintf("%1$03d", $mat_id);
			
			$numinventaire = $inventaire . $id_type . $num_unique;
		
			$maj_dsit = "UPDATE materiels SET mat_dsit = '$numinventaire' WHERE mat_id=$mat_id;";
		
			$con_gespac->Execute($maj_dsit);
			
			$log->Insert($maj_dsit);
				
		}
	}
	
	echo "Mise à jour des numéros d'inventaire effectuée.<br>";

?>
