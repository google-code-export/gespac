<?PHP

	#formulaire de modification
	#des droits !



	header("Content-Type:text/html; charset=iso-8859-1"); 	// règle le problème d'encodage des caractères

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');

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
		
		
		// Pour checker toutes les cases en lecture
		$('L_CheckAll').addEvent ('click', function(e) {
			
			if ( $('L_CheckAll').checked == true ) {			
				$$('.Lchk').each(function (item) {	item.checked = true; }) // on coche tout
			} else {
				$$('.Lchk').each(function (item) {	item.checked = false; }) // on decoche toutes les lectures
				$$('.Echk').each(function (item) {	item.checked = false; }) // on decoche toutes les écritures (parce que si on a pas la lecture, ey, ça sert à rien de pouvoir écrire)
				$('E_CheckAll').checked = false;
			}	
		});
		
		// Pour checker toutes les cases en écriture
		$('E_CheckAll').addEvent ('click', function(e) {
			
			if ( $('E_CheckAll').checked == true ) {			
				$$('.Echk').each(function (item) {	item.checked = true; }) // on coche toutes les écritures
				$$('.Lchk').each(function (item) {	item.checked = true; }) // on coche toutes les lectures parce que si on peut écrire, on doit pouvoir lire aussi
				$('L_CheckAll').checked = true;
			} else {
				$$('.Echk').each(function (item) {	item.checked = false; }) // on decoche toutes les écritures
			}	
		});
		
	});
	
</script>


<?PHP

	// cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$id = $_GET['id'];
	
	
	// Requete pour récupérer les données des champs pour le user à modifier
	$grade_a_modifier = $con_gespac->QueryRow ( "SELECT grade_id, grade_nom, grade_menu FROM grades WHERE grade_id=$id" );		
	
	// valeurs à affecter aux champs
	$grade_id 			= $grade_a_modifier[0];
	$grade_nom	 		= $grade_a_modifier[1];
	$grade_menu	 		= $grade_a_modifier[2];

	echo "<h2>formulaire de modification des droits du grade $grade_nom</h2><br>";

	?>
	
	<form action="gestion_utilisateurs/post_droits.php?gradeid=<?PHP echo $grade_id; ?>" method="post" name="post_form" id="post_form">
	
		<center>
		
		<table width=500 class="tablehover">
			
			<th>Item</th>
			<th>Lecture <input type=checkbox id=L_CheckAll> </th>
			<th>Ecriture <input type=checkbox id=E_CheckAll> </th>

			<?PHP
				$menu_precedent = "00"; // J'initialise le menu pour la gestion des groupes d'items
				$tr_class = "tr1";

				$liste_items = $con_gespac->QueryAll ( "SELECT * FROM droits order by droit_index" );		
	

				foreach ($liste_items as $ligne) {
					
					$droit_id = $ligne ['droit_id'];
					$droit_index = $ligne ['droit_index'];
					$droit_titre = $ligne ['droit_titre'];
					$droit_page = $ligne ['droit_page'];
					$droit_etendue = $ligne ['droit_etendue'];
					$droit_description = $ligne ['droit_description'];
					
										
					$explode_index = explode ("-", $droit_index);
					$menu = $explode_index[0];	// Le menu courant (pour la couleur des lignes)

					// Si jamais on change de bloc d'items
					if ( $menu <> $menu_precedent ) {
						$tr_class = $tr_class == "tr1" ? "tr2" : "tr1" ;					
						$menu_precedent = $menu;	
					}
					
										
					// J'initialise, on sait jamais
					$L_value = "";
					if ($droit_etendue == 1) $E_value = "";
					
					// Si je trouve une valeur L-id ou E-id dans mon tableau -> alors je coche.
					$L_value = preg_match ("#L-$droit_index#", $grade_menu);
					if ($droit_etendue == 1) $E_value = preg_match ("#E-$droit_index#", $grade_menu);
					
					$L_check = $L_value == 1 ? "checked" : "" ;
					if ($droit_etendue == 1) $E_check = $E_value == 1 ? "checked" : "" ;
					
					
					echo "<tr class='$tr_class' title='$droit_description'>";
						echo "<TD>$droit_titre</TD>";
						echo "<TD><input type=checkbox id='L-$droit_index' class='Lchk' name='L-$droit_index' $L_check onclick=\"decocher_ecriture('$droit_index'); \"/></TD>";
						if ($droit_etendue == 1) echo "<TD><input type=checkbox id='E-$droit_index' class='Echk' name='E-$droit_index' $E_check onclick=\"cocher_lecture('$droit_index'); \"/></TD>";
						else echo "<TD>&nbsp;</TD>";
					echo "</tr>";
					
					
				}
			?>
		
		</table>
		
		<br>
		<input type=submit value='Modifier les droits' />

		</center>

	</FORM>
