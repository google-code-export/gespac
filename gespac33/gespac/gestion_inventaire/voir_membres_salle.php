	<!-- 



		Liste des membres de la salle 




	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');		// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	
	
	// id ocs du matériel à afficher
	$salle_id = $_GET ['salle_id'];


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
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_model, marque_type, mat_id, user_nom FROM materiels, marques, users WHERE materiels.user_id=users.user_id AND materiels.salle_id=$salle_id AND materiels.marque_id = marques.marque_id order by mat_nom" );

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
						
					$nom 		= $record[0];
					$dsit 		= $record[1];
					$serial 	= $record[2];
					$etat 		= $record[3];
					$model 		= $record[4];
					$type 		= $record[5];
					$id 		= $record[6];
					$user 		= $record[7];
					
					//echo "<td> <a href='gestion_inventaire/voir_materiel_ocs.php?height=480&width=640&mat_nom=$nom' rel='sexylightbox' title='caractéristiques de $nom'>$nom</a> </td>";
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
	
	<br>
	
<?PHP

	// On se déconnecte de la db
	$db_gespac->disconnect();


?>
