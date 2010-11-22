<?PHP


	/* fichier de creation / modif / suppr des salles
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une création
	
	reste à coder pour la suppression
	
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on récupère les paramètres de l'url	
	$action 	= $_GET['action'];
	
	
	#*********************************************
	#
	#			ACTIONS SUR SALLES
	#
	#*********************************************
	
	

	#**************** SUPPRESSION ********************#

	
	if ( $action == 'suppr' ) {
	
		$id 	= $_GET['id'];
		
		//Insertion d'un log (avant la suppression!)
		//On récupère le nom de la salle en fonction du $salle_id
		$liste_salle = $db_gespac->queryAll ( "SELECT salle_nom FROM salles WHERE salle_id = $id" );
		$salle_nom = $liste_salle [0][0];

		$log_texte = "La salle $salle_nom a été supprimée";

		$req_log_suppr_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression salle', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_suppr_salle );
				
		// déplace le matériel de la salle à supprimer dans la table STOCK (à priori la salle_id = 1)
		$req_deplace_materiel_dans_stock = "UPDATE materiels SET salle_id = 1 WHERE salle_id=$id";				// En cas, ici faire une sous requête pour obtenir le salle_id de la salle STOCK (mais bon, on créra la salle automatiquement avec cet id normalement)
		$result = $db_gespac->exec ( $req_deplace_materiel_dans_stock );
			
		// Suppression de la salle
		$req_suppr_materiel = "DELETE FROM salles WHERE salle_id=$id";
		$result = $db_gespac->exec ( $req_suppr_materiel );
		
		// On log la requête SQL
		fwrite($fp, date("Ymd His") . " " . $req_suppr_materiel."\n");
	}
	

	#**************** AJOUT ********************#
	
	
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode($_POST['nom']));
		$vlan 		= addslashes(utf8_decode($_POST['vlan']));
		$etage 		= addslashes(utf8_decode($_POST['etage']));
		$batiment 	= addslashes(utf8_decode($_POST['batiment'])); 
	
	
		$req_verifie_existence_salle = $db_gespac->queryAll("SELECT * FROM salles WHERE salle_nom='$nom'; ");
		
		if ( $req_verifie_existence_salle[0][0] ) { // alors la salle existe
			echo "<script>alert('La salle existe déjà !');</script>";
			
			//Insertion d'un log
			$log_texte = "La salle $nom existe déjà !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
			
		} else {
		
			$req_recup_uai = $db_gespac->queryAll("SELECT clg_uai FROM college; ");
			$uai = $req_recup_uai[0][0];
			
			$req_add_salle = "INSERT INTO salles ( salle_nom , salle_vlan , salle_etage , salle_batiment, clg_uai ) VALUES ( '$nom', '$vlan', '$etage', '$batiment', '$uai');";
			$result = $db_gespac->exec ( $req_add_salle );		
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_add_salle."\n");
			
			//Insertion d'un log
			$log_texte = "La salle $nom a été créée";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
	
			echo "<small>Ajout de la salle <b>$nom</b> !</small>";
		}
	}
	

	#**************** MODIFICATION ********************#

	
	if ( $action == 'mod' ) {
		
		$id 		= $_POST['salleid'];
		$ancien_nom = addslashes(utf8_decode($_POST['ancien_nom']));
		$nom 		= addslashes(utf8_decode($_POST['nom']));
		$vlan 		= addslashes(utf8_decode($_POST['vlan']));
		$etage 		= addslashes(utf8_decode($_POST['etage']));
		$batiment 	= addslashes(utf8_decode($_POST['batiment'])); 
	
		$req_verifie_existence_salle = $db_gespac->queryAll("SELECT * FROM salles WHERE salle_nom='$nom'; ");
		
		if ( $req_verifie_existence_salle[0][0] && $nom <> $ancien_nom) { // alors la salle existe
			echo "<script>alert('La salle existe déjà !');</script>";
			
			//Insertion d'un log
			$log_texte = "La salle $nom existe déjà !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Création salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
		}
		else {
			$req_modif_salle = "UPDATE salles SET salle_nom = '$nom', salle_vlan = '$vlan', salle_etage = '$etage', salle_batiment='$batiment' WHERE salle_id=$id";
			$result = $db_gespac->exec ( $req_modif_salle );
			
			// On log la requête SQL
			fwrite($fp, date("Ymd His") . " " . $req_modif_salle."\n");
			
			//Insertion d'un log

			$log_texte = "La salle $nom a été modifiée";

			$req_log_modif_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_modif_salle );
			
			echo "<small>Modification de la salle <b>$nom</b> !</small>";
		}
	}	
	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
?>