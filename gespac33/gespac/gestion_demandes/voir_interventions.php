<?PHP
session_start();

/*

	- l'ati peut créer une inter directement
	- une demande expédiée en intervention apparait ici
	- une intervention doit être commentée lors de sa cloture
	- on doit pouvoir avoir acces au dossier de la demande
	
*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
?>

<h3>Visualisation des interventions</h3>

<br>


<script type="text/javascript" src="server.php?client=all"></script>

<!--	DIV target pour Ajax	-->
<div id="target"></div>



<script type="text/javascript">	
	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
				
		for (var r = 1; r < table.rows.length; r++){
			
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
					displayStyle = '';
				}	
				else {	// on masque les rows qui ne correspondent pas
					displayStyle = 'none';
					break;
				}
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;	
		}
	}	

		
	
	// *********************************************************************************
	//
	//				Masque / Montre les interventions closes
	//
	// *********************************************************************************

	function montre_masque_dossiers_clos () {

		var table = document.getElementById("interventions_table");
		var lien = document.getElementById("masque_montre");
		
		if ( lien.title == "masque" ) {		// on masque les rows "cloturer" 
			
			lien.title = "affiche"
			
			for (var r = 1; r < table.rows.length; r++) {
				
				if (table.rows[r].cells[3].innerHTML != " EN COURS " )	// attention aux espaces avant et après !
					displayStyle = "none";			
				else
					displayStyle = "";

				// Affichage on / off en fonction de displayStyle
				table.rows[r].style.display = displayStyle;	
			}
		
		} else {	// On affiche toutes les rows
			
			lien.title = "masque"
			
			for (var r = 1; r < table.rows.length; r++) {
				// Affichage de toutes les rows du tableau
				table.rows[r].style.display = "";	
			}
		}

	}	
</script>



	<?PHP 

		// adresse de connexion à la base de données
		$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

		// cnx à la base de données GESPAC
		$db_gespac 	= & MDB2::factory($dsn_gespac);

		// stockage des lignes retournées par sql dans un tableau nommé liste_des_demandes
		$liste_des_interventions = $db_gespac->queryAll ( "SELECT interv_id, interv_date, interv_cloture, interv_text, interventions.dem_id, interventions.salle_id, salle_nom, interventions.mat_id, interventions.user_id, dem_text FROM interventions, salles, demandes WHERE interventions.salle_id=salles.salle_id AND demandes.dem_id=interventions.dem_id ORDER BY interv_date DESC" );
		
		// grade de l'utilisateur courant
		$grade = $_SESSION['grade']; 
		
	?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'interventions_table', '1')" type="text"></center>
	</form>
	
	<center><small><a href='#' id="masque_montre" onclick="montre_masque_dossiers_clos();" title="masque">masquer/montrer les interventions closes</a></small></center>
	
	<center>
	
	<table class="tablehover" id="interventions_table" width=800>
	
		<th>Dossier</th>
		<th>Inter</th>
		<th>Ouverture</th>
		<th>Cloture</th>
		<th>Salle</th>
		<th>Matériel</th>
		<th>texte</th>
		<th>&nbsp</th>

		
		<?PHP	
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_interventions as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";

					$interv_id		= $record[0]; 
					$interv_date	= $record[1]; 
					$interv_cloture	= $record[2]; 
					$interv_text	= $record[3]; 
					$dossier		= $record[4]; 
					$salle_id		= $record[5]; 
					$salle_nom		= $record[6]; 
					$mat_id			= $record[7]; 
					$user_id		= $record[8];
					$demande_txt	= $record[9];
					
					

					// On récupère le nom du matériel
					if ( $mat_id <> 0) {
						$liste_nom_materiel = $db_gespac->queryAll ( "SELECT mat_nom FROM materiels WHERE mat_id=$mat_id" );
						$mat_nom = $liste_nom_materiel[0][0];
					}
					else {	$mat_nom = "TOUS";	}
					
					
					if ( $grade > 1 )	// Si l'utilisateur n'est pas admin il ne peut pas modifier les inter
						$hidemodif = "none";
					else {
						// Ne pas pouvoir modifier une inter close
						$hidemodif = $interv_cloture == "" ? "": "none";
					}
						
					
					
					
					// On marque "EN COURS" lorsque le dossier n'est pas clos.
					$interv_cloture = $interv_cloture == "" ? "EN COURS": $interv_cloture;
											
					echo "<td> <a href='gestion_demandes/voir_dossier.php?height=480&width=640&id=$dossier' class='smoothbox' title='voir le dossier $dossier'> <img src='img/loupe.gif'>$dossier</a> </td>";
					echo "<td> $interv_id </td>";
					echo "<td> $interv_date </td>";
					echo "<td> $interv_cloture </td>";
					echo "<td> $salle_nom </td>";
					echo "<td> $mat_nom </td>";
					echo "<td> $demande_txt  </td>";

					echo "<td width=20 align=center> <a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/form_interventions.php?id=$interv_id');\" style='display:$hidemodif;'>	<img src='img/write.png' title='gérer l`intervention'>	</a> </td>";
				
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP

	// On se déconnecte de la db
	$db_gespac->disconnect();

?>
