<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Log.class.php');
	include_once ('../../../class/Sql.class.php');

	$maj_desc 	= $_POST ['import_nom'];
	$lot 		= $_POST ['pc_a_poster'];
	$lot_array 	= explode(";", $lot);

	// cnx à la db gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// Log des requêtes SQL
	$log = new Log ("../../dump/log_sql.sql");
	
	$liste = "";

	// On constitue la requête
	foreach ($lot_array as $machine) {
		if ( $machine <> "" ) $liste .= " OR mat_id=$machine";
	}
	
	// rq pour la liste des serial + inventaire
	$pc_gespac = $con_gespac->QueryAll ("SELECT mat_serial, mat_dsit, mat_nom FROM materiels WHERE mat_id='' $liste");
	
	
	// cnx à la db fog
	$con_fog = new Sql($host, $user, $pass, $fog);	
	
	foreach ($pc_gespac as $pc) {
			
		$gespac_serial = $pc['mat_serial'];
		$gespac_dsit = $pc['mat_dsit'];
		$gespac_nom = $pc['mat_nom'];
		
		// On récupère les hostIDs grace au serial
		$hostIDs = $con_fog->QueryAll ("SELECT iHostID FROM inventory WHERE iSysserial='$gespac_serial'");
		
		foreach ( $hostIDs as $hostID ) {
		
			$id = $hostID["iHostID"];	
				
			if ( $maj_desc ) {
				$sql = "UPDATE hosts SET hostName = '$gespac_dsit', hostDesc = '$gespac_nom' WHERE hostID=$id";
				$con_fog->Execute($sql);
				$log->Insert($sql);
			}
			else {
				$sql = "UPDATE hosts SET hostName = '$gespac_dsit' WHERE hostID=$id";
				$con_fog->Execute($sql);
				$log->Insert($sql);
			}
		}

	}
	
	echo "Migration des noms des machines dans FOG effectuée !";
	

?>
