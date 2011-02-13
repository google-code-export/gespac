<?PHP

	/*
		formulaire d'ajout et de modification des materiels !
		permet de cr�er un nouveau matos,
		de modifier un matos particulier
		de modifier par lot des mat�riels
	*/
	
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../config/databases.php');		// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<script type="text/javascript"> 
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit 	= $("post_materiel");
		var mat_nom 	= $("nom").value;
		var mat_serial 	= $("serial").value;
	
		if (mat_nom == "" || mat_serial == "" ) {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	// Ferme la smoothbox et rafraichit la page
	function refresh_quit (filt) {
		// lance la fonction avec un d�lais de 1000ms
		window.setTimeout("$('conteneur').load('gestion_inventaire/voir_marques.php?filter=" + filt + "');", 1000);
	}
	
	
/******************************************
	*		G�n�rateur de ssn al�atoire
	*******************************************/
	function SSNgenerator () {
		
		number = Math.floor(Math.random() * 100000);
		$('serial').value =  "NC" + number;
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

	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	
	$id = $_GET['id'];

	
	
	// *********************************************************************************
	//
	//			Formulaire ajout � partir d'une marque (champs de marque pr�remplis)
	//
	// *********************************************************************************	
		
		
		// Requ�te qui va r�cup�rer les champs � partir de la marque
		$ajout_materiel_de_marque = $db_gespac->queryAll ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques WHERE marque_id=$id" );
	
		
		// valeurs � affecter aux champs
		$materiel_id 			= $ajout_materiel_de_marque[0][0];
		$materiel_type 			= $ajout_materiel_de_marque[0][1];
		$materiel_stype			= $ajout_materiel_de_marque[0][2];
		$materiel_marque		= $ajout_materiel_de_marque[0][3];
		$materiel_modele		= $ajout_materiel_de_marque[0][4];		
		
		// Requ�te qui va r�cup�rer les origines des dotations ...
		$liste_origines = $db_gespac->queryAll ( "SELECT origine FROM origines ORDER BY origine" );
	
		// Requ�te qui va r�cup�rer les �tats des mat�riels ...
		$liste_etats = $db_gespac->queryAll ( "SELECT etat FROM etats ORDER BY etat" );
		
		echo "<h2><center>Formulaire d'ajout d'un nouveau mat�riel de marque $materiel_marque et de mod�le $materiel_modele</center></h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		
		<form action="gestion_inventaire/post_materiels.php?action=add_mat_marque" method="post" name="post_form" id="post_form">
			<input type=hidden name=add_marque_materiel value=<?PHP echo $id; ?> >
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom du materiel *</TD>
					<TD><input type=text id=nom name=nom onkeyup="validation();" /></TD>
				</tr>
				
				<tr>
					<TD>R�f�rence DSIT</TD>
					<TD><input type=text id=dsit name=dsit 	/></TD>
				</tr>
				
				<tr>
					<TD>Num�ro de s�rie *</TD> 
					<TD><input type=text id=serial name=serial onkeyup="validation();" /> <input type=button value="g�n�rer" onclick="SSNgenerator(); validation();"> </TD>
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
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine[0] ."'>" . $origine[0] ."</option>";	}	?>
						</select>

					</TD>
				</tr>
				
				<tr>
					<TD>Etat du mat�riel</TD> 
					<TD>
						<select name="etat">
							<?PHP	foreach ($liste_etats as $etat) {	$selected = $etat[0] == "Fonctionnel" ? "selected" : ""; echo "<option $selected value='" . $etat[0] ."'>" . $etat[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			
				<tr>
					<TD>Salle o� se trouve le mat�riel</TD> 
					<TD>
						<select name="salle" >
							<?PHP
								// requ�te qui va afficher dans le menu d�roulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $db_gespac->queryAll ( "SELECT DISTINCT salle_nom FROM salles" );
								foreach ( $req_salles_disponibles as $record) { 
									$salle_nom = $record[0];
									$selected = $salle_nom == "STOCK" ? " selected" : "";
									
									echo "<option $selected value='$salle_nom'>$salle_nom</option>";
								}
							?>
						</select>
					</TD>
				</tr>
			
			</table>

			<br>
			<input type=submit value='Ajouter un materiel' onclick="refresh_quit( $('filt').value );" id="post_materiel" disabled />
			</center>
		</FORM>
