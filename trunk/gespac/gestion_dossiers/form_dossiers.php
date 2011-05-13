<!--

	Pour la création :
	- On entre le type
	- On sélectionne les mat
	- On renseigne le pb
	- On envoie le bouzin
	
	Pour la modification :
	- 


-->


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

	$dossierid = $_GET["id"];

	$con_gespac = new Sql($host, $user, $pass, $gespac);
	


/*****************************************************************************
*
*					Formulaire CREATION d'un DOSSIER
*
*****************************************************************************/		
		
if ( $dossierid == -1 ) {


	$liste_materiels = $con_gespac->QueryAll ('Select mat_id, mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id;');
	$liste_types = $con_gespac->QueryAll ('Select DISTINCT marque_type, marque_id FROM marques GROUP BY marque_type;');
	$liste_salles = $con_gespac->QueryAll ('Select salle_nom, salle_id FROM salles;');

	echo "<h3>FORMULAIRE DE CREATION D'UN DOSSIER</h3>";
	
	/*	LA LISTE DES FILTRES */
	
	echo "<div class='dossier_section'>";
	
?>
	<!--*************************************
	*
	*		FILTRE des MATERIELS
	*
	**************************************-->

	<span>
		Nom <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'dossiers_mat_table', 1);" type="text" >
	</span>

<?PHP

	
	/*************************************
	*
	*		COMBOBOX des SALLES
	*
	**************************************/

		echo "<span>";
		echo "Salle ";
		echo "<SELECT id='CB_salles' onchange=\"filter(this.options[selectedIndex].text, 'dossiers_mat_table', 3);\">";
			echo "<option>---</option>";
			foreach ($liste_salles as $record) {
				$salle_nom = $record['salle_nom'];
				$salle_id = $record['salle_id'];
				echo "<option value=$salle_id>$salle_nom</option>";
			}
		echo "</SELECT>";
		echo "</span>";
		
	
	/*************************************
	*
	*		COMBOBOX des TYPES
	*
	**************************************/
		
		echo "<span>";
		echo "Type ";
		echo "<SELECT id='CB_types' onchange=\"filter(this.options[selectedIndex].text, 'dossiers_mat_table', 2);\">";
			echo "<option>---</option>";
			foreach ($liste_types as $record) {
				$marque_type = $record['marque_type'];
				$marque_id = $record['marque_id'];
				echo "<option value=$marque_id>$marque_type</option>";
			}
		echo "</SELECT>";
		echo "</span>";
				
	
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
	<center>
	<form action="gestion_dossiers/post_dossiers.php?action=add" method="post" name="post_form" id="post_form" >
		
		<div>
			Type : <br>
			<select id="type" name="type">
				<option value='reparation'>		REPARATION</option>
				<option value='installation'>	INSTALLATION</option>
				<option value='usage'>			USAGE</option>
				<option value='formation'>		FORMATION</option>
			</select>
		</div>

		<br>
		
		<div>
			Commentaire :<br>
			<textarea cols=90 rows=6 name='commentaire'></textarea>
		</div>


		<br>
			<span id='nb_selectionnes'></span><br><br>
			<input type='hidden' name='liste_mat' id='liste_mat'>	
			<input type='submit' value='poster la demande'>
		
	</form>
	</center>

<?PHP
}	// fin du IF de la création du dossier



/*****************************************************************************
*
*					Formulaire AJOUT PAGE au DOSSIER
*
*****************************************************************************/		

if ( $dossierid <> -1 ) {

	echo "<h3>FORMULAIRE DE MODIFICATION D'UN DOSSIER</h3>";
	
	$dossier_courant = $con_gespac->QueryRow ("SELECT * FROM dossiers WHERE dossier_id = $dossierid");
		
	$dossier_courant_type 	= $dossier_courant[1];
	$dossier_courant_mat 	= $dossier_courant[2];
	
	
	// type de dossier
	echo "<p>TYPE <b>$dossier_courant_type</b></p>";
	

	
	echo "<form action='gestion_dossiers/post_dossiers.php?action=modif' method='post' name='post_form' id='post_form' >";
	
		// Id du dossier
		echo "<input type=hidden name='dossierid' value='$dossierid'>";
		
		// Nouvel état du dossier
		echo "<select name=etat>";
			echo "<option value='precision'>Demander des précisions</option>";
			echo "<option value='intervention'>Déclencher Intervention</option>";
			echo "<option value='cloture'>Clore le dossier</option>";
		echo "</select>";
		
		echo "<br>";
		
		// Commentaire de la modification
		echo "<textarea name='commentaire' cols=90 rows=6></textarea>";
		
		echo "<br>";
		
		// Bouton pour poster le formulaire
		echo "<input type='submit' name='bt_submit'>";
	
	echo "</form>";
	
	
	
	
		
	// Liste du matériel concerné par le dossier
	echo "<p>";
	
		echo "<center><h4>MATERIELS</h4>";
	
		$arr_dossier_courant_mat = explode(";", $dossier_courant_mat);
		
		echo "<div class='dossier_section'>";
		
			echo "<table width=100%>";
				echo "<th>Matériel</th>";
				echo "<th>Type</th>";
				echo "<th>Salle</th>";
			
			foreach ($arr_dossier_courant_mat as $mat) {
				
				if ($mat <> '') {
					$mat = $con_gespac->QueryRow ("Select mat_id, mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id AND mat_id = $mat");
					
					$mat_nom = $mat[1];
					$mat_type = $mat[2];
					$mat_salle = $mat[3];
					
					echo "<tr><td>$mat_nom</td><td>$mat_type</td><td>$mat_salle</td></tr>";
				}
				
			}
			
			echo "</table>";
	
		echo "</div>";

	echo "</p>";
	
	
	
	
	// historique du dossier
 
	echo "<h4>HISTORIQUE</h4>";
	
	$page_dossier = $con_gespac->QueryAll ("SELECT txt_id, txt_date, txt_texte, txt_etat, users.user_nom FROM dossiers_textes, users WHERE dossier_id=$dossierid AND txt_user=user_id");

		echo "<table width=750px>";
			echo "<th>date</th>";
			echo "<th>utilisateur</th>";
			echo "<th>etat</th>";
		
			$compteur = 0;
			
			foreach ( $page_dossier as $page) {
				
				$txt_id 	= $page['txt_id'];
				$txt_date 	= $page['txt_date'];
				$txt_texte 	= $page['txt_texte'];
				$txt_etat 	= $page['txt_etat'];
				$user_nom 	= $page['user_nom'];
			
			
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
				
				echo "<tr class=$tr_class>";
					echo "<td width=60px>$txt_date</td>";
					echo "<td width=60px>$user_nom</td>";
					echo "<td width=60px>$txt_etat</td>";
				echo "</tr>";
				
				echo "<tr class=$tr_class>";
					echo "<td colspan=4>$txt_texte</td>";
				echo "</tr>";
				
				$compteur++;
				
			}
		echo "</table>";
		
}



?>











<script>
	
	
	  window.addEvent('domready', function() {

		// AJAX		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_dossiers/voir_dossiers.php')", 1500);
				}
			
			}).send(this.toQueryString());
		}); 
    });
	
	
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
		}
	}

</script>

