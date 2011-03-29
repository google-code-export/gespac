<?PHP

	session_start();

	/*
		PAGE 07-06
	
		Visualisation des icônes disponibles dans le portail 	
	
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-06#", $_SESSION['droits']);

	
?>



<h3>Visualisation des items du portail</h3>
<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";

	// Fonction de validation de la suppression d'une marque
	function validation_suppr_item (id, item) {

		var valida = confirm("Voulez-vous vraiment supprimer l'item " + item + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_menu_portail.php
		if (valida) {
			/*	poste la page en ajax	*/
			$('target').load("modules/menu_portail/post_menu_portail.php?action=suppr&id=" + id);
			/*	on recharge la page au bout de 1000ms	*/
			window.setTimeout("$('conteneur').load('modules/menu_portail/voir_menu_portail.php');", 1000);
		}
	}
	
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
				
		for (var r = 1; r < table.rows.length; r++){
			
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
					displayStyle = '';
				}	
				else {	// on masque les rows qui ne correspondent pas
					displayStyle = 'none';
					break;
				}
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;	
		}
	}	
	
	
	
</script>



<!-- 	bouton pour le filtrage du tableau	-->
<form id="filterform">
	<center><small>Filtrer :</small> <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'portail_table');" type="text" value=<?PHP echo $_GET['filter'];?> ></center>
</form>


<?PHP 

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données OCS
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_icones = $db_gespac->queryAll ( "SELECT mp_id, mp_icone, mp_nom, mp_url FROM menu_portail ORDER BY mp_nom" );


	if ( $E_chk ) echo "<a href='modules/menu_portail/form_menu_portail.php?height=200&width=640&id=-1' rel='slb_menu_portail' title='Ajouter un item'> <img src='img/add.png'>Ajouter un item</a>";
?>
	
	<center>
	<br>
	<table class="tablehover" width=800 id='portail_table'>
		<th>Icone</th>
		<th>Nom</th>
		<th>Url</th>
				
		
		<?PHP	
			if ($E_chk) echo"<th>&nbsp</th>	<th>&nbsp</th>";

			//$option_id = 0;
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_icones as $record ) {
							
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
					
					$mp_id		 	= $record[0];	
					$mp_icone	 	= "./img/" . $record[1];
					$mp_nom 		= $record[2];
					$mp_lien		= $record[3];		
					
					
					echo "<td width=40><img height=30 src=$mp_icone></td>";
					echo "<td>" . $mp_nom . "</td>";
					echo "<td>" . $mp_lien . "</td>";
					
					if ( $E_chk && $mp_nom <> "FOG" && $mp_nom <> "OCS" && $mp_nom <> "RESTAURATION"  ) {
						echo "<td width=20><a href='modules/menu_portail/form_menu_portail.php?height=180&width=640&id=$mp_id' rel='slb_menu_portail' title='Formulaire de modification de l`item $mp_nom'><img src='img/write.png' style='display:$display_mod;'> </a></td>";
						echo "<td width=20> <a href='#' onclick=\"javascript:validation_suppr_item($mp_id, '$mp_nom');\">	<img src='img/delete.png' style='display:$display_del;'>	</a> </td>";
					}
					else {
						echo "<td width=20></td>";
						echo "<td width=20></td>";
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	</center>
	
	<br>
	

<?PHP
	if ( $E_chk ) echo "<a href='modules/menu_portail/form_menu_portail.php?height=200&width=640&id=-1' rel='slb_menu_portail' title='Ajouter un item'> <img src='img/add.png'>Ajouter un item</a>";

	// On se déconnecte de la db
	$db_gespac->disconnect();
?>

<script type="text/javascript">
	
	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_menu_portail'});
	});


	// Filtre rémanent
	//filter ( $('filt'), 'portail_table' );	

</script>
