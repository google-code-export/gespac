<?PHP


	/* 
		Permet le réveil des machines
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	
	// On cherche l'adresse mac dans la liste mat_id / mat_mac
	function find_mac_by_id($needle, $haystack) {
		foreach($haystack as $key=>$value) {
			$current_key=$key;
			
			if($needle===$value OR (is_array($value) && find_mac_by_id($needle,$value))) {
				return $value[1];
			}
		}
		return false;
	} 
	
		
	$lot = $_POST ['materiel_a_poster'];
	$lot_array = explode(";", $lot);
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_id, mat_mac FROM materiels WHERE mat_mac <> '' " );	
	
	
	foreach ($lot_array as $machine) {
		if ( $machine <> "" ) {
			$mac = find_mac_by_id("$machine", $liste_des_materiels);
			exec ("sudo wakeonlan $mac" );
		}
	}
	
	
	$con_gespac->Close();
?>