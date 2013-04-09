<?PHP session_start(); ?>

<!--

	Pour la création :
	- On entre le type
	- On sélectionne les mat
	- On renseigne le pb
	- On envoie le bouzin
	
	Pour la modification :
	- 


-->


<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

	$dossierid = $_GET["id"];

	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
			
	// gestion des droits particuliers (clore des dossiers, changer l'état d'un dossier ...)
	$droits_supp = ($_SESSION['grade'] == 'root') ? true : preg_match ("#L-03-04#", $_SESSION['droits']);
	

/*****************************************************************************
*
*					Formulaire CREATION d'un DOSSIER
*
*****************************************************************************/		
		
if ( $dossierid == -1 ) {


	$liste_materiels = $con_gespac->QueryAll ('Select mat_id, mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id;');
	//$liste_types 	 = $con_gespac->QueryAll ('Select DISTINCT marque_type, marque_id FROM marques GROUP BY marque_type;');
	$liste_salles 	 = $con_gespac->QueryAll ('Select salle_nom, salle_id FROM salles;');
	$liste_etats 	 = $con_gespac->QueryAll ( "SELECT etat FROM etats ORDER BY etat" );
	$liste_types 	 = $con_gespac->QueryAll ( "SELECT type FROM dossiers_types" );

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
	
	Ou

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
				$salle_nom 	= stripcslashes(utf8_encode($record['salle_nom']));
				$salle_id 	= $record['salle_id'];
				echo "<option value=$salle_id>$salle_nom</option>";
			}
		echo "</SELECT>";
		echo "</span>";
		
	
	/*************************************
	*
	*		COMBOBOX des TYPES
	*
	**************************************/
		/*
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
		*/		
	
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
			
				<td> <input class=chkbx type=checkbox name=chk indexed=true value='$mat_id' onclick=\"select_cette_ligne('$mat_id', $compteur); \"> </td>
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
	<form action="gestion_dossiers/post_dossiers.php?action=add" method="post" name="post_form" id="post_form" onsubmit="$('post_dossier').disabled=true;">
		
		<div>
			Type :
				<select id="type" name="type">
					<?PHP 	foreach ($liste_types as $type) { echo "<option value='" . $type['type'] ."'>" . $type['type'] ."</option>"; } ?>
				</select>
				
		</div>

		<br>
		
		<div>
			Commentaire :<br>
			<textarea cols=90 rows=6 name='commentaire' id='commentaire' onkeyup="validation();"></textarea>
		</div>
		
		<?PHP
			if ( $droits_supp ) {
		?>
		<div>

			<span class='chk_span'><label for='add_inter'>Intervention Directe <label><input type='checkbox' name='add_inter'></span>
			<span class='chk_span'><label for='active_mailing'>Activer le Mailing <label><input type='checkbox' name='active_mailing' checked ></span>
			<span class='chk_span' id='mat_hs_label' style='display:none;'><label for='mat_hs'>Changer l'état du matériel <label><input type='checkbox' name='mat_hs' id='mat_hs'></span>
			<span class='chk_span'>
				<select name="etat" id="CB_etats" style="display:none;">
					<option selected><?PHP echo $materiel_etat; ?></option>
					<?PHP	foreach ($liste_etats as $etat) {	echo "<option value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
				</select>
			</span>
			
			<span class='chk_span' id ="gign" style="display:none;">GIGN : <input id='gign_txt' type="text" size=6 name="gign"></span>
	
		</div>
		
		<?PHP } else { // si pas de droits supplémentaires, on dessine l'active mailing mais on le cache ?>
			
		<div>
			<span class='chk_span'><label for='active_mailing' style="display:none;">Activer le Mailing <label><input type='checkbox' name='active_mailing' checked style="display:none;"></span>
		</div>
		<?PHP } // End if ($droits_supp) ?>


		<br>
			<span id='nb_selectionnes'></span><br><br>
			<input type='hidden' name='liste_mat' id='liste_mat'>	
			<input type='submit' value='Créer le dossier' id='post_dossier' disabled>
		
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
	
	$dossier_courant 	 = $con_gespac->QueryRow ("SELECT * FROM dossiers WHERE dossier_id = $dossierid");
	
	//Récupérer le txt_id le plus récent pour avoir le dernier état
	$dossier_courant_txt = $con_gespac->QueryRow ("SELECT * FROM dossiers_textes WHERE dossier_id = $dossierid ORDER BY txt_id DESC");
	
	$dossier_courant_type 	  = $dossier_courant[1];
	$dossier_courant_mat 	  = $dossier_courant[2];
	$dossier_courant_txt_etat = $dossier_courant_txt[5];
	
	
	// type de dossier
	echo "<p>TYPE <b>$dossier_courant_type</b></p>";
	

	
	echo "<form action='gestion_dossiers/post_dossiers.php?action=modif' method='post' name='post_form' id='post_form' onsubmit=$('post_modif').disabled=true>";
	
		// Id du dossier
		echo "<input type=hidden name='dossierid' value='$dossierid'>";
		
		// Nouvel état du dossier
		echo "<select name=etat>";
			echo "<option value='precisions'>Précisions sur le dossier</option>";
			
			if ($droits_supp) {
				if ($dossier_courant_txt_etat <> 'intervention') {
					echo "<option value='intervention'>Déclencher Intervention</option>";
				}
				echo "<option value='clos'>Clore le dossier</option>";
			}
		echo "</select>";
		
		echo "<br>";
		
		// Commentaire de la modification
		echo "<textarea name='commentaire' id='commentaire' cols=90 rows=6 onkeyup=validation_modif();></textarea>";
		
		echo "<br>";
		
		// Bouton pour poster le formulaire
		echo "<input type='submit' name='bt_submit' value='Modifier le dossier' id='post_modif' disabled>";
	
	echo "</form>";
	
	
	
	
		
	// Liste du matériel concerné par le dossier
	echo "<p>";
	
		echo "<center><h4><a href='#' onclick='toggleMateriels();'>LISTE DU MATERIEL CONCERNE</a></h4>";
	
		$arr_dossier_courant_mat = explode(";", $dossier_courant_mat);
		
		echo "<div class='dossier_section' id='materiels' style='display:none;'>";
		
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
			echo "<th>Utilisateur</th>";
			echo "<th>etat</th>";
		
			$compteur = 0;
			
			foreach ( $page_dossier as $page) {
				
				$txt_id 	= $page['txt_id'];
				$txt_date 	= $page['txt_date'];
				$txt_texte 	= $page['txt_texte'];
				$txt_etat 	= $page['txt_etat'];
				
				if (strtoupper($_SESSION['grade']) == 'ATI' || strtoupper($_SESSION['grade']) == 'ROOT') {
					$user_nom 	= $page['user_nom'];
				} else {
					$user_nom 	= 'Anonyme';
				}
			
			
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
				
				echo "<tr class=$tr_class>";
					echo "<td width=60px>$txt_date</td>";
					echo "<td width='60px'>$user_nom</td>";
					echo "<td width=60px>$txt_etat</td>";
				echo "</tr>";
				
				echo "<tr class=$tr_class>";
					echo "<td colspan=3>$txt_texte</td>";
				echo "</tr>";
				
				$compteur++;
				
			}
		echo "</table>";
		
}


