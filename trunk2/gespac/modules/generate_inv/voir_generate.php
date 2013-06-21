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
			<form name="post_form" id="formulaire" action="modules/generate_inv/post_generate.php" method="post">
				<input type=hidden name='id_a_poster' id='id_a_poster' value=''>
				<input type=submit name='post_selection' id='post_form' value='générer' style='display:none;'>	<span id='nb_selectionnes'> [0] </span>			
			</form>
		</span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'generate_table');" type="text" value=<?PHP echo $_GET['filter'];?>><span id="filtercount" title="Nombre de lignes filtrées"></span></form>
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

	echo "<table class='bigtable hover' id='generate_table'>";
	
	echo "
		<th> <input type=checkbox id='checkall' > </th>
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

		echo "<tr id='tr_id$mat_id' class='tr_modif'>";
		
			echo "<td><input type=checkbox name=chk indexed=true id='$mat_id' value='$mat_id' class='chk_line'></td>";
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
					window.setTimeout("document.location.href='index.php?page=geninventaire&filter=" + $('#filt').val() + "'", 2500);
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
	filter ( $('#filt').val(), 'generate_table' );
	
</script>

