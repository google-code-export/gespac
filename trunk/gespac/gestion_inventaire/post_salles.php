<?PHP


	/* fichier de creation / modif / suppr des salles
	
	Si j'ai un ID c'est une modification
	Si j'en ai pas c'est une cr�ation
	
	reste � coder pour la suppression
	
	*/
	
	include ('../includes.php');
	
	// Connexion � la base GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// Log des requ�tes SQL
	$log = new Log ("../dump/log_sql.sql");
		
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
		$salle_nom = $con_gespac->QueryOne ( "SELECT salle_nom FROM salles WHERE salle_id = $id" );

		$log_texte = "La salle $salle_nom a �t� supprim�e";

		$req_log_suppr_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression salle', '$log_texte' );";
		$con_gespac->Execute ( $req_log_suppr_salle );
		
		//On log la requ�te
		$log->Insert ( $req_log_suppr_salle );
				
		// d�place le mat�riel de la salle � supprimer dans la table STOCK (� priori la salle_id = 1)
		$req_deplace_materiel_dans_stock = "UPDATE materiels SET salle_id = 1 WHERE salle_id=$id";				// En cas, ici faire une sous requ�te pour obtenir le salle_id de la salle STOCK (mais bon, on cr�ra la salle automatiquement avec cet id normalement)
		$con_gespac->Execute ( $req_deplace_materiel_dans_stock );
		
		//On log la requ�te
		$log->Insert ( $req_deplace_materiel_dans_stock );
		
		// Suppression de la salle
		$req_suppr_salle = "DELETE FROM salles WHERE salle_id=$id";
		$con_gespac->Execute ( $req_suppr_salle );
	
		//On log la requ�te
		$log->Insert ( $req_suppr_salle );
	}
	

	#**************** AJOUT ********************#
	
	
	if ( $action == 'add' ) {
	
		$nom 		= addslashes(utf8_decode($_POST['nom']));
		$vlan 		= addslashes(utf8_decode($_POST['vlan']));
		$etage 		= addslashes(utf8_decode($_POST['etage']));
		$batiment 	= addslashes(utf8_decode($_POST['batiment'])); 
	
	
		$req_verifie_existence_salle = $con_gespac->QueryRow("SELECT * FROM salles WHERE salle_nom='$nom'; ");
		
		if ( $req_verifie_existence_salle[0] ) { // alors la salle existe
			echo "La salle <b>$nom</b> existe d�j� !";
			
			//Insertion d'un log
			$log_texte = "La salle $nom existe d�j� !"; //int�r�t de loguer �a ??
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation salle', '$log_texte' );";
			$con_gespac->Execute ( $req_log_creation_salle );
			
			//On log la requ�te
			$log->Insert ( $req_log_creation_salle );
			
		} else {
		
			$uai = $con_gespac->QueryOne("SELECT clg_uai FROM college; ");
			
			$req_add_salle = "INSERT INTO salles ( salle_nom , salle_vlan , salle_etage , salle_batiment, clg_uai ) VALUES ( '$nom', '$vlan', '$etage', '$batiment', '$uai');";
			$con_gespac->Execute ( $req_add_salle );
			
			//On log la requ�te
			$log->Insert ( $req_add_salle );
			
			//Insertion d'un log
			$log_texte = "La salle $nom a �t� cr��e";
			$req_log_creation_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Cr�ation salle', '$log_texte' );";
			$con_gespac->Execute ( $req_log_creation_salle );
			
			//On log la requ�te
			$log->Insert ( $req_log_creation_salle );
	
			echo "<small>Ajout de la salle <b>$nom</b> !</small>";
		}
	}
	

	#**************** MODIFICATION ********************#

	
	if ( $action == 'mod' ) {
		
		$id 		= $_POST['salleid'];
		$nom 		= addslashes(utf8_decode($_POST['nom']));
		$vlan 		= addslashes(utf8_decode($_POST['vlan']));
		$etage 		= addslashes(utf8_decode($_POST['etage']));
		$batiment 	= addslashes(utf8_decode($_POST['batiment'])); 
	
		$verifie_existence_salle = $con_gespac->QueryOne("SELECT salle_id FROM salles WHERE salle_nom='$nom'; ");
		
		if ( $verifie_existence_salle ) { // alors le nom de la salle existe et on met � jour tout sauf le nom de la salle
			
			$req_modif_salle = "UPDATE salles SET salle_vlan = '$vlan', salle_etage = '$etage', salle_batiment='$batiment' WHERE salle_id=$id";
			$con_gespac->Execute ( $req_modif_salle );
			
			//On log la requ�te
			$log->Insert ( $req_modif_salle );
			
			//Insertion d'un log
			$log_texte = "Les infos de la salle $nom ont �t� modifi�s mais pas le nom de la salle car il doit �tre unique.";
			$req_log_modif_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification salle', '$log_texte' );";
			$con_gespac->Execute ( $req_log_modif_salle );
			
			//On log la requ�te
			$log->Insert ( $req_log_modif_salle );
	
			echo "Les infos de la salle $nom ont �t� modifi�s mais pas le nom de la salle car il doit �tre unique.";
			
		} else {
			
			$req_modif_salle = "UPDATE salles SET salle_nom = '$nom', salle_vlan = '$vlan', salle_etage = '$etage', salle_batiment='$batiment' WHERE salle_id=$id";
			$con_gespac->Execute ( $req_modif_salle );
			
			//On log la requ�te
			$log->Insert ( $req_modif_salle );
			
			//Insertion d'un log
			$log_texte = "La salle $nom a �t� modifi�e";
			$req_log_modif_salle = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification salle', '$log_texte' );";
			$con_gespac->Execute ( $req_log_modif_salle );
			
			//On log la requ�te
			$log->Insert ( $req_log_modif_salle );
			
			echo "<small>Modification de la salle <b>$nom</b> !</small>";
		}
	}
	
?>
