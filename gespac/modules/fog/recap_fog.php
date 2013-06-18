<?PHP
	
	/* fichier récapitulatif du matériel FOG
	
		vue de la db fog (association image, @MAC... à un matériel)
		
	*/
	

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	// On regarde si la base FOG existe car dans le cas de sa non existance la page ne s'affiche pas correctement
	$link_bases = mysql_pconnect('localhost', 'root', $pass);//connexion à la base de donnée
	if(!mysql_select_db('fog', $link_bases)) {echo "Base FOG non présente, il est impossible de continuer l'affichage.";}//si la base FOG n'existe pas on arrete la page
	else {

?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>


<h3>Récapitulatif FOG</h3>

<script type="text/javascript">	
	
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
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

	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	
	$liste_materiel_fog	= $con_fog->QueryAll ( "SELECT DISTINCT hostName, hostMAC, hostID FROM hosts ORDER BY hostName" );
		
?>
	
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'table_recap_fog')" type="text"></center>
	</form>
	
	<br>
	<center>
	
	<table class="tablehover" id="table_recap_fog" width=800>
	
		<th>Nom matériel FOG</th>
		<th>Adresse MAC</th>
		<th>Image associée</th>
		<th>Groupe associé</th>
		<th>Snapins associé(s)</th>
		
	
		<?PHP
			
			$compteur = 0;
			// On parcourt le tableau comparé
			foreach ( $liste_materiel_fog as $record_fog ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
										
				$nom_fog 		= $record_fog['hostName'];
				$MAC_fog		= $record_fog['hostMAC'];
				$id				= $record_fog['hostID'];
// ?
				$liste_snapins = $con_fog->QueryAll ("SELECT sName FROM hosts, snapins, snapinAssoc WHERE hosts.hostID = snapinAssoc.saHostID AND snapins.sID = snapinAssoc.saSnapinID AND hosts.hostID = '$id'");
				
				$image_associee = $con_fog->QueryAll ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $id");
				$groupe_associe = $con_fog->QueryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $id");
	
				$compteur_snapins = count($liste_snapins);
				
				// test si une image est bien associée à un hôte
				//$image_fog = (!empty($image_associee[0][0])) ? $image_associee[0][0] : "Pas d'image associée";
				
				if (!empty($image_associee[0]['imageName'])) {
					$image_fog = $image_associee[0]['imageName'];
					
				} else {
					$image_fog = "Pas d'image associée";
					
				}
				
				// test si un groupe est bien associé à un hôte
				//$groupe_fog = (!empty($groupe_associe[0][0])) ? $groupe_associe[0][0] : "Pas de groupe associé";
				
				if (!empty($groupe_associe[0]['groupName'])) {
					$groupe_fog = $groupe_associe[0]['groupName'];
					
				} else {
					$groupe_fog = "Pas de groupe associé";
				}

				echo "<tr class=$tr_class>";
					echo "<td rowspan=$compteur_snapins> $nom_fog </td>";
					echo "<td rowspan=$compteur_snapins> $MAC_fog </td>";
					echo "<td rowspan=$compteur_snapins> $image_fog </td>";
					echo "<td rowspan=$compteur_snapins> $groupe_fog </td>";
				
					//si y a des snapins, on les liste et on les affiche dans le tableau
					if ($compteur_snapins > 0) {
						
						$i = 0;
						$bg_color = ($tr_class == "tr1") ? "tr1" : "tr2";
						
						foreach ( $liste_snapins as $record_snapins ) {
					
							$nom_snapin = $record_snapins['sName'];
							
							if ( $i == 0 ) {
									echo "<td>$nom_snapin</td></tr>";
								} else {
									echo "<tr class=$bg_color><td>$nom_snapin</td></tr>";
							}

							$i++;
						}
						
					//sinon on dit qu'il n'y a pas de snapin associé
					} else {
						$nom_snapin = "Pas de snapin associé";
						echo "<td>$nom_snapin</td></tr>";
					}
					$compteur++;
				}

		?>		

	</table>
	</center>
	<?php } ?>
