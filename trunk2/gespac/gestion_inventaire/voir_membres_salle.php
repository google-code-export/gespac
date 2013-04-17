	<!-- 
		Liste des membres de la salle 
	-->


<?PHP

	// lib
	include_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');
	
	
	// id ocs du matériel à afficher
	$salle_id = $_GET ['salle_id'];

	// cnx à la base de données OCS
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_model, marque_type, mat_id, user_nom FROM materiels, marques, users WHERE materiels.user_id=users.user_id AND materiels.salle_id=$salle_id AND materiels.marque_id = marques.marque_id order by mat_nom" );

	if (count($liste_des_materiels) <1 ) {
		echo "<br><h3>Pas de matériel dans cette salle ! </h3>"; exit();
	}
	
	echo "<p><small>" . count($liste_des_materiels) . " matériel(s) dans cette salle.</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'etat', 'modele', 'type'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
?>


<center>
	
	<table id="myTable" width=620>
		<th>Nom</th>
		<th>Prêt</th>
		<th>DSIT</th>
		<th>Serial</th>
		<th>Etat</th>
		<th>Modèle</th>
		<th>Type</th>
		
		<?PHP	
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
				echo "<tr class=$tr_class>";
						
					$nom 		= $record['mat_nom'];
					$dsit 		= $record['mat_dsit'];
					$serial 	= $record['mat_serial'];
					$etat 		= $record['mat_etat'];
					$model 		= $record['marque_model'];
					$type 		= $record['marque_type'];
					$id 		= $record['mat_id'];
					$user 		= $record['user_nom'];
					
					echo "<td> $nom </td>";
					echo "<td> $user </td>";
					echo "<td> $dsit </td>";
					echo "<td> $serial </td>";
					echo "<td> $etat </td>";
					echo "<td> $model </td>";
					echo "<td> $type </td>";
				echo "</tr>";
				
				// On constitue le fichier CSV de l'extraction
				fputcsv($fp, array($nom, $user, $dsit, $serial, $etat, $model, $type), ',');
				
				$compteur++;
			}
			
			fclose($fp);
		?>		

	</table>
	
	</center>