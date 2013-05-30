<?PHP

	/*
		formulaire d'ajout et de modification des materiels !
		permet de créer un nouveau matos,
		de modifier un matos particulier
		de modifier par lot des matériels
	*/
	
	
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

?>


<script type="text/javascript"> 
	

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
	
	
		
	// **************************************************************** AJOUT d'un MARQUE par sa CORRESPONDANCE
	function validation_choisir_marque (marque_id, marque) {
			
		var valida = confirm('Voulez-vous vraiment choisir la marque ' + marque + ' ?');
		
		// si la réponse est TRUE ==> on colle dans un input la valeur corr_id
		if (valida) {
			$('#marque_id').val(marque_id);
			$('#choix_modele').hide();
			$('#table_modele_selectionne').show();
			$('#proprietes').show();
			$('#modele_selectionne').val(marque);
		}
	}
	
	

	// **************************************************************** FAIT REAPPARAITRE LE CHOIX DE SELECTION DE LA MARQUE
	function choisir_modele () {
		
		$('#choix_modele').show();
		$('#table_modele_selectionne').hide();
		$('#proprietes').hide();
		$('#marque_id').val("");
		$('#modele_selectionne').val("");
	}
	
	

	// **************************************************************** FAIT REAPPARAITRE LE MODELE DU MATERIEL
	function annuler_choix_modele (marqueid, modele) {
		
		$('#choix_modele').hide();
		$('#table_modele_selectionne').show();
		$('#proprietes').show();
		
		$('#marque_id').val(marqueid);
		$('#modele_selectionne').val(modele);
	}	
	


	// **************************************************************** masque le combo pour afficher le input et vis-versa
	function change_combo_mac() {
		
		if ($("#mac_input").is(':visible')) {
		
			$("#mac_input").hide();
			$("#textbox_type").hide();
			
			// On vide le champ inputbox
			$("#mac_input").val("");
			
			$("#textbox_type").hide();
			
			// On affiche chaque ligne contenant un radio button
			$(".combo_type").show();
			
			// On change l'intitulé du message à côté du +
			$('#change_mac').html("Adresse MAC manuelle");
				
		} else {
			
			$("#textbox_type").show();
			$("#mac_input").show();
			
			// On masque chaque ligne contenant un radio button
			$(".combo_type").hide();
			
			// On unckeck tous les radio buttons
			$(".mac_radio").prop("checked", false);
			
			// On change l'intitulé du message à côté du +
			$('#change_mac').html ("Choix des adresses MAC");
		}
		
	}
	
	

	// **************************************************************** Générateur de ssn aléatoire
	function SSNgenerator () {
		var number = Math.floor(Math.random() * 100000);
		$('#serial').val("NC" + number);
	}
	


	// **************************************************************** Activer le changement du SSN
	function SSN_modifier () {
		
		if ( $('#serial').prop("readonly") == true ) {
			$('#serial').prop("readonly",false);
			$('#img_cadenas_ferme').show();	$('#img_cadenas_ouvert').hide();
		} else {
			$('#serial').prop("readonly",true);
			$('#img_cadenas_ferme').hide();	$('#img_cadenas_ouvert').show();
		}
	}
	


	// **************************************************************** POST AJAX FORMULAIRES
	$("#post_form").click(function(event) {

		/* stop form from submitting normally */
		event.preventDefault(); 

		if ( validForm() == true) {
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
				window.setTimeout("document.location.href='index.php?page=materiels&filter=" + $('#filt').val() + "'", 2000);
			 });
		}	 
	});	

</script>