?>




<script type="text/javascript">
	
	
	  window.addEvent('domready', function() {

		// AJAX		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					SexyLightbox.close();
					window.setTimeout("document.location.href='index.php?page=dossiers'", 1500);					
				}
			
			}).send(this.toQueryString());
		}); 
		
		
		$('mat_hs').addEvent('change', function(e) {
					
			if ( $('mat_hs').checked) {
				$('CB_etats').style.display = "";
			} 
			else {
				$('CB_etats').style.display = "none";
				$('gign').style.display = "none";
			}
			
		});
		
		$('CB_etats').addEvent('change', function(e) {
				new Event(e).stop();
				
				var mystr = $('liste_mat').value;
				
				// On vérifie si l'état est cassé, volé ... et surtout si on a seulement un matériel sélectionné pour gign
				if( this.value in {'CASSE':'', 'VOLE':'','PANNE':'','PERDU':''} && mystr.split(';').length == 2) {	$('gign').style.display = ""; }
				else { $('gign').style.display = "none";	}
		});
		
		
		$$('.chkbx').addEvent('click', function(e) {
			var mystr = $('liste_mat').value;
			
			if (mystr.split(';').length > 1) {
				$('mat_hs_label').style.display = "";
				
				// On vire le gign si il y a 0 ou plus de 1 matériel sélectionné
				if ( mystr.split(';').length > 2) {
					$('gign_txt').value = "";
					$('gign').style.display = "none";
				}
								
			}
			else {
				$('mat_hs').checked = false;
				$('gign_txt').value = "";
				$('CB_etats').value = "";
				
				$('mat_hs_label').style.display = "none";
				$('CB_etats').style.display = "none";
				$('gign').style.display = "none";
			}
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
	
	// *********************************************************************************
	//
	//	Show / Hide la partie MATERIELS dans le formulaire de modification des dossiers
	//
	// *********************************************************************************

	function toggleMateriels () {
		
		if ( $('materiels').style.display == "none" ) $('materiels').style.display = "";
		else $('materiels').style.display = "none";
		
	}
	
	
	// *********************************************************************************
	//
	// 		vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	//
	// *********************************************************************************
	
	function validation () {

		var bt_submit  	= $("post_dossier");
		var commentaire	= $("commentaire").value;
		
		if (commentaire == "") {
				bt_submit.disabled = true;
			} else {
				bt_submit.disabled = false;
		}
	}
	
	function validation_modif () {

		var bt_submit  	= $("post_modif");
		var commentaire	= $("commentaire").value;
		
		if (commentaire == "") {
				bt_submit.disabled = true;
			} else {
				bt_submit.disabled = false;
		}
	}
	
	
</script>

