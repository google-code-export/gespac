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
	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-12#", $_SESSION['droits']);
	
	
?>


<h3>Formulaire de migration des utilisateurs pour architecture AD 2008</h3><br>
<small><i>Selectionnez dans les listes déroulantes les professeurs correspondants. Les sûrs à 100%, je les ai déjà selectionnés ;) </i></small>

<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 

	// Le fichier migration_users_ad2008.csv existe t'il ?
	
	$handle = fopen("../../dump/migration_users_ad2008.csv", "r");

	
	if ($handle) {

		// cnx à la base de données GESPAC
		$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

		// Création d'une table temporaire
		$table_temp = $con_gespac->Execute("CREATE TABLE table_temp (nom VARCHAR( 255 ) NOT NULL ,prenom VARCHAR( 255 ) NOT NULL ,login VARCHAR( 255 ) NOT NULL ,pass VARCHAR( 255 ) NOT NULL) ENGINE = MYISAM ;");
	
	
		$row = 0;	// [AMELIORATION] penser à virer l'entête
		$csvfile = array();
		
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			
			$line[$row][0] = $data[0];	
			$line[$row][1] = $data[1];			
			$line[$row][2] = $data[2];
			$line[$row][3] = $data[3];

			if ($line[$row][0] <> "NOM" )	{
				$con_gespac->Execute ("INSERT INTO table_temp VALUES ('" . $line[$row][0] . "', '". $line[$row][1] ."', '".$line[$row][2]."', '".$line[$row][3]."') ;");
			}

			$row++;
		}


		$liste_csv = $con_gespac->QueryAll("SELECT nom, prenom, login, pass FROM table_temp;");

		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste_des_utilisateurs = $con_gespac->QueryAll ( "SELECT user_id, user_nom, user_logon FROM users WHERE user_logon<>'ati' ORDER BY user_nom" );

	?>
		<form method="POST" action="modules/migration_users/post_migration_users.php" id="post_form">
			
			<br><br>
			
			<center>
			
			<input type=submit value="migrer les comptes">
			
			
			<br><br>
			
	
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
										<select name='$id'>";
										
										echo "<option value='inconnu'> INCONNU </option>";
										
										foreach ($liste_csv as $line) {
											
											$nom_complet = $line['nom'] . " " . $line['prenom'];
											$login = $line['login'];
											
											if (strtoupper($nom_complet) == strtoupper($nom) ) {
												$selected = "selected";
											}
											else {
												$selected = "";
											}
											
										
											echo "<option value='$login' $selected > $nom_complet </option>";
										
										}

										echo"</select>								
									</td>";
										
								} else {
									echo "<td>&nbsp</td>";
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
		
		echo "</form>";
			
	}
	
	else {
	
		echo "Il faut poster (ou reposter ?) le fichier de migration ad2008";
				
	}
	?>

	
	<script>
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		if ($('post_form')) {
			$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
				new Event(e).stop();
				new Request({

					method: this.method,
					url: this.action,

					onSuccess: function(responseText, responseXML, filt) {
						$('target').set('html', responseText);
						$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
						window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_utilisateurs.php');", 1500);
					}
				
				}).send(this.toQueryString());
			});	
		}
		
	});
		
</script>
	