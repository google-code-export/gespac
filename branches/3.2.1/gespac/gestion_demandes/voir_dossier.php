<?PHP

	#formulaire de création / modification d'une demande

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	$id = $_GET['id'];

?>


<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<!--  FONCTIONS JAVASCRIPT -->
<script>

</script>

<style>
	td { border : 1px solid #ccc; }
</style>


<?PHP

	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

		
		$req_info_demande = $db_gespac->queryAll ( "SELECT dem_id, dem_date, dem_text, dem_etat, user_demandeur_id, user_intervenant_id, user_nom, dem_type FROM demandes, salles, users, materiels WHERE demandes.user_demandeur_id=users.user_id AND dem_id=$id ORDER BY dem_date" );
		
		
		$dem_id 				= $req_info_demande[0][0];
		$dem_date 				= $req_info_demande[0][1];
		$dem_text 				= $req_info_demande[0][2];
		$dem_etat 				= $req_info_demande[0][3];
		$user_demandeur_id 		= $req_info_demande[0][4];
		$user_intervenant_id 	= $req_info_demande[0][5];
		$user_demandeur_nom		= $req_info_demande[0][6];
		$dem_type				= $req_info_demande[0][7];

		
		echo "<h2>Dossier <b>$id</b> créé le : $dem_date</h2><br>";
		
		
		// On récupère la salle et le materiel si c'est une installation ou une reparation
		if ( $dem_type == "installation" || $dem_type == "reparation" ) {
			$rq_extraction_salle_mat = $db_gespac->queryAll ( "SELECT demandes.mat_id, demandes.salle_id, salle_nom FROM demandes, salles, users WHERE salles.salle_id=demandes.salle_id AND demandes.user_demandeur_id=users.user_id AND dem_id=$dem_id" );

			$mat_id 	= $rq_extraction_salle_mat [0][0];
			$salle_id 	= $rq_extraction_salle_mat [0][1];
			$salle_nom 	= $rq_extraction_salle_mat [0][2];
			
			// On récupère le nom du matériel
			if ( $mat_id <> 0) {
				$liste_nom_materiel = $db_gespac->queryAll ( "SELECT mat_nom FROM materiels WHERE mat_id=$mat_id" );
				$mat_nom = $liste_nom_materiel[0][0];
			}
			else {	$mat_nom = "TOUS";	}
			
		} else {
			$mat_nom 	= "NA";
			$salle_nom 	= "NA";
		}
					
		

		echo "	<center>
				<table width=600px>
					<th>Etat Actuel</th>
					<th>Type</th>
					<th>Demandeur</th>
					<th>Salle</th>
					<th>Matériel</th>
					
					<tr>
						<td>$dem_etat</td>
						<td>$dem_type</td>
						<td>$user_demandeur_nom</td>
						<td>$salle_nom</td>
						<td>$mat_nom</td>
					</tr>

					<tr>
						<td colspan=7 style='border: 1px solid #ccc;'>$dem_text</td>
					</tr>

				</table>
		";

	?>	
	
	<br>
	Suivi du dossier : 
	<br><br>
	
	<!-- 	BLOC HISTORIQUE DES DEMANDES	-->
	<div id="historique">
		
		<?PHP 
			// historique des demandes
			$historique_demandes = $db_gespac->queryAll ( "SELECT txt_date, txt_texte, user_nom, txt_etat FROM demandes_textes, users WHERE dem_id=$id AND users.user_id=demandes_textes.user_id ORDER BY txt_date DESC;" );
			
			echo "
				<table style='border: 1px solid #ccc; width:600px;'>
					
					<th>Date</th>
					<th>Intervenant</th>
					<th>Etat</th>
					<th>commentaire</th>";
		
			foreach ( $historique_demandes as $record ) {
			
				$txt_date 	= $record[0];
				$txt_texte 	= $record[1];
				$user_nom 	= $record[2];
				$txt_etat	= $record[3];
				

				echo "
					<tr>
						<td>$txt_date</td>
						<td>$user_nom</td>
						<td>$txt_etat</td>
						<td>$txt_texte</td>
				";
						
			}
			
			echo "</table>";
		?>
	
			
	</div>
