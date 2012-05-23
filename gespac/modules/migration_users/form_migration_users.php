<?PHP

/*

	Vérifier que le fichier existe
	
	si il existe, lancer la comparaison
	
	Sinon, mettre un message et un lien


*/

	session_start();
	
	/* fichier de visualisation des utilisateurs :
	
		view de la db gespac avec tous les users du parc
	*/
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-12#", $_SESSION['droits']);
	
	
?>


<h3>Formulaire de migration des utilisateurs pour architecture AD 2008</h3>


<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 


	// Le fichier migration_users_ad2008.csv existe t'il ?
	
	$handle = fopen("../../dump/migration_users_ad2008.csv", "r");

	
	if ($handle) {

		$row = 0;	// [AMELIORATION] penser à virer l'entête
		
		$csvfile = array();
		
		//array_push($csvfile, "apple", "raspberry");
		
		
					


		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			
			$line[$row][0] = $data[0];	
			$line[$row][1] = $data[1];			
			$line[$row][2] = $data[2];
			$line[$row][3] = $data[3];

			if ($line[$row][0] <> "NOM" )	{

				array_push($csvfile, array($line[$row][0], $line[$row][1],$line[$row][2],$line[$row][3]) );
				
			}

			$row++;
		}


		// cnx à la base de données GESPAC
		$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste_des_utilisateurs = $con_gespac->QueryAll ( "SELECT user_id, user_nom, user_logon FROM users WHERE user_logon<>'ati' ORDER BY user_nom" );

	?>
		
		<input type=hidden name='users_a_poster' id='users_a_poster' value=''>
		
		<center>
		<br>
		<table class="tablehover" id="migration_users_table" width=800>
			<th>Nom</th>
			<th>Logon</th>
			<th>Correspondance</th>
					
			<?PHP	
				
				$compteur = 0;
				// On parcourt le tableau
				foreach ( $liste_des_utilisateurs as $record ) {
					
					// alternance des couleurs
					$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
							
						$nom 			= $record['user_nom'];
						$logon 			= $record['user_logon'];
						$id				= $record['user_id'];
								

						echo "<tr id=tr_id$id class=$tr_class>";
						
							echo "<td> $nom </td>";
							echo "<td> $logon </td>";

							
							if ( $E_chk ) {
								
								echo "<td>
									<select>						
										<option>liste à remplir</option>
									</select>
								
								</td>";
									
							} else {
								echo "<td>&nbsp</td>";
							}
						
					echo "</tr>";
					
					$compteur++;
				}
			?>		

		</table>
		
		</center>
		
		
	<?PHP
		// On se déconnecte de la db
		$con_gespac->Close();
		
	}
	
	else {
	
		echo "Il faut poster (ou reposter ?) le fichier de migration ad2008";
				
	}
	?>
