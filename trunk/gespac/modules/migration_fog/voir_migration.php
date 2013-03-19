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

	<span class="entetes-titre">MIGRATION DES NOMS DANS FOG<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Script permettant de mettre à jour les noms des machines dans FOG avec le numéro d'inventaire de GESPAC.<br>On affiche uniquement les PC qui ont une correspondance dans FOG sur le serial, qui ont un numéro d'inventaire et dont le nom FOG est différent du numéro d'inventaire.<br>Il est important que les machines dans FOG aient leur inventaire remonté.</div>

	<span class="entetes-options">
		
		<span class="option">
			<!-- Partie post de la sélection -->
			<form name="post_form" id="post_form" action="modules/migration_fog/post_migration.php" method="post">
				<input type=hidden name='pc_a_poster' id='pc_a_poster' value=''>
				<input type=submit name='post_selection' id='post_selection' value='Effectuer la migration' style='display:none;'>
				<input type=checkbox name='import_nom' id='import_nom'><label for='import_nom' title="Met à jour le champ description dans fog avec le nom du matériel. Ca simplifie la recherche dans fog...">Nom dans la description</label>
			</form>
		</span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='img/icons/add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'migration_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
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

	echo "<table id='migration_table' class='tablehover'>";
	
	$compteur = 0;
	
	echo "
		<th> <input type=checkbox id=checkall onclick=\"checkall('migration_table');\" > </th>
		<th>Nom gespac</th>
		<th>Inventaire</th>
		<th>Serial Gespac</th>
		<th>Serial Fog</th>
		<th>Nom Fog</th>
	";

	foreach ($liste_materiels_gespac as $record) {
		
		$gespac_mat_id	= $record['mat_id'];
		$gespac_nom 	= $record['mat_nom'];
		$gespac_dsit 	= $record['mat_dsit'];
		$gespac_serial	= $record['mat_serial'];
		
		$liste_materiels_fog = $con_fog->QueryRow ("SELECT hostName, iSysserial FROM hosts, inventory WHERE hosts.hostID=inventory.ihostID AND iSysserial='$gespac_serial'");
		$fog_nom 	= $liste_materiels_fog[0];
		$fog_serial = $liste_materiels_fog[1];
				
		// alternance des couleurs
		$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
		
		// On affiche la case à cocher seulement si on a un numéro d'inventaire et si on a une correspondance avec fog par le ssn

		if ( $gespac_dsit == "" || $fog_nom == $gespac_dsit || $fog_serial == "") 
			$affiche = false; 
		else $affiche=true;	// Si la migration a déjà été faite (même num dsit et nom dans fog)
		
		if ( $affiche ) {
			echo "<tr id=tr_id$gespac_mat_id  class=$tr_class>";
				
				echo "<td> <input class=chkbx type=checkbox name=chk indexed=true value='$gespac_mat_id' onclick=\"select_cette_ligne('$gespac_mat_id', $compteur); \"> </td>";
				
				echo "<td>$gespac_nom</td>
				<td>$gespac_dsit</td>
				<td>$gespac_serial</td>

				<td>$fog_serial</td>
				<td>$fog_nom</td>
			</tr>";

			$compteur++;

		}
		
	}
	
	echo "</table>";	

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
					$('target').setStyle("display","block");
					$('target').set('html', responseText);
					window.setTimeout("document.location.href='index.php?page=migfog'", 1500);	
			
				}
			
			}).send(this.toQueryString());
		}); 
		
	
    });
	
	
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
	
	
	
	
</script>

