<?PHP
	
	/* 
		Fichier pour sélection des machines à cloner
	
	*/


	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
?>

<script type="text/javascript">
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('modules/image_fog/voir_liste.php');", 1500);
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, marque_marque, marque_model, salle_nom, mat_mac, salles.salle_id as salleid, mat_id FROM materiels, marques, salles WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND mat_mac <> '' ) ORDER BY mat_nom" );
?>
	<h3>Cloner des PC par Fog</h3><br>
	
	<span id="nb_selectionnes">[0]</span> machines sélectionnées.
	
	
	<center>
	
	<form name="post_form" id="post_form" action="modules/image_fog/post_image.php" method="post">
	
		<!--------------------------------------------	LISTE DES ID A POSTER	------------------------------------------------>
		<input type=hidden name=materiel_a_poster id=materiel_a_poster value=''>	

		<input type=submit id="imagethem" value="Imager ces machines" style="display:none"><br>
		
	</form>
	

	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'image_table', '1')" type="text"></center>
	</form>
	
	
	
	<table class="tablehover" id="image_table" width=870>
	
		<th> <input type=checkbox id=checkall onclick="checkall('image_table');" > </th>
		<th>Nom</th>
		<th>Mac</th>
		<th>Marque</th>
		<th>Modèle</th>
		<th>Image</th>
		<th>Groupe</th>
		<th>Salle</th>
		<th>&nbsp;</th>

		
		<?PHP	
		
			$compteur = 0;
			
			
			foreach ( $liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";

				$nom 		= $record['mat_nom'];
				$marque		= $record['marque_marque'];
				$model 		= $record['marque_model'];
				$salle 		= $record['salle_nom'];
				$mac 		= $record['mat_mac'];
				$salle_id 	= $record['salleid'];
				$id 		= $record['mat_id'];
				
				
				// cnx à fog
				$con_fog = new Sql($host, $user, $pass, $fog);
				
				$image_associee = $con_fog->QueryOne ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostName = '$nom'");
				$groupe_associe = $con_fog->QueryOne ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostName = '$nom'");
			
				
				echo "<tr id=tr_id$id class=$tr_class>";
					/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
					/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$nom' rel='slb_wol' title='Caractéristiques de $nom'>$nom</a> </td>";
					/*	macaddr	*/	echo "<td> $mac </td>";
					/*	marque	*/	echo "<td> $marque </td>";
					/*	modele	*/	echo "<td> $model </td>";
					/*	image	*/	echo "<td> $image_associee </td>";
					/*	groupe	*/	echo "<td> $groupe_associe </td>";
					/*	salle	*/	echo "<td> <a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$salle_id' rel='slb_wol' title='Membres de la salle $salle'>$salle</a> </td>";
					/*	cloner	*/	echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_clone_materiel($id, '$nom', '$image_associee'); \" ><img src='img/down.png' title='imager $nom'> </td>";

				echo "</tr>";
				
				$compteur++;
			}
		?>		
		
	</table>
	</center>
	
	<br>
	

<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_wol'});
	});

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";

			
	// *********************************************************************************
	//
	//				clonage d'un PC 
	//
	// *********************************************************************************	
			
	function validation_clone_materiel(id, nom, imagefog) {
	
		var valida = confirm('Voulez vous imager le matériel ' + nom + ' avec l\'image fog ' + imagefog + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_image.php
		if (valida) {	
			/*	poste la page en ajax	*/
			$('target').load("modules/image_fog/post_image.php?action=unicast&id=" + id);
		}
	}		
			
	// *********************************************************************************
	//
	//				Selection/déselection de toutes les rows
	//
	// *********************************************************************************	
	
	function checkall(_table) {
		var table = document.getElementById(_table);	// le tableau du matériel
		var checkall_box = document.getElementById('checkall');	// la checkbox "checkall"
		
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

		var chaine_id = document.getElementById('materiel_a_poster').value;
		var table_id = chaine_id.split(";");
		
		var nb_selectionnes = document.getElementById('nb_selectionnes');
		
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
					table_id.erase(tr_id);
					nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines sélectionnées			
					// alternance des couleurs calculée avec la parité
					if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
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
			document.getElementById('materiel_a_poster').value = table_id.join(";");
			

			if ( $('materiel_a_poster').value != "" ) 
				$('imagethem').style.display = "";
			else 
				$('imagethem').style.display = "none";
		}
	}
	
	
		
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
		
</script>