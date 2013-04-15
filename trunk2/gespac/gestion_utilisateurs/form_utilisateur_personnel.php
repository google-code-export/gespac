<?PHP

	session_start();

	$login =  $_SESSION['login'];

	#formulaire de modification de son propre compte


?>



<div class="entetes" id="entete-moncompte">	
	<span class="entetes-titre">MODIFIER MON COMPTE<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Permet de modifier le compte actuellement connecté.</div>
</div>

<div class="spacer"></div>


<?PHP

	if ( $login <> "ati" ) {

		// connexion à la base de données GESPAC
		$con_gespac = new Sql($host, $user, $pass, $gespac);

			
			#***************************************************************************
			# 				MODIFICATION de l'utilisateur
			#***************************************************************************
			
			
			// Requete pour récupérer les données des champs pour le user à modifier
			$user_a_modifier = $con_gespac->QueryRow ( "SELECT user_id, user_nom, user_logon, user_password, user_mail, user_skin, user_accueil, user_mailing FROM users WHERE user_logon='$login'" );
			
			// valeurs à affecter aux champs
			$user_id 			= $user_a_modifier[0];
			$user_nom	 		= $user_a_modifier[1];
			$user_logon	 		= $user_a_modifier[2];
			$user_password 		= $user_a_modifier[3];
			$user_mail 			= $user_a_modifier[4];
			$user_skin 			= $user_a_modifier[5];
			$user_accueil		= $user_a_modifier[6];
			$user_mailing		= $user_a_modifier[7];
			
			$checked = $user_mailing == 1 ? "checked" : "";
		
			echo "<h2>Formulaire de modification du compte $user_nom</h2><br>";
			
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
						<TD><input type=text size=30 name=nom id=nom value= "<?PHP echo $user_nom; ?>" 	/></TD>
					</tr>
					
					<tr>
						<TD>Password</TD> 
						<TD><input type=password size=30 name=password value= "<?PHP echo $user_password; ?>"	/></TD>
					</tr>
									
					<tr>
						<TD>Mail</TD> 
						<TD><input type=text name=mail size=30 value= "<?PHP echo $user_mail; ?>"	/></TD>
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
					
					<?PHP
						$lines = file('../menu.txt');

						echo "<option value='bienvenue.php'>Bienvenue</option>";	// Page par défaut
						
						
						// Requête de récupération des droits
						$liste_items = $con_gespac->QueryAll ( "SELECT * FROM droits order by droit_index" );
						
						foreach ($liste_items as $ligne) {
					
							$droit_id = $ligne ['droit_id'];
							$droit_index = $ligne ['droit_index'];
							$droit_titre = $ligne ['droit_titre'];
							$droit_page = $ligne ['droit_page'];
							$droit_etendue = $ligne ['droit_etendue'];
							$droit_description = $ligne ['droit_description'];

							$L_chk = preg_match ("#$droit_index#", $_SESSION['droits']) ;

							if ($L_chk && ($droit_index <> "03-04" && $droit_index <> "01-01"))	// Oui parce que si on met en page de démarrage la page de retour au menu, ça sert à rien !
								echo "<option value='$droit_page'>$droit_titre</option>";
						}
					?>
					
						</select>
						</TD>
					</tr>
					
					
				</table>
				
				<br><br>
				<input type=submit value='Modifier mon compte' >

				</center>

			</form>
		
		<?PHP
		}
		else {
			echo "<center><h2>Modification du compte $login impossible ! <br> Ce compte de supervision ne doit pas être utilisé en production. <br><br>Merci de créer votre propre compte !</h2></center>";
		}
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

				onSuccess: function(responseText, responseXML) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					window.setTimeout("document.location.href='index.php?page=moncompte'", 2500);	
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>
