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
	
	.liste_section {
		float : left;
		width : auto;
		min-width : 65%;
		height : 300px;
		overflow : auto;
		margin : 10px;
		padding : 10px;
		border : 1px dotted black;
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
	
	$liste_materiels = $con_gespac->QueryAll ('Select mat_id, mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id;');
	$liste_types = $con_gespac->QueryAll ('Select DISTINCT marque_type, marque_id FROM marques GROUP BY marque_type;');
	$liste_salles = $con_gespac->QueryAll ('Select salle_nom, salle_id FROM salles;');


		/*	LA LISTE DES FILTRES */
		
		echo "<div class='dossier_section'>";
		
?>
		<!--*************************************
		*
		*		FILTRE des MATERIELS
		*
		**************************************-->
		
<form id="filterform">
	Nom <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'dossiers_mat_table', 1);" type="text" value=<?PHP echo $_GET['filter'];?> >
</form>

<?PHP

		
		/*************************************
		*
		*		COMBOBOX des SALLES
		*
		**************************************/


			echo "Salle ";
			echo "<SELECT id='CB_salles' onchange=\"filter(this.options[selectedIndex].text, 'dossiers_mat_table', 3);\">";
				echo "<option>---</option>";
				foreach ($liste_salles as $record) {
					$salle_nom = $record['salle_nom'];
					$salle_id = $record['salle_id'];
					echo "<option value=$salle_id>$salle_nom</option>";
				}
			echo "</SELECT>";
			
			
			echo "<br><br>";
		
		/*************************************
		*
		*		COMBOBOX des TYPES
		*
		**************************************/

			echo "Type ";
			echo "<SELECT id='CB_types' onchange=\"filter(this.options[selectedIndex].text, 'dossiers_mat_table', 2);\">";
				echo "<option>---</option>";
				foreach ($liste_types as $record) {
					$marque_type = $record['marque_type'];
					$marque_id = $record['marque_id'];
					echo "<option value=$marque_id>$marque_type</option>";
				}
			echo "</SELECT>";
			
			
			echo "<br>";
			
		echo "</div>";



		
		/*************************************
		*
		*		LISTE DE SELECTION
		*
		**************************************/


		echo "<div class='liste_section'>";
			echo "<table id='dossiers_mat_table' width=100%>";
			
			$compteur = 0;

			foreach ($liste_materiels as $record) {
				
				$mat_id	= $record['mat_id'];
				$nom 	= $record['mat_nom'];
				$type 	= $record['marque_type'];
				$salle 	= $record['salle_nom'];
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
				
				echo "<tr id=tr_id$mat_id  class=$tr_class>
				
					<td> <input type=checkbox name=chk indexed=true value='$mat_id' onclick=\"select_cette_ligne('$mat_id', $compteur); \"> </td>
					<td>$nom</td>
					<td>$type</td>
					<td>$salle</td>
				</tr>";
				
				$compteur++;
			}
			
			echo "</table>";	
			
		echo "</div>";
		
?>

<div>
	Commentaire :<br>
	<textarea cols=127 rows=6></textarea>
</div>

<form>
	<br>
	<center>
		<span id='nb_selectionnes'></span><br><br>
		<input type=hidden name='liste_mat' id='liste_mat'>	
		<input type=button value='poster la demande' id ='post_dossier'>
	</center>
</form>



<script>
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id, col){
		
		if ( phrase == "---") phrase = "";
		
		var words = phrase.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
				
		for (var r = 0; r < table.rows.length; r++){
			
			ele = table.rows[r].cells[col].innerHTML.replace(/<[^>]+>/g,"");
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
	//				selectionne une ligne du tableau pour postage
	//
	// *********************************************************************************

	function select_cette_ligne( tr_id, num_ligne, check ) {

		var chaine_id = $('liste_mat').value;
		var table_id = chaine_id.split(";");

		var nb_selectionnes = $('nb_selectionnes');
		
		var ligne = "tr_id" + tr_id;	//on récupère l'tr_id de la row
		var li = document.getElementById(ligne);
		
		if ( li.style.display == "" ) {	// si une ligne est masquée on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas déjà cochée
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]  matériel(s) dans la sélection</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;
				
				case 0: // On force la déselection
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]  matériel(s) dans la sélection</small>";	 // On entre le nombre de machines sélectionnées			
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					}
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]  matériel(s) dans la sélection</small>";	 // On entre le nombre de machines sélectionnées			

						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]  matériel(s) dans la sélection</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			$('liste_mat').value = table_id.join(";");

			if ( $('liste_mat').value != "" ) {
				$('post_dossier').style.display = "";

			} else { 
				$('post_dossier').style.display = "none";
			}
		}
	}

</script>

