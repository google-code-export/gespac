<?PHP

	#formulaire d'ajout et de modification
	#des marques

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');		

	
	$action = $_GET['action'];

	
	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);

	
	// *********************************************************************************
	//
	//			Formulaire vierge de création
	//
	// *********************************************************************************
	
	
	if ( $action == 'add' ) {
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#filt').focus();
		</script>

		
		<!--
		
			GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE
		
		-->
		<div id='creer_modele_par_corr'>
			<form>
				<center>
			
				<p>Choisir un modèle : <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter_marque(this.value, 'corr_table');" type="text"><span id="marquescount" title="Nombre de lignes filtrées"></span></p>
				
				
				<?PHP
				// ici il faut récupérer les lignes DISTINCTES histoire de ne pas surcharger le tableau
				$liste_correspondances = $con_gespac->QueryAll ( "SELECT corr_id, corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances GROUP BY corr_modele ORDER BY corr_modele" );
				?>
				
				<table id="corr_table" class='alternate smalltable'>

					<?PHP
						foreach ( $liste_correspondances as $corr ) {
						
							$corr_id 			= $corr['corr_id'];
							$corr_marque_ocs 	= $corr['corr_marque_ocs'];
							$corr_type 			= $corr['corr_type'];
							$corr_stype 		= $corr['corr_stype'];
							$corr_marque 		= $corr['corr_marque'];
							$corr_modele 		= $corr['corr_modele'];
						
							echo "<tr style='display:none;' class='tr_filter'>";
								echo "<td width=200>&nbsp $corr_type</td>";
								echo "<td width=200>&nbsp $corr_stype</td>";
								echo "<td width=200>&nbsp $corr_marque</td>";
								echo "<td width=200>&nbsp $corr_modele</td>";
								echo "<td><a href='#' onclick=\"validation_ajout_marque($corr_id, '$corr_marque $corr_modele');\"><img src='img/add.png'> </a></td>";
							echo "</tr>";
						
						}
					
					?>
					
				</table>

				<br>
				<span id='creer_modele' style='display:none;'><a href='#' onclick="affiche_creer_modele();">Créer un nouveau modèle</a></span>
				</center>

			</FORM>
		</div>
		
		<!--
		
			GESTION MANUELLE DE L'INSERTION D'UNE MARQUE
		
		-->
		<div id='creer_nouveau_modele' style='display:none'>
			<form action="gestion_inventaire/post_marques.php?action=add" method="post" name="post_form" id="formulaire">

				<center>
				<table width=500>
								
					<tr>
						<TD>Famille</TD>
						<TD>
							<div id="combo_type">
								<div id="listbox_type" style='display:inline;'>
									<select name=select_type id=select_type>
										<option value=''> >>> Selectionnez une valeur <<< </option>
										<?PHP
											$liste_des_types = $con_gespac->queryAll ( "SELECT DISTINCT marque_type FROM marques ORDER BY marque_type" );
											foreach ( $liste_des_types as $record ) {	// on remplit la liste des types
												$type = $record['marque_type'];
												echo "<option value='$type'>$type</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_type" style='display:none;'><input name=text_type id=text_type type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_type', 'textbox_type', 'select_type', 'text_type');"> <img src='./img/add.png'> </a> </td>
							</div>
						</TD>
					</tr>
					
					<tr>
						<TD>Sous-famille</TD>
						<TD>
							<div id="combo_stype">
								<div id="listbox_stype" style='display:inline;'>
									<select name=select_stype id=select_stype>
										<option value=''> >>> Selectionnez une valeur <<< </option>
										<?PHP
											$liste_des_stypes = $con_gespac->queryAll ( "SELECT DISTINCT marque_stype FROM marques ORDER BY marque_stype" );
											foreach ( $liste_des_stypes as $record ) {	// on remplit la liste des types
												$stype = $record['marque_stype'];
												echo "<option value='$stype'>$stype</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_stype" style='display:none;'><input name=text_stype id=text_stype type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_stype', 'textbox_stype', 'select_stype', 'text_stype');"> <img src='./img/add.png'> </a> </td>
							</div>
						</TD>
					</tr>
					
					<tr>
						<TD>Marque</TD>
						<TD>
							<div id="combo_marque">
								<div id="listbox_marque" style='display:inline;'>
									<select name=select_marque id=select_marque>
										<option value=''> >>> Selectionnez une valeur <<< </option>
										<?PHP
											$liste_des_marques = $con_gespac->queryAll ( "SELECT DISTINCT marque_marque FROM marques ORDER BY marque_marque" );
											foreach ( $liste_des_marques as $record ) {	// on remplit la liste des types
												$marque = $record['marque_marque'];
												echo "<option value='$marque'>$marque</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_marque" style='display:none;'><input name=text_marque id=text_marque type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_marque', 'textbox_marque', 'select_marque', 'text_marque');"> <img src='./img/add.png'> </a> </td>
							</div>					
						</TD>
					</tr>
					
					<tr>
						<TD>Modèle</TD>
						<TD>
							<div id="combo_modele">
								<div id="listbox_modele" style='display:inline;'>
									<select name=select_modele id=select_modele>
										<option value=''> >>> Selectionnez une valeur <<< </option>
										<?PHP	
											$liste_des_modeles = $con_gespac->queryAll ( "SELECT DISTINCT marque_model FROM marques ORDER BY marque_model" );
											foreach ( $liste_des_modeles as $record ) {	// on remplit la liste des types
												$modele = $record['marque_model'];
												echo "<option value='$modele'>$modele</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_modele" style='display:none;'><input name=text_modele id=text_modele type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_modele', 'textbox_modele', 'select_modele', 'text_modele');"> <img src='./img/add.png'> </a> </td>
							</div>						
						</TD>
					</tr>

				</table>

				<br>
				<input type=submit value='Ajouter une marque'>
				
				<br><br>
				<a href='#' onclick="affiche_liste_modele();">Liste des modèles</a>

				</center>

			</FORM>
		<div>		
				

		<?PHP
	} 
	
	
	// *********************************************************************************
	//
	//			formulaire de modification prérempli
	//
	// *********************************************************************************
	
	
	if ($action == "mod") {
	
		$id = $_GET['id'];
	
		echo "<h2>Formulaire de modification d'une marque</h2><br>";
		

		// Requete pour récupérer les données des champs pour la marque à modifier
		$marque_a_modifier = $con_gespac->queryRow ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques WHERE marque_id=$id" );

		// valeur à affecter aux champs
		$marque_id 		= $marque_a_modifier[0];
		$marque_type 	= $marque_a_modifier[1];
		$marque_stype 	= $marque_a_modifier[2];
		$marque_marque 	= $marque_a_modifier[3];
		$marque_modele	= $marque_a_modifier[4];
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#select_type').focus();
		</script>

		
		<!--
		
			GESTION PAR CORRESPONDANCE DE LA MODIFICATION D'UNE MARQUE
		
		-->
		<DIV id='modif_modele_par_corr' >
			<form>
				<center>
			
				<p>Choisir un modèle : <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'corr_table');" type="text"></p>
				
				
				<?PHP
				// ici il faut récupérer les lignes DISTINCTES histoire de ne pas surcharger le tableau
				$liste_correspondances = $con_gespac->queryAll ( "SELECT corr_id, corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances GROUP BY corr_modele ORDER BY corr_modele" );
				?>
				
				<table id="corr_table" class='alternate smalltable'>

					<?PHP
						foreach ( $liste_correspondances as $corr ) {
						
							$corr_id 			= $corr['corr_id'];
							$corr_marque_ocs 	= $corr['corr_marque_ocs'];
							$corr_type 			= $corr['corr_type'];
							$corr_stype 		= $corr['corr_stype'];
							$corr_marque 		= $corr['corr_marque'];
							$corr_modele 		= $corr['corr_modele'];
						
							echo "<tr style='display:none' class='tr_filter'>";
								echo "<td width=200>$corr_type</td>";
								echo "<td width=200>$corr_stype</td>";
								echo "<td width=200>$corr_marque</td>";
								echo "<td width=200>$corr_modele</td>";
								echo "<td><a href='#' onclick=\"validation_modif_marque($corr_id, '$corr_marque $corr_modele', '$marque_marque $marque_modele', $marque_id);\"><img src='img/write.png'> </a></td>";
							echo "</tr>";
						
						}
					
					?>
					
				</table>

				<br>
				<span id='modif_modele' style='display:none;'><a href='#' onclick="affiche_modif_modele();">Modification manuelle du modèle</a></span>
				</center>

			</FORM>
		</DIV>
		
		
		
		
		<!--
		
			GESTION MANUELLE DE LA MODIFICATION D'UNE MARQUE
		
		-->
		<DIV id='modif_manuelle_modele' style='display:none'>
			
			<form action="gestion_inventaire/post_marques.php?action=mod" method="post" name="post_form" id="post_form">

				<input type=hidden name=marqueid value=<?PHP echo $id;?> >
				<center>
				<table width=500>
								
					<tr>
						<TD>Famille</TD>
						<TD>
							<div id="combo_type">
								<div id="listbox_type" style='display:inline;'>
									<select name=select_type id=select_type>
										<?PHP
											$liste_des_types = $con_gespac->queryAll ( "SELECT DISTINCT marque_type FROM marques" );
											foreach ( $liste_des_types as $record ) {	// on remplit la liste des types
												$type = $record['marque_type'];
												$selected = $marque_type == $type ? "selected" : "";
												echo "<option $selected value='$type'>$type</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_type" style='display:none;'><input name=text_type id=text_type type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_type', 'textbox_type', 'select_type', 'text_type');"> <img src='./img/add.png'> </a> </td>
							</div>
						</TD>
					</tr>
					
					<tr>
						<TD>Sous-famille</TD>
						<TD>
							<div id="combo_stype">
								<div id="listbox_stype" style='display:inline;'>
									<select name=select_stype id=select_stype>
										<?PHP
											$liste_des_stypes = $con_gespac->queryAll ( "SELECT DISTINCT marque_stype FROM marques" );
											foreach ( $liste_des_stypes as $record ) {	// on remplit la liste des types
												$stype = $record['marque_stype'];
												$selected = $marque_stype == $stype ? "selected" : "";
												echo "<option $selected value='$stype'>$stype</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_stype" style='display:none;'><input name=text_stype id=text_stype type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_stype', 'textbox_stype', 'select_stype', 'text_stype');"> <img src='./img/add.png'> </a> </td>
							</div>
						</TD>
					</tr>
					
					<tr>
						<TD>Marque</TD>
						<TD>
							<div id="combo_marque">
								<div id="listbox_marque" style='display:inline;'>
									<select name=select_marque id=select_marque>
										<?PHP
											$liste_des_marques = $con_gespac->queryAll ( "SELECT DISTINCT marque_marque FROM marques" );
											foreach ( $liste_des_marques as $record ) {	// on remplit la liste des types
												$marque = $record['marque_marque'];
												$selected = $marque_marque == $marque ? "selected" : "";
												echo "<option $selected value='$marque'>$marque</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_marque" style='display:none;'><input name=text_marque id=text_marque type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_marque', 'textbox_marque', 'select_marque', 'text_marque');"> <img src='./img/add.png'> </a> </td>
							</div>					
						</TD>
					</tr>
					
					<tr>
						<TD>Modèle</TD>
						<TD>
							<div id="combo_modele">
								<div id="listbox_modele" style='display:inline;'>
									<select name=select_modele id=select_modele>
										<?PHP
											$liste_des_modeles = $con_gespac->queryAll ( "SELECT DISTINCT marque_model FROM marques" );
											foreach ( $liste_des_modeles as $record ) {	// on remplit la liste des types
												$modele = $record['marque_model'];
												$selected = $marque_modele == $modele ? "selected" : "";
												echo "<option $selected value='$modele'>$modele</option>";
											}	
										?>
									</select>
								</div>
								<div id="textbox_modele" style='display:none;'><input name=text_modele id=text_modele type="text"></div>
								<td><a href="#" onclick="change_combo('listbox_modele', 'textbox_modele', 'select_modele', 'text_modele');"> <img src='./img/add.png'> </a> </td>
							</div>						
						</TD>
					</tr>

				</table>

				<br>
				<input type=submit value='Modifier cette marque' >

				</center>

			</FORM>
		<DIV>
				
<?PHP

	}	
?>


<script type="text/javascript"> 
	
	// masque le combo pour afficher le input et vis-versa
	function change_combo(select_tr_id, input_tr_id, select_id, input_id) {
		
		var inputbox = document.getElementById (input_tr_id);
		var list = document.getElementById (select_tr_id);
		
		var inputvalue = document.getElementById (input_id);
		var selectvalue = document.getElementById (select_id);
		

		if (inputbox.style.display == "inline") {
			inputvalue.value = "";
			inputbox.style.display = 'none';
			list.style.display = 'inline';		
			
		} else {
			selectvalue.value = '';
			inputbox.style.display = 'inline';
			list.style.display = 'none';
		}
	}
	
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des marques pour correspondance
	//
	// *********************************************************************************


	function filter_marque (phrase, tableid){
		
		var data = phrase.split(" ");
		var cells=$("#" + tableid + " td");
					
		if(data != "") {
			// On cache toutes les lignes
			cells.parent("tr").hide();
			// puis on filtre pour n'afficher que celles qui répondent au critère du filtre
			cells.filter(function() {
				return $(this).text().toLowerCase().indexOf(data) > -1;
			}).parent("tr").show();		
		} else {
			// On montre toutes les lignes
			cells.parent("tr").hide();
		}
		
		$("#marquescount").html( $("#" + tableid + " tr:visible").length );
		
		if ($("creer_modele")) {
			if ($("#" + tableid + " tr:visible").length < 1 && data != "") {$("#creer_modele").show();}
			else {$("#creer_modele").hide();}
		}
		
		if ($("modif_modele")) {
			if ($("#" + tableid + " tr:visible").length < 1 && data != "") {$("#modif_modele").show();}
			else {$("#modif_modele").hide();}
		}
		
	}

	
	
	// *********************************************************************************
	//
	//		PERMET DE PASSER A LA CREATION MANUELLE D'une MARQUE
	//
	// *********************************************************************************
	
	function affiche_creer_modele() {
		$('#creer_nouveau_modele').show();
		$('#creer_modele_par_corr').hide();
		$('#creer_modele').hide();
	}
	
	
	// *********************************************************************************
	//
	//		PERMET DE PASSER A LA LISTE des MODèLES
	//
	// *********************************************************************************
	
	function affiche_liste_modele() {
		$('#creer_modele_par_corr').show();
		$('#creer_nouveau_modele').hide();
	}
	
	
	// *********************************************************************************
	//
	//			AJOUT d'un MARQUE par sa CORRESPONDANCE
	//
	// *********************************************************************************
	
	function validation_ajout_marque (corr_id, marque) {
			
		var valida = confirm('Voulez-vous vraiment ajouter la marque ' + marque + ' ?');
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
			$('#dialog').dialog('close');
			$('#targetback').show(); $('#target').show();
			$('#target').load("gestion_inventaire/post_marques.php?action=add_corr&corr_id=" + corr_id);
			window.setTimeout("document.location.href='index.php?page=marques&filter=" + $('#filt').val() + "'", 1500);			
		}
	}	
		
	
		
	// *********************************************************************************
	//
	//			MODIF d'une MARQUE par sa CORRESPONDANCE
	//
	// *********************************************************************************
	
	function validation_modif_marque (corr_id, marque, oldmarque, marque_id) {
			
		var valida = confirm('Voulez-vous vraiment modifier la marque ' + oldmarque + ' par la marque ' + marque + ' ?');
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("gestion_inventaire/post_marques.php?action=modif_corr&corr_id=" + corr_id + "&marque_id=" + marque_id);
			SexyLightbox.close();
			window.setTimeout("document.location.href='index.php?page=marques&filter=" + $('filt').value + "'", 1500);
		}
	}
	
	
	// *********************************************************************************
	//
	//		PERMET DE PASSER A LA MODIFICATION MANUELLE D'une MARQUE
	//
	// *********************************************************************************
	
	function affiche_modif_modele() {
		$('modif_manuelle_modele').style.display = "";
		$('modif_modele_par_corr').style.display = "none";
		$('modif_modele').style.display = "none";
	}
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/

	
	$(function() {	
	
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
		
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
				window.setTimeout("document.location.href='index.php?page=salles&filter=" + $('#filt').val() + "'", 1500);
			 });
			 
		});	
	});
	
/*	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					SexyLightbox.close();
					window.setTimeout("document.location.href='index.php?page=marques&filter=" + $('filt').value + "'", 1500);
				}
			
			}).send(this.toQueryString());
		});			
	});
	
	*/
</script>
