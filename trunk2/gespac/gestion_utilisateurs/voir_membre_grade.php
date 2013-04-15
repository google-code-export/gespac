	<!-- 



		Liste des membres du grade 




	-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	
	// id ocs du matériel à afficher
	$grade_id = $_GET ['grade_id'];

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_users = $con_gespac->QueryAll ( "SELECT user_nom, user_logon, user_mail, user_skin, user_accueil, user_mailing FROM users WHERE grade_id=$grade_id" );

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
						
					$nom 		= $record['user_nom'];
					$login 		= $record['user_logon'];
					$mail 		= $record['user_mail'];
					$skin 		= $record['user_skin'];
					$accueil 	= $record['user_accueil'];
					$mailing 	= $record['user_mailing'];
					
					
					if ($mailing == 1) {
						$mailing_nom   = "Activé";
						$mailing_color = "#00DE00";
					} else {
						$mailing_nom   = "Désactivé";
						$mailing_color = "#EE0000";
					}
					
					echo "<td> $nom </td>";
					echo "<td> $login </td>";
					echo "<td> $mail </td>";
					echo "<td> $skin </td>";
					echo "<td> $accueil </td>";
					echo "<td><font color=$mailing_color><b> $mailing_nom </b></font></td>";
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
	$con_gespac->Close();


?>
