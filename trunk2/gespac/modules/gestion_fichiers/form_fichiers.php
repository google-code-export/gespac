

<script type="text/javascript"> 
	
	$(function(){
		
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
					window.setTimeout("document.location.href='index.php?page=gestfichiers&filter=" + $('#filt').val() + "'", 2500);
				 });
			}			 
		});		
		
		//---------------------------------------- Post file par Ajax (fonction par olanod : http://stackoverflow.com/users/931340/olanod)
		$('#post_add').click(function(){
			var formData = new FormData($('form#formulaire')[0]);
			$.ajax({
				url: $("#formulaire").attr( 'action' ),  //server script to process data
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
					window.setTimeout("document.location.href='index.php?page=gestfichiers&filter=" + $('#filt').val() + "'", 2500);
				}
			});
		});
	
	
		// Sur click des checkboxes pour les droits
	
		$('#grade_ecriture').click(function() {
			
			if ( $('#grade_ecriture').prop("checked") == true ) {
				$('#grade_lecture').prop("checked",true);
			}
			else {
				$('#tous_ecriture').prop("checked",false);
			}
			
			CalcDroits();
		});

		$('#grade_lecture').click(function() {
			if ( $('#grade_lecture').prop("checked") == false ) {
				$('#grade_ecriture').prop("checked",false);
				$('#tous_ecriture').prop("checked",false);
				$('#tous_lecture').prop("checked",false);
			}
			
			CalcDroits();
		});

		$('#tous_lecture').click(function() {
			
			if ( $('#tous_lecture').prop("checked") == false ) {
				$('#tous_ecriture').prop("checked",false);
				$('#grade_lecture').prop("checked",false);
				$('#grade_ecriture').prop("checked",false);
			}
			else {
				$('#grade_lecture').prop("checked",true);
			}
			
			CalcDroits();
		});		
		
		$('#tous_ecriture').click(function() {
			
			if ( $('#tous_ecriture').prop("checked") == true ) {
				$('#tous_lecture').prop("checked",true);
				$('#grade_lecture').prop("checked",true);
				$('#grade_ecriture').prop("checked",true);
			}
			else {
				$('#grade_ecriture').prop("checked",false);
			}
			
			CalcDroits();
			
		});	
		
	});
	
	
	// Permet de mettre une valeur numérique aux cases cochées
	function CalcDroits () {
		var chiffre1 = "0";
		var chiffre2 = "0";
		
		if ( $("#grade_lecture").prop("checked") == true ) chiffre1="1";
		if ( $("#grade_ecriture").prop("checked") == true ) chiffre1="2";
		
		if ( $("#tous_lecture").prop("checked") == true ) chiffre2="1";
		if ( $("#tous_ecriture").prop("checked") == true ) chiffre2="2";
		
		$("#droits").val(chiffre1+chiffre2);
	}
	
	// Permet de cocher les cases en fonction d'une valeur numérique
	function AffectDroits (valeur) {

			if ( valeur == "00") {
				$("#grade_lecture").prop("checked",false); $("#grade_ecriture").prop("checked",false); $("#tous_lecture").prop("checked",false);	$("#tous_ecriture").prop("checked",false); }
			if ( valeur == "10") {
				$("#grade_lecture").prop("checked",true); $("#grade_ecriture").prop("checked",false); $("#tous_lecture").prop("checked",false); $("#tous_ecriture").prop("checked",false); }	
			if ( valeur == "11") {
				$("#grade_lecture").prop("checked",true); $("#grade_ecriture").prop("checked",false); $("#tous_lecture").prop("checked",true); $("#tous_ecriture").prop("checked",false); }
			if ( valeur == "20") {
				$("#grade_lecture").prop("checked",true); $("#grade_ecriture").prop("checked",true); $("#tous_lecture").prop("checked",false); $("#tous_ecriture").prop("checked",false); }	
			if ( valeur == "21") {
				$("#grade_lecture").prop("checked",true); $("#grade_ecriture").prop("checked",true); $("#tous_lecture").prop("checked",true); $("#tous_ecriture").prop("checked",false); }			
			if ( valeur == "22") {
				$("#grade_lecture").prop("checked",true); $("#grade_ecriture").prop("checked",true); $("#tous_lecture").prop("checked",true); $("#tous_ecriture").prop("checked",true); }
	}
	
	
