<?PHP

	/***********************************************************
	*
	*	REMPLISSAGE DU DIV "historique" de form_interventions.php
	*	avec toutes les inter faites sur un pc particulier
	*
	************************************************************/



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

	// mydiv est le div "historique"
	var mydiv = document.getElementById('<?PHP echo $_GET['div_id']; ?>');
	
	//mydiv.style.display = '';
	
	var tableau = "<center><br><br>HISTORIQUE DES INTERVENTIONS POUR CE MATERIEL<br><br><table align=center BORDER=1 CELLSPACING=0 style='border: 1px solid #ccc;padding: 4px;text-align:center; font-size: small;'>";

	<?PHP

		$mat_id 	= $_GET['mat'];
		$salle_id 	= $_GET['salle'];

		// requête qui va afficher dans l'historique
		$req_historique = $db_gespac->queryAll ( "SELECT dem_id, dem_date, dem_etat, user_nom, dem_text FROM demandes, users WHERE users.user_id = demandes.user_demandeur_id AND mat_id = $mat_id AND salle_id = $salle_id" );

		foreach ( $req_historique as $record) { 
			
			$dem_id 	= $record[0]; 
			$dem_date 	= $record[1]; 
			$dem_etat 	= $record[2]; 
			$user_nom 	= $record[3]; 
			$dem_text 	= $record[4]; 
			
	?>

	tableau += "<tr>"; 
	
		tableau += "<td><?PHP echo $dem_id; ?></td><td><?PHP echo $dem_date; ?></td><td><?PHP echo $dem_etat; ?></td><td><?PHP echo $user_nom; ?></td><td><?PHP echo $dem_text; ?></td>"; 

	tableau += "</tr>"; 	

	<?PHP
		}
	?>
	
	tableau += "</table>"; 
	
	mydiv.innerHTML = tableau;
	