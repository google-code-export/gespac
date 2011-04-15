<!--

	Pour la création :
	- On entre le type
	- On sélectionne les mat
	- On ajoute la sélection à la liste
	- On renseigne le pb
	- On envoie le bouzin
	
	Pour la modification :
	- 


-->

<script>
	
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
	
	
	
</script>
<style>
	
	.dossier_section {
		float : left;
		width : auto;
		min-width : 25%;
		height : 300px;
		overflow : auto;
		margin : 10px;
		padding : 10px;
		border : 1px dotted black;
	}
	
	.dossier_section table {
		width : 100%;
	}
	
	#encart {
		float:left;
		border : 1px dotted black;
		width:90%;
	}
	
</style>


<form>
	
	TYPE : 
	<select id="type" name="type">
		<option value='reparation'>		REPARATION</option>
		<option value='installation'>	INSTALLATION</option>
		<option value='usage'>			USAGE</option>
		<option value='formation'>		FORMATION</option>
	</select>
	
	

	
</form>


<?PHP
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_materiels = $con_gespac->QueryAll ('Select mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id;');
	$liste_types = $con_gespac->QueryAll ('Select DISTINCT marque_type FROM marques;');
	$liste_salles = $con_gespac->QueryAll ('Select salle_nom FROM salles;');


		/*	LA LISTE DES FILTRES */
		
		echo "<div class='dossier_section'>";
		
?>
			<!-- 	bouton pour le filtrage du tableau	-->
<form id="filterform">
	<center><small>Filtrer :</small> <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'dossiers_mat_table');" type="text" value=<?PHP echo $_GET['filter'];?> ></center>
</form>

<?PHP
			echo "Salle : COMBOBOX<br>";
			echo "Type : COMBOBOX<br>";
			
		echo "</div>";



		/*	LA LISTE DE SELECTION */

		echo "<div class='dossier_section'>";
			echo "<table id='dossiers_mat_table'>";

			foreach ($liste_materiels as $record) {
				
				$nom = $record['mat_nom'];
				$type = $record['marque_type'];
				$salle = $record['salle_nom'];
				
				echo "<tr>
				
					<td>$nom</td>
					<td>$type</td>
					<td>$salle</td>
					<td><img src='img/add.png'></td>
				</tr>";
				
			}
			
			echo "</table>";
		echo "</div>";
		
		
		
		/*	LA LISTE DES MATOS SELECTIONNES */
		
		echo "<div class='dossier_section'>";
			echo "liste : LISTBOX";
		echo "</div>";
		



?>
