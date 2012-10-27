<!--

	Permet la migration entre les anciens dossiers et les nouveaux

-->
		

<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères


	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// rq pour la liste des PC
	$liste_demandes = $con_gespac->QueryAll ("SELECT dem_id, dem_date, dem_text, dem_etat, dem_type, user_demandeur_id, mat_id, salle_id FROM demandes;");
	
	
	foreach ( $liste_demandes as $demande) {
		
		$dem_id		= $demande ["dem_id"];
		$dem_date	= $demande ["dem_date"];
		$dem_text	= utf8_encode(addslashes($demande ["dem_text"]));
		$dem_etat	= utf8_encode($demande ["dem_etat"]);
		$dem_type	= $demande ["dem_type"];
		$dem_user	= $demande ["user_demandeur_id"];
		$mat_id		= $demande ["mat_id"];
		$salle_id	= $demande ["salle_id"];
		
		// On regarde si cette demande existe déjà
		$demande_traitee = $con_gespac->QueryOne ("SELECT dossier_id FROM dossiers_textes WHERE txt_etat='ouverture' AND txt_date='$dem_date';");
		
		if ( $demande_traitee ) {
			echo "Cette demande a déjà été traitée. <br>";
		}
		else {
			
			// On blablatte un peu histoire de dire ce qu'on fait :
			echo "<b>Traitement de la demande $dem_id</b><br>";

			
			 // je créé le dossier pour cette demande
			$con_gespac->Execute("INSERT INTO dossiers (dossier_type) VALUES ('$dem_type')");
			// On devrait vérifier l'existence de ce type de dossier dans dossiers_types
			
			//On récupère l'id du dossier qu'on vient de créer
			$dossier_id = $con_gespac->GetLastID();
			
			// Ne pas oublier la première page du dossier
			$con_gespac->Execute("INSERT INTO dossiers_textes (dossier_id, txt_user, txt_date, txt_etat, txt_texte) VALUES ($dossier_id, $dem_user, '$dem_date', 'ouverture', '$dem_text')");

			// blabla
			echo "<li>$dem_date : " . utf8_decode(stripcslashes($dem_text));
			
			// Si le mat_id <> 0 c'est qu'on a qu'un seul matériel affecté par 
			if ( $mat_id <> 0 ) {
				
				// On met à jour le dernier dossier créé avec le bon mat_id
				$maj_mat_id = "UPDATE dossiers SET dossier_mat='$mat_id' WHERE dossier_id=$dossier_id;";
				$con_gespac->Execute ($maj_mat_id);
				
			}
			else {
			
				// On sélectionne tous les pc de la demandes.salle_id
				$liste_materiels_salle = $con_gespac->QueryAll("SELECT mat_id FROM materiels WHERE salle_id=$salle_id");

				// On concatène tous les matériels de la salle dans une chaine
				foreach ($liste_materiels_salle as $mat) {
					$concat_mat .= ";" . $mat['mat_id'];
				}
				
				// On supprime le dernier ;
				$concat_mat = preg_replace("[^;]", "", $concat_mat);
				
				// On met à jour le dernier dossier créé avec le bon mat_id
				$maj_mat_id = "UPDATE dossiers SET dossier_mat='$concat_mat' WHERE dossier_id=$dossier_id;";
				$con_gespac->Execute ($maj_mat_id);
				
			}
			
			
			
			// Pour chaque page supplémentaire de la demande, on insère une page au dossiers_textes
			$liste_demandes_textes = $con_gespac->QueryAll ("SELECT * FROM demandes_textes WHERE dem_id=$dem_id");
			
			foreach ($liste_demandes_textes as $texte) {
			
				$txt_id 	= $texte['txt_id'];
				$txt_date 	= $texte['txt_date']; 	
				$txt_etat 	= utf8_encode($texte['txt_etat']); 	
				$txt_texte 	= utf8_encode(addslashes($texte['txt_texte'])); 	
				$user_id 	= $texte['user_id'];
					
				$con_gespac->Execute("INSERT INTO dossiers_textes (dossier_id, txt_user, txt_date, txt_etat, txt_texte) VALUES ($dossier_id, $user_id, '$txt_date', '$txt_etat', '$txt_texte')");
				
				// blabla
				echo "<li>$txt_date : " . utf8_decode(stripcslashes($txt_texte));
			}
			
		}
	
		echo "<hr>";
			
	}


?>
