	<!-- 



		Liste des membres du grade 




	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');		// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	
	
	// id ocs du matériel à afficher
	$grade_id = $_GET ['grade_id'];


	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_users = $db_gespac->queryAll ( "SELECT user_nom, user_logon, user_mail, user_skin, user_accueil, user_mailing FROM users WHERE grade_id=$grade_id" );

	echo "<p><small>" . count($liste_des_users) . " utilisateur(s) dans ce grade.</small></p>";
	
	$fp = fopen('../dump/extraction.csv', 'w+');	//Ouverture du fichier
	fputcsv($fp, array('nom', 'login', 'mail', 'skin', 'accueil', 'mailing'), ',' );	// ENTETES
	echo "<center><a href='./dump/extraction.csv' target=_blank>fichier CSV</a></center><br>";
?>


<center>
	
	<table id="myTable" width=620>
		<th>Nom</th>
		<th>Login</th>
		<th>Mail</th>
		<th>Skin</th>
		<th>Accueil</th>
		<th>Mailing</th>
		
		<?PHP	
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_users as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
				echo "<tr class=$tr_class>";
						
					$nom 		= $record[0];
					$login 		= $record[1];
					$mail 		= $record[2];
					$skin 		= $record[3];
					$accueil 	= $record[4];
					$mailing 	= $record[5];
					
					echo "<td> $nom </td>";
					echo "<td> $login </td>";
					echo "<td> $mail </td>";
					echo "<td> $skin </td>";
					echo "<td> $accueil </td>";
					echo "<td> $mailing </td>";
				echo "</tr>";
				
				// On constitue le fichier CSV de l'extraction
				fputcsv($fp, array($nom, $login, $mail, $skin, $accueil, $mailing), ',');
				
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
