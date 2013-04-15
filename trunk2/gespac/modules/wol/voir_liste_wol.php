<?PHP
	session_start();
	
	/* 
		Fichier pour sélection des machines à réveiller
	*/


	// vérifie le droit d'ouverture de la page
	$L_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#L-07-02#", $_SESSION['droits']);

	if (!$L_chk) exit("<center><h2>Vous n'avez pas les droits pour ouvrir cette page.</h2></center>");


?>

<div class="entetes" id="entete-wol">	

	<span class="entetes-titre">WAKE ON LAN<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Cette page permet d'envoyer un signal d'allumage aux machines sélectionnées.</div>

	<span class="entetes-options">

		<span class="option"><?PHP
			echo "
			<form action='modules/wol/post_wol.php' method='post' name='post_form' id='post_form'>
				<input type=hidden name=materiel_a_poster id=materiel_a_poster value=''>	
				
				<span id='nb_selectionnes' title=\"nombre de machines sélectionnées\"></span>
				<span id='wakethem' style='display:none;'> <input type=submit value='Réveiller la selection'></span>					
				
			</form>";?>
		</span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'wol_table');" type="text" value=<?PHP echo $_GET['filter'];?>> 
				<span id="nb_filtre" title="nombre de machines affichés"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id as salleid, mat_mac FROM materiels, marques, salles WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND mat_mac <> '' ) ORDER BY mat_nom" );
?>

	<center>
		
	<table class="tablehover" id="wol_table">
	
		<th> <input type=checkbox id=checkall onclick="checkall('wol_table');" > </th>
		<th>Nom</th>
		<th>Serial</th>
		<th>Etat</th>
		<th>Salle</th>
		<th>MacADD</th>
		
		<?PHP	
			
	
		
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";

				$nom 		= $record['mat_nom'];
				$dsit 		= $record['mat_dsit'];
				$serial 	= $record['mat_serial'];
				$etat 		= $record['mat_etat'];
				$marque		= $record['marque_marque'];
				$model 		= $record['marque_model'];
				$type 		= $record['marque_type'];
				$stype		= $record['marque_stype'];
				$id 		= $record['mat_id'];
				$salle 		= $record['salle_nom'];
				$salle_id 	= $record['salleid'];
				$mac 		= $record['mat_mac'];
			
				// On reteste la validité de l'adresse mac
				$mac_valide = preg_match("#([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}#", $mac);
			
				if ($mac_valide) {
					echo "<tr id=tr_id$id class=$tr_class>";
						/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
						/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$nom' rel='slb_wol' title='Caractéristiques de $nom'>$nom</a> </td>";
						/*	serial	*/	echo "<td> $serial </td>";
						/*	etat	*/	echo "<td> $etat </td>";
						/*	salle	*/	echo "<td> <a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$salle_id' rel='slb_wol' title='Membres de la salle $salle'>$salle</a> </td>";
						/*	macaddr	*/	echo "<td> $mac </td>";

					echo "</tr>";
					
					$compteur++;
				}
			}
		?>		
		
	</table>
	</center>
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>


<script type="text/javascript">
	
	/******************************************
	*		AJAX
	*******************************************/
	
	window.addEvent('domready', function(){
		
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_wol'});
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
		
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
					$('target').set('html', responseText);
					window.setTimeout("document.location.href='index.php?page=wol'", 2500);	
				}
			
			}).send(this.toQueryString());
		});			
	});
	

	
	// *********************************************************************************
	//
	//				Selection/déselection de toutes les rows
	//
	// *********************************************************************************	
	
	function checkall(_table) {
		var table = document.getElementById(_table);	// le tableau du matériel
		var checkall_box = document.getElementById('checkall');	// la checkbox "checkall"
		
		for ( var i = 1 ; i < table.rows.length ; i++ ) {

			var lg = table.rows[i].id					// le tr_id (genre tr115)
			
			if (checkall_box.checked == true) {
				document.getElementsByName("chk")[i - 1].checked = true;	// on coche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 1 )					//on selectionne la ligne et on ajoute l'index
			} else {
				document.getElementsByName("chk")[i - 1].checked = false;	// on décoche toutes les checkbox
				select_cette_ligne( lg.substring(5), i, 0 )					//on déselectionne la ligne et on la retire de l'index
			}
		}
	}
	
	
	// *********************************************************************************
	//
	//				Ajout des index pour postage sur clic de la checkbox
	//
	// *********************************************************************************	
	 
	function select_cette_ligne( tr_id, num_ligne, check ) {

		var chaine_id = document.getElementById('materiel_a_poster').value;
		var table_id = chaine_id.split(";");
		
		var ligne = "tr_id" + tr_id;	//on récupère l'tr_id de la row
		var li = document.getElementById(ligne);	
		
		if ( li.style.display == "" ) {	// si une ligne est masquée on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas déjà cochée
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
					}
				break;
				
				case 0: // On force la déselection
					table_id.erase(tr_id);
					// alternance des couleurs calculée avec la parité
					if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			$('materiel_a_poster').value = table_id.join(";");
			
			
			if ( $('materiel_a_poster').value != "" ) {
				$('wakethem').setStyle("display", "inline");
				$('nb_selectionnes').innerHTML = "->" + table_id.length-1;	// On entre le nombre de machines sélectionnées
			}
			else 
				$('wakethem').setStyle("display", "none");
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
