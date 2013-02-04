<?PHP

	#formulaire d'ajout et de modification
	#des grades !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

?>

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = $("post_user");
		var grade_nom = $("nom").value;
		
		if (grade_nom == "") {
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

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_grades.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>

<?PHP

	//connexion à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$id = $_GET['id'];


	
	#***************************************************************************
	# 				CREATION du grade
	#***************************************************************************
	
	
	if ( $id == '-1' ) {	// Formulaire vierge de création
	
		echo "<h2>formulaire de création d'un nouveau grade</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>

		<form action="gestion_utilisateurs/post_grades.php?action=add" method="post" name="post_form" id="post_form">
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom*</TD>
					<TD><input type=text name=nom id=nom onkeyup="validation();"/></TD>
				</tr>

			</table>

			<br>
			<input id='post_user' type=submit value='Ajouter grade' disabled>
			</center>

		</FORM>
				

		<?PHP
		
		
		
		
		#***************************************************************************
		# 				MODIFICATION du grade
		#***************************************************************************
		
		
		
	} else {	// formulaire de modification prérempli
	
		echo "<h2>formulaire de modification d'un grade</h2><br>";
		
		// Requete pour récupérer les données des champs pour le user à modifier
		$grade_a_modifier = $con_gespac->QueryRow ( "SELECT grade_id, grade_nom FROM grades WHERE grade_id=$id" );		
		
		// valeurs à affecter aux champs
		$grade_id 			= $grade_a_modifier[0];
		$grade_nom	 		= $grade_a_modifier[1];

		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_utilisateurs/post_grades.php?action=mod" method="post" name="post_form" id="post_form">
			<input type=hidden name=id value=<?PHP echo $grade_id;?> >
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom *</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $grade_nom; ?>" 	/></TD>
				</tr>
				
			</table>
			
			<br>
			<input type=submit value='Modifier ce grade'>

			</center>

		</FORM>
		
		<?PHP
	}	
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>
