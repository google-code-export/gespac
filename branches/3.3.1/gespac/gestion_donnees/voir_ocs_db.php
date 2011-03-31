
<h3>Importation de la base OCS</h3>

<hr>

		
<script type="text/javascript" src="server.php?client=all"></script>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

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

	header("Content-Type:text/html; charset=iso-8859-15" ); 	// règle le problème d'encodage des caractères
 
	include_once ('../config/databases.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	include_once ('../config/pear.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// On regarde si la base OCS existe car dans le cas de sa non existance la page ne s'affiche pas
	$link_bases = mysql_pconnect('localhost', 'root', $pass);//connexion à la base de donnée
	
	if(!mysql_select_db('ocsweb', $link_bases)) { //si la base OCS n'existe pas on arrete la page
		echo "Base OCS non présente, il est impossible de continuer l'import."; 
	}
	else {
		// cnx à la base de données
		$dsn_gespac		= 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;
		$db_gespac 		= & MDB2::factory($dsn_gespac);
		$liste_ssn		= $db_gespac->queryAll ( "select mat_serial FROM materiels" );
		
		$liste_ssn_gespac = array();	// on initialise la variable comme un tableau
		foreach ($liste_ssn as $row) { array_push ($liste_ssn_gespac, $row[0]);	} // On remplit le nouveau tableau avec un seul array et pas 2.
		array_unique ($liste_ssn_gespac); // je rends unique le tableau de ssn, inutile de multiplier les clés pour la recherche
		
		$db_gespac->disconnect();	// décnx

		
		// adresse de connexion à la base de données
		$dsn_ocs 	= 'mysql://'. $user .':' . $pass . '@localhost/' . $ocsweb;

		// cnx à la base de données
		$db_ocs 	= & MDB2::factory($dsn_ocs);

		// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
		//$liste_hardware = $db_ocs->queryAll ( "select hardware.id, name, smanufacturer, smodel, ssn, macaddr, speed from hardware, bios, networks where hardware.id=bios.hardware_id AND hardware.id=networks.hardware_id AND (networks.speed='100 Mb/s' OR networks.speed='1 Gb/s') ORDER BY name" );
		$liste_hardware = $db_ocs->queryAll ( "select hardware.id, name, smanufacturer, smodel, ssn, macaddr, speed from hardware, bios, networks where hardware.id=bios.hardware_id AND hardware.id=networks.hardware_id ORDER BY name" );
		$liste_serial = $db_ocs->queryAll ( "select ssn from bios" );


		// stockage des lignes retournées par sql dans un tableau (je ne récupère que les écrans avec un serial non vide)
		$liste_monitors = $db_ocs->queryAll ( "SELECT DISTINCT serial, hardware_id, manufacturer, caption, name from hardware, monitors WHERE serial <> '' AND hardware.id = hardware_id;" );
		
		$nb_materiels = count($liste_hardware) + count($liste_monitors);
		$nb_materiels_dans_gespac = 0;
		
		// On se déconnecte de la db
		$db_ocs->disconnect();
		

		$liste_hardware_array = array();	// on initialise la variable comme un tableau
		foreach ($liste_hardware as $row) { array_push ($liste_hardware_array, $row[4]);	} // On remplit le nouveau tableau avec un seul array et pas 2. $row[4] correspond au ssn ocs
			
		$liste_doublons = array_unique(array_diff_assoc($liste_hardware_array, array_unique($liste_hardware_array))); // on extrait la liste des doublons dans un tableau
		
	?>
		<!--	C'est le lien permettant de lancer l'import de la base	-->
		<p><center><a href="#" onclick="validation();">Lancer la procédure d'importation</a></center></p>

		<?PHP		
			echo "<b>$nb_materiels</b> éléments dans la base OCS";
			echo "<span id='nb_materiels'></span>";	
		?>
		
		<br><br>
		<table width=800 align=center>
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
						
				$id 			= $record[0];
				$name 			= $record[1];
				$manufacturer 	= $record[2];
				$model 			= $record[3];
				$ssn 			= $record[4];
				$mac 			= $record[5];
				$speed 			= $record[6];
				
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
						
					switch ($record[3]) {
						case "8189M7G" 	: $model = "Think Centre"; 	break;
						case "8307LG9" 	: $model = "NetVista"; 		break;
						default 		: $model = $record[3];		break;
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
				
				$ssn = $record[0];
				$id   = $record[1];
				$manufacturer = $record[2];
				$model = $record[3];
				$name = "Ecran_de_" . $record[4]; 
				
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
	
<br>



