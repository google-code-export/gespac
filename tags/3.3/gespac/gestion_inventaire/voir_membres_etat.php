	<!-- 



		Liste des membres d'un état particulier 




	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');		// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	
	
	// id ocs du matériel à afficher
	$etat = $_GET ['etat'];


	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;


	// options facultatives de cnx à la db
	$options = array(
		'debug'       => 2,
		'portability' => MDB2_PORTABILITY_ALL,
	);

	// cnx à la base de données OCS
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, marque_type, marque_marque, marque_model, mat_id, salle_nom FROM materiels, marques, salles WHERE materiels.salle_id=salles.salle_id AND mat_etat='$etat' AND materiels.marque_id = marques.marque_id order by mat_nom" );

	echo "<p><small>" . count($liste_des_materiels) . " matériel(s) avec l'état $etat.</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'famille', 'marque', 'modele', 'salle'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
?>


<center>
	
	<table id="myTable" width=620>
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
					
					$nom 		= $record[0];
					$dsit 		= $record[1];
					$serial 	= $record[2];
					$famille	= $record[3];
					$marque		= $record[4];
					$model 		= $record[5];
					$id 		= $record[6];
					$salle 		= $record[7];
					
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
	$db_gespac->disconnect();


?>
