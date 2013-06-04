<?PHP

	#formulaire d'ajout et de modification
	#des grades !

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
					window.setTimeout("document.location.href='index.php?page=grades&filter=" + $('#filt').val() + "'", 2500);
				 });
			}			 
		});	
	});
</script>

<?PHP

	//connexion à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$action = $_GET['action'];

	
	#***************************************************************************
	# 				@@CREATION du grade
	#***************************************************************************
	
	
	if ( $action == 'add' ) {	// Formulaire vierge de création

		?>
		
		<script>$('#nom').focus();</script>

		<form action="gestion_utilisateurs/post_grades.php?action=add" method="post" name="post_form" id="formulaire">
			<center>
			<table class="formtable">
			
				<tr>
					<TD>Nom*</TD>
					<TD><input type=text name="nom" id="nom" class="valid nonvide"></TD>
				</tr>

			</table>

			<br>
			<input type=submit value='Ajouter grade' id="post_form">
			</center>

		</FORM>
				

		<?PHP
			
	} 
	

	
	#***************************************************************************
	# 				@@MODIFICATION du grade
	#***************************************************************************
	
	
	if ( $action == 'mod') {

		$id = $_GET['id'];
		
		// Requete pour récupérer les données des champs pour le user à modifier
		$grade_a_modifier = $con_gespac->QueryRow ( "SELECT grade_id, grade_nom FROM grades WHERE grade_id=$id" );		
		
		// valeurs à affecter aux champs
		$grade_id 			= $grade_a_modifier[0];
		$grade_nom	 		= $grade_a_modifier[1];

		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#nom').focus();
		</script>
		
		<form action="gestion_utilisateurs/post_grades.php?action=mod" method="post" name="post_form" id="formulaire">
			<input type=hidden name=id value=<?PHP echo $grade_id;?> >
			<center>
			<table class="formtable">
			
				<tr>
					<TD>Nom *</TD>
					<TD><input class="valid nonvide" type="text" name="nom" id="nom" value= "<?PHP echo $grade_nom; ?>" ></TD>
				</tr>
				
			</table>
			
			<br>
			<input type=submit value='Modifier ce grade' id="post_form">

			</center>

		</FORM>
		
		<?PHP
	}	
	
	#***************************************************************************
	# 				@@SUPPRESSION du grade
	#***************************************************************************
	
	
	if ( $action == 'del') {

		$id = $_GET['id'];
		$nom = $con_gespac->QueryOne ( "SELECT grade_nom FROM grades WHERE grade_id=$id" );

		echo "Voulez vous vraiment supprimer le grade <b>$nom</b> ? <br> Les utilisateurs de ce grade seront déplacés dans le grade [invité]. ";
	?>	
		<center><br><br>
		<form action="gestion_utilisateurs/post_grades.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $id;?>" name="id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>	
	
	<?PHP
	}
?>
