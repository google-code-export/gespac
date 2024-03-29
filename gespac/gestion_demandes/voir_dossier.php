<?PHP

	#formulaire de cr�ation / modification d'une demande

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

	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

		
		$req_info_demande = $db_gespac->queryAll ( "SELECT dem_id, dem_date, dem_text, dem_etat, user_demandeur_id, user_intervenant_id, user_nom, dem_type FROM demandes, salles, users, materiels WHERE demandes.user_demandeur_id=users.user_id AND dem_id=$id ORDER BY dem_date" );
		
		
		$dem_id 				= $req_info_demande[0][0];
		$dem_date 				= $req_info_demande[0][1];
		$dem_text 				= stripslashes($req_info_demande[0][2]);
		$dem_etat 				= $req_info_demande[0][3];
		$user_demandeur_id 		= $req_info_demande[0][4];
		$user_intervenant_id 	= $req_info_demande[0][5];
		$user_demandeur_nom		= stripslashes($req_info_demande[0][6]);
		$dem_type				= $req_info_demande[0][7];

		//on r�cup�re le grade_id de l'utilisateur connect�
		$login = $_SESSION['login'];
		//on r�cup�re le grade_nom de l'utilisateur connect�
		$grade_nom = $db_gespac->queryOne ("SELECT grade_nom FROM users, grades WHERE grades.grade_id = users.grade_id AND user_logon='$login'");
		
		if ($grade_nom == 'ati' | $grade_nom == 'root') {
			// si le grade est celui d'un ati ou du root on met la valeur de la session � 1
			$_SESSION['entete_demandeur'] = 1;
		} else {
			$_SESSION['entete_demandeur'] = 0;
		}
		
		echo "<h2>Dossier <b>$id</b> cr�� le : $dem_date</h2><br>";
		
		
		// On r�cup�re la salle et le materiel si c'est une installation ou une reparation
		if ( $dem_type == "installation" || $dem_type == "reparation" ) {
			$rq_extraction_salle_mat = $db_gespac->queryAll ( "SELECT demandes.mat_id, demandes.salle_id, salle_nom FROM demandes, salles, users WHERE salles.salle_id=demandes.salle_id AND demandes.user_demandeur_id=users.user_id AND dem_id=$dem_id" );

			$mat_id 	= $rq_extraction_salle_mat [0][0];
			$salle_id 	= $rq_extraction_salle_mat [0][1];
			$salle_nom 	= stripslashes($rq_extraction_salle_mat [0][2]);
			
			// On r�cup�re le nom du mat�riel
			if ( $mat_id <> 0) {
				$liste_nom_materiel = $db_gespac->queryAll ( "SELECT mat_nom FROM materiels WHERE mat_id=$mat_id" );
				$mat_nom = stripslashes($liste_nom_materiel[0][0]);
			}
			else {	$mat_nom = "TOUS";	}
			
		} else { //ce n'est ni une installation ni une r�paration
			$mat_nom 	= "NA";
			$salle_nom 	= "NA";
		}
					
		
		// On change la couleur quand le dossier est clos et on masque la case de modification
		switch ($dem_etat) {
			case "clos" : {
				$etat_couleur = "#36F572";
				break;
			}
						
			case "attente" : {
				$etat_couleur = "#FFD700";
				break;
			}
						
			case "rectifier" : {
				$etat_couleur = "#FFD700";
				break;
			}
						
			case "precisions" : {
				$etat_couleur = "#FFD700";
				break;
			}
						
			case "intervention" : {
				$etat_couleur = "#F57236";
				break;
			}
		}
		

		echo "	<center>
				<table width=600px>
					<th>Etat Actuel</th>
					<th>Type</th>
					<th class='td_demandeur'>Demandeur</th>
					<th>Salle</th>
					<th>Mat�riel</th>
					
					<tr>
						<td bgcolor=$etat_couleur>$dem_etat</td>
						<td>$dem_type</td>
						<td class='td_demandeur'>$user_demandeur_nom</td>
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
				$txt_texte 	= stripslashes($record[1]);
				$user_nom 	= stripslashes($record[2]);
				$txt_etat	= stripslashes($record[3]);
				
				// On change la couleur quand le dossier est clos et on masque la case de modification
					switch ($txt_etat) {
						case "clos" : {
							$etat_couleur = "#36F572";
							break;
						}
						
						case "attente" : {
							$etat_couleur = "#FFD700";
							break;
						}
						
						case "rectifier" : {
							$etat_couleur = "#FFD700";
							break;
						}
						
						case "precisions" : {
							$etat_couleur = "#FFD700";
							break;
						}
						
						case "intervention" : {
							$etat_couleur = "#F57236";
							break;
						}
					}
				

				echo "
					<tr>
						<td>$txt_date</td>
						<td>$user_nom</td>
						<td bgcolor=$etat_couleur>$txt_etat</td>
						<td>$txt_texte</td>
				";
						
			}
			
			echo "</table>";
		?>
	
			
	</div>
	
	<script type="text/javascript">
	
	function hidethem (col_name, show) { 
	
		if ( show == true)
			var state = "";
		else var state = "none";
	
		$$(col_name).each(function(item) {
			item.style.display = state;
		})
	}
	
	
	function init_entetes (value) {
		
		if (value.substr(0, 1) == "1") { hidethem('.td_demandeur', true);} 
		else {hidethem('.td_demandeur', false);}
	}
	
	init_entetes ('<?PHP echo $_SESSION['entete_demandeur'];?>');
	
	</script>