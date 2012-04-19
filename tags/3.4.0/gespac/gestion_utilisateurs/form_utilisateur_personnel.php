<?PHP
session_start();
	
	#formulaire de modification de son propre compte

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	// lib
	include ('../config/databases.php');	// fichiers de configuration des bases de donn�es
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');

	$login =  $_SESSION['login'];

?>

<script type="text/javascript"> 
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
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
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres et lorsqu'on poste la page formation on d�passe la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('index.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>


<?PHP

	if ( $login <> "ati" ) {

		// connexion � la base de donn�es GESPAC
		$con_gespac = new Sql($host, $user, $pass, $gespac);

			
			#***************************************************************************
			# 				MODIFICATION de l'utilisateur
			#***************************************************************************
			
			
			// Requete pour r�cup�rer les donn�es des champs pour le user � modifier
			$user_a_modifier = $con_gespac->QueryRow ( "SELECT user_id, user_nom, user_logon, user_password, user_mail, user_skin, user_accueil, user_mailing FROM users WHERE user_logon='$login'" );
			
			// valeurs � affecter aux champs
			$user_id 			= $user_a_modifier[0];
			$user_nom	 		= $user_a_modifier[1];
			$user_logon	 		= $user_a_modifier[2];
			$user_password 		= $user_a_modifier[3];
			$user_mail 			= $user_a_modifier[4];
			$user_skin 			= $user_a_modifier[5];
			$user_accueil		= $user_a_modifier[6];
			$user_mailing		= $user_a_modifier[7];
			
			$checked = $user_mailing == 1 ? "checked" : "";
		
			echo "<h2>Formulaire de modification de $user_nom</h2><br>";
			
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
						<td title="La modification sera visible � la prochaine connexion...">Skin</td>
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
					
						<TD>Page de D�marrage</TD>
						<TD><select name="page" size="1">
					
					<?PHP
						$lines = file('../menu.txt');

						echo "<option value='bienvenue.php'>Bienvenue</option>";	// Page par d�faut
						
						
						// Requ�te de r�cup�ration des droits
						$liste_items = $con_gespac->QueryAll ( "SELECT * FROM droits order by droit_index" );
						
						foreach ($liste_items as $ligne) {
					
							$droit_id = $ligne ['droit_id'];
							$droit_index = $ligne ['droit_index'];
							$droit_titre = $ligne ['droit_titre'];
							$droit_page = $ligne ['droit_page'];
							$droit_etendue = $ligne ['droit_etendue'];
							$droit_description = $ligne ['droit_description'];

							$L_chk = preg_match ("#$droit_index#", $_SESSION['droits']) ;

							if ($L_chk && ($droit_index <> "03-04" && $droit_index <> "01-01"))	// Oui parce que si on met en page de d�marrage la page de retour au menu, �a sert � rien !
								echo "<option value='$droit_page'>$droit_titre</option>";
						}
					?>
					
						</select>
						</TD>
					</tr>
					
					
				</table>
				
				<br>
				<input type=submit value='Modifier mon compte' >

				</center>

			</FORM>
		
		<?PHP
		}
		else {
			
				echo "<center><h2>Modification du compte $login impossible ! <br> Ce compte de supervision ne doit pas �tre utilis� en production. <br><br>Merci de cr�er votre propre compte !</h2></center>";
			
		}
		?>
		
<!--	DIV target pour Ajax	-->
<div id="target"></div>
