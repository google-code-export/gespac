


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	$action = $_GET['action'];
	
	
	
	if ( $action == 'ajout' ) {
		
		echo "<h2>Ajouter un flux RSS ou ATOM</h2><br>";
?>	
	
		<form onsubmit="return !HTML_AJAX.formSubmit(this,'target');" action="modules/rss/post_rss.php?action=add" method="post" name="frmTest" id="frmTest">
		
			<center>
			<table width=500 class="form_table">
			
				<tr>
					<TD>Nom du flux *</TD>
					<TD><input type="text" name="nom" id="nom" onkeyup="validation();"/></TD>
				</tr>
				
				<tr>
					<TD>URL du flux *</TD>
					<TD><input type="text" name="url" id="url" onkeyup="validation();"/></TD>
				</tr>
				
			</table>

			<br>
			<input type=submit value='Ajouter le flux' onclick="refresh_quit();" id="post_flux" disabled>

			</center>

		</FORM>
	
	
	
<?PHP	
	}
?>


	<!--  SERVEUR AJAX -->
	<script type="text/javascript" src="server.php?client=all"></script>


	<script>
		// Donne le focus au premier champ du formulaire
		$('nom').focus();
	</script>


	<script type="text/javascript"> 
		
		// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
		function validation () {

			var bt_submit = $("post_flux");
			var nom = $("nom").value;
			var url = $("url").value;
			
			if (nom == "" || url == "") {
				bt_submit.disabled = true;
			} else {
				bt_submit.disabled = false;
			}
		}
		
		// ferme la smoothbox et rafraichis la page
		function refresh_quit () {

			// lance la fonction avec un délais de 1500ms
			window.setTimeout("HTML_AJAX.replace('conteneur', 'modules/rss/rss.php');", 1500);
			TB_remove();
		}
			
	</script>