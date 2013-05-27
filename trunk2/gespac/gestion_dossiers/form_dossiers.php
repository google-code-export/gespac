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

	$action = $_GET["action"];
	
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
			
	// gestion des droits particuliers (clore des dossiers, changer l'état d'un dossier ...)
	$droits_supp = ($_SESSION['grade'] == 'root') ? true : preg_match ("#L-03-04#", $_SESSION['droits']);

	$liste_etats 	 = $con_gespac->QueryAll ( "SELECT etat FROM etats ORDER BY etat" );

	//$liste_types 	 = $con_gespac->QueryAll ('Select DISTINCT marque_type, marque_id FROM marques GROUP BY marque_type;');
	//$liste_salles 	 = $con_gespac->QueryAll ('Select salle_nom, salle_id FROM salles;');
	//$liste_types 	 = $con_gespac->QueryAll ( "SELECT DISTINCT type FROM dossiers_types" );
	

/*****************************************************************************
*
*					Formulaire CREATION d'un DOSSIER
*
*****************************************************************************/		
		
if ( $action == "add" ) {


	$liste_materiels = $con_gespac->QueryAll ('Select mat_id, mat_nom, marque_type, salle_nom, mat_dsit FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id;');
	
	/*	LA LISTE DES FILTRES */
	
	echo "<center><div>";
	
?>
	<!--*************************************
	*
	*		FILTRE des MATERIELS
	*
	**************************************-->

	<span>
		Filtrer les matériels <input width=65 name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'dossiers_mat_table');" type="text" >
	</span>
	
<?PHP

	
	/*************************************
	*
	*		COMBOBOX des SALLES
	*
	**************************************/
/*
		echo "<span> Ou ";
		echo "Salle ";
		echo "<SELECT id='CB_salles' onchange=\"filter($('#CB_salles option:selected').text(), 'dossiers_mat_table');\">";
			echo "<option>---</option>";
			foreach ($liste_salles as $record) {
				$salle_nom 	= stripcslashes(utf8_encode($record['salle_nom']));
				$salle_id 	= $record['salle_id'];
				echo "<option value=$salle_id>$salle_nom</option>";
			}
		echo "</SELECT>";
		echo "</span>";
	*/	
	
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
		echo "<table id='dossiers_mat_table' class='smalltable hover alternate' width=600 >";

		foreach ($liste_materiels as $record) {
			
			$mat_id	= $record['mat_id'];
			$nom 	= $record['mat_nom'];
			$type 	= $record['marque_type'];
			$salle 	= $record['salle_nom'];
			$tag 	= $record['mat_dsit'];

			echo "<tr id='tr_id$mat_id' >
			
				<td> <input class='chk_line' id='$mat_id' type='checkbox' name='chk' indexed=true value='$mat_id'> </td>
				<td>$nom</td>
				<td>$tag</td>
				<td>$type</td>
				<td>$salle</td>
			</tr>";
			
		}
		
		echo "</table>";	
		
	echo "</div>";
	
	?>
	<center>
	<form action="gestion_dossiers/post_dossiers.php?action=add" method="post" name="post_form" id="formulaire">
		
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
			<span class='chk_span' id='change_etat' style="display:none;">
				<br>Changer l'état du matériel en : 
				<select name="etat" id="CB_etats">
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
			<input type='submit' value='Créer le dossier' id='post_form' disabled>
		
	</form>
	</center>

<?PHP
}	// fin du IF de la création du dossier



/*****************************************************************************
*
*					Formulaire MODIFICATION DU DOSSIER
*
*****************************************************************************/		

if ( $action == "mod" ) {

	$dossierid = $_GET["id"];
	
	$dossier_courant 	 = $con_gespac->QueryRow ("SELECT * FROM dossiers WHERE dossier_id = $dossierid");
	
	//Récupérer le txt_id le plus récent pour avoir le dernier état
	$dossier_courant_txt = $con_gespac->QueryRow ("SELECT * FROM dossiers_textes WHERE dossier_id = $dossierid ORDER BY txt_id DESC");
	
	$dossier_courant_type 	  = $dossier_courant[1];
	$dossier_courant_mat 	  = $dossier_courant[2];
	$dossier_courant_txt_etat = $dossier_courant_txt[5];
	
	
	// type de dossier
	echo "<p>TYPE <b>$dossier_courant_type</b></p>";
	

	
	echo "<form action='gestion_dossiers/post_dossiers.php?action=modif' method='post' name='post_form' id='formulaire'>";
	
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
		echo "<center><br><input type='submit' name='bt_submit' value='Modifier le dossier' id='post_form' disabled></center>";
	
	echo "</form>";
	
	
	
	
		
	// Liste du matériel concerné par le dossier
	echo "<br><p>";
	
		echo "<center><h4><a href='#' id='togglemateriels'>LISTE DU MATERIEL CONCERNE</a></h4>";
	
		$arr_dossier_courant_mat = explode(";", $dossier_courant_mat);
		
		echo "<div id='listemateriels' style='display:none;'>";
		
			echo "<table width=600 class='smalltable alternate'>";
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

		echo "<table width=600 class='smalltable' >";
			
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
			
				echo "<tr>";
					echo "<td class='td_$txt_etat' width='60px'>$txt_date</td>";
					echo "<td class='td_$txt_etat' width='60px'>$user_nom</td>";
					echo "<td class='td_$txt_etat' width='60px'>$txt_etat</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan=3>$txt_texte</td>";
				echo "</tr>";

			}
		echo "</table>";
		
}	// Fin if mod dossier



