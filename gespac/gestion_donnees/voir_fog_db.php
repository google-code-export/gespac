
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
			$('target').load('gestion_donnees/import_fog_db.php');
		}
	}

</script>



<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

	// cnx gespac
	$con_fog = new Sql($host, $user, $pass, $fog);

	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_hardware = $con_fog->QueryAll ( "SELECT Distinct hostName FROM hosts;" );


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
			
			$name = $record['hostName'];	
			
			$id = $con_fog->QueryOne ( "SELECT hostID FROM hosts WHERE hostName='$name' ;" );
			
			// alternance des couleurs
			$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
			
			echo "<tr class=$tr_class>";

			$inventory = $con_fog->QueryAll ( "SELECT iSysman, iSysproduct, iSysserial, iSystype FROM inventory WHERE iHostID=$id;" );
			
			$manufacturer 	= $inventory[0]['iSysman'];
			$model 			= $inventory[0]['iSysproduct'];
			$ssn 			= $inventory[0]['iSysserial'];
			$type 			= $inventory[0]['iSystype'];
				
				
				switch ($inventory[0]['iSysproduct']) {
					case "8189M7G" 	: $model = "Think Centre"; 		break;
					case "8307LG9" 	: $model = "NetVista"; 			break;
					case "HP Compaq 6715b (GU475EC#ABF)" 	: $model = "Compaq 6715b"; 			break;
					
					default 		: $model = $inventory[0]['iSysproduct'];	break;
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
	$con_fog->Close();
?>
