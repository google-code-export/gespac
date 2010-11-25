<?PHP

	#formulaire de modification
	#des droits !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<!--  SERVEUR AJAX 
<script type="text/javascript" src="server.php?client=all"></script>
-->

<script type="text/javascript"> 
	
	// vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = $("post_user");
		var grade_nom = $("nom").value;
		var grade_niveau = $("niveau").value;
		
		if (grade_nom == "" || grade_niveau == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	};
	
	
	// si on coche en écriture, la lecture s'active aussi
	function cocher_lecture (item) {
		
		var item_E = $("E-" + item);
		var item_L = $("L-" + item);
		
		if ( item_E.checked == true)
			item_L.checked = true;	
	}
	
	//si on décoche la lecture, c'est l'écriture qui se désactive
	function decocher_ecriture (item) {
		 
		var item_E = $("E-" + item);
		var item_L = $("L-" + item);
		 
		 if ( item_L.checked == false )
			item_E.checked = false;
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

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$id = $_GET['id'];
	
	
	// Requete pour récupérer les données des champs pour le user à modifier
	$grade_a_modifier = $db_gespac->queryRow ( "SELECT grade_id, grade_nom, grade_niveau, grade_menu FROM grades WHERE grade_id=$id" );		
	
	// valeurs à affecter aux champs
	$grade_id 			= $grade_a_modifier[0];
	$grade_nom	 		= $grade_a_modifier[1];
	$grade_niveau	 	= $grade_a_modifier[2];
	$grade_menu	 		= $grade_a_modifier[3];

	echo "<h2>formulaire de modification des droits du grade $grade_nom</h2><br>";

	?>
	
	<form action="gestion_utilisateurs/post_droits.php?gradeid=<?PHP echo $grade_id; ?>" method="post" name="post_form" id="post_form">
	
		<center>
		
		<table width=500 class="tablehover">
			
			<th>Item</th>
			<th>Lecture</th>
			<th>Ecriture</th>

			<?PHP
				$lines = file('../menu.txt');

				foreach ($lines as $line) {
				
					$line = str_replace('"','',$line);
					$explode_line = explode (";", $line);
					$id = $explode_line[0];
					$value = $explode_line[1];
					
					// J'initialise, on sait jamais
					$L_value = "";
					$E_value = "";
					
					// Si je trouve une valeur L-id ou E-id dans mon tableau -> alors je coche.
					$L_value = preg_match ("#L-$id#", $grade_menu);
					$E_value = preg_match ("#E-$id#", $grade_menu);
					
					$L_check = $L_value == 1 ? "checked" : "" ;
					$E_check = $E_value == 1 ? "checked" : "" ;
					
					
					echo "
						<tr>
							<TD>$value</TD>
							<TD><input type=checkbox id='L-$id' name='L-$id' $L_check onclick=\"decocher_ecriture('$id'); \"/></TD>
							<TD><input type=checkbox id='E-$id' name='E-$id' $E_check onclick=\"cocher_lecture('$id'); \"/></TD>
						</tr>";
				}
			?>
		
		</table>
		
		<br>
		<input type=submit value='Modifier les droits'>

		</center>

	</FORM>

