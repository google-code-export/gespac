<?PHP session_start(); ?>

<!--
	Visualisation des PC à migrer dans FOG
	On sélectionne les PC dans la liste et on met
	à jour dans le post les noms des machines dans FOG.

-->


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	
	// gestion des droits particuliers (Migrer les pc)
	$droits_supp = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-08#", $_SESSION['droits']);
	


	echo "<h3>MIGRATION des TAGS DSIT vers FOG</h3>";
	echo "<br><small><i>On affiche uniquement les PC qui ont une correspondance dans FOG sur le serial, qui ont un numéro d'inventaire et dont le nom FOG est différent du numéro d'inventaire...</small></i>";
	echo "<br><br>";
	
	
	?>

	<!-- Partie post de la sélection -->
	<form name="post_form" id="post_form" action="modules/migration_fog/post_migration.php" method="post">
		<center>
		<input type=hidden name='pc_a_poster' id='pc_a_poster' value=''>
		<input type=submit name='post_selection' id='post_selection' value='Effectuer la migration'>	
		<span id='nb_selectionnes'>[0] </span> sélectionné(s)
		</center>
	</form>
	
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'migration_table', '0')" type="text"></center>
	</form>
	

	
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

	echo "<table id='migration_table' width=100%>";
	
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
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('modules/migration_fog/voir_migration.php')", 15000);
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

		var nb_selectionnes = $('nb_selectionnes');
		
		var ligne = "tr_id" + tr_id;	//on récupère l'tr_id de la row
		var li = document.getElementById(ligne);
		
		if ( li.style.display == "" ) {	// si une ligne est masquée on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas déjà cochée
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;
				
				case 0: // On force la déselection
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines sélectionnées			
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					}
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines sélectionnées			

						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			$('pc_a_poster').value = table_id.join(";");
			
			if ( $('pc_a_poster').value != "" ) {
				$('post_selection').style.display = "";

			} else { 
				$('post_selection').style.display = "none";
			}

		}
	}
	
	
	
	
</script>