/*****************************************************************************
*
*					Formulaire SUPPRESSION DU DOSSIER
*
*****************************************************************************/		

if ( $action == "del" ) {
	
	$dossierid = $_GET["id"];
	
	echo "Voulez-vous vraiment supprimer le dossier <b>$dossierid</b> ? <br><br>ATTENTION, toutes les pages du dossier seront détruites !";
	
		?>	
		<center><br><br>
		<form action="gestion_dossiers/post_dossiers.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $dossierid;?>" name="id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
	<?PHP
	
}


?>




<script type="text/javascript">
	
	$(function(){
		
		//--------------------------------------- Selection d'une ligne
		$('.chk_line').click(function(){

			var id = $(this).attr('id');
			
			if ( $(this).is(':checked') ){		
				$('#liste_mat').val( $('#liste_mat').val() + ";" + id );
				$("#tr_id" + id).addClass("selected");
			}
			else {
				$('#liste_mat').val( $('#liste_mat').val().replace(";" + id + ";", ";") );	// Supprime la valeur au milieu de la chaine
				var re = new RegExp(";" + id + "$", "g"); $('#liste_mat').val( $('#liste_mat').val().replace(re, "") );			// Supprime la valeur en fin de la chaine
				$("#tr_id" + id).removeClass("selected");
				$('#checkall').prop("checked", false);
			}
			
			// On affiche les boutons
			if ( $('#liste_mat').val() != "" ) {				
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)'); $('#change_etat').show();
			} else { 
				$('#nb_selectionnes').hide(); $('#change_etat').hide();
			}
			
		});	
		
		//--------------------------------------- Sur changement d'état d'un matériel
		$('#CB_etats').change(function(){
			
			var nbmat = $('#liste_mat').val().split(';').length -1;
			var etat = $('#CB_etats option:selected').text();
			var arr = [ 'CASSE', 'VOLE', 'PANNE', 'PERDU' ];
			
			if (jQuery.inArray(etat, arr) != -1 && nbmat == 1) $('#gign').show();
			else $('#gign').hide();
			
		});
		
		
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
			
			// On désactive le bouton de post, histoire de ne pas poster 2 fois le formulaire
			$(this).prop('disabled',true);
		
			// Permet d'avoir les données à envoyer
			var dataString = $("#formulaire").serialize();
			
			// action du formulaire
			var url = $("#formulaire").attr( 'action' );
			
			var request = $.ajax({
				type: "POST",
				url: url,
				data: dataString,
				dataType: "html"
			 });
			 
			 request.done(function(msg) {
				$('#dialog').dialog('close');
				$('#targetback').show(); $('#target').show();
				$('#target').html(msg);
				window.setTimeout("document.location.href='index.php?page=dossiers&filter=" + $('#filt').val() + "'", 2500);
			 });		 
		});	
		
		
		//--------------------------------------- Fait apparaitre la partie matériels concernés dans la modification d'un dossier
		$('#togglemateriels').click(function(){
			$('#listemateriels').toggle();
		});
		
	});
	
		
	// *********************************************************************************
	//
	// 		vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	//
	// *********************************************************************************
	
	function validation () {
		var bt_submit  	= $("#post_form");
		var commentaire	= $("#commentaire").val();
		
		if (commentaire == "") bt_submit.prop('disabled', true);
		else bt_submit.prop('disabled', false);
	}
	
	function validation_modif () {

		var bt_submit  	= $("#post_form");
		var commentaire	= $("commentaire").value;
		
		if (commentaire == "") bt_submit.prop('disabled', true);
		else bt_submit.prop('disabled', false);
	}
	
	
</script>

