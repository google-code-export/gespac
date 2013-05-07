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
				<small><a href="#" title="Cherchez dans une colonne précise avec le séparateur deux points (CDI:n pour le nom, CDI:t pour tout le tableau) " onclick="alert('Cherchez dans une colonne précise avec le séparateur deux points (CDI:n pour le nom, CDI:s pour la salle, CDI:t pour tout le tableau, ...) \n Le filtre d`exclusion permet de ne pas sélectionner une valeur particulière.\n Ainsi `CDI:n / ecran:n` permet de selectionner tout le matériel appelé CDI mais pas les écrans CDI. \n On peut aussi ajouter des champs avec l`opérateur +. par exemple `cdi:n+fonctionnel:e/ecran:n+d3e:s`.');">[?]</a></small> 
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" type="text" value=<?PHP echo $_GET['filter']; ?> >
				<span id="filtercount" title='nombre de matériels affichés'></span>
			</form>
		</span>
		
		<span class="option">	<!-- Créer CSV -->
			<?PHP echo "<span><a href='#' onclick=\"AffichePage('target','gestion_inventaire/post_export_filtre.php?filtre=" . urlencode($filtre) . "');\" title='générer CSV'> <img src='" . ICONSPATH . "csv.png'></a></span>";	?>
		</span>
		
		<span class="option">	<!-- Ajout Matériel -->
		<?PHP if ( $E_chk ) {echo "<span><a href='gestion_inventaire/form_materiels.php?action=add' class='editbox' title='Ajouter un matériel'> <img src='" . ICONSPATH . "add.png'></a></span>";} ?>
		</span>
		
		<span class="option">	<!-- Modifier le lot -->
			<?PHP if ( $E_chk ) {echo "<span id='modif_selection'><a href='gestion_inventaire/form_materiels.php?action=modlot' class='editbox' title='Modifier la selection'> <img src='" . ICONSPATH . "modif1.png'></a></span>";}?>
		</span>
		
		<span class="option">	<!-- renommer le lot -->
		<?PHP if ( $E_chk ) {echo "<span id='rename_selection'><a href='gestion_inventaire/form_materiels.php?action=renomlot' class='editbox' title='Renommer la selection'> <img src='" . ICONSPATH . "pen.png'></a> </span>";} ?>
		</span>
		
		<span class="option">	<!-- affecter une salle au lot -->
			<?PHP if ( $E_chk ) { ?>
				<span id='affect_selection'><a href='#' onclick='toggle_affectsalle();'><img src="<?PHP echo ICONSPATH . "refresh.png";?>" title="Affectation directe à une salle"></a></span>
				<div id='affect_box'>
					<form action="gestion_inventaire/post_materiels.php?action=affect" method="post" name="post_form" id="post_form" >
						<input type=hidden name='materiel_a_poster' id='materiel_a_poster' value=''>	

					<?PHP 
						echo "<select name=salle_select id=salle_select>";
				
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
						echo "<input type=submit value='Affecter' >";
					?>

					</form>
				</div>
			<?PHP } ?>
		</span>
						
		<span class="option">	<!-- Affichage des colonnes -->		
			<a href='#' onclick='showhide_options();'><img src="<?PHP echo ICONSPATH . "eye.png";?>" title="colonnes à montrer ou à cacher"></a>
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
	

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	$filtre = $_GET['filter'];
	$filter_explode_exclusion = @explode ("/", $filtre);
	$value_like_tab 	= $filter_explode_exclusion[0];	// Pour le filtre d'inclusion
	$value_notlike_tab 	= $filter_explode_exclusion[1]; // Pour le filtre d'exclusion


	/**************************
	* 	PARTIE INCLUSION
	***************************/
	
	$filter_explode_like = @explode ("+", $value_like_tab);
	$curseur_like = 1;
	
	
	foreach ( $filter_explode_like as $value_like) {
	
		// Si la valeur du champ est renseignée on l'intègre à la requête
		if ( $value_like <> "" ) {
			
			$value_like_explode = @explode (":", $value_like);
			$value_inc 			= trim($value_like_explode[0]);
			$champ_inc 			= trim($value_like_explode[1]);
			
			
			// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
			if ( !isset($champ_inc)  || $champ_inc == "") $champ_inc = -1;
			
			switch ($champ_inc) {
				case "t" :	$like .= "(mat_nom LIKE '%$value_inc%' OR user_nom LIKE '%$value_inc%' OR mat_dsit LIKE '%$value_inc%' OR mat_serial LIKE '%$value_inc%' OR mat_origine LIKE '%$value_inc%' OR mat_etat LIKE '%$value_inc%' OR marque_type LIKE '%$value_inc%' OR marque_stype LIKE '%$value_inc%' OR marque_marque LIKE '%$value_inc%' OR marque_model LIKE '%$value_inc%' OR salle_nom LIKE '%$value_inc%')";	break;
				case "n" :	$like .= "mat_nom LIKE '%$value_inc%'";			break;
				case "p" :	$like .= "user_nom LIKE '%$value_inc%'";		break;
				case "d" :	$like .= "mat_dsit LIKE '%$value_inc%'";		break;
				case "s" :	$like .= "mat_serial LIKE '%$value_inc%'";		break;
				case "e" :	$like .= "mat_etat LIKE '%$value_inc%'";		break;
				case "f" :	$like .= "marque_type LIKE '%$value_inc%'";		break;
				case "sf" :	$like .= "marque_stype LIKE '%$value_inc%'";	break;
				case "m" :	$like .= "marque_marque LIKE '%$value_inc%'";	break;
				case "mo" :	$like .= "marque_model LIKE '%$value_inc%'";	break;
				case "sa" :	$like .= "salle_nom LIKE '%$value_inc%'";		break;
				case "o" :	$like .= "mat_origine LIKE '%$value_inc%'";		break;
				default :	$like .= "mat_nom LIKE '%$value_inc%'";			break;
			}
			
			// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
			if ( $curseur_like <> count($filter_explode_like) ) {
				$like .= " AND ";
			}
			
			$curseur_like++;
		}
			
	}
	
	
	/**************************
	* 	PARTIE EXCLUSION
	***************************/
	
	$filter_explode_notlike = @explode ("+", $value_notlike_tab);
	$curseur_notlike = 1;
		
		
	foreach ( $filter_explode_notlike as $value_notlike) {
	
		// Si la valeur du champ est renseignée on l'intègre à la requête
		if ( $value_notlike <> "" ) {
			
			$value_notlike_explode = @explode (":", $value_notlike);
			$value_exc 			= trim($value_notlike_explode[0]);
			$champ_exc 			= trim($value_notlike_explode[1]);
			
			
			// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
			if ( !isset($champ_exc)  || $champ_exc == "") $champ_exc = -1;
			
			switch ($champ_exc) {
				case "t" :	$notlike .= "(mat_nom NOT LIKE '%$value_exc%' OR user_nom NOT LIKE '%$value_exc%' OR mat_dsit NOT LIKE '%$value_exc%' OR mat_serial NOT LIKE '%$value_exc%' OR mat_origine NOT LIKE '%$value_exc%' OR mat_etat NOT LIKE '%$value_exc%' OR marque_type NOT LIKE '%$value_exc%' OR marque_stype NOT LIKE '%$value_exc%' OR marque_marque NOT LIKE '%$value_exc%' OR marque_model NOT LIKE '%$value_exc%' OR salle_nom NOT LIKE '%$value_exc%')";	break;
				case "n" :	$notlike .= "mat_nom NOT LIKE '%$value_exc%'";			break;
				case "p" :	$notlike .= "user_nom NOT LIKE '%$value_exc%'";			break;
				case "d" :	$notlike .= "mat_dsit NOT LIKE '%$value_exc%'";			break;
				case "s" :	$notlike .= "mat_serial NOT LIKE '%$value_exc%'";		break;
				case "e" :	$notlike .= "mat_etat NOT LIKE '%$value_exc%'";			break;
				case "f" :	$notlike .= "marque_type NOT LIKE '%$value_exc%'";		break;
				case "sf" :	$notlike .= "marque_stype NOT LIKE '%$value_exc%'";		break;
				case "m" :	$notlike .= "marque_marque NOT LIKE '%$value_exc%'";	break;
				case "mo" :	$notlike .= "marque_model NOT LIKE '%$value_exc%'";		break;
				case "sa" :	$notlike .= "salle_nom NOT LIKE '%$value_exc%'";		break;
				case "o" :	$notlike .= "mat_origine NOT LIKE '%$value_exc%'";		break;
				default :	$notlike .= "mat_nom NOT LIKE '%$value_exc%'";			break;
			}
		}
		
		// Si ce n'est pas le dernier élément du tableau on rajoute " AND " sinon on ne rajoute rien			
		if ( $curseur_notlike <> count($filter_explode_notlike) ) {
			$notlike .= " AND ";
		}
		
		$curseur_notlike++;
			
	}
	
	
	/**************************
	* 		PARTIE JONCTION
	***************************/
	
	// permet de mettre la particule "AND" dans le cas ou le filtre d'exclusion existe
	if ( $value_like_tab <> "" && $value_notlike_tab <> "" ) $jonction = " AND ";

	
	//echo $like . $jonction . $notlike;
	
		
	if ( $_GET['filter'] <> '' ) {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND $like $jonction $notlike) $orderby" );
		echo "<script>$('#filtercount').html('" . count($liste_des_materiels) . "');</script>";
	}
	else {
		$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) $orderby" );
		echo "<script>$('#filtercount').html('" . count($liste_des_materiels) . "');</script>";
	}
	
