
<h3>Importation de la base FOG</h3>

<hr>

		
<script type="text/javascript" src="server.php?client=all"></script>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">

	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
	
	// Fonction de validation de l'import de la base OCS
	function validation () {
	
		var valida = confirm('oula, vous allez importer la base FOG dans la base GESPAC ... ok ?');
		
		// si la réponse est TRUE ==> on lance la page import_ocs_db.php
		if (valida) {
			HTML_AJAX.replace('target', 'gestion_donnees/import_fog_db.php');
		}
	}

</script>



<?PHP

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	// adresse de connexion à la base de données
	$dsn_fog 	= 'mysql://'. $user .':' . $pass . '@localhost/fog';

	// cnx à la base de données OCS
	$db_fog 	= & MDB2::factory($dsn_fog);

	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	//$liste_hardware = $db_fog->queryAll ( "	SELECT hostID, hostName, iSysman, iSysproduct, iSysserial, iSystype FROM hosts, inventory WHERE hostID = iHostID;" );
	$liste_hardware = $db_fog->queryAll ( "	SELECT Distinct hostName FROM hosts;" );


?>
	<!--	C'est le lien permettant de lancer l'import de la base	-->
	<p><center><a href="#" onclick="validation();">Lancer la procédure d'importation</a></center></p>

	<?PHP		echo count($liste_hardware) . " élément(s) dans la table FOG";	?>

	<table width=800 align=center>
		<th>id</th>
		<th>name</th>
		<th>manufacturer</th>
		<th>model</th>
		<th>ssn</th>
		<th>Type</th>
		

		<?PHP	

		$compteur = 0;
		// On parcourt le tableau
		foreach ($liste_hardware as $record ) {
			
			$name = $record[0];	
			
			$hostID = $db_fog->queryAll ( "	SELECT hostID FROM hosts WHERE hostName='$name' ;" );
			$id	= $hostID[0][0];
			
			
			// alternance des couleurs
			$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
			
			echo "<tr class=$tr_class>";

			$inventory = $db_fog->queryAll ( "SELECT iSysman, iSysproduct, iSysserial, iSystype FROM inventory WHERE iHostID=$id;" );
			
			$manufacturer 	= $inventory[0][0];
			$model 			= $inventory[0][1];
			$ssn 			= $inventory[0][2];
			$type 			= $inventory[0][3];
				
				
				switch ($inventory[0][1]) {
					case "8189M7G" 	: $model = "Think Centre"; 		break;
					case "8307LG9" 	: $model = "NetVista"; 			break;
					case "HP Compaq 6715b (GU475EC#ABF)" 	: $model = "Compaq 6715b"; 			break;
					
					default 		: $model = $inventory[0][1];	break;
				}

				echo "<td>" . $id . "</td>";
				echo "<td>" . $name . "</td>";
				echo "<td>" . $manufacturer . "</td>";
				echo "<td>" . $model . "</td>";
				echo "<td>" . $ssn . "</td>";			
				echo "<td>" . $type . "</td>";			
			
			echo "</tr>";
			
			//$option_id++;
			$compteur ++;
			
		}
		?>

	</table>

<br>



<?PHP

// On se déconnecte de la db
$db_fog->disconnect();

?>
