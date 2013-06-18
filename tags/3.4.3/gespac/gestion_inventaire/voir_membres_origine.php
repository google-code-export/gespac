	<!-- 



		Liste des membres d'une origine particuli�re




	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../includes.php');
	
	
	// id ocs du mat�riel � afficher
	$origine = $_GET ['origine'];

	// cnx � la base de donn�es OCS
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);

	// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, marque_type, marque_marque, marque_model, mat_id, salle_nom FROM materiels, marques, salles WHERE materiels.salle_id=salles.salle_id AND mat_origine='$origine' AND materiels.marque_id = marques.marque_id order by mat_nom" );

	echo "<p><small>" . count($liste_des_materiels) . " mat�riel(s) avec l'origine $origine.</small></p>";
	
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
		<th>Mod�le</th>
		<th>Salle</th>
		
		<?PHP	
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_materiels as $record ) {
				// On �crit les lignes en brut dans la page html

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

	// On se d�connecte de la db
	$con_gespac->Close();


?>
