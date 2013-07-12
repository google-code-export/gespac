<?PHP
	
	// cnx à la base de données GESPAC
	$con_gespac	= new Sql($host, $user, $pass, $gespac);
	
	// Requête qui va récupérer les origines des dotations ...
	$liste_origines = $con_gespac->QueryAll ( "SELECT origine FROM origines ORDER BY origine" );
	
	// Requête qui va récupérer les états des matériels ...
	$liste_etats = $con_gespac->QueryAll ( "SELECT etat FROM etats ORDER BY etat" );
	
?>
	
	
	
<div class="entetes" id="entete-importcsv">	
	<span class="entetes-titre">IMPORT DE MATERIELS PAR CSV<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet d'importer en masse des matériels via un fichier CSV.<br>ATTENTION : Il faut UN fichier CSV par modèle !</div>
</div>

<div class="spacer"></div>
	
	
<script type="text/javascript"> 
	

	// **************************************************************** AJOUT d'un MARQUE par sa CORRESPONDANCE
	function validation_choisir_marque (corr_id, marque) {
			
		var valida = confirm('Voulez-vous vraiment choisir la marque ' + marque + ' ?');
		
		// si la réponse est TRUE ==> on colle dans un input la valeur corr_id
		if (valida) {
			$('#marque_id').val(corr_id);
			
			$('#choix_modele').hide();
			$('#table_modele_selectionne').show();
			
			$('#modele_selectionne').val(marque);
			$('#choix_fichier').show();
		}
	}
	

	// **************************************************************** FAIT REAPPARAITRE LE CHOIX DE SELECTION DE LA MARQUE
	function choisir_modele () {
		
		$('#choix_modele').show();
		$('#table_modele_selectionne').hide();
		$('#choix_fichier').hide();
		
		$('#marque_id').val("");
		$('#modele_selectionne').val("");
	}

	
		
	// **************************************************************** Fonction de filtrage des marques pour correspondance
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
		

		if ($("pasderesultat")) {
			if ($("#" + tableid + " tr:visible").length < 1 && data != "") {$("#pasderesultat").show();}
			else {$("#pasderesultat").hide();}
		}
	}
	
	
	$(function(){

		//---------------------------------------- Montre et cache l'aide
		$('#showhelp').click(function() {
			$('#help').toggle();
		});
	
		
		
		//---------------------------------------- Post file par Ajax (fonction par olanod : http://stackoverflow.com/users/931340/olanod)
		$('#post_add').click(function(){
			var formData = new FormData($('form#formulaire')[0]);
			
			$.ajax({			
				url: "gestion_inventaire/post_import_csv.php",  //server script to process data
				type: 'POST',
				xhr: function() {  // custom xhr
					var myXhr = $.ajaxSettings.xhr();
					return myXhr;
				},
				// Data du formulaire
				data: formData,
				//Options to tell JQuery not to process data or worry about content-type
				cache: false,
				contentType: false,
				processData: false,
				complete : function(res) {
					$('#targetback').show(); $('#target').show();
					$('#target').html(res.responseText);
					window.setTimeout("document.location.href='index.php?page=materiels'", 2500);
				}
			});
		});
		
		
		$('#filt').focus();
		
	});
	
</script>
		

<center>
	
	<form enctype="multipart/form-data" id="formulaire">
		
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
		
		
		<!--

		GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

		-->
		
		<div id='choix_modele' style='border:1px dotted grey;'>
		
			<center>
		
			<table class='formtable' >
				<tr>
					<td>Choisir un modèle :</td>
					<td><input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter_marque(this.value, 'marque_table_csv');" type="text"> </td>
				</tr>
			</table>

			
			<?PHP
			$liste_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_marque, marque_model, marque_type, marque_stype FROM marques ORDER BY marque_model" );
			?>
							
			<!-- s'affiche si il n'y a pas de résultat -->
			<div id="pasderesultat" style='display:none'>Pas de résultat, vous devez créer le modèle manuellement.</div>
			

			<table id="marque_table_csv" class='alternate smalltable'>
				
				<?PHP
					foreach ( $liste_marques as $marque ) {
					
						$marque_id 		= $marque['marque_id'];
						$marque_marque 	= $marque['marque_marque'];
						$marque_model 	= $marque['marque_model'];
						$marque_type 	= $marque['marque_type'];
						$marque_stype 	= $marque['marque_stype'];
					
						echo "<tr style='display:none' class='tr_filter'>";
							echo "<td width=200>$marque_type</td>";
							echo "<td width=200>$marque_stype</td>";
							echo "<td width=200>$marque_marque</td>";
							echo "<td width=200>$marque_model</td>";
							echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_model');\"><img src='img/arrow-right.png' width=16 height=16 title='Choisir ce modèle'> </a></td>";
						echo "</tr>";
					
					}
				
				?>
				
			</table>
		</div>	
		

		<table class="formtable" style='display:none' id="table_modele_selectionne">
			<tr>
				<td>Modèle sélectionné :</td>
				<td><input type=hidden name="marque_id" id="marque_id"> <input type="text" id="modele_selectionne"> </td>
				<td><a href='#' onclick="choisir_modele();">changer</a></td>
			</tr>
		 </table>


				 
		<br>	
		
		<div id='choix_fichier' style="display:none;">
		
			<!-- choix de l'origine et de l'état -->	
			
			<table class="formtable">
				<tr>
					<TD>Origine</TD> 
					<TD>
						<select name="origine">
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine['origine'] ."'>" . $origine['origine'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<TD>Etat du matériel</TD>
					<TD>
						<select name="etat">
							<?PHP	foreach ($liste_etats as $etat) {	$selected = $etat['etat'] == "Fonctionnel" ? "selected" : ""; echo "<option $selected value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			</table>
		
			
			<br>
			
			 
			<!-- Fichier à poster -->	
				 
			<table class="formtable">	 
				<tr><td>Fichier CSV</td><td><input type="file" name="myfile"></td></tr>
				<tr><td colspan=2><br><center><input type="button" name="envoyer" id="post_add" value="Envoyer le fichier"></center></td></tr>
			</table>
		</div> 
	 	  
	</form>


	<br>
	<br>


	<!-- AIDE -->

	<br><b><a href='#' id='showhelp'>formalisme du fichier CSV</a></b><br><br>

	<div id='help' style="text-align:left; width:400px;display:none;">
		<small>
				"Nom_materiel1";"no_serie1";"no_inventaire1";"adresse_mac1"<br>
				"Nom_materiel2";"no_serie2";"no_inventaire2";"adresse_mac2"<br>
				"Nom_materiel3";"no_serie3";"no_inventaire3";""<br>
				"Nom_materiel4";"no_serie4";"";""<br>
				...<br>
				...
				<br><br>
				- Le numéro d'inventaire et l'adresse MAC sont facultatifs mais doivent apparaitre<br>
				- Chaque octet de l'adresse MAC est séparée par des :
		</small>
	</div>	

</center>
