<?PHP
	#formulaire d'ajout et de modification
	#des users !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	include ('../config/databases.php');	// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');
	
?>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<script type="text/javascript"> 
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
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
	*				AJAX
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
					window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_utilisateurs.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>

<?PHP

	// connexion � la base de donn�es GESPAC
	$con_gespac 	= new Sql ($host, $user,$pass, $gespac);
	
	$id 	= $_GET['id'];
	$action = $_GET['action'];

	
	#***************************************************************************
	# 				CREATION de l'utilisateur
	#***************************************************************************
	
	
	if ( $id == '-1' ) {	// Formulaire vierge de cr�ation
	
		echo "<h2>Formulaire de cr�ation d'un nouvel utilisateur</h2><br>";
		
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
							// Requete pour r�cup�rer la liste des grades
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
					
						<TD>Page de D�marrage</TD>
						<TD><select name="page" size="1">
					
					<?PHP
						$lines = file('../menu.txt');

						echo "<option value='bienvenue.php'>Bienvenue</option>";	// Page par d�faut

						foreach ($lines as $line) {

							$line = str_replace('"','',$line);
							$explode_line = explode (";", $line);
							$id = $explode_line[0];
							$value = $explode_line[1];
							$path_page = $explode_line[2];	
												
							$selected = $path_page == $user_accueil ? "selected" : "" ; //pour une raison �trange, �a ne marche pas ...
							
							$L_chk = preg_match ("#$id#", $droits) ;
	
							if ($L_chk && $value <> "Retour au portail")	// Oui parce que si on met en page de d�marrage la page de retour au menu, �a sert � rien !
								echo "<option $selected value='$path_page'>$value</option>";
						}
					?>
					
						</select>
						</TD>
					</tr>
				
			</table>

			<br>
			<input id='post_user' type=submit value='Ajouter utilisateur' disabled>
			</center>

		</FORM>
				

		<?PHP
		
	} 
	
	if ($action == 'mod') {
	
	#***************************************************************************
	# 				MODIFICATION de l'utilisateur
	#***************************************************************************
		
		
		
		// formulaire de modification pr�rempli
	
		echo "<h2>formulaire de modification d'un utilisateur</h2><br>";
		
		// Requete pour r�cup�rer les donn�es des champs pour le user � modifier
		$user_a_modifier = $con_gespac->QueryRow ( "SELECT user_id, user_nom, user_logon, user_password, users.grade_id, user_mail, user_skin, user_accueil, grade_nom, user_mailing FROM users, grades WHERE users.grade_id=grades.grade_id AND user_id=$id" );		
		
		// valeurs � affecter aux champs
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
							// Requete pour r�cup�rer la liste des grades
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
			<input type=submit value='Modifier cet utilisateur'>

			</center>

		</FORM>
		
		<?PHP
	}	

	
	// *********************************************************************************
	//
	//			Formulaire modification par lot non pr�rempli
	//
	// *********************************************************************************		
		
	
	if ($action == 'modlot') {
		
		echo "<h2>Formulaire de modification d'un lot</h2><br>";
			
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('mailing').focus();
		</script>

		<form action="gestion_utilisateurs/post_utilisateurs.php?action=modlot" method="post" name="post_form" id="post_form">
			<center>
			
			<input type=hidden name='lot_users' id='lot_users'>
			
			<!-- Ici on r�cup�re la valeur du champ users_a_poster de la page voir_utilisateurs.php -->
			<script>$("lot_users").value = $('users_a_poster').value;</script>
			

			<table width=500>
				<tr>
					<TD> Activer le mailing</TD> 
						<TD><select name="mailing" id="mailing">
							<option value=2>Ne pas modifier</option>
							<option value=1>Activer</option>
							<option value=0>D�sactiver</option>
							</select>
						</TD>
				</tr>
				
				
				<tr>
					<TD>Grade</TD>
					<TD><select name="grade">
						<option value="">Ne pas modifier</option>
						<?PHP
							// Requete pour r�cup�rer la liste des grades
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
			<input type=submit value='Modifier le lot' >
			<input type=button value='Sortir sans modifier' onclick="SexyLightbox.close();" >

			</center>
		
		</FORM>
				

		<?PHP	
	}
		
		
?>		

<!--	DIV target pour Ajax	-->
<div id="target"></div>
