<?PHP	#********************************************	# création et postage des données du collège	#	# création de la salle D3E et STOCK	#	#********************************************			// lib	include_once ('config/databases.php');	// fichiers de configuration des bases de données	require_once ('fonctions.php');	include_once ('../class/Sql.class.php');	?><div class="entetes" id="entete-college">		<span class="entetes-titre">CREATION DU COLLEGE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>	<div class="helpbox">C'est la fiche de création du collège et d'initialisation de la base (création des salles spéciales ...)</div></div><div class="spacer"></div> <script>	 	 	 $(function() {			 		// On masque le menu pour obliger la saisie		$('#menu').hide();			// **************************************************************** POST AJAX FORMULAIRES		$("#post_form").click(function(event) {			/* stop form from submitting normally */			event.preventDefault(); 			if ( validForm() == true) {				// Permet d'avoir les données à envoyer				var dataString = $("#formulaire").serialize();								// action du formulaire				var url = $("#formulaire").attr( 'action' );								var request = $.ajax({					type: "POST",					url: url,					data: dataString,					dataType: "html"				 });				 				 request.done(function(msg) {					$('#dialog').dialog('close');					$('#targetback').show(); $('#target').show();					$('#target').html(msg);					window.setTimeout("document.location.href='index.php?page=college'", 2500);				 });			}	 		});		});			 	// Donne le focus au premier champ du formulaire	$('#clg_uai').focus();</script><center>		<h3>ATTENTION : Merci de ne PAS utiliser l'adresse @CG13 pour compléter le mail ATI !</h3><hr>	<form action="gestion_college/post_college.php?action=creat" method="post" name="post_form" id="formulaire">				<center>				<table width=500>					<tr>						<TD>UAI *</TD>						<TD align=left><input type=text id=clg_uai name=clg_uai size=8 maxlength=8 class="valid uai nonvide" required></TD>					</tr>									<tr>						<TD>Nom collège *</TD>						<TD align=left><input type=text id=clg_nom name=clg_nom size=30  class="valid caps nonvide" required></TD>					</tr>									<tr>						<TD>Nom et prénom ATI *</TD>						<TD align=left><input type=text id=clg_ati name=clg_ati class="valid nonvide" ></TD>					</tr>													<tr>						<TD>Mail ATI *</TD>						<TD align=left><input type=mail id=clg_ati_mail name=clg_ati_mail class="valid nonvide mail" required></TD>					</tr>									<tr>						<TD>Adresse</TD>						<TD align=left><input type=text id=clg_adresse name=clg_adresse maxlength=40 /></TD>					</tr>									<tr>						<TD>Code Postal</TD>						<TD align=left><input type=text name=clg_cp size=5 maxlength=5 /></TD>					</tr>									<tr>						<TD>Ville *</TD>						<TD align=left><input type=text name=clg_ville id=clg_ville class="valid nonvide caps" required></TD>					</tr>										<tr>						<TD>Tel</TD>						<TD align=left><input type=text name=clg_tel /></TD>					</tr>									<tr>						<TD>Fax</TD>						<TD align=left><input type=text name=clg_fax /></TD>					</tr>									<tr>						<TD>Site web</TD>						<TD align=left>http://<input type=text size=40 name=clg_web class="valid url" ></TD>					</tr>													<tr>						<TD>Accès GRR</TD>						<TD align=left>http://<input type=text size=40 name=clg_grr class="valid url" /></TD>					</tr>										</table>					<br>		<center>			<input type=submit name=Envoyer value="Créer le collège" id="post_form"><br><br>					</center>	</form></center>