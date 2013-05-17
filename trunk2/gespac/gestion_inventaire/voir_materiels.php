<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?PHP
	
	/* 
		fichier de visualisation de l'inventaire :
	*/

	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-02-01#", $_SESSION['droits']);

	if ( !isset($_SESSION['entetes']) ) $_SESSION['entetes'] = "0111001111";	// Cases à cocher par défaut			
?>



<!-- L'ENTETE DE LA PAGE ET SES OPTIONS	-->

<div class="entetes" id="entete-materiels">	

	<span class="entetes-titre">LES MATERIELS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span><span id='nb_selectionnes'></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des matériels du parc.<br>On peut aussi modifier ou renommer un lot de matériels.</div>
	
	<span class="entetes-options">
				
		<span class="option">	<!-- filtre du matériels -->
			<form>
				<small><a href="#" id='searchtaskshelp_bt'>[?]</a></small> 
				<input placeholder=" filtrer" name="filt" id="filt" style='width:300px;' onKeyPress="return disableEnterKey(event)" type="text" value=<?PHP echo $_GET['filter']; ?> >
				<span id="filtercount" title='nombre de matériels affichés'></span>
			</form>			
				
			<div id='searchtaskshelp' style='display:none;'>
				- <b>recherche</b> : cherche dans le nom des matériels<br><br>
				- <b>!recherche</b> : cherche tous les noms de matériels qui ne correspondent pas à la recherche<br><br>
				- <b>champ=valeur</b> : cherche dans la colonne spécifiée les valeurs qui correspondent à la recherche (ex : "d=07p")<br><br>
				- <b>champ!=valeur</b> : cherche dans la colonne spécifiée les valeurs qui ne correspondent pas à la recherche<br><br>
				- <b>t=valeur</b> : cherche dans toutes les colonnes les valeurs qui correspondent à la recherche<br><br>
				- <b>&&</b> permet de combiner plusieurs facteurs de recherche<br><br>
				Exemple : <b>sdc&&!ecran&&m=hp&&!mo=netvista</b> <br>
				- tous les matériels appelés "sdc" <br>
				- mais pas "ecran" <br>
				- avec une marque égale à "hp" <br>
				- et un modele différent de "netvista"<br>
				<br><br>
				La recherche n'est pas sensible à la casse.<br>
				De plus on cherche par sur une ressemblance pas une égalité : Si le matériel s'appelle "07C123456", "d=07C123" peut suffire
			</div>
		</span>
		
		
		<span class="option">	<!-- Créer CSV -->
			<?PHP echo "<span><a href='#' id='creer_csv' title='générer CSV'> <img src='" . ICONSPATH . "csv.png'></a></span>";	?>
		</span>
		
		<span class="option">	<!-- Ajout Matériel -->
		<?PHP if ( $E_chk ) {echo "<span><a href='gestion_inventaire/form_materiels.php?action=add&maxheight=650&width=550' class='editbox' title='Ajouter un matériel'> <img src='" . ICONSPATH . "add.png'></a></span>";} ?>
		</span>
		
		<span class="option">	<!-- Modifier le lot -->
			<?PHP if ( $E_chk ) {echo "<span id='modif_selection'><a href='gestion_inventaire/form_materiels.php?action=modlot' class='editbox' title='Modifier la selection'> <img src='" . ICONSPATH . "modif1.png'></a></span>";}?>
		</span>
		
		<span class="option">	<!-- renommer le lot -->
		<?PHP if ( $E_chk ) {echo "<span id='rename_selection'><a href='gestion_inventaire/form_materiels.php?action=renomlot' class='editbox' title='Renommer la selection'> <img src='" . ICONSPATH . "pen.png'></a> </span>";} ?>
		</span>
		
		<span class="option">	<!-- affecter une salle au lot -->
			<?PHP if ( $E_chk ) { ?>
				<span id='affect_selection'><a href='#'><img src="<?PHP echo ICONSPATH . "refresh.png";?>" title="Affectation directe à une salle"></a></span>
				<div id='affect_box'>
					<form action="gestion_inventaire/post_materiels.php?action=affect" method="post" name="post_form" id="form_affect_salles" >
						<input type=hidden name='materiel_a_poster' id='materiel_a_poster' value=''>	

					<?PHP 
						echo "<select name='salle_select' id='salle_select'>";
				
						// Pour le remplissage de la combobox des salles pour l'affectation
							
						// stockage des lignes retournées par sql dans un tableau nommé combo_des_salles
						$combo_des_salles = $con_gespac->QueryAll ( "SELECT salle_id, salle_nom FROM salles ORDER BY salle_nom;" );
						
						foreach ($combo_des_salles as $combo_option ) {
						
							$option_id 		= $combo_option['salle_id'];
							$option_salle 	= $combo_option['salle_nom'];
							
							//On colle par défaut la salle STOCK, donc ID = 1
							$defaut = $option_id == 1 ? "selected" : "";
							
							echo "<option value=$option_id $defaut> $option_salle </option>";
						}
					
						echo "</select>";
						echo "<input type=submit value='Affecter' id='post_affect_salles'>";
					?>

					</form>
				</div>
			<?PHP } ?>
		</span>
						
		<span class="option">	<!-- Affichage des colonnes -->		
			<span id='affiche_colonne'><a href='#'><img src="<?PHP echo ICONSPATH . "eye.png";?>" title="colonnes à montrer ou à cacher"></a></span>
			<div id="options_colonnes">
				<input type="checkbox" class="opt_entete" id="chk_pret" onclick="hidethem('.td_pret', this.checked);post_modif_entete();"><label for="chk_pret">Prêt</label><br>
				<input type="checkbox" class="opt_entete" id="chk_dsit" onclick="hidethem('.td_dsit', this.checked);post_modif_entete();"><label for="chk_dsit">DSIT</label><br>
				<input type="checkbox" class="opt_entete" id="chk_serial" onclick="hidethem('.td_serial', this.checked);post_modif_entete();"><label for="chk_serial">Serial</label><br>
				<input type="checkbox" class="opt_entete" id="chk_etat" onclick="hidethem('.td_etat', this.checked);post_modif_entete();"><label for="chk_etat">Etat</label><br>
				<input type="checkbox" class="opt_entete" id="chk_type" onclick="hidethem('.td_type', this.checked);post_modif_entete();"><label for="chk_type">Famille</label><br>
				<input type="checkbox" class="opt_entete" id="chk_stype" onclick="hidethem('.td_stype', this.checked);post_modif_entete();"><label for="chk_stype">Sous Famille</label><br>
				<input type="checkbox" class="opt_entete" id="chk_modele" onclick="hidethem('.td_modele', this.checked);post_modif_entete();"><label for="chk_modele">Modèle</label><br>
				<input type="checkbox" class="opt_entete" id="chk_marque" onclick="hidethem('.td_marque', this.checked);post_modif_entete();"><label for="chk_marque">Marque</label><br>
				<input type="checkbox" class="opt_entete" id="chk_salle" onclick="hidethem('.td_salle', this.checked);post_modif_entete();"><label for="chk_salle">Salle</label><br>
				<input type="checkbox" class="opt_entete" id="chk_origine" onclick="hidethem('.td_origine', this.checked);post_modif_entete();"><label for="chk_origine">Origine</label>
			</div>
		</span>
	
	</span>