?>
	
	

	<table class="bigtable alternate hover" id="mat_table">
		<!-- Entêtes du tableau des matériels. On gère ici le tri.-->
		<?PHP if ( $E_chk ) echo "<th> <input type=checkbox id=checkall onclick=\"checkall('mat_table');\" > </th>"; ?>
		
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
				
					/*	chckbox	*/	if ( $E_chk ) echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
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
						/*	suppr	*/	echo "<td class='buttons'><a href='#' onclick=\"javascript:validation_suppr_materiel('$id', '$model', '$nom', this.parentNode.parentNode.rowIndex, $id_pret);\">	<img src='" . ICONSPATH . "delete.png' title='supprimer $nom'>	</a> </td>";
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
			}, 1000 );
		});
		
	});
	

	/*window.addEvent('domready', function(){

		

		// AJAX		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					window.setTimeout("document.location.href='index.php?page=materiels&filter=" + $('filt').value + "'", 1500);			
				}
			
			}).send(this.toQueryString());
		}); 

		
	});*/
	
		
	// *********************************************************************************
	//
	//			Fonction de validation de la suppression d'un matériel
	//
	// *********************************************************************************
		function validation_suppr_materiel (id, model, nom, row, id_pret) {
		
		if (id_pret == 0) {
		
			var valida = confirm('Voulez vous supprimer le matériel ' + nom + ' de modèle ' + model + " ?");
			
			// si la réponse est TRUE ==> on lance la page post_materiels.php
			if (valida) {
				
				/* On déselectionne toutes les coches */
				select_cette_ligne ( id, row, 0 );
				
				$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
				$('target').load("gestion_inventaire/post_materiels.php?action=suppr&id=" + id);
				window.setTimeout("document.location.href='index.php?page=materiels&filter=" + $('filt').value + "'", 1500);
				
			}
			
		} else {
			
			alert('Cette machine est prêtée ! Rendez-la avant la suppression !');
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

		var chaine_id = $('materiel_a_poster').value;
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
						nb_selectionnes.innerHTML = "[" + (table_id.length-1) + "] sélectionné(s)";	// On entre le nombre de machines sélectionnées	
					}
				break;
				
				case 0: // On force la déselection
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						nb_selectionnes.innerHTML = " [" + (table_id.length-1) + "] sélectionné(s)";	 // On entre le nombre de machines sélectionnées			
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					}
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						nb_selectionnes.innerHTML = " [" + (table_id.length-1) + "] sélectionné(s)";	 // On entre le nombre de machines sélectionnées			

						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = " [" + (table_id.length-1) + "] sélectionné(s)";	// On entre le nombre de machines sélectionnées	
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			$('materiel_a_poster').value = table_id.join(";");
			

			if ( $('materiel_a_poster').value != "" ) {
				$('modif_selection').setStyle("display","inline");
				$('rename_selection').setStyle("display","inline");
				$('affect_selection').setStyle("display","inline");
				nb_selectionnes.setStyle("display","inline");
			} else { 
				$('modif_selection').setStyle("display","none");
				$('rename_selection').setStyle("display","none");
				$('affect_selection').setStyle("display","none");
				nb_selectionnes.setStyle("display","none");
			}
		}
	}

		
	// *********************************************************************************
	//
	//			Modifie à la volée l'affectation dans la salle
	//
	// *********************************************************************************	
	 	
	function change_affectation_salle ( salleid ) {
		var table = document.getElementById("mat_table");
		
		var salle_selected_id = document.getElementById('salle_select').selectedIndex;
		var salle_selected_text = document.getElementById('salle_select').options[salle_selected_id].text;	


		for (var r = 1; r < table.rows.length; r++){
			if ( document.getElementsByName("chk")[r-1].checked == true ) {
				
				var lg = "<a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=" + salleid + "' rel='slb_mat' title='Membres de la salle " + salle_selected_text + "'>" + salle_selected_text + "</a>";
				// On change le texte de la salle par la nouvelle affectation
				document.getElementById('mat_table').rows[r].cells[6].innerHTML = lg;
			}
		}
	}
	

	
	// *********************************************************************************
	//
	//			Montre ou masque des colonnes
	//
	// *********************************************************************************	
	
	function hidethem (col_name, show) {
	
		if ( show == true)
			var state = "";
		else var state = "none";
	
		$("." + col_name).each(function(item) {
			item.css("display", state);
		})
		
		// On ajuste la taille de la barre d'entête
		//$("entete-materiels").style.width = $("contenu").getStyle('width');
	}
	
	
	function post_modif_entete () {
		$('target').load("gestion_inventaire/post_materiels.php?action=entetes&value=" + etat_entetes() );
	}
	
	// *********************************************************************************
	//
	//			Montre ou masque les options d'affichage de la page
	//
	// *********************************************************************************	
	
	function showhide_options() {
		if ( $('options_colonnes').style.display == 'block' ) {
			$('options_colonnes').style.display = 'none'; 
		} else {
			$('options_colonnes').style.display = 'block';
		}
	}
	
	// *********************************************************************************
	//
	//			Montre ou masque l'affecation directe à une salle
	//
	// *********************************************************************************	
	
	function toggle_affectsalle() {
		if ( $('affect_box').style.display == 'block' ) {
			$('affect_box').style.display = 'none'; 
		} else {
			$('affect_box').style.display = 'block';
		}
	}	
	
	
	// *********************************************************************************
	//
	//			Tri ORDERBY
	//
	// *********************************************************************************	
	
	function order_by (tri, phrase) {
		document.location.href="index.php?page=materiels&tri=" + tri + "&filter=" + phrase;
	}
	
	
	// *********************************************************************************
	//
	//		On retourne la liste des états des entêtes (colonne montrée ou masquée)
	//
	// *********************************************************************************	
	
	function etat_entetes () {
		
		var liste = "";
	
		$$('.opt_entete').each(function(item) {
			if ( item.checked )
				liste += "1";
			else 
				liste += "0";
		});	
				
		return liste;
	}
	
	// *********************************************************************************
	//
	//		initialisation des cases à cocher et de l'état des colonnes (hide/show)
	//
	// *********************************************************************************	
	
	function init_entetes (value) {
		
		if (value.substr(0, 1) == "1") {$('chk_pret').checked = true; hidethem('.td_pret', true);} 
		else {$('chk_pret').checked = false; hidethem('.td_pret', false);}
		
		if (value.substr(1, 1) == "1") {$('chk_dsit').checked = true; hidethem('.td_dsit', true);} 
		else {$('chk_dsit').checked = false; hidethem('.td_dsit', false);}
		
		if (value.substr(2, 1) == "1") {$('chk_serial').checked = true; hidethem('.td_serial', true);} 
		else {$('chk_serial').checked = false; hidethem('.td_serial', false);}
		
		if (value.substr(3, 1) == "1") {$('chk_etat').checked = true; hidethem('.td_etat', true);} 
		else {$('chk_etat').checked = false; hidethem('.td_etat', false);}
		
		if (value.substr(4, 1) == "1") {$('chk_type').checked = true; hidethem('.td_type', true);} 
		else {$('chk_type').checked = false; hidethem('.td_type', false);}
		
		if (value.substr(5, 1) == "1") {$('chk_stype').checked = true; hidethem('.td_stype', true);} 
		else {$('chk_stype').checked = false; hidethem('.td_stype', false);}
		
		if (value.substr(6, 1) == "1") {$('chk_modele').checked = true; hidethem('.td_modele', true);} 
		else {$('chk_modele').checked = false; hidethem('.td_modele', false);}
		
		if (value.substr(7, 1) == "1") {$('chk_marque').checked = true; hidethem('.td_marque', true);} 
		else {$('chk_marque').checked = false; hidethem('.td_marque', false);}
		
		if (value.substr(8, 1) == "1") {$('chk_salle').checked = true; hidethem('.td_salle', true);} 
		else {$('chk_salle').checked = false; hidethem('.td_salle', false);}
	
		if (value.substr(9, 1) == "1") {$('chk_origine').checked = true; hidethem('.td_origine', true);} 
		else {$('chk_origine').checked = false; hidethem('.td_origine', false);}
	}
	
	// On initialise tout le bazar d'entête en cochant les checkbox et en cachant/montrant les colonnes
	init_entetes ('<?PHP echo $_SESSION['entetes'];?>');
		
</script>
