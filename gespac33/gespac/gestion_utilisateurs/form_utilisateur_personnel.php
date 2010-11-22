<?PHP
session_start();
	
	#formulaire de modification de son propre compte

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

	$login =  $_SESSION['login'];

?>

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit 	  = $("post_user");
		var user_nom 	  = $("nom").value;
		var user_password = $("password").value;
		
		if (user_nom == "" || user_password == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('index.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>


<?PHP

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

		
		#***************************************************************************
		# 				MODIFICATION de l'utilisateur
		#***************************************************************************
		
		
		// Requete pour récupérer les données des champs pour le user à modifier
		$user_a_modifier = $db_gespac->queryAll ( "SELECT user_id, user_nom, user_logon, user_password, user_niveau, user_mail, user_skin, user_accueil, user_mailing FROM users WHERE user_logon='$login'" );		
		
		// valeurs à affecter aux champs
		$user_id 			= $user_a_modifier[0][0];
		$user_nom	 		= $user_a_modifier[0][1];
		$user_logon	 		= $user_a_modifier[0][2];
		$user_password 		= $user_a_modifier[0][3];
		$user_niveau	 	= $user_a_modifier[0][4];
		$user_mail 			= $user_a_modifier[0][5];
		$user_skin 			= $user_a_modifier[0][6];
		$user_accueil		= $user_a_modifier[0][7];
		$user_mailing		= $user_a_modifier[0][8];
		
		$checked = $user_mailing == 1 ? "checked" : "";
	
		echo "<h2>formulaire de modification de $user_nom</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_utilisateurs/post_utilisateur_personnel.php" method="post" name="post_form" id="post_form">
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $user_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>Password</TD> 
					<TD><input type=password name=password value= "<?PHP echo $user_password; ?>"	/></TD>
				</tr>
								
				<tr>
					<TD>Mail</TD> 
					<TD><input type=text name=mail value= "<?PHP echo $user_mail; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Mailing</TD> 
					<TD><input type=checkbox name=mailing <?PHP echo $checked;?>	/></TD>
				</tr>
				
				<tr>
					<td title="La modification sera visible à la prochaine connexion...">Skin</td>
					<td>
						<select name="skin">
						<?PHP
							$dossier = opendir("../skins");
							while ( $skin = readdir($dossier) ) {
								if ( $skin != "." && $skin != ".." && $skin != ".svn") {
									$selected = $skin == $user_skin ? "selected" : "" ;
									echo "<option $selected value=$skin>$skin</option>";
								}
							}
							closedir($dossier);
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<?PHP 
						$selected = $accueil == $user_accueil ? "selected" : "" ;
					?>
				
					<TD>Page de Démarrage</TD>
					<TD><select name="page" size="1">
							<option value="modules/stats/csschart.php">stats</option>
							<option value="modules/rss/rss.php">rss</option>
							<option value="gestion_demandes/voir_demandes.php">demandes</option>
							<option value="gestion_demandes/voir_interventions.php">interventions</option>
							<option value="modules/stats/utilisation_parc.php">utilisation parc</option>
							<option value="modules/wol/voir_liste_wol.php">WOL</option>
						</select>
					</TD>
				</tr>
				
			</table>
			
			<br>
			<input type=submit value='Modifier mon compte' >

			</center>

		</FORM>
		
		
<!--	DIV target pour Ajax	-->
<div id="target"></div>