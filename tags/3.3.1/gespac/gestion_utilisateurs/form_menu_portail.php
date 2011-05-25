<?PHP

	#formulaire de modification
	#des items du menu portail !



	header("Content-Type:text/html; charset=iso-8859-1"); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

?>


<script type="text/javascript"> 

	window.addEvent('domready', function(){
		
		// MOTEUR AJAX
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (pour les url trop longues)
					window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_grades.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});		
		
	});
	
</script>


<h3>Visualisation des items du portail</h3>
<br>


<?PHP 

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données OCS
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
		
	$grade_id = $_GET['id'];
	
	
	// Requete pour récupérer les données des champs pour le grade à modifier
	$droits_menu_portail = $db_gespac->queryOne ( "SELECT grade_menu_portail FROM grades WHERE grade_id=$grade_id" );		
	

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_icones = $db_gespac->queryAll ( "SELECT mp_id, mp_icone, mp_nom, mp_url FROM menu_portail ORDER BY mp_nom" );

?>

<FORM action="gestion_utilisateurs/post_menu_portail.php?gradeid=<?PHP echo $grade_id; ?>" method="post" name="post_form" id="post_form">
	
	<center>
	
	</br>
	
	<table width=500 id='portail_table' class="tablehover">
		
		<th>&nbsp;</th>
		<th>Icone</th>
		<th>Nom</th>
		<th>Url</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
				
		
		<?PHP	

			$compteur = 0; // Pour alternance des couleurs
			
			// On parcourt le tableau
			foreach ($liste_des_icones as $record ) {
							
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
					
					$mp_id		 	= $record[0];	
					$mp_icone	 	= "./img/" . $record[1];
					$mp_nom 		= $record[2];
					$mp_lien		= $record[3];	
					
					$menu_portail_exist = preg_match ("#item$mp_id#", $droits_menu_portail);
					$check = $menu_portail_exist == 1 ? "checked" : "" ;	
					
					echo "<td><input type=checkbox id='item$mp_id' class='Lchk' name='item$mp_id' $check \"/></td>";
					echo "<td width=40><img height=30 src=$mp_icone></td>";
					echo "<td>" . $mp_nom . "</td>";
					echo "<td>" . $mp_lien . "</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";

				echo "</tr>";
				
				$compteur++;
			}
		?>	

	</table>
		
	<br>
	<input type="submit" value='Modifier le menu du portail' />
	
	</center>
	
</FORM>
