<?PHP


	/* fichier de creation / modif / suppr des salles
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une cr�ation
	
	reste � coder pour la suppression
	
	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	// on r�cup�re les param�tres de l'url	
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
		//On r�cup�re le nom de la salle en fonction du $salle_id
		$liste_salle = $db_gespac->queryAll ( "SELECT salle_nom FROM salles WHERE salle_id = $id" );
		$salle_nom = $liste_salle [0][0];

		$log_texte = "La salle $salle_nom a �t� supprim�e";

		$req_log_suppr_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression salle', '$log_texte' );";
		$result = $db_gespac->exec ( $req_log_suppr_salle );
				
		// d�place le mat�riel de la salle � supprimer dans la table STOCK (� priori la salle_id = 1)
		$req_deplace_materiel_dans_stock = "UPDATE materiels SET salle_id = 1 WHERE salle_id=$id";				// En cas, ici faire une sous requ�te pour obtenir le salle_id de la salle STOCK (mais bon, on cr�ra la salle automatiquement avec cet id normalement)
		$result = $db_gespac->exec ( $req_deplace_materiel_dans_stock );
			
		// Suppression de la salle
		$req_suppr_materiel = "DELETE FROM salles WHERE salle_id=$id";
		$result = $db_gespac->exec ( $req_suppr_materiel );
		
		// On log la requ�te SQL
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
			echo "<script>alert('La salle existe d�j� !');</script>";
			
			//Insertion d'un log
			$log_texte = "La salle $nom existe d�j� !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
			
		} else {
		
			$req_recup_uai = $db_gespac->queryAll("SELECT clg_uai FROM college; ");
			$uai = $req_recup_uai[0][0];
			
			$req_add_salle = "INSERT INTO salles ( salle_nom , salle_vlan , salle_etage , salle_batiment, clg_uai ) VALUES ( '$nom', '$vlan', '$etage', '$batiment', '$uai');";
			$result = $db_gespac->exec ( $req_add_salle );		
			
			// On log la requ�te SQL
			fwrite($fp, date("Ymd His") . " " . $req_add_salle."\n");
			
			//Insertion d'un log
			$log_texte = "La salle $nom a �t� cr��e";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation salle', '$log_texte' );";
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
			echo "<script>alert('La salle existe d�j� !');</script>";
			
			//Insertion d'un log
			$log_texte = "La salle $nom existe d�j� !";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_creation_salle );
		}
		else {
			$req_modif_salle = "UPDATE salles SET salle_nom = '$nom', salle_vlan = '$vlan', salle_etage = '$etage', salle_batiment='$batiment' WHERE salle_id=$id";
			$result = $db_gespac->exec ( $req_modif_salle );
			
			// On log la requ�te SQL
			fwrite($fp, date("Ymd His") . " " . $req_modif_salle."\n");
			
			//Insertion d'un log

			$log_texte = "La salle $nom a �t� modifi�e";

			$req_log_modif_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification salle', '$log_texte' );";
			$result = $db_gespac->exec ( $req_log_modif_salle );
			
			echo "<small>Modification de la salle <b>$nom</b> !</small>";
		}
	}	
	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
?>