<?PHP

	$action = $_GET['action'];

	if ( $action == 'ajout' ) {

?>	
	
		<form action="modules/rss/post_rss.php?action=add" method="post" name="post_add_flux_rss" id="formulaire">
		
			<center>
			<table class="formtable">
			
				<tr>
					<TD>Nom du flux *</TD>
					<TD><input type="text" name="nom" id="nom" class="valid nonvide"></TD>
				</tr>
				
				<tr>
					<TD>URL du flux *</TD>
					<TD><input type="text" name="url" id="url" class="valid nonvide url"></TD>
				</tr>
				
			</table>

			<br>
			<input type=submit value='Ajouter le flux' id="post_form">

			</center>

		</FORM>
	
	
	
<?PHP	
	}
?>


	<script type="text/javascript" src="../../js/main.js"></script>

	<script>
		// Donne le focus au premier champ du formulaire
		$('#nom').focus();

	/*	
		// ferme la smoothbox et rafraichis la page
		function refresh_quit () {
			// lance la fonction avec un délais de 1500ms
			window.setTimeout("$('conteneur').load('modules/rss/rss.php');", 1500);

		};
		
		// On soumet le formulaire pour l'ajout d'un flux RSS
		$('post_add_flux_rss').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({
				method: this.method,
				url: this.action,
				onSuccess: function(responseText, responseXML) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					SexyLightbox.close();
					window.setTimeout("document.location.href='index.php?page=rss'", 2500);						
				}
			}).send(this.toQueryString());
		});	
*/
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
					window.setTimeout("document.location.href='index.php?page=rss'", 2500);
				 });
			}			 
		});	
	});
	</script>
