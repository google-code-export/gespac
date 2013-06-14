<?PHP

	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	
	$action = $_GET['action'];


	//------------------------------------- @@ MODIFICATION 
	if ($action == 'mod' ) {

		$id = $_GET['id'];

		// Requete pour récupérer les données des champs pour le user à modifier
		$item_a_modifier = $con_gespac->queryRow ( "SELECT mp_id, mp_nom, mp_url FROM menu_portail WHERE mp_id=$id" );		
		
		// valeurs à affecter aux champs
		$mp_id 		= $item_a_modifier[0];
		$mp_nom	 	= $item_a_modifier[1];
		$mp_url		= $item_a_modifier[2];

		
?>		
	
		<form action="modules/menu_portail/post_menu_portail.php?action=mod" method="post" name="post_form" id="formulaire">
			<center>
				
				<input type=hidden name=id value="<?PHP echo $id; ?>">
					
			<table class="formtable">

				<tr>
					<TD>nom</TD> 
					<TD><input name="mp_nom" id="mp_nom" type="text" value="<?PHP echo $mp_nom;?>" class="valid nonvide"></TD>
				</tr>
				
				<tr>
					<TD>Url</TD>
					<TD><input name="mp_url" id="mp_url" type="text" value="<?PHP echo $mp_url;?>" class="valid nonvide" ></TD>
				</tr>

			</table>
			
			<br>
			<center>
			<input type="submit" name="envoyer" id="post_form" value="Envoyer">

			</center>
			
		</FORM>
	
		


<?PHP		
	}
	
	
	//------------------------------------- @@ CREATION 
	if ($action == "add") {
?>
		
		<center>
		<form enctype="multipart/form-data" id="formulaire">
			 <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
			 
			 <table class="formtable">	 
				<tr>
					<TD>Nom *</TD> 
					<TD><input name="mp_nom" id="mp_nom" type="text" class="valid nonvide"></TD>
				</tr>
				<tr>
					<TD>Url *</TD>
					<TD><input name="mp_url" id="mp_url" type="text" class="valid nonvide" ></TD>
				</tr>
				<tr>
					<td>Icone (png)</td>
					<td><input type="file" name="myfile"></td>
				</tr>
				<tr>
					<td colspan=2><br><center><input type="button" name="envoyer" id="post_add" value="Créer l'icône"></center></td>
				</tr>
			 </table>
			 </center>
			  
		</form>
		</center>

<?PHP }		

	
	
	//------------------------------------- @@ SUPRESSION 
	if ($action == "del") {
		
		$id = $_GET['id'];
		$nom = $con_gespac->QueryOne ( "SELECT mp_nom FROM menu_portail WHERE mp_id=$id" );

		echo "Voulez vous vraiment supprimer l'item <b>$nom</b> ? <br> Les utilisateurs ne pourront plus atteindre ce point de menu. ";
	?>	
		<center><br><br>
		<form action="modules/menu_portail/post_menu_portail.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $id;?>" name="id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>	

<?PHP } ?>	


<script>
	
	$('#mp_nom').focus();
		
	//---------------------------------------- Post file par Ajax (fonction par olanod : http://stackoverflow.com/users/931340/olanod)
	$('#post_add').click(function(){
		var formData = new FormData($('form#formulaire')[0]);
		
		$.ajax({			
			url: "modules/menu_portail/post_menu_portail.php?action=add",  //server script to process data
			type: 'POST',
			xhr: function() {  // custom xhr
				var myXhr = $.ajaxSettings.xhr();
				return myXhr;
			},
			// Data du formulaire
			data: formData,
			//Options to tell JQuery not to process data or worry about content-type
			cache: false,
			contentType: false,
			processData: false,
			complete : function(res) {
				$('#dialog').dialog('close');
				$('#targetback').show(); $('#target').show();
				$('#target').html(res.responseText);
				window.setTimeout("document.location.href='index.php?page=modportail'", 2500);
			}
		});
	});
	

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
				window.setTimeout("document.location.href='index.php?page=modportail&filter=" + $('#filt').val() + "'", 2500);
			 });
		}			 
	});	

</script>	
