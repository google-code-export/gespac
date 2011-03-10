<?PHP


	/* 
		Permet le r�veil des machines
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
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
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_id, mat_mac FROM materiels WHERE mat_mac <> '' " );	
	
	
	foreach ($lot_array as $machine) {
		if ( $machine <> "" ) {
			$mac = find_mac_by_id("$machine", $liste_des_materiels);
			//echo "essai de r�veil de $machine : $mac<br>";
			exec ("sudo wakeonlan $mac" );
		}
	}
	
	
	$db_gespac->disconnect();
?>