</script>



<?PHP

$action = $_GET['action'];

// @@CREATION

if ( $action == "add" ) {

?>
	 
	<form enctype="multipart/form-data" action="modules/gestion_fichiers/post_fichiers.php?action=creation" id='formulaire'>
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
		 <center>
		 <table class="formtable">
			
			<tr>
				<td>Fichier</td>
				<td><input type="file" name="myfile"></td>
			</tr>
			
			<tr>
				<td>Description</td>
				<td><textarea style='width:213px;' name="description"></textarea> </td>
			</tr>
			

		</table>
		 
		<br>
		
		<b>droits</b><input type=hidden id="droits" name="droits" value="00">

		<table width=50px>
			<td>&nbsp;</td>
			<td>lecture</td>
			<td>écriture</td>
			<tr>
				<td>grade</td>
				<td><input type=checkbox id=grade_lecture></td>
				<td><input type=checkbox id=grade_ecriture></td>
			</tr>
			<tr>
				<td>tous</td>
				<td><input type=checkbox id=tous_lecture></td>
				<td><input type=checkbox id=tous_ecriture></td>
			</tr>
		</table>
		 
		<br>
		
		<br>
		<input type="button" id="post_add" name="envoyer" value="Envoyer le fichier">
		<br> 
		<small>fichier limité à 10Mio.</small>
		 
		</center>
		  
	</form>

<?PHP
}


// @@MODIFICATION

if ( $action == "mod" ) {
	
	$id = $_GET['id'];

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$fichier = $con_gespac->QueryRow ('SELECT * FROM fichiers WHERE fichier_id='.$id);
	
	$fic_chemin = $fichier[1];
	$fic_desc 	= $fichier[2];
	$fic_droits = $fichier[3];

?>
	<script>AffectDroits("<?PHP echo $fic_droits;?>");</script>
	
	<form method="POST" action="modules/gestion_fichiers/post_fichiers.php?action=mod&id=<?PHP echo $fic_id; ?>" name="post_form" id="formulaire">

		<center>
		<table class='formtable'>
			<input type=hidden value="<?PHP echo $id;?>" name="id">
			<tr>
				<td>Fichier</td>
				<td><input type="text" readonly name="myfile" value=<?PHP echo $fic_chemin;?> size=27></td>
			</tr>
			
			<tr>
				<td>Description</td>
				<td><textarea name="description"><?PHP echo $fic_desc;?></textarea> </td>
			</tr>
			

		</table>
		 
		<br>
		
		<b>droits</b> <input type=hidden id="droits" name="droits" value=<?PHP echo $fic_droits;?>>
		 
		<table width=50px>
			<td>&nbsp;</td>
			<td>lecture</td>
			<td>écriture</td>
			<tr>
				<td>grade</td>
				<td><input type=checkbox id=grade_lecture></td>
				<td><input type=checkbox id=grade_ecriture></td>
			</tr>
			<tr>
				<td>tous</td>
				<td><input type=checkbox id=tous_lecture></td>
				<td><input type=checkbox id=tous_ecriture></td>
			</tr>
		</table>
		 
		<br>
		
		<br>
		<input type="submit" name="envoyer" id='post_form' value="Envoyer le fichier">
		 
		</center>
		  
	</form>

<?PHP
	}
	
	// @@SUPPRESSION

	if ( $action == "del" ) {
			
		// lib
		require_once ('../../fonctions.php');
		include_once ('../../config/databases.php');
		include_once ('../../../class/Sql.class.php');
		
		$con_gespac = new Sql($host, $user, $pass, $gespac);

		$id = $_GET['id'];
		$nom = $con_gespac->QueryOne ( "SELECT fichier_chemin FROM fichiers WHERE fichier_id=$id" );

		echo "Voulez vous vraiment supprimer le fichier <b>$nom</b> ? ";
	?>	
		<center><br><br>
		<form action="modules/gestion_fichiers/post_fichiers.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $id;?>" name="id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>	
<?PHP
	}
?>


