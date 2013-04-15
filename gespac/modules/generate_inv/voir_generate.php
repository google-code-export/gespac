<?PHP session_start(); ?>

<!--
	Visualisation des matériels SANS numéro d'inventaire
	On génère un numéro DSIT UNIQUE avec une codification lourdingue :
	C pour collège
	4 derniers chiffres de l'uai
	1 carac pour le type :
	 * C pour les pc fixes
	 * I pour imprimante
	 * P pour portables
	 * V pour les tableaux numériques
	 * E pour écran
	3 chiffres aléatoires de 000 à 999. En fait on va utiliser l'index du matériel pour s'assurer de son unicité et on bourrera avec des 0.
	

-->

<?PHP
	
	// gestion des droits particuliers (Migrer les pc)
	$droits_supp = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-10#", $_SESSION['droits']);

?>
	
	
	<div class="entetes" id="entete-geninventaire">	

	<span class="entetes-titre">CREATION DES NUMEROS d'INVENTAIRE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">On génère un numéro d'inventaire codifié pour chaque matériel sans numéro DSIT.<br> En jaune, les lignes avec un id qui dépasse 999.<br> En rouge, les matériels avec une origine DOTATION supérieure à 2010.</div>

	<span class="entetes-options">
		
		<span class="option">
			<!-- Partie post de la sélection -->
			<form name="post_form" id="post_form" action="modules/generate_inv/post_generate.php" method="post">
				<input type=hidden name='pc_a_poster' id='pc_a_poster' value=''>
				<input type=submit name='post_selection' id='post_selection' value='générer' style='display:none;'>	<span id='nb_selectionnes'> [0] </span>			
			</form>
		</span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'generate_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>
	




	
	<?PHP
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$uai = $con_gespac->QueryOne("SELECT clg_uai FROM college;");
	
	// On commence à générer le numéro d'inventaire
	$inventaire = "C" . substr($uai, 3, 4);

	// Liste des mat_id libres dans la base
	$free_mat_id = $con_gespac->QueryAll("SELECT mat_id+1 FROM materiels WHERE (mat_id + 1) NOT IN (SELECT mat_id FROM materiels) ORDER BY mat_id;");
	
	// rq pour la liste des PC
	$liste_materiels_gespac = $con_gespac->QueryAll ("SELECT mat_id, mat_nom, mat_dsit, mat_serial, marque_type, marque_stype, marque_marque, marque_model, mat_origine FROM materiels, marques WHERE materiels.marque_id=marques.marque_id AND (mat_dsit='' OR mat_dsit IS NULL);");
			
	/*************************************
	*
	*		LISTE DE SELECTION
	*
	**************************************/

	echo "<table class='tablehover' id='generate_table'>";
	
	$compteur = 0;
	
	echo "
		<th> <input type=checkbox id=checkall onclick=\"checkall('generate_table');\" > </th>
		<th>id</th>
		<th>Nom</th>
		<th>Serial</th>
		<th>Famille</th>
		<th>SFamille</th>
		<th>Marque</th>
		<th>Modèle</th>
		<th>Origine</th>
		<th>Inventaire</th>
	";

	foreach ($liste_materiels_gespac as $record) {
		
		$mat_id	= $record['mat_id'];
		$nom 	= $record['mat_nom'];
		$dsit 	= $record['mat_dsit'];
		$serial	= $record['mat_serial'];
		$type	= $record['marque_type'];
		$stype	= $record['marque_stype'];
		$marque	= $record['marque_marque'];
		$modele	= $record['marque_model'];
		$origine= $record['mat_origine'];
		
		// J'initialise le type à X. comme xorro ;p
		$id_type = "X";
		
		if ( $type == "PC" && $stype == "DESKTOP") $id_type = "C";
		if ( $type == "PC" && $stype == "PORTABLE") $id_type = "P";
		if ( $type == "IMPRIMANTE") $id_type = "I";
		if ( $type == "TBI") $id_type = "V";
		if ( $type == "ECRAN") $id_type = "E";
		
				
		// On limite le id à 3 digits
		if ( $mat_id > 999 ) {
			// On change le mat_id avec le premier id libre dans la table materiels.

			$my_id = $free_mat_id[0]["mat_id+1"];
			
			// Je vire un élément du tableau des free_id
			$free_mat_id = array_slice($free_mat_id, 1);
			
			$tr_color = " style=background-color:yellow;";
		
			// bourrage de zero de l'index sur 3 digits
			$num_unique = sprintf("%1$03d", $my_id);
		}
		else {
			
			$tr_color = " style=background-color:;";
			
			// bourrage de zero de l'index sur 3 digits
			$num_unique = sprintf("%1$03d", $mat_id);
		}
		
		
		$origine_annee = intval(substr($origine, -4));
		$origine_type = substr($origine, 0, 3);
		
		if ($origine <> "INCONNU" && $origine_type=="DOT" && $origine_annee>2010)	
			$tr_color = " style=background-color:red;";
		else 
			$tr_color = " style=background-color:none;";
		
		
		$numinventaire = $inventaire . $id_type . $num_unique;

				
		// alternance des couleurs
		$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
		
		echo "<tr id=tr_id$mat_id class=$tr_class $tr_color>";
		
			echo "<td><input class=chkbx type=checkbox name=chk indexed=true value='$mat_id' onclick=\"select_cette_ligne('$mat_id', $compteur); \"></td>";
			echo "<td>$mat_id</td>";
			echo "<td>$nom</td>";
			echo "<td>$serial</td>";
			echo "<td>$type</td>";
			echo "<td>$stype</td>";
			echo "<td>$marque</td>";
			echo "<td>$modele</td>";
			echo "<td>$origine</td>";
			echo "<td>$numinventaire</td>";

		
		echo "</tr>";
	
		$compteur++;
		
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
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					window.setTimeout("document.location.href='index.php?page=geninventaire'", 1500);			
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

