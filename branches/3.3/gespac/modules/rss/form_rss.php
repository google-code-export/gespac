<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	$action = $_GET['action'];
	
	
	
	if ( $action == 'ajout' ) {
		
		echo "<h2>Ajouter un flux RSS ou ATOM</h2><br>";
?>	
	
		<form action="modules/rss/post_rss.php?action=add" method="post" name="post_add_flux_rss" id="post_add_flux_rss">
		
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


	<script type="text/javascript" src="../../js/main.js"></script>

	<script>
		// Donne le focus au premier champ du formulaire
		$('nom').focus();
		
		
		// Validation du formulaire
		function validation () {

			var bt_submit = $("post_flux");
			var rss_nom = $("nom").value;
			var rss_url = $("url").value;

			if (rss_nom == "" || rss_url == "") {
				bt_submit.disabled = true;
			} else {
				bt_submit.disabled = false;
			}
		};
		
		// ferme la smoothbox et rafraichis la page
		function refresh_quit () {
			// lance la fonction avec un délais de 1500ms
			window.setTimeout("$('conteneur').load('modules/rss/rss.php');", 1500);
			//TB_remove();
		};
		
		// On soumet le formulaire pour l'ajout d'un flux RSS
		$('post_add_flux_rss').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({
				method: this.method,
				url: this.action,
				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					window.setTimeout("$('conteneur').load('modules/rss/rss.php');", 1500);
				}
			}).send(this.toQueryString());
		});	

		
	</script>
