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
	</script>