<?PHP
	
	// action à executer
	$action	 = $_GET['action'];
	
	// Connexion à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );
	
	// Requête qui va récupérer les origines des dotations ...
	$liste_origines = $con_gespac->QueryAll ( "SELECT DISTINCT origine FROM origines ORDER BY origine" );
	
	// Requête qui va récupérer les états des matériels ...
	$liste_etats = $con_gespac->QueryAll ( "SELECT DISTINCT etat FROM etats ORDER BY etat" );
	

	
	// *********************************************************************************
	//
	//			@@Formulaire vierge de création
	//
	// *********************************************************************************	
	

	if ( $action == 'add' ) {

	?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#filt').focus();
		</script>
		
		<form action="gestion_inventaire/post_materiels.php?action=add" method="post" name="post_form" id="formulaire">
			
				<!--

				GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

				-->
					
				<div id='choix_modele'>
				
					<center>
				
					Choisir un modèle * : <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter_marque(this.value, 'corr_table');" type="text"> </input>
				
					<br><br>
					
					<?PHP
					// ici il faut récupérer les lignes DISTINCTES histoire de ne pas surcharger le tableau
					$liste_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques ORDER BY marque_model" );
					?>
					
					<!-- s'affiche si il n'y a pas de résultat -->
					<div id="pasderesultat" style='display:none; color:red;'>Pas de résultat, vous devez d'abord créer le modèle manuellement.</div>
					
					<table id="corr_table" class='alternate smalltable'>

						<?PHP
							foreach ( $liste_marques as $marque) {
							
								$marque_id 			= $marque['marque_id'];
								$marque_type 		= $marque['marque_type'];
								$marque_stype 		= $marque['marque_stype'];
								$marque_marque 		= $marque['marque_marque'];
								$marque_modele 		= $marque['marque_model'];
							
								echo "<tr style='display:none' class='tr_filter'>";
									echo "<td width=200>$marque_type</td>";
									echo "<td width=200>$marque_stype</td>";
									echo "<td width=200>$marque_marque</td>";
									echo "<td width=200>$marque_modele</td>";
									echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_modele');\"><img src='./img/arrow-right.png' width=16 height=16 title='Choisir ce modèle'> </a></td>";
								echo "</tr>";
							
							}
						
						?>
						
					</table>
				</div>	
				
				<table width="500" align="center" cellpadding="10" style='display:none;' id="table_modele_selectionne">
					<tr>
						<td>Modèle sélectionné *</td>
						<td><input type=hidden name=marque_id id=marque_id> <input type="text" id="modele_selectionne"> </td>
						<td><a href='#' onclick="choisir_modele();">changer</a></td>
					</tr>
				 </table>
				<br>
				
				<center>
					
				<table width=500 style='text-align:left;display:none;' id='proprietes'>
				
				<tr>
					<TD>Nom du materiel *</TD>
					<TD><input type=text id=nom name=nom required class="valid"></TD>
				</tr>
				
				<tr>
					<TD>Référence DSIT</TD>
					<TD><input type=text id=dsit name=dsit 	/></TD>
				</tr>
				
				<tr>
					<TD>Numéro de série *</TD> 
					<TD><input required type=text id=serial name=serial  class="valid"> <input type=button value="générer" onclick="SSNgenerator();"></TD>
				</tr>
				
				<tr>
					<TD>Adresse MAC</TD> 
					<TD><input type=text id=mac name=mac size=17 maxlength=17 /></TD>
				</tr>
				
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
				
			
				<tr>
					<TD>Salle où se trouve le matériel</TD>
					<TD>
						<select name="salle" >
							<?PHP
								// requête qui va afficher dans le menu déroulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $con_gespac->QueryAll ( "SELECT DISTINCT salle_nom FROM salles" );	// [AMELIORATION] DISTINCT ? PK DISTINCT ?
								foreach ( $req_salles_disponibles as $record) { 
								
									$salle_nom = $record['salle_nom'];
									
									// Salle par défaut : STOCK
									$selected = $salle_nom == "STOCK" ? " selected" : "";
									
								?>
									<option <?PHP echo $selected ?> value="<?PHP echo $salle_nom ?>"><?PHP echo $salle_nom ?></option>
							<?PHP
								}
							?>
						</select>
					</TD>
				</tr>
				<tr>
					<td colspan=2><br><center><input type=submit value='Ajouter un materiel' id="post_form"></center></td>
				</tr>
			</table>

			<br>
			

			</center>

		</FORM>
				

		<?PHP
		
	} 
	
	


	// *********************************************************************************
	//
	//			@@Formulaire de modification de la sélection
	//
	// *********************************************************************************		
		
	
	if ($action == 'modlot') {
			
		?>

		<form action="gestion_inventaire/post_materiels.php?action=modlot" method="post" name="post_form" id="formulaire">
			<center>
			
			<input type=hidden name=lot id=lot>
			<!-- Ici on récupère la valeur du champ materiels_a_poster de la page voir_materiels_table.php -->
			<script>$("#lot").val( $('#materiel_a_poster').val() );</script>

			<table>
				
				<tr>
					<TD>Origine</TD> 
					<TD>
						<select name="origine" id="origine">
							<option value="">Ne pas modifier</option>
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine['origine'] ."'>" . $origine['origine'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<TD>Etat du matériel</TD>
					<TD>
						<select name="etat">
							<option value="">Ne pas modifier</option>
							<?PHP	foreach ($liste_etats as $etat) {	echo "<option value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			
				<tr>
					<TD>Salle où se trouve le matériel</TD>
					<TD>
						<select name="salle" >
							<option value="">Ne pas modifier</option>
							<?PHP
								$req_salles_disponibles = $con_gespac->QueryAll ( "SELECT salle_nom FROM salles" );
								foreach ( $req_salles_disponibles as $record) { 
								
									$salle_nom = $record['salle_nom'];
									
								?>
									<option value="<?PHP echo $salle_nom ?>"><?PHP echo $salle_nom ?></option>
							<?PHP
								}
							?>
						</select>
					</TD>
				</tr>
								
			</table>

			<br>
			<input type=submit value='Modifier le lot' id='post_form'>
			<input type=button value='sortir sans modifier' onclick="$('#dialog').dialog('close');" >

			</center>

		</FORM>
				

		<?PHP

		
	} 
	
		
		
		
		
	// *********************************************************************************
	//
	//			@@Formulaire modification unique prérempli
	//
	// *********************************************************************************	
	
	
	if ($action == 'mod') {
	
		$id = $_GET['id'];	// Id du matériel à modifier
			
		// Requete pour récupérer les données des champs pour le matériel à modifier
		$materiel_a_modifier = $con_gespac->QueryRow ( "SELECT mat_id, mat_nom, mat_dsit, mat_serial, mat_etat, salle_nom, marque_type, marque_model, mat_origine, marque_stype, marque_marque, mat_mac, materiels.marque_id, user_id FROM materiels, marques, salles WHERE mat_id=$id AND materiels.marque_id = marques.marque_id AND materiels.salle_id = salles.salle_id" );		
		
		// valeurs à affecter aux champs
		$materiel_id 			= $materiel_a_modifier[0];
		$materiel_nom	 		= $materiel_a_modifier[1];
		$materiel_dsit	 		= $materiel_a_modifier[2];
		$materiel_serial 		= $materiel_a_modifier[3];
		$materiel_etat	 		= $materiel_a_modifier[4];
		$materiel_salle			= $materiel_a_modifier[5];
		$materiel_type 			= $materiel_a_modifier[6];
		$materiel_modele		= $materiel_a_modifier[7];
		$materiel_origine		= $materiel_a_modifier[8];
		$materiel_stype			= $materiel_a_modifier[9];
		$materiel_marque		= $materiel_a_modifier[10];
		$materiel_mac			= $materiel_a_modifier[11];
		$marque_id				= $materiel_a_modifier[12];
		$user_id				= $materiel_a_modifier[13];

		$disabled = $user_id <> 1 ? " style='display:none;'": "";	// Si le matériel est prêté on vire la possibilité de modifier la salle
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#nom').focus();
		</script>
		
		<form action="gestion_inventaire/post_materiels.php?action=mod" method="post" name="post_form" id="formulaire">
			<input type=hidden name=materiel_id value=<?PHP echo $id;?> >
			
				<!--

				GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

				-->
				
				<table width="500" align="center" cellpadding="10" id="table_modele_selectionne">
					<tr>
						<td>Modèle sélectionné *</td>
						<td><input type="hidden" name="marque_id" id="marque_id" value=<?PHP echo $marque_id;?> > <input type="text" id="modele_selectionne" value="<?PHP echo $materiel_marque.' '.$materiel_modele; ?>" > </td>
						<td><a href='#' onclick="choisir_modele();">changer</a></td>
					</tr>
				 </table>
				<br>
				
				<div id='choix_modele' style='display:none'>
				
					<center>
				
					Choisir un modèle * :<input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter_marque(this.value, 'corr_table');" type="text"> </input>
					<a href='#' onclick="annuler_choix_modele(<?PHP echo $marque_id;?>, '<?PHP echo $materiel_marque.' '.$materiel_modele; ?>');">annuler</a>
						
					<br><br>
					
					<?PHP
					// ici il faut récupérer les lignes DISTINCTES histoire de ne pas surcharger le tableau
					$liste_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques ORDER BY marque_model" );
					?>
					
					<!-- s'affiche si il n'y a pas de résultat -->
					<div id="pasderesultat" style='display:none; color:red;'>Pas de résultat, vous devez d'abord créer le modèle manuellement.</div>
					
					<table id="corr_table" class='alternate smalltable'>

						<?PHP
							foreach ( $liste_marques as $marque) {
							
								$marque_id 			= $marque['marque_id'];
								$marque_type 		= $marque['marque_type'];
								$marque_stype 		= $marque['marque_stype'];
								$marque_marque 		= $marque['marque_marque'];
								$marque_modele 		= $marque['marque_model'];
							
								echo "<tr style='display:none' class='tr_filter'>";
									echo "<td width=200>&nbsp $marque_type</td>";
									echo "<td width=200>&nbsp $marque_stype</td>";
									echo "<td width=200>&nbsp $marque_marque</td>";
									echo "<td width=200>&nbsp $marque_modele</td>";
									echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_modele');\"><img src='./img/arrow-right.png' width=16 height=16 title='Choisir ce modèle'> </a></td>";
								echo "</tr>";
							
							}
						
						?>
						
					</table>
				</div>	 
			<br>
			<center>
			<table width=500 id='proprietes' style='text-align:left;'>
			
				<tr>
					<TD>Nom du materiel *</TD>
					<TD><input type=text name=nom id=nom required class="valid" value= "<?PHP echo $materiel_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>Référence DSIT</TD>
					<TD><input type=text name=dsit value= "<?PHP echo $materiel_dsit; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Numéro de série *</TD>
					<TD><input type="text" name="serial" id="serial" class="valid" value= "<?PHP echo $materiel_serial; ?>" readOnly='true'	/>
						<a href='#' onclick='SSN_modifier();'>
							<img src='./img/cadenas_ferme.png' id="img_cadenas_ouvert" title="Passer en écriture">
							<img src='./img/cadenas_ouvert.png' id="img_cadenas_ferme" style="display:none;" title="Passer en Read only">
						</a><!--<input type=button value="Passer en écriture" id="activer_ssn" onclick="SSN_modifier ();">-->
					</TD>
				</tr>
				
				<?PHP
				
				// Adresse MAC dans OCS
				
				$mat_ssn = $_GET['mat_ssn'];
				
				$con_ocs = new Sql ( $host, $user, $pass, $ocsweb );
				
				if ( $con_ocs->Exists() ) {
					// RQ POUR INFO OCS
					$materiel_ocs    = $con_ocs->QueryRow ( "SELECT networks.HARDWARE_ID as hid, hardware.ID as id FROM hardware, bios, networks WHERE bios.SSN = '$mat_ssn' AND bios.HARDWARE_ID = hardware.id AND networks.HARDWARE_ID = hardware.id;" );
					$materiel_ocs_id = $materiel_ocs[1];
				}
				
				
				if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
					
					$rq_cartes_reseaux = $con_ocs->QueryAll ( "SELECT MACADDR, SPEED FROM networks WHERE HARDWARE_ID = " . $materiel_ocs[0] );
				
					// Liste des boutons radion à remplacer
					foreach ($rq_cartes_reseaux as $record) {
						$SPEED   = $record['SPEED'];
						$MACADDR = $record['MACADDR'];
						
						$select = ($materiel_mac == $MACADDR) ? "checked" : "";
						
						echo "<TR class='combo_type'>";
							echo "<TD>Adresse MAC $SPEED</TD>";
							echo "<TD><input type='radio' name='mac_radio' class='mac_radio' value='$MACADDR' $select > $MACADDR </TD>"; 	//création d'un bouton radio à côté de chaque adresse mac
						echo "</TR>";
					}
					
					
					
					
					// Le inputbox de remplacement quand on clique sur le plus
					echo "<TR id='textbox_type' style='display:none;'>
							<TD>Adresse MAC</TD>
							<TD><input name='mac_input' id='mac_input' size=17 maxlength=17 type='text' value='$materiel_mac' style='display:none;'></TD>
						</TR>";
						
					// Le bouton + pour switcher entre le input et les radio buttons	
					echo "<tr><td>&nbsp</td><td align=center><a href=# onclick=\"change_combo_mac();\"> <img src='./img/add.png' style='float:top;'> <span id='change_mac'>Adresse MAC manuelle</span></a></td></tr>";
						
				} 
				else {	// Le matériel n'existe pas, on ne propose qu'un input
					echo "<TR id='textbox_type'>
							<TD>Adresse MAC</TD>
							<TD><input name='mac_input' id='mac_input' size=17 maxlength=17 type='text' value='$materiel_mac'></TD>
						</TR>";
				}
				
				$con_ocs->Close();
				
				?>
								
				<tr>
					<TD>Origine</TD> 
					<TD>	
						<select name="origine">
							<option value=<?PHP echo $materiel_origine; ?>><?PHP echo $materiel_origine; ?></option>
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine['origine'] ."'>" . $origine['origine'] ."</option>";	}	?>
						</select>

					</TD>
				</tr>
				
				<tr>
					<TD>Etat du matériel</TD> 
					<TD>
						<select name="etat" id="CB_etat">
							<option selected><?PHP echo $materiel_etat; ?></option>
							<?PHP	foreach ($liste_etats as $etat) {	echo "<option value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr id="tr_gign" style="display:none;">
					<td>Dossier GIGN</td>
					<td><input type="text" name="num_gign">
				</tr>
				
			
				<tr <?PHP echo $disabled; ?> >
				
					<TD>Salle où se trouve le matériel</TD> 
					<TD>
						<select name="salle" >
							<?PHP
							$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );
								// requête qui va afficher dans le menu déroulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $con_gespac->QueryAll ( "SELECT salle_nom FROM salles" );
								foreach ( $req_salles_disponibles as $salle) { 
									$salle_nom = $salle['salle_nom'];
									$selected = $salle_nom == $materiel_salle ? "selected" : "";
									
									echo "<option $selected value='$salle_nom'>$salle_nom</option>";
								}
							?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<td colspan=2><br><center><input type='submit' value='Modifier ce matériel' id="post_form" ></center></td>
				</tr>

			</table>

			<br>
			

			</center>

		</FORM>

		
<?PHP
	}	
	
	
	// *********************************************************************************
	//
	//			@@Formulaire de renommage de la selection
	//
	// *********************************************************************************	
	
	
	if ($action == 'renomlot') {

?>

		<form action="gestion_inventaire/post_materiels.php?action=renomlot" method="post" name="post_form" id="formulaire">
			<center>
			
			<input type=hidden name=lot id=lot>
			<!-- Ici on récupère la valeur du champ materiels_a_poster de la page voir_materiels_table.php -->
			<script>$("#lot").val( $('#materiel_a_poster').val() );</script>

			<table>
				
				<tr>
					<TD>Préfixe du lot *</TD> 
					<TD>
						<input type="text" name="prefixe" id="prefixe" class="valid" />
					</TD>
				</tr>
				
				<tr>
					<TD>Suffixe séquentiel</TD> 
					<TD>
						<input type='checkbox' name='suffixe' id='suffixe' checked />
					</TD>
				</tr>
				
				<tr>
					<TD>nombre de chiffre</TD> 
					<TD>
						<select name='bourrage' id='bourrage' >
							<option value=1>1</option>
							<option value=2>2</option>
							<option value=3>3</option>
							<option value=4>4</option>
							<option value=5>5</option>
							<option value=6>6</option>
							<option value=7>7</option>
							<option value=8>8</option>
							<option value=9>9</option>
						</select>
					</TD>
				</tr>
				
			</table>

			<br>
			<input type=submit value='Renommer le lot'  id="post_form">
			<input type=button value='sortir sans renommer' onclick="$('#dialog').dialog('close');">

			</center>

		</FORM>


<?PHP	
	}
	
	// *********************************************************************************
	//
	//			@@Formulaire suppression
	//
	// *********************************************************************************	
	

	if ( $action == 'del' ) {
		
		$mat_id = $_GET['id'];
		$mat = $con_gespac->QueryRow ( "SELECT mat_nom, mat_serial, user_id FROM materiels WHERE mat_id=$mat_id" );

		$mat_nom = $mat[0];
		$mat_serial = $mat[1];
		$user_id = $mat[2];

		if ($user_id <> 1) {echo "<h3>Vous ne pouvez pas supprimer le matériel <u>$mat_nom ($mat_serial)</u> : <br>Il est prêté. Rendez le d'abord !</h3>"; exit();}

		echo "Voulez vous vraiment supprimer le matériel $mat_nom portant le numéro de série $mat_serial ?";
	?>	
		<center><br><br>
		<form action="gestion_inventaire/post_materiels.php?action=suppr" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $mat_id;?>" name="mat_id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
	<?PHP		
	}
?>
