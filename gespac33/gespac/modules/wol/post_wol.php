<?PHP


	/* 
		Permet le réveil des machines
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');

	
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
	
	// adresse de connexion à la base de données
	$dsn_gespac = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_id, mat_mac FROM materiels WHERE mat_mac <> '' " );	
	
	
	foreach ($lot_array as $machine) {
		if ( $machine <> "" ) {
			$mac = find_mac_by_id("$machine", $liste_des_materiels);
			//echo "essai de réveil de $machine : $mac<br>";
			exec ("sudo wakeonlan $mac" );
		}
	}
	
	
	$db_gespac->disconnect();
?>