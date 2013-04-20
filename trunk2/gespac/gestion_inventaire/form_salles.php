<?PHP
	/*
		formulaires des salles
		ajout, modification, suppression, vidage D3E
	*/


	// lib
	include_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');
	
	// Connexion à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);

?>

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = document.getElementById("post_form");
		var salle_nom = document.getElementById("nom").value;
		
		if (salle_nom == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	$(function() {	
	
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
		
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
				window.setTimeout("document.location.href='index.php?page=salles&filter=" + $('#filt').val() + "'", 1500);
			 });
			 
		});	
	});
		
</script>

<?PHP

	$action = $_GET['action'];

	
	//********************************************* formulaire de création
	if ( $action == 'add' ) {

?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_inventaire/post_salles.php?action=add" method="post" name="post_form" id='formulaire'>
		
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom salle *</TD>
					<TD><input type=text name=nom id=nom onkeyup="validation();" required/></TD>
				</tr>
				
				<tr>
					<TD>VLAN</TD>
					<TD>
						<select name="vlan" size="1">
							<option selected>1 pour 5</option>
							<option>existant</option>

							<option>1 pour 5 et existant</option>
							<option>N/A</option>
						</select>
					</TD>
				</tr>

				<tr>
					<TD>Etage</TD>
					<TD><input type=text name=etage 	/></TD>
				</tr>

				<tr>
					<TD>Batiment</TD>
					<TD><input type=text name=batiment 	/></TD>
				</tr>

			</table>

			<br>
			<input type=submit value='Ajouter une salle' id="post_form" disabled>

			</center>

		</FORM>
				

		<?PHP
	} 
	
	
	//********************************************* formulaire de modification prérempli
	if ($action == "mod") {	
	
		$id = $_GET['id'];

		// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
		$salle_a_modifier = $con_gespac->QueryRow ( "SELECT salle_id, salle_nom, salle_vlan, salle_etage, salle_batiment FROM salles WHERE salle_id=$id" );

		// valeur à affecter aux champs
		$salle_id 		= $salle_a_modifier[0];
		$salle_nom 		= $salle_a_modifier[1];
		$salle_vlan 	= $salle_a_modifier[2];
		$salle_etage 	= $salle_a_modifier[3];
		$salle_bat 		= $salle_a_modifier[4];

		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>

		<form action="gestion_inventaire/post_salles.php?action=mod" method="post" name="post_form" id='formulaire'>
			
			<input type=hidden name=salleid value=<?PHP echo $id;?> >
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom salle</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $salle_nom; ?>" required />
				</tr>
				
				<tr>
					<TD>VLAN</TD>
					<TD>
						<select name="vlan" size="1">
							<option selected><?PHP echo $salle_vlan; ?></option>
							<option>1 pour 5</option>
							<option>existant</option>
							<option>1 pour 5 et existant</option>
							<option>N/A</option>
						</select>
					</TD>
				</tr>

				<tr>
					<TD>Etage</TD>
					<TD><input type=text name=etage value= "<?PHP echo $salle_etage; ?>"	/></TD>
				</tr>

				<tr>
					<TD>Batiment</TD>
					<TD><input type=text name=batiment value= "<?PHP echo $salle_bat; ?>"	/></TD>
				</tr>

			</table>

			<br>
			<input type=submit value='Modifier cette salle' id="post_form">

			</center>

		</FORM>
				
	<?PHP
	}

	//********************************************* formulaire de modification prérempli
	if ($action == "del") {	
	
		$salle_id = $_GET['id'];
		$salle_nom = $con_gespac->QueryOne ( "SELECT salle_nom FROM salles WHERE salle_id=$salle_id" );

		echo "Voulez vous vraiment supprimer la salle <b>$salle_nom</b> ? <br> Le matériel contenu dans cette salle sera automatiquement remis dans le STOCK. ";
	?>	
		<center><br><br>
		<form action="gestion_inventaire/post_salles.php?action=del" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $salle_id;?>" name="salle_id">
			<input type=submit value='Supprimer' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
	<?PHP	
	}
	
	//********************************************* formulaire de modification prérempli
	if ($action == "d3e") {	
	
		echo "Voulez vous vraiment vider la salle <b>D3E</b> ? <br> TOUT le matériel contenu dans cette salle sera SUPPRIME.<br>Un fichier récapitulatif sera créé dans le gestionnaire de fichiers.";
	?>	
		<center><br><br>
		<form action="gestion_inventaire/post_salles.php?action=d3e" method="post" name="post_form" id='formulaire'>
			<input type=submit value='Vider' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
	<?PHP	
	}
	
?>
