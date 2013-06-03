<?PHP
	#formulaire d'ajout et de modification
	#des users !
	
	// lib
	include ('../config/databases.php');	// fichiers de configuration des bases de données
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');
	
?>



<script type="text/javascript"> 


	$(function() {	
				
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
			
			if ( validForm() == true) {
			
				// Permet d'avoir les données à envoyer
				var dataString = $("#formulaire").serialize();
				
				// action du formulaire
				var url = $("#formulaire").attr( 'action' );
				
				var request = $.ajax({
					type: "POST",
					url: url,
					data: dataString,
					dataType: "html"
				 });
				 
				 request.done(function(msg) {
					$('#dialog').dialog('close');
					$('#targetback').show(); $('#target').show();
					$('#target').html(msg);
					window.setTimeout("document.location.href='index.php?page=utilisateurs&filter=" + $('#filt').val() + "'", 2500);
				 });
			}			 
		});	
	});
</script>

<?PHP

	// connexion à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user,$pass, $gespac);
	
	$action = $_GET['action'];

	
	
	
	#***************************************************************************
	# 				@@ CREATION de l'utilisateur
	#***************************************************************************
	
	if ( $action == 'add' ) {	// Formulaire vierge de création

		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#nom').focus();
		</script>

		<form action="gestion_utilisateurs/post_utilisateurs.php?action=add" method="post" name="post_form" id="formulaire">
			<center>
			<table class="formtable" >
			
				<tr>
					<TD>Nom *</TD>
					<TD><input type=text name=nom id=nom class='valid nonvide'></TD>
				</tr>
				
				<tr>
					<TD>Login *</TD>
					<TD><input type=text name=login id=login class='valid nonvide'></TD>
				</tr>
				
				<tr>
					<TD>Password *</TD> 
					<TD><input type=password name=password id=password class='valid nonvide'></TD>
				</tr>
				
				<tr>
					<TD>Mail</TD> 
					<TD><input type=text name=mail class='valid mail'></TD>
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
							$liste_grades = $con_gespac->QueryAll ( "SELECT grade_id, grade_nom FROM grades" );		
							
							foreach ( $liste_grades as $record ) {
							
								$grade_id 	= $record['grade_id'];
								$grade_nom 	= $record['grade_nom'];
							
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
						<?PHP 
							$selected = $accueil == $user_accueil ? "selected" : "" ;
						?>
					
						<TD>Page de Démarrage</TD>
						<TD><select name="page" size="1">
					
					<?PHP
						$lines = file('../menu.txt');

						echo "<option value='bienvenue.php'>Bienvenue</option>";	// Page par défaut

						foreach ($lines as $line) {

							$line = str_replace('"','',$line);
							$explode_line = explode (";", $line);
							$id = $explode_line[0];
							$value = $explode_line[1];
							$path_page = $explode_line[2];	
												
							$selected = $path_page == $user_accueil ? "selected" : "" ; //pour une raison étrange, ça ne marche pas ...
							
							$L_chk = preg_match ("#$id#", $droits) ;
	
							if ($L_chk && $value <> "Retour au portail")	// Oui parce que si on met en page de démarrage la page de retour au menu, ça sert à rien !
								echo "<option $selected value='$path_page'>$value</option>";
						}
					?>
					
						</select>
						</TD>
					</tr>
				
			</table>

			<br>
			<input id='post_form' type=submit value='Ajouter utilisateur'>
			</center>

		</FORM>
				

		<?PHP
		
	} 
	
	
	#***************************************************************************
	# 				@@ MODIFICATION de l'utilisateur
	#***************************************************************************
	
	if ($action == 'mod') {
			
		$id 	= $_GET['id'];
				
		// Requete pour récupérer les données des champs pour le user à modifier
		$user_a_modifier = $con_gespac->QueryRow ( "SELECT user_id, user_nom, user_logon, user_password, users.grade_id, user_mail, user_skin, user_accueil, grade_nom, user_mailing FROM users, grades WHERE users.grade_id=grades.grade_id AND user_id=$id" );		
		
		// valeurs à affecter aux champs
		$user_id 			= $user_a_modifier[0];
		$user_nom	 		= $user_a_modifier[1];
		$user_logon	 		= $user_a_modifier[2];
		$user_password 		= $user_a_modifier[3];
		$grade_id	 		= $user_a_modifier[4];
		$user_mail 			= $user_a_modifier[5];
		$user_skin 			= $user_a_modifier[6];
		$user_accueil		= $user_a_modifier[7];
		$grade_nom			= $user_a_modifier[8];
		$user_mailing		= $user_a_modifier[9];

		$checked = $user_mailing == 1 ? "checked" : "";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#nom').focus();
		</script>
		
		<form action="gestion_utilisateurs/post_utilisateurs.php?action=mod" method="post" name="post_form" id="formulaire">
			<input type=hidden name=id value=<?PHP echo $user_id;?> >
			<center>
			<table class="formtable">
			
				<tr>
					<TD>Nom *</TD>
					<TD><input type="text" name="nom" class="valid" id="nom" value= "<?PHP echo $user_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>log In *</TD>
					<TD><input type="text" name="login" class="valid" value= "<?PHP echo $user_logon; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Password *</TD> 
					<TD><input type="password" name="password" class="valid" value= "<?PHP echo $user_password; ?>"	/></TD>
				</tr>
								
				<tr>
					<TD>Mail</TD> 
					<TD><input type="text" name="mail" value= "<?PHP echo $user_mail; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Mailing</TD> 
					<TD><input type="checkbox" name="mailing" <?PHP echo $checked;?>	/></TD>
				</tr>
				
				
				<tr>
					<TD>Grade</TD>
					<TD><select name="grade">
						<?PHP
							// Requete pour récupérer la liste des grades
							$liste_grades = $con_gespac->QueryAll ( "SELECT grade_id, grade_nom FROM grades" );		
							
							foreach ( $liste_grades as $record ) {
							
								$grade_id_lst 	= $record['grade_id'];
								$grade_nom_lst 	= $record['grade_nom'];
						
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
				
			</table>
			
			<br>
			<input type=submit value='Modifier cet utilisateur' id="post_form">

			</center>

		</FORM>
		
		<?PHP
	}	

			
	#***************************************************************************
	# 				@@ MODIFICATION PAR LOT non prérempli
	#***************************************************************************	
	
	if ($action == 'modlot') {

		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#mailing').focus();
		</script>

		<form action="gestion_utilisateurs/post_utilisateurs.php?action=modlot" method="post" name="post_form" id="formulaire">
			<center>
			
			<input type=hidden name='lot_users' id='lot_users'>
			
			<!-- Ici on récupère la valeur du champ users_a_poster de la page voir_utilisateurs.php -->
			<script>$("#lot_users").val($('#id_a_poster').val());</script>
			

			<table class='formtable'>
				<tr>
					<TD> Activer le mailing</TD> 
						<TD><select name="mailing" id="mailing">
							<option value=2>Ne pas modifier</option>
							<option value=1>Activer</option>
							<option value=0>Désactiver</option>
							</select>
						</TD>
				</tr>
				
				
				<tr>
					<TD>Grade</TD>
					<TD><select name="grade">
						<option value="">Ne pas modifier</option>
						<?PHP
							// Requete pour récupérer la liste des grades
							$liste_grades = $con_gespac->queryAll ( "SELECT grade_id, grade_nom FROM grades" );		
							
							foreach ( $liste_grades as $record ) {
							
								$grade_id_lst 	= $record['grade_id'];
								$grade_nom_lst 	= $record['grade_nom'];
						
								$selected = $grade_id_lst == $grade_id ? "selected" : "";
							
								echo "<option value='$grade_id_lst' $selected>$grade_nom_lst</option>";
							}
						?>	
							
						</select>
					</TD>
				</tr>
				
				<tr>
					<td>Skin</td>
					<td><select name="skin">
						<option value="">Ne pas modifier</option>
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

			</table>

			<br>
			<input type=submit value='Modifier le lot' id="post_form" >
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>

			</center>
		
		</FORM>
				

		<?PHP	
	}
	
		
	#***************************************************************************
	# 				@@ SUPPRESSION de l'utilisateur
	#***************************************************************************
	
	if ($action == 'del') {

		$id = $_GET['id'];
		$user = $con_gespac->QueryRow ( "SELECT user_nom FROM users WHERE user_id=$id" );

		$nom = $user[0];
		$grade = $user[1];
		
		$nb_prets = $con_gespac->QueryOne ( "SELECT COUNT(mat_id) FROM materiels WHERE user_id=$id" );

		if ($nb_prets > 0) {echo "<h3>Vous ne pouvez pas supprimer l'utilisateur <u>$nom</u> : <br>Rendez d'abord les matériels prêtés !</h3>"; exit();}

		echo "Voulez vous vraiment supprimer l'utilisateur $nom ?";
	?>	
		<center><br><br>
		<form action="gestion_utilisateurs/post_utilisateurs.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $id;?>" name="id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
	<?PHP	
	}
	
	
?>		