</div>


<div class=spacer></div>


<?PHP

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) ORDER BY mat_nom" );

	$tri_nom 	 = "nom_asc";
	$tri_pret 	 = "pret_asc";
	$tri_dsit 	 = "dsit_asc";
	$tri_serial  = "serial_asc";
	$tri_etat 	 = "etat_asc";
	$tri_type 	 = "type_asc";
	$tri_stype 	 = "stype_asc";
	$tri_marque  = "marque_asc";
	$tri_modele  = "modele_asc";
	$tri_salle   = "salle_asc";
	$tri_origine = "origine_asc";
	
	
	if (isset($_GET['tri'])) {
		
		switch ($_GET['tri']) {
			
		case "nom_asc"	    : 	$orderby = "ORDER BY mat_nom asc";			$tri_nom = "nom_desc";			$img_nom = "<img src=\"./img/down.png\" />";		break;
		case "nom_desc"     :	$orderby = "ORDER BY mat_nom desc";											$img_nom = "<img src=\"./img/up.png\" />";			break;
		//case "pret_asc"     :	$orderby = "ORDER BY user_nom asc";			$tri_pret = "pret_desc";		$img_pret = "<img src=\"./img/down.png\" />";		break;
		//case "pret_desc"    :	$orderby = "ORDER BY user_nom desc";										$img_pret = "<img src=\"./img/up.png\" />";			break;
		case "dsit_asc"     :	$orderby = "ORDER BY mat_dsit asc";			$tri_dsit = "dsit_desc";		$img_dsit = "<img src=\"./img/down.png\" />";		break;
		case "dsit_desc"    :	$orderby = "ORDER BY mat_dsit desc";										$img_dsit = "<img src=\"./img/up.png\" />";			break;
		case "serial_asc"   :	$orderby = "ORDER BY mat_serial asc";		$tri_serial = "serial_desc";	$img_serial = "<img src=\"./img/down.png\" />";		break;
		case "serial_desc"  :	$orderby = "ORDER BY mat_serial desc";										$img_serial = "<img src=\"./img/up.png\" />";		break;
		case "etat_asc"     :	$orderby = "ORDER BY mat_etat asc";			$tri_etat = "etat_desc";		$img_etat = "<img src=\"./img/down.png\" />";		break;
		case "etat_desc"    :	$orderby = "ORDER BY mat_etat desc";										$img_etat = "<img src=\"./img/up.png\" />";			break;
		case "type_asc"     :	$orderby = "ORDER BY marque_type asc";		$tri_type = "type_desc";		$img_type = "<img src=\"./img/down.png\" />";		break;
		case "type_desc"    :	$orderby = "ORDER BY marque_type desc";										$img_type = "<img src=\"./img/up.png\" />"; 		break;
		case "stype_asc"    :	$orderby = "ORDER BY marque_stype asc";		$tri_stype = "stype_desc";		$img_stype = "<img src=\"./img/down.png\" />";		break;
		case "stype_desc"   :	$orderby = "ORDER BY marque_stype desc";									$img_stype = "<img src=\"./img/up.png\" />";		break;
		case "marque_asc"   :	$orderby = "ORDER BY marque_marque asc";	$tri_marque = "marque_desc";	$img_marque = "<img src=\"./img/down.png\" />";		break;
		case "marque_desc"  :	$orderby = "ORDER BY marque_marque desc";									$img_marque = "<img src=\"./img/up.png\" />";		break;
		case "modele_asc"   :	$orderby = "ORDER BY marque_model asc";		$tri_modele = "modele_desc";	$img_modele = "<img src=\"./img/down.png\" />";		break;
		case "modele_desc"  :	$orderby = "ORDER BY marque_model desc";									$img_modele = "<img src=\"./img/up.png\" />";		break;
		case "salle_asc"    :	$orderby = "ORDER BY salle_nom asc";		$tri_salle = "salle_desc";		$img_salle = "<img src=\"./img/down.png\" />";		break;
		case "salle_desc"   :	$orderby = "ORDER BY salle_nom desc";										$img_salle = "<img src=\"./img/up.png\" />";		break;
		case "origine_asc"  :	$orderby = "ORDER BY mat_origine asc";		$tri_origine = "origine_desc";	$img_origine = "<img src=\"./img/down.png\" />";	break;
		case "origine_desc" :	$orderby = "ORDER BY mat_origine desc";										$img_origine = "<img src=\"./img/up.png\" />";		break;
			
		default 			:	$orderby = "ORDER BY mat_nom asc";	break;
		
		}
		
	} else {
		
		$orderby = "ORDER BY mat_nom asc";
	}
	
	//-------------------------------------------------------------------------------------------------------- LE FILTRE

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	$filtre = $_GET['filter'];
	$segments = @explode ("&&", $filtre);
	$curseur = 1;	
	$where = " ";
	
	foreach ($segments as $segment) {
		
		// On explose les segments par les '='
		$couple = explode ('=', $segment);
		
		// Pour la partie champ
		$key = $couple[0];
		
		// pour la partie valeur
		if ($couple[1]) {
			$value = $couple[1];	
			
			if (substr($key, -1) == '!') {	// forme négative ?
				$not = " NOT ";
				$key = substr ($key, 0, -1);
			} else $not = " ";
		}
		else {
			$value=$couple[0];
			
			if (substr($value, 0, 1) == '!') { // forme négative ?	
				$not = " NOT ";
				$value = substr ($value,1);
			} else $not = "";		
		}

	
		
		switch ($key) {
			case "t" :	break;
			case "n" :	$champ = "mat_nom";			break;
			case "p" :	$champ = "user_nom";		break;
			case "d" :	$champ = "mat_dsit";		break;
			case "s" :	$champ = "mat_serial";		break;
			case "e" :	$champ = "mat_etat";		break;
			case "f" :	$champ = "marque_type";		break;
			case "sf" :	$champ = "marque_stype";	break;
			case "m" :	$champ = "marque_marque";	break;
			case "mo" :	$champ = "marque_model";	break;
			case "sa" :	$champ = "salle_nom";		break;
			case "o" :	$champ = "mat_origine";		break;
			default :	$champ = "mat_nom";			break;
		}
	
		if ($key == "t")  $where .= "(mat_nom LIKE '%$value%' OR user_nom LIKE '%$value%' OR mat_dsit LIKE '%$value%' OR mat_serial LIKE '%$value%' OR mat_origine LIKE '%$value%' OR mat_etat LIKE '%$value%' OR marque_type LIKE '%$value%' OR marque_stype LIKE '%$value%' OR marque_marque LIKE '%$value%' OR marque_model LIKE '%$value%' OR salle_nom LIKE '%$value%')";
		else $where .= " $champ $not LIKE '%" . $value . "%'";
	
		// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
		if ( $curseur <> count($segments) ) $where .= " AND ";
	
		$curseur++;
				
	}


	if ( $_GET['filter'] <> '' ) {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND $where) $orderby" );
		echo "<script>$('#filtercount').html('" . count($liste_des_materiels) . "');</script>";
	}
	else {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) $orderby" );
		echo "<script>$('#filtercount').html('" . count($liste_des_materiels) . "');</script>";
	}
	
