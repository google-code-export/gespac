<?PHP
	
	/* 
		Fichier pour sélection des machines à réveiller
	
	*/

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
?>

<script type="text/javascript" src="server.php?client=all"></script>


<script type="text/javascript">	

	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";

			
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
		
		var nb_selectionnes = document.getElementById('nb_selectionnes');
		
		var ligne = "tr_id" + tr_id;	//on récupère l'tr_id de la row
		var li = document.getElementById(ligne);	
		
		if ( li.style.display == "" ) {	// si une ligne est masquée on ne la selectionne pas (pratique pour le filtre)
		
			switch (check) {
				case 1: // On force la selection si la ligne n'est pas déjà cochée
					if ( !table_id.contains(tr_id) ) { // la valeur n'existe pas dans la liste
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;
				
				case 0: // On force la déselection
					table_id.erase(tr_id);
					nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines sélectionnées			
					// alternance des couleurs calculée avec la parité
					if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
				break;
				
				
				default:	// le check n'est pas précisé, la fonction détermine si la ligne est selectionnée ou pas
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	 // On entre le nombre de machines sélectionnées			

						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					
					} else {	// le tr_id n'est pas trouvé dans la liste, on créé un nouvel tr_id à la fin du tableau
						table_id.push(tr_id);
						li.className = "selected";
						nb_selectionnes.innerHTML = "<small>[" + (table_id.length-1) + "]</small>";	// On entre le nombre de machines sélectionnées	
					}
				break;			
			}
	
			// on concatène tout le tableau dans une chaine de valeurs séparées par des ;
			document.getElementById('materiel_a_poster').value = table_id.join(";");
			

			if ( $('materiel_a_poster').value != "" ) 
				$('wakethem').style.display = "";
			else 
				$('wakethem').style.display = "none";
		}
	}

		
	
	// *********************************************************************************
	//
	//			ferme la smoothbox et rafraichis la page
	//
	// *********************************************************************************	
	
	function refresh_quit () {
		// lance la fonction avec un délais de 1500ms
		window.setTimeout("HTML_AJAX.replace('conteneur', 'modules/wol/voir_liste_wol.php');", 150000);
		TB_remove();
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




<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP
	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_mac FROM materiels, marques, salles WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND mat_mac <> '' ) ORDER BY mat_nom" );
?>
	
	<span id="nb_selectionnes">[0]</span> machines à réveiller.
	
	
	<center>
	
	<form name=elements_selectionnes id=elements_selectionnes  onsubmit="return !HTML_AJAX.formSubmit(this,'target');" action="modules/wol/post_wol.php" method="post">
	
		<!--------------------------------------------	LISTE DES ID A POSTER	------------------------------------------------>
		<input type=hidden name=materiel_a_poster id=materiel_a_poster value=''>	

		<input type=submit id="wakethem" value="Wake" onclick="refresh_quit ();" style="display:none">	
		
	</form>
	

	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'wol_table', '1')" type="text"></center>
	</form>
	
	
	
	<table class="tablehover" id="wol_table" width=870>
	
		<th> <input type=checkbox id=checkall onclick="checkall('wol_table');" > </th>
		<th>Nom</th>
		<!--<th>DSIT</th>-->
		<th>Serial</th>
		<th>Etat</th>
		<!--<th>Famille</th>
		<th>Sous-famille</th>
		<th>Marque</th>
		<th>Modèle</th>-->
		<th>Salle</th>
		<th>MacADD</th>
		
		<?PHP	
			
	
		
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_materiels as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";

				
				$nom 		= $record[0];
				$dsit 		= $record[1];
				$serial 	= $record[2];
				$etat 		= $record[3];
				$marque		= $record[4];
				$model 		= $record[5];
				$type 		= $record[6];
				$stype		= $record[7];
				$id 		= $record[8];
				$salle 		= $record[9];
				$salle_id 	= $record[10];
				$mac 		= $record[11];
			
				
				// test si la machine est prétée ou pas
				$rq_machine_pretee = $db_gespac->queryAll ( "SELECT mat_id FROM materiels WHERE user_id<>1 AND mat_id=$id" );
				$mat_id = @$rq_machine_pretee[0][0];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
						
				if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
						$id_pret = 0;
					} else {	// la machine est prêtée ($mat_id existe)
						$id_pret = 1;
					}
					
				
				echo "<tr id=tr_id$id class=$tr_class>";
					/*	chckbox	*/	echo "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur) ; \"> </td>";	
					/*	nom		*/	echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$nom' class='smoothbox' title='Caractéristiques de $nom'>$nom</a> </td>";
					/*	dsit			echo "<td> $dsit </td>";*/
					/*	serial	*/		echo "<td> $serial </td>";
					/*	etat	*/		echo "<td> $etat </td>";
					/*	type			echo "<td> <a href='gestion_inventaire/voir_membres-marque_type.php?height=480&width=720&marque_type=$type' class='smoothbox' title='membres du type de marque $type'>$type</a></td>";*/
					/*	stype			echo "<td> <a href='gestion_inventaire/voir_membres-marque_stype.php?height=480&width=720&marque_stype=$stype' class='smoothbox' title='membres de sous type $stype'>$stype</a></td>";*/
					/*	marque		echo "<td> <a href='gestion_inventaire/voir_membres-marque_marque.php?height=480&width=720&marque_marque=$marque' class='smoothbox' title='membres de marque $marque'>$marque</a></td>";*/
					/*	modele		echo "<td> <a href='gestion_inventaire/voir_membres-marque_model.php?height=480&width=720&marque_model=$model' class='smoothbox' title='membres de modèle $model'>$model</a></td>";*/
					/*	salle	*/		echo "<td> <a href='gestion_inventaire/voir_membres_salle.php?height=480&width=640&salle_id=$salle_id' class='smoothbox' title='Membres de la salle $salle'>$salle</a> </td>";
					/*	macaddr	*/	echo "<td> $mac </td>";

				echo "</tr>";
				
				$compteur++;
			}
		?>		
		
	</table>
	</center>
	
	<br>
	
	
<?PHP
	// On se déconnecte de la db
	$db_gespac->disconnect();
?>
