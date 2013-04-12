<div class="entetes" id="entete-importocs">	

	<span class="entetes-titre">IMPORT BASE OCS<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Cette page permet d'importer les données de la base OCS vers GESPAC. Très bien quand on part de zéro.<br>Attention ! Si votre base gespac est plutôt à jour, merci d'utiliser l'import CSV.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_marques.php?height=300&width=640&id=-1' rel='slb_marques' title='Ajout d une marque'><img src='img/icons/add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'table_ocs');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<h3><center>Attention ! Si votre base gespac est plutôt à jour, merci d'utiliser l'import CSV.</center></h3>

<hr>

<script type="text/javascript">
	
	// Fonction de validation de l'import de la base OCS
	function validation () {
	
		var valida = confirm('Voulez-vous vraiment importer la base OCS dans la base GESPAC ?');
		
		// si la réponse est TRUE ==> on lance la page import_ocs_db.php
		if (valida) {
			$('conteneur').load('gestion_donnees/import_ocs_db.php');
		}
	}

</script>



<?PHP
	
	
	// On regarde si la base OCS existe car dans le cas de sa non existance la page ne s'affiche pas
	$link_bases = mysql_pconnect('localhost', 'root', $pass);	//connexion à la base de donnée
	
	if(!mysql_select_db('ocsweb', $link_bases)) { //si la base OCS n'existe pas on arrete la page
		echo "Base OCS non présente, il est impossible de continuer l'import."; 
	}
	else {
		// cnx gespac
		$con_gespac = new Sql($host, $user, $pass, $gespac);
		
		$liste_ssn	= $con_gespac->QueryAll ( "select mat_serial FROM materiels" );
		
		$liste_ssn_gespac = array();	// on initialise la variable comme un tableau
		foreach ($liste_ssn as $row) { array_push ($liste_ssn_gespac, $row['mat_serial']);	} // On remplit le nouveau tableau avec un seul array et pas 2.
		array_unique ($liste_ssn_gespac); // je rends unique le tableau de ssn, inutile de multiplier les clés pour la recherche
		
		
		// cnx ocs
		$con_ocs = new Sql($host, $user, $pass, $ocsweb);

		// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
		$liste_hardware = $con_ocs->QueryAll ( "select hardware.id as id, name, smanufacturer, smodel, ssn, macaddr, speed from hardware, bios, networks where hardware.id=bios.hardware_id AND hardware.id=networks.hardware_id ORDER BY name" );
		$liste_serial = $con_ocs->QueryAll ( "select ssn from bios" );


		// stockage des lignes retournées par sql dans un tableau (je ne récupère que les écrans avec un serial non vide)
		$liste_monitors = $con_ocs->QueryAll ( "SELECT DISTINCT serial, hardware_id, manufacturer, caption, name from hardware, monitors WHERE serial <> '' AND hardware.id = hardware_id;" );
		
		$nb_materiels = count($liste_hardware) + count($liste_monitors);
		$nb_materiels_dans_gespac = 0;
		

		$liste_hardware_array = array();	// on initialise la variable comme un tableau
		foreach ($liste_hardware as $row) { array_push ($liste_hardware_array, $row['ssn']);	} // On remplit le nouveau tableau avec un seul array et pas 2. $row[4] correspond au ssn ocs
			
		$liste_doublons = array_unique(array_diff_assoc($liste_hardware_array, array_unique($liste_hardware_array))); // on extrait la liste des doublons dans un tableau
		
	?>
		<!--	C'est le lien permettant de lancer l'import de la base	-->
		<p><center><a href="#" onclick="validation();">Lancer la procédure d'importation</a></center></p>

		<?PHP		
			echo "<b>$nb_materiels</b> éléments dans la base OCS";
			echo "<span id='nb_materiels'></span>";	
		?>
		
		<br><br>
		<table align=center id='table_ocs'>
			<th>id</th>
			<th>name</th>
			<th>manufacturer</th>
			<th>model</th>
			<th>ssn</th>
			<th>mac</th>
			

			<?PHP	

			$compteur = 0;
			
			// On parcourt le tableau pour les matériels
			foreach ($liste_hardware as $record ) {
						
				$id 			= $record['id'];
				$name 			= $record['name'];
				$manufacturer 	= $record['smanufacturer'];
				$model 			= $record['smodel'];
				$ssn 			= $record['ssn'];
				$mac 			= $record['macaddr'];
				$speed 			= $record['speed'];
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
							
				// si le num de série est déjà dans gespac, on met le ssn en vert
				if ( in_array($ssn, $liste_ssn_gespac) ) {
						$tr_class = "tr_doublon";
						$nb_materiels_dans_gespac++;					
				}
				
				// si le num de série est dans la liste des doublons on le passe en jaune
				if ( in_array($ssn, $liste_doublons) ) {
						$tr_class = "tr_doublon_ocs";
				}
					
				
				echo "<tr class=$tr_class>";
						
					switch ($record['smodel']) {
						case "8189M7G" 	: $model = "Think Centre"; 	break;
						case "8307LG9" 	: $model = "NetVista"; 		break;
						default 		: $model = $record['smodel'];		break;
					}

					echo "<td>" . $id . "</td>";
					echo "<td>" . $name . "</td>";
					echo "<td>" . $manufacturer . "</td>";
					echo "<td>" . $model . "</td>";
					echo "<td>" . $ssn . "</td>";			
					echo "<td>" . $mac . "</td>";			
					
				echo "</tr>";
				
				$compteur++;
			}

			$compteur = 0;
			// On parcourt le tableau pour les moniteurs
			foreach ($liste_monitors as $record ) {
				
				$ssn = $record['serial'];
				$id   = $record['hardware_id'];
				$manufacturer = $record['manufacturer'];
				$model = $record['caption'];
				$name = "Ecran_de_" . $record['name']; 
				
				// si le num de série est déjà dans gespac, on met le ssn en vert
				if ( in_array($ssn, $liste_ssn_gespac) ) {
						$tr_class = "tr_doublon";
						$nb_materiels_dans_gespac++;
				} else {
					// alternance des couleurs
					$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
				}
				
				echo "<tr class=$tr_class>";

					echo "<td>" . $id . "</td>";
					echo "<td>" . $name . "</td>";
					echo "<td>" . $manufacturer . "</td>";
					echo "<td>" . $model . "</td>";
					echo "<td>" . $ssn . "</td>";			
					echo "<td> NA </td>";			
				
				echo "</tr>";
				
				$compteur++;
				
			}
			
			?>

		</table>

		<?PHP
		}
		?>
	
	<script>$("nb_materiels").innerHTML = "<?PHP echo ' (<span style=\"background-color:yellow\"><b>' . count($liste_doublons). "</b></span> doublons).  <b><span style='background-color:#29C920'>" .$nb_materiels_dans_gespac . "</b> déjà présents dans GESPAC.</span>"; ?>";</script>

<script>
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
				
		for (var r = 1; r < table.rows.length; r++){ // pour chaque ligne du tableau
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
