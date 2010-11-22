<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
?>

	var mydiv = document.getElementById('<?PHP echo $_GET['div_id']; ?>');
	var select_dst  = document.getElementById('<?PHP echo $_GET['id_to_modify']; ?>');
	
	// on vide la select box
	while( select_dst.options.length > 0 ) select_dst.options[0] = null;
	
	select_dst.options[0] = new Option(">>> Choisir une valeur <<<", "");
	

	<?PHP

		$select_value 	= str_replace("%20", " ", $_GET['value']);
		$mydiv 			= $_GET['div_id'];
		$select_dst 	= $_GET['id_to_modify'];
		
		$select_type 	= $_GET['type'];
		$select_stype 	= $_GET['stype'];
		$select_marque 	= $_GET['marque'];
		
		switch ( $select_dst ) {
			case "stype" :
				$req_chainage = $db_gespac->queryAll ( "SELECT DISTINCT marque_stype FROM marques WHERE marque_type = '$select_value'" );
				break;
			case "marque" :
				$req_chainage = $db_gespac->queryAll ( "SELECT DISTINCT marque_marque FROM marques WHERE marque_type='$select_type' AND marque_stype = '$select_value'" );
				break;
			case "modele" :
				$req_chainage = $db_gespac->queryAll ( "SELECT DISTINCT marque_model FROM marques WHERE marque_type='$select_type' AND marque_stype = '$select_stype' AND marque_marque='$select_value' " );
				break;
		}

		foreach ( $req_chainage as $record) { 
			
			$item = addslashes(utf8_decode($record[0])); 
	?>
	

	
		mydiv.style.display = '';
		select_dst.options[select_dst.options.length] = new Option("<?PHP echo $item ?>", "<?PHP echo $item ?>");
		
	<?PHP
		}
	?>
	