<?PHP

	#formulaire d'ajout et de modification
	#des marques



	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../config/databases.php');		// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<script type="text/javascript"> 
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = document.getElementById("post_salle");
		var salle_nom = document.getElementById("nom").value;
		
		if (salle_nom == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit (filt) {

		// lance la fonction avec un d�lais de 1500ms
		
		window.setTimeout("$('conteneur').load('gestion_inventaire/voir_salles.php?filter=" + filt + "');", 1500);
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
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres et lorsqu'on poste la page formation on d�passe la taille maxi d'une url)
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
		
</script>

<?PHP

	$id = $_GET['id'];

	if ( $id == '-1' ) {	// Formulaire vierge de cr�ation
	
		echo "<h2>formulaire de cr�ation d'une salle</h2><br>";
		
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
					<TD><input type=text name=nom id=nom onkeyup="validation();"/></TD>
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
			<input type=submit value='Ajouter une salle' onclick="refresh_quit( $('filt').value );" id="post_salle" disabled>

			</center>

		</FORM>
				

		<?PHP
	} 
	else {	// formulaire de modification pr�rempli
	
		echo "<h2>formulaire de modification d'une salle</h2><br>";
		
		#***************************************************************************
		# Requete pour r�cup�rer les donn�es des champs pour la salle � modifier
		#***************************************************************************
		
		// adresse de connexion � la base de donn�es
		$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

		// options facultatives de cnx � la db
		$options = array(
			'debug'       => 2,
			'portability' => MDB2_PORTABILITY_ALL,
		);

		// cnx � la base de donn�es OCS
		$db_gespac 	= & MDB2::connect($dsn_gespac, $options);

		// stockage des lignes retourn�es par sql dans un tableau nomm� avec originalit� "array" (mais "tableau" peut aussi marcher)
		$salle_a_modifier = $db_gespac->queryAll ( "SELECT salle_id, salle_nom, salle_vlan, salle_etage, salle_batiment FROM salles WHERE salle_id=$id" );

		// valeur � affecter aux champs
		$salle_id 		= $salle_a_modifier[0][0];
		$salle_nom 		= $salle_a_modifier[0][1];
		$salle_vlan 	= $salle_a_modifier[0][2];
		$salle_etage 	= $salle_a_modifier[0][3];
		$salle_bat 		= $salle_a_modifier[0][4];

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
					<TD><input type=text name=nom id=nom value= "<?PHP echo $salle_nom; ?>"	/><input type=hidden name=ancien_nom id=nom value= "<?PHP echo $salle_nom; ?>"	/></TD>	<!-- Je colle en hidden l'ancien nom pour voir si ce dernier a �t� modifi� ou pas -->
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
			<input type=submit value='Modifier cette salle' onclick="refresh_quit( $('filt').value );" >

			</center>

		</FORM>
				
	<?PHP
	}	
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>
