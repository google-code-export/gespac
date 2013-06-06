<?PHP

	#formulaire de modification
	#des items du menu portail !

	// lib
	include ('../config/databases.php');	// fichiers de configuration des bases de données
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');
	
?>


<script type="text/javascript"> 

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
				window.setTimeout("document.location.href='index.php?page=grades&filter=" + $('#filt').val() + "'", 2500);
			 });
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

<FORM action="gestion_utilisateurs/post_menu_portail.php?gradeid=<?PHP echo $grade_id; ?>" method="post" name="post_form" id="formulaire">
	
	<center>
	
	</br>
	
	<table class="smalltable alternate hover" id='portail_table'>
		
		<th>&nbsp;</th>
		<th>Icone</th>
		<th>Nom</th>
		<th>Url</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
				
		
		<?PHP	
			
			// On parcourt le tableau
			foreach ($liste_des_icones as $record ) {
		
				echo "<tr>";
					
					$mp_id		 	= $record['mp_id'];	
					$mp_icone	 	= "img/" . $record['mp_icone'];
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
				
			}
		?>	

	</table>
		
	<br>
	<input type="submit" value='Modifier le menu du portail' id="post_form">
	
	</center>
	
</FORM>
