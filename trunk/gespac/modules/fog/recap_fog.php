<?PHP
	
	/* fichier récapitulatif du matériel FOG
	
		vue de la db fog (association image, @MAC... à un matériel)
		
	*/
	

	// On regarde si la base FOG existe car dans le cas de sa non existance la page ne s'affiche pas correctement
	$link_bases = mysql_pconnect('localhost', 'root', $pass);//connexion à la base de donnée
	if(!mysql_select_db('fog', $link_bases)) {echo "Base FOG non présente, il est impossible de continuer l'affichage.";}//si la base FOG n'existe pas on arrete la page
	else {

?>


<div class="entetes" id="entete-recapfog">	

	<span class="entetes-titre">RECAPITULATIF FOG<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">La liste des machines présentes dans FOG avec association aux groupes et aux snapins.</div>

	<span class="entetes-options">

		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'table_recap_fog');" type="text"> 
				<span id="nb_filtre" title="nombre de machines affichées"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>

<script type="text/javascript">	


	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var compteur = 0;
				
		for (var r = 1; r < table.rows.length; r++){
			
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
					displayStyle = '';
					compteur++;
				}	
				else {	// on masque les rows qui ne correspondent pas
					displayStyle = 'none';
					break;
				}
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;	
			
			$('nb_filtre').innerHTML = "<small>" + compteur + "</small>";
		}
	}	
	
</script>

<?PHP

	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	$liste_materiel_fog	= $con_fog->QueryAll ( "SELECT DISTINCT hostName, hostMAC, hostID FROM hosts ORDER BY hostName" );
		
?>
	
	<center>
	
	<table class="tablehover" id="table_recap_fog">
	
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
				$groupe_fog = "";
				$snapin_fog = "";
				$image_fog = "";
				
				$liste_snapins = $con_fog->QueryAll ("SELECT sName FROM hosts, snapins, snapinAssoc WHERE hosts.hostID = snapinAssoc.saHostID AND snapins.sID = snapinAssoc.saSnapinID AND hosts.hostID = '$id'");
				
				$image_associee = $con_fog->QueryOne ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $id");
				$groupes_associes = $con_fog->QueryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $id");
	
				$compteur_snapins = count($liste_snapins);
				

				// Image associée
				if (!empty($image_associee)) $image_fog = $image_associee;
				else $image_fog = "Pas d'image associée";
				
				// Groupes associés
				if (!empty($groupes_associes)) foreach ($groupes_associes as $groupe) $groupe_fog .= $groupe["groupName"] . "<br>";	
				else $groupe_fog = "Pas de groupe associé";
				
				// snapins associés
				if (!empty($liste_snapins)) foreach ($liste_snapins as $snapin) $snapin_fog .= $snapin["sName"] . "<br>";	
				else $snapin_fog = "Pas de snapin associé";
				


				echo "<tr class=$tr_class>";
					echo "<td> $nom_fog </td>";
					echo "<td> $MAC_fog </td>";
					echo "<td> $image_fog </td>";
					echo "<td> $groupe_fog </td>";
					echo "<td> $snapin_fog </td>";
				echo "</tr>";
			
					$compteur++;
				}

		?>		

	</table>
	</center>
	<?php } ?>
