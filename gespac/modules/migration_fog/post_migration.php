<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');


	$lot = $_POST ['pc_a_poster'];
	$lot_array = explode(";", $lot);

	// cnx à la db gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste = "";

	// On constitue la requête
	foreach ($lot_array as $machine) {
		if ( $machine <> "" ) $liste .= " OR mat_id=$machine";
	}
	
	// rq pour la liste des serial + inventaire
	$pc_gespac = $con_gespac->QueryAll ("SELECT mat_serial, mat_dsit FROM materiels WHERE mat_id='' $liste");
	
	
	// cnx à la db fog
	$con_fog = new Sql($host, $user, $pass, $fog);	
	
	foreach ($pc_gespac as $pc) {
			
		$gespac_serial = $pc['mat_serial'];
		$gespac_dsit = $pc['mat_dsit'];
		
		// On récupère le hostID grace au serial
		$hostID = $con_fog->QueryOne ("SELECT iHostID FROM inventory WHERE iSysserial='$gespac_serial'");
		
		$con_fog->Execute("UPDATE hosts SET hostName = '$gespac_dsit' WHERE hostID=$hostID");
		
	}
	

?>
