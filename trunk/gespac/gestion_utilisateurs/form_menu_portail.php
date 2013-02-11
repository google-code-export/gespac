<?PHP

	#formulaire de modification
	#des items du menu portail !

	// lib
	include ('../config/databases.php');	// fichiers de configuration des bases de données
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');
	
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
					$('target').setStyle("display","block");
					$('target').set('html', responseText);
					SexyLightbox.close();
					window.setTimeout("document.location.href='index.php?page=grades&filter=" + $('filt').value + "'", 2500);	
				}
			
			}).send(this.toQueryString());
		});		
		
	});
	
</script>


<small>Pour ajouter un raccourci, allez dans les "MODULES" et choisissez "MENU PORTAIL"</small>

<?PHP 
	
	$grade_id = $_GET['id'];
	
	// cnx à la base de données OCS
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);
	
	
	// Requete pour récupérer les données des champs pour le grade à modifier
	$droits_menu_portail = $con_gespac->QueryOne ( "SELECT grade_menu_portail FROM grades WHERE grade_id=$grade_id");
	

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_icones = $con_gespac->QueryAll ( "SELECT mp_id, mp_icone, mp_nom, mp_url FROM menu_portail ORDER BY mp_nom" );

?>

<FORM action="gestion_utilisateurs/post_menu_portail.php?gradeid=<?PHP echo $grade_id; ?>" method="post" name="post_form" id="post_form">
	
	<center>
	
	</br>
	
	<table width=500 id='portail_table'>
		
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
					
					$mp_id		 	= $record['mp_id'];	
					$mp_icone	 	= "./img/" . $record['mp_icone'];
					$mp_nom 		= $record['mp_nom'];
					$mp_lien		= $record['mp_url'];	
					
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
