<?PHP
	
	/* 
	 
	 Page 02-01
	  
	 fichier de visualisation de l'inventaire :
	
		view de la db gespac avec tous le matos du parc

		combobox filtre ajax pour n'avoir que les imprimantes, que les pc ... 
		Pour chaque matos :
		
			boutons visualisation pour avoir la fiche détaillée (éventuellement avec liste des demandes et des inters, liste des prets ...)
			bouton modification
			bouton suppression avec de belles confirmations
			bouton ajout, avec demande du type, du model et si on peux le préter
			mais checker si le materiel est unique ou pas !!!!!
	
		lors de l'ajout d'un nouveau matériel, penser à permettre l'affectation directe à une salle !
	
	*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	$E_chk = preg_match ("#E-02-01#", $_SESSION['droits']);
	
	if ( !isset($_SESSION['entetes']) ) $_SESSION['entetes'] = "0111001111";	// Cases à cocher par défaut
			
?>


<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP
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
	
	
	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$filter_explode_exclusion = @explode ("/", $_GET['filter']);
	$value_like 	= $filter_explode_exclusion[0];	// Pour le filtre d'inclusion
	$value_notlike 	= $filter_explode_exclusion[1]; // Pour le filtre d'exclusion
	
	// Si la valeur d'exclusion est renseingée on l'intègre à la requête
	if ( $value_like <> "" ) {
		$filter_explode_inclusion = @explode (":", $value_like);
		$value_inc 		= trim($filter_explode_inclusion[0]);
		$champ_num_inc 	= trim($filter_explode_inclusion[1]);
		
		
		// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
		if ( !isset($champ_num_inc)  || $champ_num_inc == "") $champ_num_inc = -1;
		
		switch ($champ_num_inc) {
			case 0 :	$like = "(mat_nom LIKE '%$value_inc%' OR user_nom LIKE '%$value_inc%' OR mat_dsit LIKE '%$value_inc%' OR mat_serial LIKE '%$value_inc%' OR mat_origine LIKE '%$value_inc%' OR mat_etat LIKE '%$value_inc%' OR marque_type LIKE '%$value_inc%' OR marque_stype LIKE '%$value_inc%' OR marque_marque LIKE '%$value_inc%' OR marque_model LIKE '%$value_inc%' OR salle_nom LIKE '%$value_inc%')";	break;
			case 1 :	$like = "mat_nom LIKE '%$value_inc%'";			break;
			case 2 :	$like = "user_nom LIKE '%$value_inc%'";			break;
			case 3 :	$like = "mat_dsit LIKE '%$value_inc%'";			break;
			case 4 :	$like = "mat_serial LIKE '%$value_inc%'";		break;
			case 5 :	$like = "mat_etat LIKE '%$value_inc%'";			break;
			case 6 :	$like = "marque_type LIKE '%$value_inc%'";		break;
			case 7 :	$like = "marque_stype LIKE '%$value_inc%'";		break;
			case 8 :	$like = "marque_marque LIKE '%$value_inc%'";	break;
			case 9 :	$like = "marque_model LIKE '%$value_inc%'";		break;
			case 10 :	$like = "salle_nom LIKE '%$value_inc%'";		break;
			case 11 :	$like = "mat_origine LIKE '%$value_inc%'";		break;
			default :	$like = "mat_nom LIKE '%$value_inc%'";			break;
		}
	}
	
	// permet de mettre la particule "AND" dans le cas ou le filtre d'exclusion existe
	if ( $value_like <> "" && isset($value_notlike) ) $jonction = " AND ";
		
	
	// Si la valeur d'exclusion est renseingée on l'intègre à la requête
	if ( isset($value_notlike) ) {
	
		$filter_explode_exclusion = @explode (":", $value_notlike);
		$value_exc 		= trim($filter_explode_exclusion[0]);
		$champ_num_exc 	= trim($filter_explode_exclusion[1]);
		
		// Si le champ numérique n'est pas renseigné, on lui affecte une valeur bidon pour tomber dans le cas "default"
		if ( !isset($champ_num_exc) || $champ_num_exc == "" ) $champ_num_exc = -1;
		
		switch ($champ_num_exc) {
			case 0 :	$notlike = "(mat_nom NOT LIKE '%$value_exc%' OR user_nom NOT LIKE '%$value_exc%' OR mat_dsit NOT LIKE '%$value_exc%' OR mat_serial NOT LIKE '%$value_exc%' OR mat_origine NOT LIKE '%$value_exc%' OR mat_etat NOT LIKE '%$value_exc%' OR marque_type NOT LIKE '%$value_exc%' OR marque_stype NOT LIKE '%$value_exc%' OR marque_marque NOT LIKE '%$value_exc%' OR marque_model NOT LIKE '%$value_exc%' OR salle_nom NOT LIKE '%$value_exc%')";	break;
			case 1 :	$notlike = "mat_nom NOT LIKE '%$value_exc%'";			break;
			case 2 :	$notlike = "user_nom NOT LIKE '%$value_exc%'";			break;
			case 3 :	$notlike = "mat_dsit NOT LIKE '%$value_exc%'";			break;
			case 4 :	$notlike = "mat_serial NOT LIKE '%$value_exc%'";		break;
			case 5 :	$notlike = "mat_etat NOT LIKE '%$value_exc%'";			break;
			case 6 :	$notlike = "marque_type NOT LIKE '%$value_exc%'";		break;
			case 7 :	$notlike = "marque_stype NOT LIKE '%$value_exc%'";		break;
			case 8 :	$notlike = "marque_marque NOT LIKE '%$value_exc%'";		break;
			case 9 :	$notlike = "marque_model NOT LIKE '%$value_exc%'";		break;
			case 10 :	$notlike = "salle_nom NOT LIKE '%$value_exc%'";			break;
			case 11 :	$notlike = "mat_origine NOT LIKE '%$value_exc%'";		break;
			default :	$notlike = "mat_nom NOT LIKE '%$value_exc%'";			break;
		}
	}
		
	if ( $_GET['filter'] <> '' ) {
		$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND $like $jonction $notlike) $orderby" );
		echo "<script>$('nb_filtre').innerHTML = '<small>[" . count($liste_des_materiels) . "]</small>';</script>";
	}
	else {
		$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) $orderby" );
		echo "<script>$('nb_filtre').innerHTML = ''</script>";
	}
	
?>
	
	
	<form action="gestion_inventaire/post_materiels.php?action=affect" method="post" name="post_form" id="post_form" >
	
		<!--------------------------------------------	LISTE DES ID A POSTER	------------------------------------------------>
		<input type=hidden name='materiel_a_poster' id='materiel_a_poster' value=''>	
		
		<span>
		
		<?PHP 
			if ( $E_chk ) {	// test de droit en écriture sur l'affectation de matériel, l'ajout de matériel et la modification par lot
		
				echo "<select name=salle_select id=salle_select>";
		
				// Pour le remplissage de la combobox des salles pour l'affectation
					
				// stockage des lignes retournées par sql dans un tableau nommé combo_des_salles
				$combo_des_salles = $db_gespac->queryAll ( "SELECT salle_id, salle_nom FROM salles ORDER BY salle_nom;" );
				
				foreach ($combo_des_salles as $combo_option ) {
				
					$option_id 		= $combo_option[0];
					$option_salle 	= $combo_option[1];
					
					//On colle par défaut la salle STOCK, donc ID = 1
					$defaut = $option_id == 1 ? "selected" : "";
					
					echo "<option value=$option_id $defaut> $option_salle </option>";
				}
			
			echo "</select>";
			echo "<input type=submit value='Affecter' >";
				
			}
			?>
			
		</span>
		
		
		<!-- Ajout d'un matériel et Modification par lot-->
		<?PHP
			if ( $E_chk ) {
				echo "<span style='float:right; margin-right:20px'><a href='gestion_inventaire/form_materiels.php?height=600&width=640&action=add' rel='slb_mat' title='ajout d un matériel'> <img src='img/add.png'>Ajouter un matériel </a></span>";
				echo "<span id='modif_selection' style='display:none; float:right; margin-right:20px'><a href='gestion_inventaire/form_materiels.php?height=200&width=640&action=modlot' rel='slb_mat' title='modifier selection'> <img src='img/write.png'>Modifier lot</a> <span id='nb_selectionnes'></span> </span>";
				echo "<span id='rename_selection' style='display:none; float:right; margin-right:20px'><a href='gestion_inventaire/form_materiels.php?height=180&width=640&action=renomlot' rel='slb_mat' title='renommer selection'> <img src='img/write.png'>Renommer lot</a> </span>";
			}
		?>
		
		
		<!-- Gestion de l'affichage des colonnes ici. -->	
						
		<a href='#' onclick='showhide_options();'><span id="options_label" style="font-size:small;">+ options</span></a>
		<a href="#basdepage"><img src="./img/down.png" title="Aller en bas de page"></a>
		<div id="options_colonnes">
			<input type="checkbox" class="opt_entete" id="chk_pret" onclick="hidethem('.td_pret', this.checked);post_modif_entete();" 		 	> Prêt &nbsp
			<input type="checkbox" class="opt_entete" id="chk_dsit" onclick="hidethem('.td_dsit', this.checked);post_modif_entete();" 		 	> DSIT &nbsp
			<input type="checkbox" class="opt_entete" id="chk_serial" onclick="hidethem('.td_serial', this.checked);post_modif_entete();" 	 	> Serial &nbsp
			<input type="checkbox" class="opt_entete" id="chk_etat" onclick="hidethem('.td_etat', this.checked);post_modif_entete();" 		 	> Etat &nbsp
			<input type="checkbox" class="opt_entete" id="chk_type" onclick="hidethem('.td_type', this.checked);post_modif_entete();" 			> Famille &nbsp
			<input type="checkbox" class="opt_entete" id="chk_stype" onclick="hidethem('.td_stype', this.checked);post_modif_entete();" 		> Sous Famille &nbsp
			<input type="checkbox" class="opt_entete" id="chk_modele" onclick="hidethem('.td_modele', this.checked);post_modif_entete();" 	 	> Modèle &nbsp
			<input type="checkbox" class="opt_entete" id="chk_marque" onclick="hidethem('.td_marque', this.checked);post_modif_entete();" 	 	> Marque &nbsp
			<input type="checkbox" class="opt_entete" id="chk_salle" onclick="hidethem('.td_salle', this.checked);post_modif_entete();"			> Salle &nbsp
			<input type="checkbox" class="opt_entete" id="chk_origine" onclick="hidethem('.td_origine', this.checked);post_modif_entete();"		> Origine &nbsp
		</div>
				
	</form>
	
	
	<center>
	
	<table class="tablehover" id="mat_table" width=870>
		<!-- Entêtes du tableau des matériels. On gère ici le tri.-->
		<th> <input type=checkbox id=checkall onclick="checkall('mat_table');" > </th>
		<th title="1 : le nom de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_nom; ?>', $('filt').value);">
			Nom<sup>1</sup> <?PHP echo $img_nom; ?></a></th>
			
		<th class="td_pret" style='display:none' title="2 : le nom du professeur à qui le matériel est prêté">Prêté à<sup>2</sup></th>
		
		<th class="td_dsit" title="3 : le numéro de série de la DSIT">
			<a href="#" onclick="order_by('<?PHP echo $tri_dsit; ?>', $('filt').value);">
			DSIT<sup>3</sup><?PHP echo $img_dsit; ?></a></th>
			
		<th class="td_serial" title="4 : le numéro de série de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_serial; ?>', $('filt').value);">
			Serial<sup>4</sup><?PHP echo $img_serial; ?></a></th>
			
		<th class="td_etat" title="5 : L'état général de la machine">
			<a href="#" onclick="order_by('<?PHP echo $tri_etat; ?>', $('filt').value);">
			Etat<sup>5</sup><?PHP echo $img_etat; ?></a></th>
			
		<th class="td_type" style='display:none' title="6 : Famille du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_type; ?>', $('filt').value);">
			Famille<sup>6</sup><?PHP echo $img_type; ?></a></th>
			
		<th class="td_stype" style='display:none' title="7 : Sous Famille du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_stype; ?>', $('filt').value);">
			Sous-famille<sup>7</sup> <?PHP echo $img_stype; ?></a></th>
			
		<th class="td_marque" title="8 : Marque du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_marque; ?>', $('filt').value);">
			Marque<sup>8</sup> <?PHP echo $img_marque; ?></a></th>
			
		<th class="td_modele" title="9 : Modèle du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_modele; ?>', $('filt').value);">
			Modèle<sup>9</sup> <?PHP echo $img_modele; ?></a></th>
			
		<th class="td_salle"  title="10 : Salle où est affecté le matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_salle; ?>', $('filt').value);">
			Salle<sup>10</sup> <?PHP echo $img_salle; ?></a></th>
			
		<th class='td_origine' title="11 : Propriétaire et année d'achat du matériel">
			<a href="#" onclick="order_by('<?PHP echo $tri_origine; ?>', $('filt').value);">
			Origine<sup>11</sup> <?PHP echo $img_origine; ?></a></th>
	
	<?PHP 
	
	if ( $E_chk ) {
		echo "<th>&nbsp</th>
		<th>&nbsp</th>";
	}
	
		

			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";

				$nom 		= $record[0];
				$dsit 		= $record[1];
				$serial 	= $record[2];
				$etat 		= $record[3];
				$marque		= $record[4];
				$model 		= $record[5];
				$type 		= $record[6];
				$stype		= $record[7];
				$id 		= $record[8];
				$salle 		= $record[9];
				$salle_id 	= $record[10];
				$origine 	= $record[11];
				$user	 	= $record[12];
			
				
				// test si la machine est prétée ou pas
				$rq_machine_pretee = $db_gespac->queryAll ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$id" );
				$mat_id = @$rq_machine_pretee[0][0];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
						
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
				
				echo "<tr id=tr_id$id class=$tr_class>";
					/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
					/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$nom&mat_ssn=$serial' rel='slb_mat' title='Nom du matériel : $nom'>$nom</a> </td>";
					/*	pret	*/	echo "<td class='td_pret' style='display:none'><font color=$font_color> $pret </font></td>";
					/*	dsit	*/	echo "<td class='td_dsit'> $dsit </td>";
					/*	serial	*/	echo "<td class='td_serial'> $serial </td>";
					/*	etat	*/	echo "<td class='td_etat'> <a href='gestion_inventaire/voir_membres_etat.php?height=480&width=640&etat=$etat' rel='slb_mat' title='Liste des materiels $etat'>$etat</a> </td>";
					/*	type	*/	echo "<td class='td_type' style='display:none'> <a href='gestion_inventaire/voir_membres-marque_type.php?height=480&width=720&marque_type=$type' rel='slb_mat' title='Liste de la famille $type'>$type</a></td>";
					/*	stype	*/	echo "<td class='td_stype' style='display:none'> <a href='gestion_inventaire/voir_membres-marque_stype.php?height=480&width=720&marque_stype=$stype' rel='slb_mat' title='Liste de la sous famille $stype'>$stype</a></td>";
					/*	marque	*/	echo "<td class='td_marque'> <a href='gestion_inventaire/voir_membres-marque_marque.php?height=480&width=720&marque_marque=$marque' rel='slb_mat' title='Liste de la marque $marque'>$marque</a></td>";
					/*	modele	*/	echo "<td class='td_modele' > <a href='gestion_inventaire/voir_membres-marque_model.php?height=480&width=720&marque_model=$model' rel='slb_mat' title='Liste du modèle $model'>$model</a></td>";
					/*	salle	*/	echo "<td class='td_salle'> <a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$salle_id' rel='slb_mat' title='Liste du matériel dans la salle $salle'>$salle</a> </td>";
					/*	origine	*/	echo "<td class='td_origine'> <a href='gestion_inventaire/voir_membres_origine.php?height=480&width=640&origine=$origine' rel='slb_mat' title='Liste du matériel ayant pour origine $origine'>$origine</a> </td>";
					
					if ( $E_chk ) {
						/*	modif	*/	echo "<td><a href='gestion_inventaire/form_materiels.php?height=400&width=640&action=mod&id=$id&mat_ssn=$serial' rel='slb_mat' title='Formulaire de modification du matériel $nom'><img src='img/write.png'> </a></td>";
						/*	suppr	*/	echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_materiel($id, '$model', '$nom', this.parentNode.parentNode.rowIndex, $id_pret);\">	<img src='img/delete.png' title='supprimer $nom'>	</a> </td>";
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		
		
	</table>
	</center>
	<!--	Ancre bas de page	-->
	<a name="basdepage"></a>
	<br>
	
	
<?PHP
	// On se déconnecte de la db
	$db_gespac->disconnect();
?>



<script type="text/javascript">

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";


    window.addEvent('domready', function() {

		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_mat'});

		// AJAX		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_inventaire/voir_materiels.php');", 1500);
				}
			
			}).send(this.toQueryString());
		}); 
    });
    

	
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
				
				/*	poste la page en ajax	*/
				$('target').load("gestion_inventaire/post_materiels.php?action=suppr&id=" + id);
				
				/*	supprimer la ligne du tableau	*/
				$('mat_table').deleteRow(row);
			}
			
		} else {
			
			alert('Cette machine est déjà prêtée ! Rendez-la avant la suppression !');
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
			

			if ( $('materiel_a_poster').value != "" ) {
				$('modif_selection').style.display = "";
				$('rename_selection').style.display = "";
			} else { 
				$('modif_selection').style.display = "none";
				$('rename_selection').style.display = "none";
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
	
		$$(col_name).each(function(item) {
			item.style.display = state;
		})
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
			$('options_label').innerHTML = "+ options";
		} else {
			$('options_colonnes').style.display = 'block';
			$('options_label').innerHTML = "- options";
		}
	}
	
	
	
	// *********************************************************************************
	//
	//			Tri ORDERBY
	//
	// *********************************************************************************	
	
	function order_by (tri, phrase) {
		$('tableau').load("gestion_inventaire/voir_materiels_table.php?tri=" + tri + "&filter=" + phrase);
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