?>
	
	

	<table class="bigtable alternate hover" id="mat_table">
		<!-- Entêtes du tableau des matériels. On gère ici le tri.-->
		<?PHP if ( $E_chk ) echo "<th> <input type='checkbox' id='checkall'> </th>"; ?>
		
		<th title="n : le nom de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_nom; ?>', filt.value);">
			Nom<sup>n</sup> <?PHP echo $img_nom; ?></a></th>
			
		<th class="td_pret" style='display:none' title="p : le nom du professeur à qui le matériel est prêté">Prêté à<sup>p</sup></th>
		
		<th class="td_dsit" title="d : le numéro de série de la DSIT">
			<a href="#" onclick="order_by('<?PHP echo $tri_dsit; ?>', filt.value);">
			DSIT<sup>d</sup><?PHP echo $img_dsit; ?></a></th>
			
		<th class="td_serial" title="s : le numéro de série de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_serial; ?>', filt.value);">
			Serial<sup>s</sup><?PHP echo $img_serial; ?></a></th>
			
		<th class="td_etat" title="e : L'état général de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_etat; ?>', filt.value);">
			Etat<sup>e</sup><?PHP echo $img_etat; ?></a></th>
			
		<th class="td_type" style='display:none' title="f : Famille du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_type; ?>', filt.value);">
			Famille<sup>f</sup><?PHP echo $img_type; ?></a></th>
			
		<th class="td_stype" style='display:none' title="sf : Sous Famille du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_stype; ?>', filt.value);">
			Sous-famille<sup>sf</sup> <?PHP echo $img_stype; ?></a></th>
			
		<th class="td_marque" title="m : Marque du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_marque; ?>', filt.value);">
			Marque<sup>m</sup> <?PHP echo $img_marque; ?></a></th>
			
		<th class="td_modele" title="mo : Modèle du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_modele; ?>', filt.value);">
			Modèle<sup>mo</sup> <?PHP echo $img_modele; ?></a></th>
			
		<th class="td_salle"  title="sa : Salle où est affecté le matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_salle; ?>', filt.value);">
			Salle<sup>sa</sup> <?PHP echo $img_salle; ?></a></th>
			
		<th class='td_origine' title="o : Propriétaire et année d'achat du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_origine; ?>', filt.value);">
			Origine<sup>o</sup> <?PHP echo $img_origine; ?></a></th>
	
	<?PHP 
	
	if ( $E_chk ) {
		echo "<th>&nbsp</th>
		<th>&nbsp</th>";
	}
	
		

			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {

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
				$salle_id 	= $record['salle_id'];
				$origine 	= $record['mat_origine'];
				$user	 	= $record['user_nom'];
			
				
				// test si la machine est prétée ou pas
				$rq_machine_pretee = $con_gespac->QueryOne ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$id" );
				$mat_id = @$rq_machine_pretee;	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
						
				if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
						$id_pret = 0;
					} else {	// la machine est prêtée ($mat_id existe)
						$id_pret = 1;
					}
				
				if ($salle == 'PRETS') {
					if ($user == 'ati') {
						$font_color = "#18c900";
						$pret = "Disponible";
					} else {
						$font_color = "#0BAFF0";
						$pret = "$user";
					}
				} else {
					$font_color = "#FF0000";
					$pret = "Indisponible";
				}
				//gestion_inventaire/voir_membres-marque_stype.php?maxheight=650&marque_stype=$soustype
				echo "<tr id=tr_id$id>";
				
					/*	chckbox	*/	if ( $E_chk ) echo "<td> <input type=checkbox name=chk indexed=true value='$id' class='chk_line' id='$id'> </td>";	
					/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?maxheight=650&mat_nom=$nom&mat_ssn=$serial' class='editbox' title='Fiche du matériel $nom'>$nom</a> </td>";
					/*	pret	*/	echo "<td class='td_pret' style='display:none'><font color=$font_color> $pret </font></td>";
					/*	dsit	*/	echo "<td class='td_dsit'> $dsit </td>";
					/*	serial	*/	echo "<td class='td_serial'> $serial </td>";
					/*	etat	*/	echo "<td class='td_etat'> <a href='gestion_inventaire/voir_membres_etat.php?maxheight=650&etat=$etat' class='editbox' title='Liste des materiels $etat'>$etat</a> </td>";
					/*	type	*/	echo "<td class='td_type' style='display:none'> <a href='gestion_inventaire/voir_membres-marque_type.php?maxheight=650&marque_type=$type' class='editbox' title='Liste de la famille $type'>$type</a></td>";
					/*	stype	*/	echo "<td class='td_stype' style='display:none'> <a href='gestion_inventaire/voir_membres-marque_stype.php?maxheight=650&marque_stype=$stype' class='editbox' title='Liste de la sous famille $stype'>$stype</a></td>";
					/*	marque	*/	echo "<td class='td_marque'> <a href='gestion_inventaire/voir_membres-marque_marque.php?maxheight=650&marque_marque=$marque' class='editbox' title='Liste de la marque $marque'>$marque</a></td>";
					/*	modele	*/	echo "<td class='td_modele' > <a href='gestion_inventaire/voir_membres-marque_model.php?maxheight=650&marque_model=$model' class='editbox' title='Liste du modèle $model'>$model</a></td>";
					/*	salle	*/	echo "<td class='td_salle'> <a href='gestion_inventaire/voir_membres_salle.php?maxheight=650&salle_id=$salle_id' class='editbox' title='Liste du matériel dans la salle $salle'>$salle</a> </td>";
					/*	origine	*/	echo "<td class='td_origine'> <a href='gestion_inventaire/voir_membres_origine.php?maxheight=650&origine=$origine' class='editbox' title='Liste du matériel ayant pour origine $origine'>$origine</a> </td>";
					
					if ( $E_chk ) {
						/*	modif	*/	echo "<td class='buttons'><a href='gestion_inventaire/form_materiels.php?action=mod&id=$id&mat_ssn=$serial' class='editbox' title='Formulaire de modification du matériel $nom'><img src='" . ICONSPATH . "edit.png'> </a></td>";
						/*	suppr	*/	echo "<td class='buttons'><a href='gestion_inventaire/form_materiels.php?action=del&id=$id' class='editbox' title='Supprimer un matériel'>	<img src='" . ICONSPATH . "delete.png' title='supprimer $nom'>	</a> </td>";
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		
		
	</table>

	<!--	Ancre bas de page	-->
	<a name="basdepage"></a>

	<br>

</body>


<script type="text/javascript">	

	$(function() {
		
		
		//-------------------------------------------------- POST AJAX FORMULAIRES
		$("#post_affect_salles").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
		
			// Permet d'avoir les données à envoyer
			var dataString = $("#form_affect_salles").serialize();
			
			// action du formulaire
			var url = $("#form_affect_salles").attr( 'action' );
			
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
			 
		});	
			
			
			
		//--------------------------------------- créer un fichier CSV du filtre
		 //onclick=\"AffichePage('target','gestion_inventaire/post_export_filtre.php?filtre=" . urlencode($filtre) . "');\"
		$('#creer_csv').click(function() {
			$('#target').load("gestion_inventaire/post_export_filtre.php?filter=" + $('#filt').val() );
		});
		
		
		
		
		
		
		//--------------------------------------- Fait apparaitre l'aide pour le filtre
		
		$('#searchtaskshelp_bt').click(function() {
			$('#searchtaskshelp').dialog({title:'Aide de la barre de recherche',width:'740',height:'460'}); 
		});
	
		
		
		//--------------------------------------- Selection d'une ligne
		
		$('.chk_line').click(function(){
			
			var poster = $('#materiel_a_poster').val();
			var id = $(this).attr('id');
			
			if ( $(this).is(':checked') ){		
				$('#materiel_a_poster').val( $('#materiel_a_poster').val() + ";" + id );
				$("#tr_id" + id).addClass("selected");
			}
			else {
				$('#materiel_a_poster').val( $('#materiel_a_poster').val().replace(";" + id + ";", ";") );	// Supprime la valeur au milieu de la chaine
				var re = new RegExp(";" + id + "$", "g"); $('#materiel_a_poster').val( $('#materiel_a_poster').val().replace(re, "") );			// Supprime la valeur en fin de la chaine
				$("#tr_id" + id).removeClass("selected");
				$('#checkall').prop("checked", false);
			}
			
			// On affiche les boutons
			if ( $('#materiel_a_poster').val() != "" ) {
				$('#modif_selection').show();	$('#rename_selection').show(); $('#affect_selection').show();				
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
			} else { 
				$('#modif_selection').hide();	$('#rename_selection').hide(); $('#affect_selection').hide(); $('#nb_selectionnes').hide();
			}
			
		});
		
		
		
		//--------------------------------------- Selection de toutes les lignes
		
		$('#checkall').click(function(){
			
			if ( $('#checkall').is(':checked') ){		
				
				$('.chk_line').prop("checked", true);	// On coche toutes les cases

				$('#materiel_a_poster').val("");	// On vide les matos à poster
				$('.chk_line').each (function(){$('#materiel_a_poster').val( $('#materiel_a_poster').val() + ";" + $(this).attr('id') );	});	// On alimente le input à poster
				
				$('#modif_selection').show();	$('#rename_selection').show(); $('#affect_selection').show();		// On fait apparaitre les boutons
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
				$('tr').addClass("selected");	// On colorie toutes les lignes	
			}
			else {
				$('#materiel_a_poster').val("");	// On vide les matos à poster
				$('.chk_line').prop("checked", false);	// On décoche toutes les cases
				$('tr').removeClass("selected");	// On vire le coloriage de toutes les lignes	
				$('#modif_selection').hide();	$('#rename_selection').hide(); $('#affect_selection').hide(); $('#nb_selectionnes').hide();
			}			
		});

		
		
		//--------------------------------------- Affiche l'affectation aux salles
		
		$('#affect_selection').click(function(){
			$('#affect_box').slideToggle();
		});
		
		
		
		//--------------------------------------- Affiche le choix des colonnes
		
		$('#affiche_colonne').click(function(){
			$('#options_colonnes').slideToggle();
		});
		


		//--------------------------------------- Le filtre
				
		// Fonction de temporisation du filtre
		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
				};
		})();
				
		$('#filt').keyup(function() {
			delay(function(){
				document.location.href='index.php?page=materiels&filter=' + encodeURIComponent( $('#filt').val() );
			}, 2000 );
		});
		
	});
	


	//-------------------------------------	On poste dans $_SESSION les entêtes affichées
	
	function post_modif_entete () {
		$('#target').load("gestion_inventaire/post_materiels.php?action=entetes&value=" + etat_entetes() );
	}
	
	
	
	//-------------------------------------	Tri des colonnes
	
	function order_by (tri, phrase) {
		document.location.href="index.php?page=materiels&tri=" + tri + "&filter=" + phrase;
	}
	
	
	
	//-------------------------------------	On retourne la liste des états des entêtes (colonne montrée ou masquée)
	
	function etat_entetes () {
		
		var liste = "";
	
		$('.opt_entete').each(function() {
			if ( $(this).prop("checked") )
				liste += "1";
			else 
				liste += "0";
		});	
				
		return liste;
	}
	
	
	
	//-------------------------------------	Montre ou masque des colonnes	-> Passer cette fonction dans $(function(){})
	
	function hidethem (col_name, show) {
		if ( show == true) $(col_name).show();
		else $(col_name).hide();
	}
	
	
	
	//------------------------------------ initialisation des cases à cocher et de l'état des colonnes (hide/show)
	
	function init_entetes (value) {
		
		if (value.substr(0, 1) == "1") {$('#chk_pret').attr('checked',true); hidethem('.td_pret', true);} 
		else {$('#chk_pret').attr('checked',false); hidethem('.td_pret', false);}
		
		if (value.substr(1, 1) == "1") {$('#chk_dsit').attr('checked',true); hidethem('.td_dsit', true);} 
		else {$('#chk_dsit').attr('checked',false); hidethem('.td_dsit', false);}
		
		if (value.substr(2, 1) == "1") {$('#chk_serial').attr('checked',true); hidethem('.td_serial', true);} 
		else {$('#chk_serial').attr('checked',false); hidethem('.td_serial', false);}
		
		if (value.substr(3, 1) == "1") {$('#chk_etat').attr('checked',true); hidethem('.td_etat', true);} 
		else {$('#chk_etat').attr('checked',false); hidethem('.td_etat', false);}
		
		if (value.substr(4, 1) == "1") {$('#chk_type').attr('checked',true); hidethem('.td_type', true);} 
		else {$('#chk_type').attr('checked',false); hidethem('.td_type', false);}
		
		if (value.substr(5, 1) == "1") {$('#chk_stype').attr('checked',true); hidethem('.td_stype', true);} 
		else {$('#chk_stype').attr('checked',false); hidethem('.td_stype', false);}
		
		if (value.substr(6, 1) == "1") {$('#chk_modele').attr('checked',true); hidethem('.td_modele', true);} 
		else {$('#chk_modele').attr('checked',false); hidethem('.td_modele', false);}
		
		if (value.substr(7, 1) == "1") {$('#chk_marque').attr('checked',true); hidethem('.td_marque', true);} 
		else {$('#chk_marque').attr('checked',false); hidethem('.td_marque', false);}
		
		if (value.substr(8, 1) == "1") {$('#chk_salle').attr('checked',true); hidethem('.td_salle', true);} 
		else {$('#chk_salle').attr('checked',false); hidethem('.td_salle', false);}
	
		if (value.substr(9, 1) == "1") {$('#chk_origine').attr('checked',true); hidethem('.td_origine', true);} 
		else {$('#chk_origine').attr('checked',false); hidethem('.td_origine', false);}
	}
	
	
	// On initialise tout le bazar d'entête en cochant les checkbox et en cachant/montrant les colonnes
	init_entetes ('<?PHP echo $_SESSION['entetes'];?>');
		
</script>
