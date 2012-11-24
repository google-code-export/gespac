<?PHP
session_start();

	/*
		PAGE 02-03
	
		Visualisation des salles
		
		bouton ajouter une salle
		
		sur chaque salle possibilité de la modifier
		
		de la supprimer en faisant gaffe à bien rebalancer TOUTES les machines dans la salle de stockage
	
	
	*/


	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-02-03#", $_SESSION['droits']);

	
?>



<h3>Visualisation des salles</h3>
<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";

	// Fonction de validation de la suppression d'une marque
	function validation_suppr_salle (id, salle, row) {
	
		if (id == 1 | id == 2 | id == 3) {
			alert('IMPOSSIBLE de supprimer la salle ' + salle + ' !');
		} else {
		
			var valida = confirm('Voulez-vous vraiment supprimer la salle ' + salle + ' ?\n ATTENTION, tout le matériel de cette salle sera rebasculé en salle STOCK !');
			
			// si la réponse est TRUE ==> on lance la page post_marques.php
			if (valida) {
				/*	poste la page en ajax	*/
				$('target').load("gestion_inventaire/post_salles.php?action=suppr&id=" + id);
				/*	on recharge la page au bout de 1000ms	*/
				window.setTimeout("$('conteneur').load('gestion_inventaire/voir_salles.php');", 1000);
			}
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
	<center><small>Filtrer :</small> <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'salle_table');" type="text" value=<?PHP echo $_GET['filter'];?> ></center>
</form>


<?PHP 

	// Connexion à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_salles = $con_gespac->QueryAll ( "SELECT salle_id, salle_nom, salle_vlan, salle_etage, salle_batiment, est_modifiable FROM salles ORDER BY salle_nom" );

	if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='img/add.png'>Ajouter une salle</a>";
?>
	
	<center>
	<br>
	<table class="tablehover" width=800 id='salle_table'>
		<th>Nom</th>
		<th>VLAN</th>
		<th>Etage</th>
		<th>Bâtiment</th>
				
		
		<?PHP	
			if ($E_chk) echo"<th>&nbsp</th>	<th>&nbsp</th>";

			//$option_id = 0;
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_salles as $record ) {
							
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
						
					$id		 		= $record['salle_id'];
					$nom	 		= $record['salle_nom'];
					$vlan 			= $record['salle_vlan'];
					$etage 			= $record['salle_etage'];
					$batiment 		= $record['salle_batiment'];
					$est_modifiable = $record['est_modifiable'];
					
					// valeur nominale pour la checkbox
					$chkbox_state = $apreter == 1 ? "checked" : "unchecked";
					
					// On récupère la valeur inverse pour la poster
					$change_apreter = $apreter == 1 ? 0 : 1;
					
					//faire un queryOne
					$nb_matos_dans_cette_salle 	= $con_gespac->QueryOne ( "SELECT COUNT(*) FROM materiels WHERE salle_id=$id" );

					echo "<td><a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$id' rel='slb_salles' title='membres de la salle $nom'>$nom</a> [" . $nb_matos_dans_cette_salle ."] </td>";
					echo "<td>" . $vlan . "</td>";
					echo "<td>" . $etage . "</td>";
					echo "<td>" . $batiment . "</td>";
					
					
					if ( $E_chk && $est_modifiable ) {
						echo "<td><a href='gestion_inventaire/form_salles.php?height=250&width=640&id=$id' rel='slb_salles' title='Formulaire de modification de la salle $nom'><img src='img/write.png'> </a></td>";
						echo "<td> <a href='#' onclick=\"javascript:validation_suppr_salle($id, '$nom', this.parentNode.parentNode.rowIndex);\">	<img src='img/delete.png'>	</a> </td>";
							
					} else {
						echo "<td>&nbsp</td>	<td>&nbsp</td>";
					}	
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	</center>
	
	<br>
	

<?PHP
if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?height=250&width=640&id=-1' rel='slb_salles' title='Ajouter une salle'> <img src='img/add.png'>Ajouter une salle</a>";

	// On se déconnecte de la db
	$con_gespac->Close();
?>

<script type="text/javascript">
	
	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_salles'});
	});


	// Filtre rémanent
	filter ( $('filt'), 'salle_table' );	

</script>
