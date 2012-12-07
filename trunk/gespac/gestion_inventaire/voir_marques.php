<?PHP
	session_start();

	/*
	 
		Page 02-02
	
		Visualisation des marques

	*/

	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-02-02#", $_SESSION['droits']);
	
		
?>


<h3>Visualisation des marques et modèles</h3>
<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!-- 	bouton pour le filtrage du tableau	-->
<form>
	<center><small>Filtrer :</small> <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'marque_table', '1');" type="text" value=<?PHP echo $_GET['filter'];?> ></center>
</form>

<?PHP 

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);
	
	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_type, marque_stype, marque_model, marque_marque FROM marques WHERE marque_suppr = 0 ORDER BY marque_type, marque_stype, marque_marque, marque_model" );

	
	if ($E_chk)	echo "<a href='gestion_inventaire/form_marques.php?height=250&width=640&id=-1' rel='slb_marques' title='Ajout d une marque'> <img src='img/add.png'>Ajouter un modèle</a>";
?>
	<!-- Gestion de l'affichage des modèles vides ici	
		<span style="float:right;"><input type="checkbox" id="case_cochee" onclick="cacher_modele(); alterner_couleurs ();" checked> Cacher les modèles vides </span>
	-->			
	
	<p>
	
	<center>
	<table class="tablehover" width=800 id='marque_table' >
	
		<th>Famille</th>
		<th>Sous-famille</th>
		<th>Marque</th>
		<th>Modèle</th>
		
		<?PHP	
		
		if ( $E_chk )
		echo "<th>&nbsp</th>
		<th>&nbsp</th>
		<th>&nbsp</th>";
		
		
		
			$compteur = 0;
			//$option_id = 0;
			
			// On parcourt le tableau
			foreach ($liste_des_marques as $record ) {
				// On écrit les lignes en brut dans la page html
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
				
				//echo "<tr>";
				echo "<tr id=tr_id$id class=$tr_class>";
						
					$id		 	= $record['marque_id'];
					$type 		= $record['marque_type'];
					$soustype 	= $record['marque_stype'];
					$model 		= $record['marque_model'];
					$marque 	= $record['marque_marque'];
					
					// valeur nominale pour la checkbox
					$chkbox_state = $apreter == 1 ? "checked" : "unchecked";
					
					// On récupère la valeur inverse pour la poster
					$change_apreter = $apreter == 1 ? 0 : 1;
										
					$nb_matos_de_ce_type 		= $con_gespac->QueryOne ( "SELECT COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id=marques.marque_id AND marque_type = '$type'" );
					$nb_matos_de_ce_soustype 	= $con_gespac->QueryOne ( "SELECT COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id=marques.marque_id AND marque_stype = '$soustype'" );
					$nb_matos_de_cette_marque 	= $con_gespac->QueryOne ( "SELECT COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id=marques.marque_id AND marque_marque = '$marque'" );
					$nb_matos_de_ce_modele 		= $con_gespac->QueryOne ( "SELECT COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id=marques.marque_id AND marque_model = '$model'" );
					
					// On teste si le quadruplet famille/sfamille/marque/modele existe dans la table des correspondances. Si c'est le cas, on interdit la modification.
					$quadruplet	= $con_gespac->QueryOne ( "SELECT corr_id FROM correspondances WHERE corr_type = '$type' AND corr_stype='$soustype' AND corr_marque='$marque' AND corr_modele='$model' " );
					$afficher_modifier = $quadruplet <> "" ? "none" : "" ;
									
					
					echo "<td><input type=hidden class='nbmodel' value=$nb_matos_de_ce_modele><a href='gestion_inventaire/voir_membres-marque_type.php?height=480&width=720&marque_type=$type' rel='slb_marques' title='Liste des matériels de famille $type'>" . $type . "</a> [" . $nb_matos_de_ce_type ."] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_stype.php?height=480&width=720&marque_stype=$soustype' rel='slb_marques' title='Liste des matériels de sous famille $soustype'>" . $soustype . "</a> [" . $nb_matos_de_ce_soustype . "] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_marque.php?height=480&width=720&marque_marque=$marque' rel='slb_marques' title='Liste des matériels de marque $marque'>" . $marque . "</a> [" . $nb_matos_de_cette_marque . "] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_model.php?height=480&width=720&marque_model=$model' rel='slb_marques' title='Liste des matériels de modèle $model'>" . $model . "</a> [" . $nb_matos_de_ce_modele ."] </td>";
					
					if ($E_chk) {
						echo "<td><a href='gestion_inventaire/form_ajout_materiel_par_marque.php?height=280&width=640&id=$id' rel='slb_marques' title='Formulaire d`ajout d`un materiel'><img src='img/add.png'> </a></td>";
						echo "<td><a href='gestion_inventaire/form_marques.php?height=250&width=640&id=$id' rel='slb_marques' title='Formulaire de modification de la marque $nom'><img src='img/write.png' style='display:$afficher_modifier'> </a></td>";
						echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_marque($id, '$model', '$marque', this.parentNode.parentNode.rowIndex, '" . $nb_matos_de_ce_modele ."');\">	<img src='img/delete.png'>	</a> </td>";
					}
					
				echo "</tr>";
				
				//$option_id++;
				$compteur++;
			}
		?>		

	</table>
	</center>
	
	<br>
	

