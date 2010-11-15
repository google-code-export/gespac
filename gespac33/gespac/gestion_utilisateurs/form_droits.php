<?PHP

	#formulaire de modification
	#des droits !



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>

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
					window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_grades.php');", 150000);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
	
</script>


<?PHP

	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$id = $_GET['id'];
	
	
	// Requete pour récupérer les données des champs pour le user à modifier
	$grade_a_modifier = $db_gespac->queryRow ( "SELECT grade_id, grade_nom, grade_niveau FROM grades WHERE grade_id=$id" );		
	
	// valeurs à affecter aux champs
	$grade_id 			= $grade_a_modifier[0];
	$grade_nom	 		= $grade_a_modifier[1];
	$grade_niveau	 	= $grade_a_modifier[2];

	echo "<h2>formulaire de modification des droits du grade $grade_nom</h2><br>";
	
	
	?>
	
	<script>
		// Donne le focus au premier champ du formulaire
		$('nom').focus();
	</script>
	
	<form action="gestion_utilisateurs/post_droits.php" method="post" name="post_form" id="post_form">
	
		<input type=hidden name=id value=<?PHP echo $grade_id;?> >
		<center>
		
		<table width=500>
			
			<th>Item</th>
			<th>Lecture</th>
			<th>Ecriture</th>
		
			<tr>
				<TD>Voir inventaire</TD>
				<TD><input type=checkbox name='L-01-01' /></TD>
				<TD><input type=checkbox name='E-01-01' /></TD>
			</tr>
			<tr>
				<TD>Voir marques</TD>
				<TD><input type=checkbox name='L-01-02' /></TD>
				<TD><input type=checkbox name='E-01-02' /></TD>
			</tr>
			<tr>
				<TD>Voir salles</TD>
				<TD><input type=checkbox name='L-01-03' /></TD>
				<TD><input type=checkbox name='E-01-03' /></TD>
			</tr>
			
			
			<tr>
				<TD>Voir dossiers</TD>
				<TD><input type=checkbox name='L-02-01' /></TD>
				<TD><input type=checkbox name='E-02-01' /></TD>
			</tr>
			<tr>
				<TD>Voir interventions</TD>
				<TD><input type=checkbox name='L-02-02' /></TD>
				<TD><input type=checkbox name='E-02-02' /></TD>
			</tr>			
		

			<tr>
				<TD>Importer DB OCS</TD>
				<TD><input type=checkbox name='L-03-01' /></TD>
				<TD><input type=checkbox name='E-03-01' /></TD>
			</tr>
			<tr>
				<TD>Exports</TD>
				<TD><input type=checkbox name='L-03-02' /></TD>
				<TD><input type=checkbox name='E-03-02' /></TD>
			</tr>
			<tr>
				<TD>dump base GESPAC</TD>
				<TD><input type=checkbox name='L-03-03' /></TD>
				<TD><input type=checkbox name='E-03-03' /></TD>
			</tr>
			<tr>
				<TD>Dump base OCS</TD>
				<TD><input type=checkbox name='L-03-04' /></TD>
				<TD><input type=checkbox name='E-03-04' /></TD>
			</tr>
			<tr>
				<TD>Voir les logs</TD>
				<TD><input type=checkbox name='L-03-05' /></TD>
				<TD><input type=checkbox name='E-03-05' /></TD>
			</tr>	
			<tr>
				<TD>Importer CSV</TD>
				<TD><input type=checkbox name='L-03-06' /></TD>
				<TD><input type=checkbox name='E-03-06' /></TD>
			</tr>	

			
			<tr>
				<TD>Gestion Prêts</TD>
				<TD><input type=checkbox name='L-04-01' /></TD>
				<TD><input type=checkbox name='E-04-01' /></TD>
			</tr>	
			
			
			
			<tr>
				<TD>Voir utilisateurs</TD>
				<TD><input type=checkbox name='L-05-01' /></TD>
				<TD><input type=checkbox name='E-05-01' /></TD>
			</tr>	
			<tr>
				<TD>Voir Grades</TD>
				<TD><input type=checkbox name='L-05-02' /></TD>
				<TD><input type=checkbox name='E-05-02' /></TD>
			</tr>	
			<tr>
				<TD>Importer comptes IACA</TD>
				<TD><input type=checkbox name='L-05-03' /></TD>
				<TD><input type=checkbox name='E-05-03' /></TD>
			</tr>				
			

			
				
			<tr>
				<TD>Récap Fog</TD>
				<TD><input type=checkbox name='L-06-01' /></TD>
				<TD><input type=checkbox name='E-06-01' /></TD>
			</tr>	
			<tr>
				<TD>Wake on Lan</TD>
				<TD><input type=checkbox name='L-06-02' /></TD>
				<TD><input type=checkbox name='E-06-02' /></TD>
			</tr>		
			<tr>
				<TD>Exports Perso</TD>
				<TD><input type=checkbox name='L-06-03' /></TD>
				<TD><input type=checkbox name='E-06-03' /></TD>
			</tr>			


			
			
			<tr>
				<TD>Fiche collège</TD>
				<TD><input type=checkbox name='L-07-01' /></TD>
				<TD><input type=checkbox name='E-07-01' /></TD>
			</tr>
			<tr>
				<TD>flux RSS</TD>
				<TD><input type=checkbox name='L-07-02' /></TD>
				<TD><input type=checkbox name='E-07-02' /></TD>
			</tr>
			<tr>
				<TD>Stats camemberts</TD>
				<TD><input type=checkbox name='L-07-03' /></TD>
				<TD><input type=checkbox name='E-07-03' /></TD>
			</tr>	
			<tr>
				<TD>Stats bâtons</TD>
				<TD><input type=checkbox name='L-07-04' /></TD>
				<TD><input type=checkbox name='E-07-04' /></TD>
			</tr>	
			<tr>
				<TD>Stats Utilisation du parc</TD>
				<TD><input type=checkbox name='L-07-05' /></TD>
				<TD><input type=checkbox name='E-07-05' /></TD>
			</tr>	

			
		
		</table>
		
		<br>
		<input type=submit value='Modifier les droits'>

		</center>

	</FORM>

