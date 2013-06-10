<?PHP
	session_start();
	
	/* 
		Fichier pour sélection des machines à réveiller
	*/


	// vérifie le droit d'ouverture de la page
	$L_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#L-07-02#", $_SESSION['droits']);

	if (!$L_chk) exit("<center><h2>Vous n'avez pas les droits pour ouvrir cette page.</h2></center>");


?>

<div class="entetes" id="entete-wol">	

	<span class="entetes-titre">WAKE ON LAN<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet d'envoyer un signal d'allumage aux machines sélectionnées.</div>

	<span class="entetes-options">

		<span class="option"><?PHP
			echo "
			<form action='modules/wol/post_wol.php' method='post' id='formulaire'>
				<input type=hidden name='materiel_a_poster' id='id_a_poster' value=''>	
				
				<span id='nb_selectionnes' title='nombre de machines sélectionnées'></span>
				<span id='wakethem' style='display:none;'> <input type='submit' id='post_form' value='Réveiller la selection'></span>					
				
			</form>";?>
		</span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'wol_table');" type="text" value=<?PHP echo $_GET['filter'];?>> 
				<span id="filtercount" title="Nombre de lignes filtrées"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id as salleid, mat_mac FROM materiels, marques, salles WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND mat_mac <> '' ) ORDER BY mat_nom" );
?>

	<center>
		
	<table class="bigtable alternate hover" id="wol_table">
	
		<th> <input type=checkbox id='checkall'> </th>
		<th>Nom</th>
		<th>Serial</th>
		<th>Etat</th>
		<th>Salle</th>
		<th>MacADD</th>
		
		<?PHP	
			
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				$nom 		= $record['mat_nom'];
				$dsit 		= $record['mat_dsit'];
				$serial 	= $record['mat_serial'];
				$etat 		= $record['mat_etat'];
				$marque		= $record['marque_marque'];
				$model 		= $record['marque_model'];
				$type 		= $record['marque_type'];
				$stype		= $record['marque_stype'];
				$id 		= $record['mat_id'];
				$salle 		= $record['salle_nom'];
				$salle_id 	= $record['salleid'];
				$mac 		= $record['mat_mac'];
			
				// On reteste la validité de l'adresse mac
				$mac_valide = preg_match("#([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}#", $mac);
			
				if ($mac_valide) {
					echo "<tr id='tr_id$id' class='tr_modif'>";
						/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' id='$id' class='chk_line'> </td>";	
						/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?maxheight=650&mat_nom=$nom' class='infobox' title='Caractéristiques de $nom'>$nom</a> </td>";
						/*	serial	*/	echo "<td> $serial </td>";
						/*	etat	*/	echo "<td> $etat </td>";
						/*	salle	*/	echo "<td> <a href='gestion_inventaire/voir_membres_salle.php?maxheight=650&salle_id=$salle_id' class='infobox' title='Membres de la salle $salle'>$salle</a> </td>";
						/*	macaddr	*/	echo "<td> $mac </td>";

					echo "</tr>";
					
				}
			}
		?>		
		
	</table>
	</center>
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>


<script type="text/javascript">
	
	// Filtre rémanent
	filter ( $('#filt').val(), 'wol_table' );
	
	$(function(){
	
		//--------------------------------------- Selection d'une ligne	
		$('.chk_line').click(function(){
			
			var id = $(this).attr('id');
			
			if ( $(this).is(':checked') ){		
				$('#id_a_poster').val( $('#id_a_poster').val() + ";" + id );
				$("#tr_id" + id).addClass("selected");
			}
			else {
				$('#id_a_poster').val( $('#id_a_poster').val().replace(";" + id + ";", ";") );	// Supprime la valeur au milieu de la chaine
				var re = new RegExp(";" + id + "$", "g"); $('#id_a_poster').val( $('#id_a_poster').val().replace(re, "") );			// Supprime la valeur en fin de la chaine
				$("#tr_id" + id).removeClass("selected");
				$('#checkall').prop("checked", false);
			}
			
			// On affiche les boutons
			if ( $('#id_a_poster').val() != "" ) {
				$('#modif_selection').show();	$('#affect_selection').show();				
				$('#nb_selectionnes').show(); $('#wakethem').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
			} else { 
				$('#modif_selection').hide(); $('#affect_selection').hide(); $('#nb_selectionnes').hide(); $('#wakethem').hide();
			}
			
		});
		
		
		
		//--------------------------------------- Selection de toutes les lignes
		$('#checkall').click(function(){
			
			if ( $('#checkall').is(':checked') ){		
				
				$('.chk_line:visible').prop("checked", true);	// On coche toutes les cases visibles

				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line:visible').each (function(){$('#id_a_poster').val( $('#id_a_poster').val() + ";" + $(this).attr('id') );	});	// On alimente le input à poster
				
				$('#wakethem').show();		// On fait apparaitre les boutons
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
				$('.tr_modif:visible').addClass("selected");	// On colorie toutes les lignes	visibles
			}
			else {
				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line').prop("checked", false);	// On décoche toutes les cases
				$('.tr_modif').removeClass("selected");	// On vire le coloriage de toutes les lignes	
				$('#wakethem').hide(); $('#nb_selectionnes').hide();
			}			
		});	
		
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
					$('#targetback').show(); $('#target').show();
					$('#target').html(msg);
					window.setTimeout("document.location.href='index.php?page=wol&filter=" + $('#filt').val() + "'", 2500);
				 });
			}			 
		});	
	});
	
	
</script>
