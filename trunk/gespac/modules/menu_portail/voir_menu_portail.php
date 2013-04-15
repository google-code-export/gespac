<?PHP

	session_start();

	/*
		PAGE 07-06
	
		Visualisation des icônes disponibles dans le portail 	
	
	*/


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-06#", $_SESSION['droits']);

	
?>


<div class="entetes" id="entete-modportail">	

	<span class="entetes-titre">ICONES DU PORTAIL<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des raccourcis du portail.<br>L'affectation des raccourcis à un groupe d'utilisateurs se fait dans les <a href='index.php?page=grades'>GRADES</a>.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='index.php?page=grades' title='Aller à la page de gestion des grades'><img src='" . ICONSPATH . "accueil.png'></a>";?></span>
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='modules/menu_portail/form_menu_portail.php?height=200&width=640&id=-1' rel='slb_menu_portail' title='Ajouter un item'><img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'portail_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<script type="text/javascript">	

	// Fonction de validation de la suppression d'une marque
	function validation_suppr_item (id, item) {

		var valida = confirm("Voulez-vous vraiment supprimer l'item " + item + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_menu_portail.php
		if (valida) {
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("modules/menu_portail/post_menu_portail.php?action=suppr&id=" + id);
			window.setTimeout("document.location.href='index.php?page=modportail'", 1500);		
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


<?PHP 
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_icones = $con_gespac->QueryAll ( "SELECT mp_id, mp_icone, mp_nom, mp_url, est_modifiable FROM menu_portail ORDER BY mp_nom" );

?>
	
	<center>
	<table class="tablehover" id='portail_table'>
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
					
					$mp_id		 		= $record['mp_id'];	
					$mp_icone	 		= "./img/" . $record['mp_icone'];
					$mp_nom 			= $record['mp_nom'];
					$mp_lien			= $record['mp_url'];
					$est_modifiable		= $record['est_modifiable'];
					
					//Si l'icone n'existe pas
					if (!file_exists($mp_icone)) $mp_icone="./img/cancel.png";
					
					echo "<td width=40><img height=30 src=$mp_icone></td>";
					echo "<td>" . $mp_nom . "</td>";
					echo "<td>" . $mp_lien . "</td>";
					
					if ( $E_chk && $est_modifiable) {
						echo "<td width=20><a href='modules/menu_portail/form_menu_portail.php?height=180&width=640&id=$mp_id' rel='slb_menu_portail' title='Formulaire de modification de l`item $mp_nom'><img src='img/write.png' style='display:$display_mod;'> </a></td>";
						echo "<td width=20> <a href='#' onclick=\"javascript:validation_suppr_item($mp_id, '$mp_nom');\">	<img src='img/delete.png' style='display:$display_del;'>	</a> </td>";
					} else {
						echo "<td width=20></td>";
						echo "<td width=20></td>";
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	</center>
	
<?PHP
	$con_gespac->Close();
?>

<script type="text/javascript">
	
	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_menu_portail'});
	});


	// Filtre rémanent
	//filter ( $('filt'), 'portail_table' );	

</script>
