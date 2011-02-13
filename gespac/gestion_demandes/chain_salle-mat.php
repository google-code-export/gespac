<?PHP

	/***************************************************************
	*
	*	REMPLISSAGE DU COMBOBOX "pc_demande" de "form_demandes.php"
	*	en fonction du combobox des salles du m�me formulaire
	*
	****************************************************************/



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
?>

	var mydiv = document.getElementById('<?PHP echo $_GET['div_id']; ?>');
	var sbox  = document.getElementById('<?PHP echo $_GET['id_to_modify']; ?>');

	// on vide la select box
	while( sbox.options.length > 0 ) sbox.options[0] = null;

	sbox.options[sbox.options.length] = new Option(">>> S�lectionnez un PC <<<");
	sbox.options[sbox.options.length] = new Option("Toute la salle", 0);

	<?PHP

		$salle_id = $_GET['value'];

		// requ�te qui va afficher dans le menu d�roulant les pc de la salle $salle_id
		$req_types_disponibles = $db_gespac->queryAll ( "SELECT mat_id, mat_nom FROM materiels WHERE salle_id = $salle_id ORDER BY mat_nom" );

		foreach ( $req_types_disponibles as $record) { 
			
			$matid = $record[0]; 
			$matnom = utf8_decode($record[1]); 
	?>
			mydiv.style.display = '';
			sbox.options[sbox.options.length] = new Option("<?PHP echo $matnom ?>", "<?PHP echo $matid ?>");
	<?PHP
		}
	?>
	