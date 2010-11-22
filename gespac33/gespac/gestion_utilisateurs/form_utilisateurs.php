<?PHP

	#formulaire d'ajout et de modification
	#des users !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = $("post_user");
		var user_nom = $("nom").value;
		var user_login = $("login").value;
		var user_password = $("password").value;
		
		if (user_nom == "" || user_login == "" || user_password == "") {
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
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_utilisateurs.php');", 1500);
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
	
	$id = $_GET['id'];


	
	#***************************************************************************
	# 				CREATION de l'utilisateur
	#***************************************************************************
	
	
	if ( $id == '-1' ) {	// Formulaire vierge de création
	
		echo "<h2>formulaire de création d'un nouvel utilisateur</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>

		<form action="gestion_utilisateurs/post_utilisateurs.php?action=add" method="post" name="post_form" id="post_form">
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom *</TD>
					<TD><input type=text name=nom id=nom onkeyup="validation();"/></TD>
				</tr>
				
				<tr>
					<TD>Login *</TD>
					<TD><input type=text name=login id=login onkeyup="validation();" /></TD>
				</tr>
				
				<tr>
					<TD>Password *</TD> 
					<TD><input type=password name=password id=password onkeyup="validation();"	/></TD>
				</tr>
				
				<tr>
					<TD>Mail</TD> 
					<TD><input type=text name=mail 	/></TD>
				</tr>
				
				<tr>
					<TD>Mailing</TD> 
					<TD><input type=checkbox name=mailing checked /></TD>
				</tr>
				
				<tr>
					<TD>Grade</TD>
					<TD><select name="grade">
						<?PHP
							// Requete pour récupérer la liste des grades
							$liste_grades = $db_gespac->queryAll ( "SELECT grade_id, grade_nom FROM grades" );		
							
							foreach ( $liste_grades as $record ) {
							
								$grade_id 	= $record[0];
								$grade_nom 	= $record[1];
							
								echo "<option value=$grade_id>$grade_nom</option>";
							}
						?>	
							
						</select>
					</TD>
				</tr>
				
				<tr>
					<td>Skin</td>
					<td>
						<select name="skin">
						<?PHP
							$dossier = opendir("../skins");
							while ( $skin = readdir($dossier) ) {
								if ( $skin != "." && $skin != ".." && $skin != ".svn") {
									echo "<option value=$skin>$skin</option>";
								}
							}
							closedir($dossier);
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<TD>Page de Démarrage</TD>
					<TD><select name="page" size="1">
							<option value="gestion_inventaire/voir_materiels.php">matériels</option>
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
			<input id='post_user' type=submit value='Ajouter utilisateur' disabled>
			</center>

		</FORM>
				

		<?PHP
		
		
		
		
		#***************************************************************************
		# 				MODIFICATION de l'utilisateur
		#***************************************************************************
		
		
		
	} else {	// formulaire de modification prérempli
	
		echo "<h2>formulaire de modification d'un utilisateur</h2><br>";
		
		// Requete pour récupérer les données des champs pour le user à modifier
		$user_a_modifier = $db_gespac->queryAll ( "SELECT user_id, user_nom, user_logon, user_password, users.grade_id, user_mail, user_skin, user_accueil, grade_nom, user_mailing FROM users, grades WHERE users.grade_id=grades.grade_id AND user_id=$id" );		
		
		// valeurs à affecter aux champs
		$user_id 			= $user_a_modifier[0][0];
		$user_nom	 		= $user_a_modifier[0][1];
		$user_logon	 		= $user_a_modifier[0][2];
		$user_password 		= $user_a_modifier[0][3];
		$grade_id	 		= $user_a_modifier[0][4];
		$user_mail 			= $user_a_modifier[0][5];
		$user_skin 			= $user_a_modifier[0][6];
		$user_accueil		= $user_a_modifier[0][7];
		$grade_nom			= $user_a_modifier[0][8];
		$user_mailing		= $user_a_modifier[0][9];

		$checked = $user_mailing == 1 ? "checked" : "";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_utilisateurs/post_utilisateurs.php?action=mod" method="post" name="post_form" id="post_form">
			<input type=hidden name=id value=<?PHP echo $user_id;?> >
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $user_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>log In</TD>
					<TD><input type=text name=login value= "<?PHP echo $user_logon; ?>"	/></TD>
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
					<TD>Grade</TD>
					<TD><select name="grade">
						<?PHP
							// Requete pour récupérer la liste des grades
							$liste_grades = $db_gespac->queryAll ( "SELECT grade_id, grade_nom FROM grades" );		
							
							foreach ( $liste_grades as $record ) {
							
								$grade_id_lst 	= $record[0];
								$grade_nom_lst 	= $record[1];
						
								$selected = $grade_id_lst == $grade_id ? "selected" : "";
							
								echo "<option value='$grade_id_lst' $selected>$grade_nom_lst</option>";
							}
						?>	
							
						</select>
					</TD>
				</tr>
				
				<tr>
					<td>Skin</td>
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
							<option value="gestion_inventaire/voir_materiels.php">matériels</option>
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
			<input type=submit value='Modifier cet utilisateur'>

			</center>

		</FORM>
		
		<?PHP
	}	
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>