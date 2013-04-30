<?PHP
	session_start();

	/*
	 
		Page 02-02
	
		Visualisation des marques

	*/

	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-02-02#", $_SESSION['droits']);
	
		
?>


<div class="entetes" id="entete-marques">	

	<span class="entetes-titre">LES MARQUES<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des marques et modèles du parc.<br>Certaines marques sont issues de la table des correspondances et ne peuvent pas être modifiées.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_marques.php?height=300&width=640&action=add' class='editbox' title='Ajouter une marque'><img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'marque_table');" type="text" value=<?PHP echo $_GET['filter'];?>><span id="filtercount" title="Nombre de lignes filtrées"></span></form>
		</span>
	</span>

</div>

<div class="spacer"></div>

<?PHP 

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);
	
	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_marques = $con_gespac->QueryAll ( "SELECT marque_id, marque_type, marque_stype, marque_model, marque_marque FROM marques WHERE marque_suppr = 0 ORDER BY marque_type, marque_stype, marque_marque, marque_model" );

?>

	<p>
	
	<center>
	<table class="tablehover" id='marque_table' >
	
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
						
					
					echo "<td><input type=hidden class='nbmodel' value=$nb_matos_de_ce_modele><a href='gestion_inventaire/voir_membres-marque_type.php?maxheight=650&marque_type=$type' class='editbox' title='Liste des matériels de famille $type'>" . $type . "</a> [" . $nb_matos_de_ce_type ."] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_stype.php?maxheight=650&marque_stype=$soustype' class='editbox' title='Liste des matériels de sous famille $soustype'>" . $soustype . "</a> [" . $nb_matos_de_ce_soustype . "] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_marque.php?maxheight=650&marque_marque=$marque' class='editbox' title='Liste des matériels de marque $marque'>" . $marque . "</a> [" . $nb_matos_de_cette_marque . "] </td>";
					echo "<td><a href='gestion_inventaire/voir_membres-marque_model.php?maxheight=650&marque_model=$model' class='editbox' title='Liste des matériels de modèle $model'>" . $model . "</a> [" . $nb_matos_de_ce_modele ."] </td>";
					
					if ($E_chk) {
						echo "<td><a href='gestion_inventaire/form_ajout_materiel_par_marque.php?height=500&width=640&id=$id' rel='slb_marques' title='Formulaire d`ajout d`un materiel'><img src='" . ICONSPATH . "add3.png'> </a></td>";
						echo "<td><a href='gestion_inventaire/form_marques.php?height=250&width=640&id=$id' rel='slb_marques' title='Formulaire de modification de la marque $nom'><img src='" . ICONSPATH . "edit.png' style='display:$afficher_modifier'></a></td>";
						echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_marque($id, '$model', '$marque', this.parentNode.parentNode.rowIndex, '" . $nb_matos_de_ce_modele ."');\">	<img src='" . ICONSPATH . "delete.png'>	</a> </td>";
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	</center>
	
	<br>

<script type="text/javascript">

	// Filtre rémanent	
	filter ( $('#filt').val(), 'marque_table' );
	

	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression d'unew marque
	//
	// *********************************************************************************	

	function validation_suppr_marque (id, modele, marque, row, nb_de_suppr) {
		if (nb_de_suppr == 0) {
			var valida = confirm('Voulez-vous vraiment supprimer le modèle "' + modele + '" de marque "' + marque + '" ?');
		
			// si la réponse est TRUE ==> on lance la page post_marques.php
			if (valida) {
				$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
				$('target').load("gestion_inventaire/post_marques.php?action=suppr&id=" + id);
				window.setTimeout("document.location.href='index.php?page=marques&filter=" + $('filt').value + "'", 1500);			
			}
		} else {
			alert('IMPOSSIBLE de supprimer cette marque car des machines y sont associées !');
		}
	}

	
	
</script>