<?PHP
	if ($E_chk)	echo "<a href='gestion_inventaire/form_marques.php?height=250&width=640&id=-1' rel='slb_marques' title='Ajout d une marque'> <img src='img/add.png'>Ajouter un modèle</a>";

// On se déconnecte de la db
//$con_gespac->disconnect();


?>


<script type="text/javascript">

	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_marques'});
	});


	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	// On applique l'alternance des couleurs
	//alterner_couleurs();
	
	// Filtre rémanent
	filter ( $('filt'), 'marque_table' );	
	

	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression d'une marque
	//
	// *********************************************************************************	

	function validation_suppr_marque (id, modele, marque, row, nb_de_suppr) {
		if (nb_de_suppr == 0) {
			var valida = confirm('Voulez-vous vraiment supprimer le modèle "' + modele + '" de marque "' + marque + '" ?');
		
			// si la réponse est TRUE ==> on lance la page post_marques.php
			if (valida) {
			/*	supprimer la ligne du tableau	*/
				document.getElementById('marque_table').deleteRow(row);
			/*	poste la page en ajax	*/
				$('target').load("gestion_inventaire/post_marques.php?action=suppr&id=" + id);
			}
		} else {
			alert('IMPOSSIBLE de supprimer cette marque car des machines y sont associées !');
		}
	}
	
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		//$('case_cochee').checked = false;
	
		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
				
		for (var r = 1; r < table.rows.length; r++){ // pour chaque ligne du tableau
		
		// if (table.rows[r].style.display == '') { // et si le nb de car du filtre est > à la taille de la phrase courante si on efface des caractères, le filtre marche tjs

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
			//}
		}
		
		alterner_couleurs ();
		
	}	
	
	
	// *********************************************************************************
	//
	//			Cacher les modèles vides
	//
	// *********************************************************************************	
	
	function cacher_modele () {
				
		if ( $('case_cochee').checked == true ) 
			var state = "none";
			else var state = "";
		
		$$('.nbmodel').each(function (item) {
			if ( item.value == 0 )
				item.parentNode.parentNode.style.display = state;
		})	
		
	}
	
	
	
	// *********************************************************************************
	//
	//			Alternance couleur après masquage des modèles vides
	//
	// *********************************************************************************	
	
	function alterner_couleurs () {
		
		var compteur = 0;
		
		$$('.nbmodel').each(function (item) {
		var visible = item.parentNode.parentNode.style.display;
			if (visible == "") {
				if ((compteur % 2) == 0) {
					var tr_class = "tr1";
				} else { 
					var tr_class = "tr2";
				}
				
				item.parentNode.parentNode.className = tr_class;
				compteur ++;
			}
		})
	}
	
</script>

