<?PHP session_start(); ?>

<!--
	Visualisation des PC à migrer dans FOG
	On sélectionne les PC dans la liste et on met
	à jour dans le post les noms des machines dans FOG.

-->

<?PHP
	// gestion des droits particuliers (Migrer les pc)
	$droits_supp = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-08#", $_SESSION['droits']);
?>
	
	
<div class="entetes" id="entete-migfog">	

	<span class="entetes-titre">MIGRATION DES NOMS DANS FOG<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Script permettant de mettre à jour les noms des machines dans FOG avec le numéro d'inventaire de GESPAC.<br>On affiche uniquement les PC qui ont une correspondance dans FOG sur le serial, qui ont un numéro d'inventaire et dont le nom FOG est différent du numéro d'inventaire.<br>Il est important que les machines dans FOG aient leur inventaire remonté.</div>

	<span class="entetes-options">
		
		<span class="option">
			<!-- Partie post de la sélection -->
			<form name="post_form" id="formulaire" action="modules/migration_fog/post_migration.php" method="post">
				<input type=hidden name='id_a_poster' id='id_a_poster' value=''>
				<input type=submit name='post_selection' id='post_form' value='Effectuer la migration' style='display:none;'>
				<input type=checkbox name='import_nom' id='import_nom'><label for='import_nom' title="Met à jour le champ description dans fog avec le nom du matériel. Ca simplifie la recherche dans fog...">Nom dans la description</label>
			</form>
		</span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'migration_table');" type="text" value=<?PHP echo $_GET['filter'];?>><span id="filtercount" title="Nombre de lignes filtrées"></span></form>
		</span>
	</span>

</div>

<div class="spacer"></div>
	
	<?PHP
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// rq pour la liste des PC
	$liste_materiels_gespac = $con_gespac->QueryAll ("SELECT mat_id, mat_nom, mat_dsit, mat_serial, marque_type FROM materiels, marques WHERE materiels.marque_id=marques.marque_id AND marques.marque_type='PC';");
	
	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	
		
	/*************************************
	*
	*		LISTE DE SELECTION
	*
	**************************************/

	echo "<table id='migration_table' class='bigtable hover'>";
	
	echo "
		<th> <input type=checkbox id='checkall' > </th>
		<th>Nom gespac</th>
		<th>Inventaire</th>
		<th>Serial Gespac</th>
		<th>Serial Fog</th>
		<th>Nom Fog</th>
	";

	foreach ($liste_materiels_gespac as $record) {
		
		$id	= $record['mat_id'];
		$gespac_nom 	= $record['mat_nom'];
		$gespac_dsit 	= $record['mat_dsit'];
		$gespac_serial	= $record['mat_serial'];
		
		$liste_materiels_fog = $con_fog->QueryRow ("SELECT hostName, iSysserial FROM hosts, inventory WHERE hosts.hostID=inventory.ihostID AND iSysserial='$gespac_serial'");
		$fog_nom 	= $liste_materiels_fog[0];
		$fog_serial = $liste_materiels_fog[1];

		// On affiche la case à cocher seulement si on a un numéro d'inventaire et si on a une correspondance avec fog par le ssn

		if ( $gespac_dsit == "" || $fog_nom == $gespac_dsit || $fog_serial == "") 
			$affiche = false; 
		else $affiche=true;	// Si la migration a déjà été faite (même num dsit et nom dans fog)
		
		if ( $affiche ) {
			echo "<tr id='tr_id$id' class='tr_modif'>";
				
				echo "<td> <input type=checkbox name=chk indexed=true id='$id' value='$id' class='chk_line'> </td>";
				
				echo "<td>$gespac_nom</td>
				<td>$gespac_dsit</td>
				<td>$gespac_serial</td>

				<td>$fog_serial</td>
				<td>$fog_nom</td>
			</tr>";
		}	
	}
	
	echo "</table>";	

?>




<script type="text/javascript">
	
	$(function(){
	
	
		//---------------------------------------  POST AJAX FORMULAIRES
		
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
					window.setTimeout("document.location.href='index.php?page=migfog&filter=" + $('#filt').val() + "'", 2500);
				 });
			}			 
		});	
	
	

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
				$('#post_form').show();
				$('#post_form').val("Migration " + ($('.chk_line:checked').length) + " PC");
				
			} else { 
				$('#post_form').hide();
			}
			
		});
		
		
		
		//--------------------------------------- Selection de toutes les lignes
		
		$('#checkall').click(function(){
			
			if ( $('#checkall').is(':checked') ){		
				
				$('.chk_line:visible').prop("checked", true);	// On coche toutes les cases visibles

				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line:visible').each (function(){$('#id_a_poster').val( $('#id_a_poster').val() + ";" + $(this).attr('id') );	});	// On alimente le input à poster
				
				$('#post_form').show();
				$('#post_form').val("Migration " + ($('.chk_line:checked').length) + " PC");
				$('.tr_modif:visible').addClass("selected");	// On colorie toutes les lignes	visibles
			}
			else {
				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line').prop("checked", false);	// On décoche toutes les cases
				$('.tr_modif').removeClass("selected");	// On vire le coloriage de toutes les lignes	
				$('#post_form').hide();
			}			
		});	
		
		
	});
	
	

	// Filtre rémanent
	filter ( $('#filt').val(), 'migration_table' );
	
	/*
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
					window.setTimeout("document.location.href='index.php?page=migfog'", 1500);	
			
				}
			
			}).send(this.toQueryString());
		}); 
		
	
    });
	
	
	
	

	// *********************************************************************************
	//
	//				Selection/déselection de toutes les rows
	//
	// *********************************************************************************	
	
	
	function checkall(_table) {
		var table = $(_table);	// le tableau du matériel
		var checkall_box = $('checkall');	// la checkbox "checkall"
		
		for ( var i = 1 ; i < table.rows.length ; i++ ) {

			var lg = table.rows[i].id					// le tr_id (genre tr115)
			
			if (checkall_box.checked == true) {
				document.getElementsByName("chk")[i - 1].checked = true;	// on coche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 1 )					//on selectionne la ligne et on ajoute l'index
			} else {
				document.getElementsByName("chk")[i - 1].checked = false;	// on décoche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 0 )					//on déselectionne la ligne et on la retire de l'index
			}
			
		}
	}
	
	
	// *********************************************************************************
	//
	//				Ajout des index pour postage sur clic de la checkbox
	//
	// *********************************************************************************	
	 
	function select_cette_ligne( tr_id, num_ligne, check ) {

		var chaine_id = $('pc_a_poster').value;
		var table_id = chaine_id.split(";");
		var ligne = "tr_id" + tr_id;	//on récupère l'tr_id de la row
		var li = document.getElementById(ligne);
		
		if ( li.style.display == "" ) {	// si une ligne est masquée on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas déjà cochée
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
					}
				break;
				
				case 0: // On force la déselection
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					}
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			$('pc_a_poster').value = table_id.join(";");
			
			if ( $('pc_a_poster').value != "" ) {
				$('post_selection').setStyle("display","inline");
				$('post_selection').value = "Migration [" + (table_id.length-1) + " PC]";

			} else { 
				$('post_selection').setStyle("display","none");
			}

		}
	}
	*/
	
	
	
</script>

