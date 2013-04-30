	<!-- 

		Liste des membres d'une origine particulière

	-->


<?PHP

	// lib
	include_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');
	
	
	// id ocs du matériel à afficher
	$origine = $_GET ['origine'];

	// cnx à la base de données OCS
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, marque_type, marque_marque, marque_model, mat_id, salle_nom FROM materiels, marques, salles WHERE materiels.salle_id=salles.salle_id AND mat_origine='$origine' AND materiels.marque_id = marques.marque_id order by mat_nom" );

	if (count($liste_des_materiels) <1 ) {
		echo "<br><h3>Pas de matériel pour cette origine ! </h3>"; exit();
	}

	echo "<p><small>" . count($liste_des_materiels) . " matériel(s) avec l'origine $origine.</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'famille', 'marque', 'modele', 'salle'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
?>


<center>
	
	<table id="myTable" class='alternate smalltable'>
		<th>Nom</th>
		<th>DSIT</th>
		<th>Serial</th>
		<th>Famille</th>
		<th>Marque</th>
		<th>Modèle</th>
		<th>Salle</th>
		
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
					$famille	= $record['marque_type'];
					$marque		= $record['marque_marque'];
					$model 		= $record['marque_model'];
					$id 		= $record['mat_id'];
					$salle 		= $record['salle_nom'];
					
					echo "<td> $nom </td>";
					echo "<td> $dsit </td>";
					echo "<td> $serial </td>";
					echo "<td> $famille </td>";
					echo "<td> $marque </td>";
					echo "<td> $model </td>";
					echo "<td> $salle </td>";
				echo "</tr>";
				
				// On constitue le fichier CSV de l'extraction
				fputcsv($fp, array($nom, $dsit, $serial, $famille, $marque, $model, $salle), ',');
				
				$compteur++;
			}
			
			fclose($fp);
		?>		

	</table>
	
	</center>
	
	<br>
	
<?PHP

	// On se déconnecte de la db
	$con_gespac->Close();


?>
