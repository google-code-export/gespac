	<!-- 

		Liste des membres d'un MODELE particulier

	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../includes.php');
	
	
	// libell� du type de marque r�cup�r� de la page voir_marques.php
	$marque_model = $_GET ['marque_model'];

	// cnx � la base de donn�es GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	

	// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id FROM materiels, marques WHERE materiels.marque_id = marques.marque_id AND marque_model='$marque_model' order by mat_nom" );

	echo "<p><small>" . count($liste_des_materiels) . " mat�riel(s).</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'dsit', 'serial', 'etat', 'marque', 'type', 'stype'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
?>


<center>
	
	<table id="myTable" width=620>
		<th>nom</th>
		<th>dsit</th>
		<th>serial</th>
		<th>etat</th>
		<th>marque</th>
		<th>type</th>
		<th>s/type</th>
		
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
					$etat 		= $record['mat_etat'];
					$marque		= $record['marque_marque'];
					$model 		= $record['marque_model'];
					$type 		= $record['marque_type'];
					$stype 		= $record['marque_stype'];
					$id 		= $record['mat_id'];
	
					//echo "<td> $nom </td>";
					echo "<td> $nom </td>";
					echo "<td> $dsit </td>";
					echo "<td> $serial </td>";
					echo "<td> $etat </td>";
					echo "<td> $marque </td>";
					echo "<td> $type </td>";
					echo "<td> $stype </td>";
				echo "</tr>";
				
				// On constitue le fichier CSV de l'extraction
				fputcsv($fp, array($nom, $dsit, $serial, $etat, $marque, $type, $stype), ',');
				
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
