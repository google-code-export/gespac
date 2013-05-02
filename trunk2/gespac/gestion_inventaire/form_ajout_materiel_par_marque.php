<?PHP

	/*
		formulaire d'ajout et de modification des materiels !
		permet de créer un nouveau matos,
		de modifier un matos particulier
		de modifier par lot des matériels
	*/
	
	
	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');		

?>

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit 	= $("#post_materiel");
		var mat_nom 	= $("#nom").val();
		var mat_serial 	= $("#serial").val();
	
		if (mat_nom == "" || mat_serial == "" ) {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	
/******************************************
	*		Générateur de ssn aléatoire
	*******************************************/
	function SSNgenerator () {
		
		number = Math.floor(Math.random() * 100000);
		$('#serial').val("NC" + number);
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
				window.setTimeout("document.location.href='index.php?page=marques&filter=" + $('#filt').val() + "'", 1500);
			 });
			 
		});	
	});

</script>

<?PHP

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	
	$id = $_GET['id'];

	
	
	// *********************************************************************************
	//
	//			Formulaire ajout à partir d'une marque (champs de marque préremplis)
	//
	// *********************************************************************************	
		
		
		// Requête qui va récupérer les champs à partir de la marque
		$ajout_materiel_de_marque = $con_gespac->QueryRow ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques WHERE marque_id=$id" );
	
		
		// valeurs à affecter aux champs
		$materiel_id 			= $ajout_materiel_de_marque[0];
		$materiel_type 			= $ajout_materiel_de_marque[1];
		$materiel_stype			= $ajout_materiel_de_marque[2];
		$materiel_marque		= $ajout_materiel_de_marque[3];
		$materiel_modele		= $ajout_materiel_de_marque[4];		
		
		// Requête qui va récupérer les origines des dotations ...
		$liste_origines = $con_gespac->QueryAll ( "SELECT origine FROM origines ORDER BY origine" );
	
		// Requête qui va récupérer les états des matériels ...
		$liste_etats = $con_gespac->QueryAll ( "SELECT etat FROM etats ORDER BY etat" );

	?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('#nom').focus();
		</script>
		
		
		<form action="gestion_inventaire/post_materiels.php?action=add_mat_marque" method="post" name="post_form" id="formulaire">
			<input type=hidden name=add_marque_materiel value=<?PHP echo $id; ?> >

			<table>
			
				<tr>
					<TD>Nom du materiel *</TD>
					<TD><input type=text id=nom name=nom required onkeyup="validation();" /></TD>
				</tr>
				
				<tr>
					<TD>Référence DSIT</TD>
					<TD><input type=text id=dsit name=dsit 	/></TD>
				</tr>
				
				<tr>
					<TD>Numéro de série *</TD> 
					<TD><input type=text id=serial name=serial required onkeyup="validation();" /> <input type=button value="générer" onclick="SSNgenerator(); validation();"> </TD>
				</tr>
				
				<tr>
					<TD>Adresse MAC</TD> 
					<TD><input type=text id=mac name=mac size=17 maxlength=17 /></TD>
				</tr>
								
				<tr>
					<TD>Origine</TD> 
					<TD>	
						<select name="origine">
							<option value=<?PHP echo $materiel_origine; ?>><?PHP echo $materiel_origine; ?></option>
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine['origine'] ."'>" . $origine['origine'] ."</option>";	}	?>
						</select>

					</TD>
				</tr>
				
				<tr>
					<TD>Etat du matériel</TD> 
					<TD>
						<select name="etat">
							<?PHP	foreach ($liste_etats as $etat) {	$selected = $etat['etat'] == "Fonctionnel" ? "selected" : ""; echo "<option $selected value='" . $etat['etat'] ."'>" . $etat['etat'] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			
				<tr>
					<TD>Salle où se trouve le matériel</TD> 
					<TD>
						<select name="salle" >
							<?PHP
								// requête qui va afficher dans le menu déroulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $con_gespac->QueryAll ( "SELECT DISTINCT salle_nom FROM salles" );
								foreach ( $req_salles_disponibles as $record) { 
									$salle_nom = $record['salle_nom'];
									$selected = $salle_nom == "STOCK" ? " selected" : "";
									
									echo "<option $selected value='$salle_nom'>$salle_nom</option>";
								}
							?>
						</select>
					</TD>
				</tr>
			
			</table>

			<br>
			<br>
			<center><input type=submit value='Ajouter le materiel' id="post_form" disabled></center>

		</FORM>
