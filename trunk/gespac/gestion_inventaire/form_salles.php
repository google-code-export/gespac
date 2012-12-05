<?PHP

	#formulaire d'ajout et de modification
	#des marques



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

		var bt_submit = document.getElementById("post_salle");
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
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').setStyle("display","block");
					$('target').set('html', responseText);
					SexyLightbox.close();
					window.setTimeout("document.location.href='index.php?page=salles&filter=" + $('filt').value + "'", 1500);
				}
			
			}).send(this.toQueryString());
		});			
	});
		
</script>

<?PHP

	$id = $_GET['id'];

	if ( $id == '-1' ) {	// Formulaire vierge de création
	
		echo "<h2>formulaire de création d'une salle</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_inventaire/post_salles.php?action=add" method="post" name="post_form" id="post_form">
		
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
			<input type=submit value='Ajouter une salle' id="post_salle" disabled>

			</center>

		</FORM>
				

		<?PHP
	} 
	else {	// formulaire de modification prérempli
	
		echo "<h2>formulaire de modification d'une salle</h2><br>";
		

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

		<form action="gestion_inventaire/post_salles.php?action=mod" method="post" name="post_form" id="post_form">
			
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
			<input type=submit value='Modifier cette salle'>

			</center>

		</FORM>
				
	<?PHP
	}	
?>