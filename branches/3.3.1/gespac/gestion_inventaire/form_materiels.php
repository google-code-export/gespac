<?PHP

	/*
		formulaire d'ajout et de modification des materiels !
		permet de cr�er un nouveau matos,
		de modifier un matos particulier
		de modifier par lot des mat�riels
	*/
	
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res

	include ('../config/databases.php');		// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

?>


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
		var elements_liste = "";
		var nb_resultats = 0;
		
			
		if (phrase.value == "") {	// Si la phrase est nulle, on masque toutes les lignes
			for (var r = 1; r < table.rows.length; r++)	table.rows[r].style.display = "none";	
		}
		else {			
			for (var r = 1; r < table.rows.length; r++){
				
				ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
				var displayStyle = 'none';
				
				for (var i = 0; i < words.length; i++) {
					if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
						displayStyle = '';
						nb_resultats++;

					}	
					else {	// on masque les rows qui ne correspondent pas
						displayStyle = 'none';
						break;
					}
				}
				
				// Affichage on / off en fonction de displayStyle
				table.rows[r].style.display = displayStyle;	
			}
			
			// Affiche le div "pasderesultat", si jamais il n'y a ... pas de r�sultat !
			if ( nb_resultats == 0 )
				$('pasderesultat').style.display = "";
			else
				$('pasderesultat').style.display = "none";
		}
	}	
	
	
		
	// *********************************************************************************
	//
	//			AJOUT d'un MARQUE par sa CORRESPONDANCE
	//
	// *********************************************************************************
	
	function validation_choisir_marque (marque_id, marque) {
			
		var valida = confirm('Voulez-vous vraiment choisir la marque ' + marque + ' ?');
		
		// si la r�ponse est TRUE ==> on colle dans un input la valeur corr_id
		if (valida) {
			$('marque_id').value = marque_id;
			
			$('choix_modele').style.display = 'none';
			$('table_modele_selectionne').style.display = '';
			
			$('modele_selectionne').value = marque;
		}
	}
	
	
	// *********************************************************************************
	//
	//			FAIT REAPPARAITRE LE CHOIX DE SELECTION DE LA MARQUE
	//
	// *********************************************************************************
	
	function choisir_modele () {
		
		$('choix_modele').style.display = '';
		$('table_modele_selectionne').style.display = 'none';
		
		$('marque_id').value = "";
		$('modele_selectionne').value = "";
	}
	
	
	// *********************************************************************************
	//
	//			FAIT REAPPARAITRE LE MODELE DU MATERIEL
	//
	// *********************************************************************************
	
	function annuler_choix_modele (marqueid, modele) {
		
		$('choix_modele').style.display = 'none';
		$('table_modele_selectionne').style.display = '';
		
		$('marque_id').value = marqueid;
		$('modele_selectionne').value = modele;
	}	
	
	// *********************************************************************************
	//
	//			masque le combo pour afficher le input et vis-versa
	//
	// *********************************************************************************
	
	function change_combo_mac() {
		
		var inputbox = $("mac_input");
		var tr_inputbox = $("textbox_type");
		
		if (inputbox.style.display == "") {

			inputbox.style.display = 'none';
			tr_inputbox.style.display = 'none';
			
			// On vide le champ inputbox
			inputbox.value = "";
			
			tr_inputbox.style.display = 'none';
			
			// On affiche chaque ligne contenant un radio button
			$$(".combo_type").each ( function (e) {
				e.style.display = "";	
			})
			
			// On change l'intitul� du message � c�t� du +
			$('change_mac').innerHTML = "Adresse MAC manuelle";
				
		} else {

			inputbox.style.display = '';
			tr_inputbox.style.display = '';
			
			// On masque chaque ligne contenant un radio button
			$$(".combo_type").each ( function (e) {
				e.style.display = "none";	
			})
			
			// On unckeck tous les radio buttons
			$$(".mac_radio").each ( function (e) {
				e.checked = false;	
			})
			
			// On change l'intitul� du message � c�t� du +
			$('change_mac').innerHTML = "Choix des adresses MAC";
		}
		
	}
	
	
	// *********************************************************************************
	//
	// 		v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
	//
	// *********************************************************************************
	
	function validation () {

		var bt_submit  = document.getElementById("post_materiel");
		var mat_nom    = document.getElementById("nom").value;
		var mat_serial = $('serial').value;
		var mat_modele = document.getElementById("modele_selectionne").value;
		
	
		if (mat_nom == "" || mat_serial == "" || mat_modele == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	

	// serveur AJAX mootools pour le chainage des combobox type et sous type
	// Beaucoup de redondances dans les param�tres. Je corrige �a un de ces jours
	
	function chainage( select_src, select_type, select_stype, select_dst, div_id ) {
	
		// select_src : c'est le select contenant le trigger et la valeur qui fait le lien
		// select_dst : select � remplir
		// div_id : div � afficher

		
		if (div_id == "tr_stype" ) {
			$("tr_marque").style.display = 'none';
			$("tr_modele").style.display = 'none';
		}
		
		if (div_id == "tr_marque" ) {
			$("tr_modele").style.display = 'none';
		}
		
	
		if ( select_type )
			var type_value = document.getElementById(select_type).value;
		if ( select_stype )	
			var stype_value = document.getElementById(select_stype).value;

	
		if ( select_src.options[0].value == '' ) {
			select_src.options[0] = null;
		}
	
		var myRequest = new Request(
		{
			url: 'gestion_inventaire/chainage.php', 
			method: 'get',
			evalResponse: true,
			
			onRequest: function() {
				$(div_id).style.display = 'none';
			},

			onFailure: function(xhr) {
				window.alert("Erreur !");
			},

			onComplete: function(response) { 
				$(div_id).style.display = '';
			}
		}
		).send('value='+select_src.value+'&id_to_modify='+select_dst+'&div_id='+div_id+'&type='+type_value+'&stype='+stype_value);
	}
	
	
	/******************************************
	*
	*		G�n�rateur de ssn al�atoire
	*
	*******************************************/
	
	function SSNgenerator () {
		
		number = Math.floor(Math.random() * 100000);
		$('serial').value =  "NC" + number;
	}
	
	/******************************************
	*
	*		Activer le changement du SSN
	*
	*******************************************/
	
	function SSN_modifier () {
		
		if ($('serial').readOnly == true) {
			$('serial').readOnly = false;
			$('img_cadenas_ferme').style.display = '';
			$('img_cadenas_ouvert').style.display = 'none';
		} else if ($('serial').readOnly == false ) {
			$('serial').readOnly = true;
			$('img_cadenas_ferme').style.display = 'none';
			$('img_cadenas_ouvert').style.display = '';
		}
	}
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form2').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();

			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					//$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres et lorsqu'on poste un champ trop grand on d�passe la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_inventaire/voir_materiels.php?filter=" + $('filt').value + "');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});	
		
		

		// Fait apparaitre le num�ro GIGN dans le cas d'un ETAT particulier
		if ($('CB_etat')) {
			$('CB_etat').addEvent('change', function(e) {
				new Event(e).stop();
				
				if( this.value in {'CASSE':'', 'VOLE':'','PANNE':'','PERDU':''} ) {	$('tr_gign').style.display = ""; }
				else { $('tr_gign').style.display = "none";	}
			});
		}

	
	});
	
	

</script>

<?PHP
	
	// action � executer
	$action	 = $_GET['action'];
	$mat_ssn = $_GET['mat_ssn'];		//le SSN va nous servir pour r�cup�rer les adresses MAC d'OCS
	
	
	// On regarde si la base OCS existe car dans le cas de sa non existance la page ne s'affiche pas
	$link_bases = mysql_pconnect('localhost', $user, $pass);//connexion � la base de donn�e
	if(!mysql_select_db('ocsweb', $link_bases)) {}
	else {
	
		// adresse de connexion � la base de donn�es
		$dsn_ocs 	= 'mysql://'. $user .':' . $pass . '@localhost/' . $ocsweb;

		// cnx � la base de donn�es OCS
		$db_ocs 	= & MDB2::factory($dsn_ocs);
		
		// RQ POUR INFO OCS
		$materiel_ocs    = $db_ocs->queryAll ( "SELECT  networks.HARDWARE_ID, hardware.ID FROM hardware, bios, networks WHERE bios.SSN = '$mat_ssn' AND bios.HARDWARE_ID = hardware.id AND networks.HARDWARE_ID = hardware.id;" );
		$materiel_ocs_id = $materiel_ocs[0][1];
		
		if ( $materiel_ocs_id ) {	// si le mat�riel existe dans ocs
			// RQ POUR INFO cartes rzo
			$rq_cartes_reseaux = $db_ocs->queryAll ( "SELECT MACADDR, SPEED FROM networks WHERE HARDWARE_ID = " . $materiel_ocs[0][0] );
		}
		
		// On se d�connecte de la db ocs
		$db_ocs->disconnect();
	
	}
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	// Requ�te qui va r�cup�rer les origines des dotations ...
	$liste_origines = $db_gespac->queryAll ( "SELECT origine FROM origines ORDER BY origine" );
	
	// Requ�te qui va r�cup�rer les �tats des mat�riels ...
	$liste_etats = $db_gespac->queryAll ( "SELECT etat FROM etats ORDER BY etat" );
	

	
	// *********************************************************************************
	//
	//			Formulaire vierge de cr�ation
	//
	// *********************************************************************************	
	

	if ( $action == 'add' ) {
	
		echo "<h2>formulaire de cr�ation d'un nouveau mat�riel</h2><br>";
		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('filt').focus();
		</script>
		
		<form action="gestion_inventaire/post_materiels.php?action=add" method="post" name="post_form" id="post_form2">
			
				<!--

				GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

				-->
					
				<div id='choix_modele'>
				
					<center>
				
					<table width="500" align="center" cellpadding="10">
						<tr>
							<td>Choisir un mod�le * :</td>
							<td><input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'corr_table');" type="text"> </input></td>
						</tr>
					</table>
				
					<br>
					
					<?PHP
					// ici il faut r�cup�rer les lignes DISTINCTES histoire de ne pas surcharger le tableau
					$liste_marques = $db_gespac->queryAll ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques GROUP BY marque_model ORDER BY marque_model" );
					?>
					
					<!-- s'affiche si il n'y a pas de r�sultat -->
					<div id="pasderesultat" style='display:none'>Pas de r�sultat, vous devez d'abord cr�er le mod�le manuellement.</div>
					
					<table id="corr_table" class='tablehover'>

						<?PHP
							foreach ( $liste_marques as $marque) {
							
								$marque_id 			= $marque[0];
								$marque_type 		= $marque[1];
								$marque_stype 		= $marque[2];
								$marque_marque 		= $marque[3];
								$marque_modele 		= $marque[4];
							
								echo "<tr style='display:none' class='tr_filter'>";
									echo "<td width=200>$marque_type</td>";
									echo "<td width=200>$marque_stype</td>";
									echo "<td width=200>$marque_marque</td>";
									echo "<td width=200>$marque_modele</td>";
									echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_modele');\"><img src='./img/arrow-right.png' width=16 height=16 title='Choisir ce mod�le'> </a></td>";
								echo "</tr>";
							
							}
						
						?>
						
					</table>
				</div>	
				
				<table width="500" align="center" cellpadding="10" style='display:none' id="table_modele_selectionne">
					<tr>
						<td>Mod�le s�lectionn� *</td>
						<td><input type=hidden name=marque_id id=marque_id> <input type="text" id="modele_selectionne"> </td>
						<td><a href='#' onclick="choisir_modele();">changer</a></td>
					</tr>
				 </table>
				<br>
				<center>
				<table width=500>
				
				<tr>
					<TD>Nom du materiel *</TD>
					<TD><input type=text id=nom name=nom onkeyup="validation();"/></TD>
				</tr>
				
				<tr>
					<TD>R�f�rence DSIT</TD>
					<TD><input type=text id=dsit name=dsit 	/></TD>
				</tr>
				
				<tr>
					<TD>Num�ro de s�rie *</TD> 
					<TD><input type=text id=serial name=serial onkeyup="validation();"/> <input type=button value="g�n�rer" onclick="SSNgenerator(); validation();"><input type=button value="activer" onclick=""></TD>
				</tr>
				
				<tr>
					<TD>Adresse MAC</TD> 
					<TD><input type=text id=mac name=mac size=17 maxlength=17 /></TD>
				</tr>
				
				<tr>
					<TD>Origine</TD> 
					<TD>
						<select name="origine">
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine[0] ."'>" . $origine[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<TD>Etat du mat�riel</TD>
					<TD>
						<select name="etat">
							<?PHP	foreach ($liste_etats as $etat) {	$selected = $etat[0] == "Fonctionnel" ? "selected" : ""; echo "<option $selected value='" . $etat[0] ."'>" . $etat[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			
				<tr>
					<TD>Salle o� se trouve le mat�riel</TD>
					<TD>
						<select name="salle" >
							<?PHP
								// requ�te qui va afficher dans le menu d�roulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $db_gespac->queryAll ( "SELECT DISTINCT salle_nom FROM salles" );	// [AMELIORATION] DISTINCT ? PK DISTINCT ?
								foreach ( $req_salles_disponibles as $record) { 
								
									$salle_nom = $record[0];
									
									// Salle par d�faut : STOCK
									$selected = $salle_nom == "STOCK" ? " selected" : "";
									
								?>
									<option <?PHP echo $selected ?> value="<?PHP echo $salle_nom ?>"><?PHP echo $salle_nom ?></option>
							<?PHP
								}
							?>
						</select>
					</TD>
				</tr>
				
			</table>

			<br>
			<input type=submit value='Ajouter un materiel' id="post_materiel" disabled>

			</center>

		</FORM>
				

		<?PHP
		
	} 
	
	


	// *********************************************************************************
	//
	//			Formulaire modification par lot non pr�rempli
	//
	// *********************************************************************************		
		
	
	if ($action == 'modlot') {
		
		
		echo "<h2>formulaire de modification d'un lot</h2><br>";
			
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('origine').focus();
		</script>

		<form action="gestion_inventaire/post_materiels.php?action=modlot" method="post" name="post_form" id="post_form2">
			<center>
			
			<input type=hidden name=lot id=lot>
			<!-- Ici on r�cup�re la valeur du champ materiels_a_poster de la page voir_materiels_table.php -->
			<script>$("lot").value = $('materiel_a_poster').value;</script>

			<table width=500>
				
				<tr>
					<TD>Origine</TD> 
					<TD>
						<select name="origine" id="origine">
							<option value="">Ne pas modifier</option>
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine[0] ."'>" . $origine[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<TD>Etat du mat�riel</TD>
					<TD>
						<select name="etat">
							<option value="">Ne pas modifier</option>
							<?PHP	foreach ($liste_etats as $etat) {	echo "<option value='" . $etat[0] ."'>" . $etat[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
			
				<tr>
					<TD>Salle o� se trouve le mat�riel</TD>
					<TD>
						<select name="salle" >
							<option value="">Ne pas modifier</option>
							<?PHP
								// requ�te qui va afficher dans le menu d�roulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $db_gespac->queryAll ( "SELECT DISTINCT salle_nom FROM salles" );	// [AMELIORATION] DISTINCT ? PK DISTINCT ?
								foreach ( $req_salles_disponibles as $record) { 
								
									$salle_nom = $record[0];
									
								?>
									<option value="<?PHP echo $salle_nom ?>"><?PHP echo $salle_nom ?></option>
							<?PHP
								}
							?>
						</select>
					</TD>
				</tr>

		<!--------------------------------------------	TYPES	------------------------------------------------------------->
				<tr style="display:none<?PHP echo $show_type;?>">	<!-- J'ai masqu� cette ligne histoire de ne pas utiliser le chainage pour la modif par lot pour le moment -->			
					<TD>Famille</TD>
					<TD>
						<select id="type" name="type" onChange="chainage(this, 'type','', 'stype', 'tr_stype'); " >
							<option value=""> >>> Choisir une valeur <<< </option>
							<?PHP
								// requ�te qui va afficher dans le menu d�roulant les types saisies dans la table 'marques'
								$req_types_disponibles = $db_gespac->queryAll ( "SELECT DISTINCT marque_type FROM marques" );
								foreach ( $req_types_disponibles as $record) { 
									$marque_type = $record[0]; 
									echo "<option value='$marque_type'>$marque_type</option>";								
								}
							?>
						</select>
					</TD>
				</tr>
				
		<!--------------------------------------------	SOUS TYPES	------------------------------------------------------------->
				<tr id="tr_stype" style="display:none">
					<td>Sous-famille</td>
					<td>
						<select id="stype" name="stype" onChange="chainage(this, 'type', '', 'marque', 'tr_marque'); " >
						</select>
					</td>
				</tr>				
				
		<!--------------------------------------------	MARQUES	------------------------------------------------------------->
				<tr id="tr_marque" style="display:none">
					<td>Marque</td>
					<td>
						<select id="marque" name="marque" onChange="chainage(this, 'type', 'stype', 'modele', 'tr_modele'); " >
						</select>
					</td>
				</tr>	
				
		<!--------------------------------------------	MODELES	------------------------------------------------------------->		
				<tr id="tr_modele" style="display:none">
					<TD>Mod�le</TD>
					<TD>
						<select id="modele" name="modele" >
						</select>
					</TD>
				</tr>
				
				
			</table>

			<br>
			<input type=submit value='Modifier le lot' >
			<input type=button value='sortir sans modifier' onclick="SexyLightbox.close();" >

			</center>

		</FORM>
				

		<?PHP

		
	} 
	
		
		
		
		
	// *********************************************************************************
	//
	//			Formulaire modification unique pr�rempli
	//
	// *********************************************************************************	
	
	
	if ($action == 'mod') {
	
		$id = $_GET['id'];	// Id du mat�riel � modifier
	
		echo "<h2>formulaire de modification d'un mat�riel</h2><br>";
		
		
		// Requete pour r�cup�rer les donn�es des champs pour le mat�riel � modifier
		$materiel_a_modifier = $db_gespac->queryAll ( "SELECT mat_id, mat_nom, mat_dsit, mat_serial, mat_etat, salle_nom, marque_type, marque_model, mat_origine, marque_stype, marque_marque, mat_mac, materiels.marque_id FROM materiels, marques, salles WHERE mat_id=$id AND materiels.marque_id = marques.marque_id AND materiels.salle_id = salles.salle_id" );		
		
		// valeurs � affecter aux champs
		$materiel_id 			= $materiel_a_modifier[0][0];
		$materiel_nom	 		= $materiel_a_modifier[0][1];
		$materiel_dsit	 		= $materiel_a_modifier[0][2];
		$materiel_serial 		= $materiel_a_modifier[0][3];
		$materiel_etat	 		= $materiel_a_modifier[0][4];
		$materiel_salle			= $materiel_a_modifier[0][5];
		$materiel_type 			= $materiel_a_modifier[0][6];
		$materiel_modele		= $materiel_a_modifier[0][7];
		$materiel_origine		= $materiel_a_modifier[0][8];
		$materiel_stype			= $materiel_a_modifier[0][9];
		$materiel_marque		= $materiel_a_modifier[0][10];
		$materiel_mac			= $materiel_a_modifier[0][11];
		$marque_id				= $materiel_a_modifier[0][12];

		
		?>
		
		<script>
			// Donne le focus au premier champ du formulaire
			$('nom').focus();
		</script>
		
		<form action="gestion_inventaire/post_materiels.php?action=mod" method="post" name="post_form" id="post_form2">
			<input type=hidden name=materiel_id value=<?PHP echo $id;?> >
			
				<!--

				GESTION PAR CORRESPONDANCE DE L'INSERTION D'UNE MARQUE

				-->
				
				<table width="500" align="center" cellpadding="10" id="table_modele_selectionne">
					<tr>
						<td>Mod�le s�lectionn� *</td>
						<td><input type="hidden" name="marque_id" id="marque_id" value=<?PHP echo $marque_id;?> > <input type="text" id="modele_selectionne" value="<?PHP echo $materiel_marque.' '.$materiel_modele; ?>" > </td>
						<td><a href='#' onclick="choisir_modele();">changer</a></td>
					</tr>
				 </table>
				<br>
				
				<div id='choix_modele' style='display:none'>
				
					<center>
				
					<table width="500" align="center" cellpadding="10">
						<tr>
							<td>Choisir un mod�le * :</td>
							<td><input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'corr_table');" type="text"> </input></td>
							<td><a href='#' onclick="annuler_choix_modele(<?PHP echo $marque_id;?>, '<?PHP echo $materiel_marque.' '.$materiel_modele; ?>');">annuler</a></td>
						</tr>
					</table>
				
					<br>
					
					<?PHP
					// ici il faut r�cup�rer les lignes DISTINCTES histoire de ne pas surcharger le tableau
					$liste_marques = $db_gespac->queryAll ( "SELECT marque_id, marque_type, marque_stype, marque_marque, marque_model FROM marques GROUP BY marque_model ORDER BY marque_model" );
					?>
					
					<!-- s'affiche si il n'y a pas de r�sultat -->
					<div id="pasderesultat" style='display:none'>Pas de r�sultat, vous devez d'abord cr�er le mod�le manuellement.</div>
					
					<table id="corr_table" class='tablehover'>

						<?PHP
							foreach ( $liste_marques as $marque) {
							
								$marque_id 			= $marque[0];
								$marque_type 		= $marque[1];
								$marque_stype 		= $marque[2];
								$marque_marque 		= $marque[3];
								$marque_modele 		= $marque[4];
							
								echo "<tr style='display:none' class='tr_filter'>";
									echo "<td width=200>&nbsp $marque_type</td>";
									echo "<td width=200>&nbsp $marque_stype</td>";
									echo "<td width=200>&nbsp $marque_marque</td>";
									echo "<td width=200>&nbsp $marque_modele</td>";
									echo "<td><a href='#' onclick=\"validation_choisir_marque($marque_id, '$marque_marque $marque_modele');\"><img src='./img/arrow-right.png' width=16 height=16 title='Choisir ce mod�le'> </a></td>";
								echo "</tr>";
							
							}
						
						?>
						
					</table>
				</div>	 
			<br>
			<center>
			<table width=500>
			
				<tr>
					<TD>Nom du materiel</TD>
					<TD><input type=text name=nom id=nom value= "<?PHP echo $materiel_nom; ?>" 	/></TD>
				</tr>
				
				<tr>
					<TD>R�f�rence DSIT</TD>
					<TD><input type=text name=dsit value= "<?PHP echo $materiel_dsit; ?>"	/></TD>
				</tr>
				
				<tr>
					<TD>Num�ro de s�rie</TD>
					<TD><input type="text" name="serial" id="serial" value= "<?PHP echo $materiel_serial; ?>" readOnly='true'	/>
						<a href='#' onclick='SSN_modifier();' onkeyup='validation();'>
							<img src='./img/cadenas_ferme.png' id="img_cadenas_ouvert" style="display" title="Passer en �criture">
							<img src='./img/cadenas_ouvert.png' id="img_cadenas_ferme" style="display:none" title="Passer en Read only">
						</a><!--<input type=button value="Passer en �criture" id="activer_ssn" onclick="SSN_modifier ();">-->
					</TD>
				</tr>
				
				<?PHP
				
				
				
				if ( $materiel_ocs_id ) {	// si le mat�riel existe dans ocs
				
					// Liste des boutons radion � remplacer
					foreach ($rq_cartes_reseaux as $record) {
						$SPEED   = $record[1];
						$MACADDR = $record[0];
						
						$select = ($materiel_mac == $MACADDR) ? "checked" : "";
						
						echo "<TR class='combo_type'>";
							echo "<TD>Adresse MAC $SPEED</TD>";
							echo "<TD><input type='radio' name='mac_radio' class='mac_radio' value='$MACADDR' $select > $MACADDR </TD>"; 	//cr�ation d'un bouton radio � c�t� de chaque adresse mac
						echo "</TR>";
					}
					
					
					// Le inputbox de remplacement quand on clique sur le plus
					echo "<TR id='textbox_type' style='display:none;'>
							<TD>Adresse MAC</TD>
							<TD><input name='mac_input' id='mac_input' size=17 maxlength=17 type='text' value='$materiel_mac' style='display:none;'></TD>
						</TR>";
						
					// Le bouton + pour switcher entre le input et les radio buttons	
					echo "<tr><td>&nbsp</td><td align=center><a href=# onclick=\"change_combo_mac();\"> <img src='./img/add.png' style='float:top;'> <span id='change_mac'>Adresse MAC manuelle</span></a></td></tr>";
					
					
						
				} 
				else {	// Le mat�riel n'existe pas, on ne propose qu'un input
					echo "<TR id='textbox_type'>
							<TD>Adresse MAC</TD>
							<TD><input name='mac_input' id='mac_input' size=17 maxlength=17 type='text' value='$materiel_mac'></TD>
						</TR>";
				}
				
				?>
								
				<tr>
					<TD>Origine</TD> 
					<TD>	
						<select name="origine">
							<option value=<?PHP echo $materiel_origine; ?>><?PHP echo $materiel_origine; ?></option>
							<?PHP	foreach ($liste_origines as $origine) {	echo "<option value='" . $origine[0] ."'>" . $origine[0] ."</option>";	}	?>
						</select>

					</TD>
				</tr>
				
				<tr>
					<TD>Etat du mat�riel</TD> 
					<TD>
						<select name="etat" id="CB_etat">
							<option selected><?PHP echo $materiel_etat; ?></option>
							<?PHP	foreach ($liste_etats as $etat) {	echo "<option value='" . $etat[0] ."'>" . $etat[0] ."</option>";	}	?>
						</select>
					</TD>
				</tr>
				
				<tr id="tr_gign" style="display:none;">
					<td>Dossier GIGN</td>
					<td><input type="text" name="num_gign">
				</tr>
				
			
				<tr>
					<TD>Salle o� se trouve le mat�riel</TD> 
					<TD>
						<select name="salle" >
							<?PHP
								// requ�te qui va afficher dans le menu d�roulant les salles saisies dans la table 'salles'
								$req_salles_disponibles = $db_gespac->queryAll ( "SELECT DISTINCT salle_nom FROM salles" );
								foreach ( $req_salles_disponibles as $record) { 
									$salle_nom = $record[0];
									$selected = $salle_nom == $materiel_salle ? "selected" : "";
									
									echo "<option $selected value='$salle_nom'>$salle_nom</option>";
								}
							?>
						</select>
					</TD>
				</tr>
				
				

			</table>

			<br>
			<input type=submit value='Modifier ce mat�riel' >

			</center>

		</FORM>

		
<?PHP
	}	
	
	
	// *********************************************************************************
	//
	//			Formulaire de renommage par lot de la selection
	//
	// *********************************************************************************	
	
	
	if ($action == 'renomlot') {
		
			echo "<h2>formulaire pour renommer un lot</h2><br>";
?>

		<form action="gestion_inventaire/post_materiels.php?action=renomlot" method="post" name="post_form" id="post_form2">
			<center>
			
			<input type=hidden name=lot id=lot>
			<!-- Ici on r�cup�re la valeur du champ materiels_a_poster de la page voir_materiels_table.php -->
			<script>$("lot").value = $('materiel_a_poster').value;</script>

			<table width=500>
				
				<tr>
					<TD>Pr�fixe du lot</TD> 
					<TD>
						<input type=text name=prefixe id=prefixe />
					</TD>
				</tr>
				
				<tr>
					<TD>Suffixe s�quentiel</TD> 
					<TD>
						<input type=checkbox name=suffixe id=suffixe checked />
					</TD>
				</tr>
				
				
			</table>

			<br>
			<input type=submit value='Renommer le lot' >
			<input type=button value='sortir sans renommer' onclick="SexyLightbox.close();" >

			</center>

		</FORM>


<?PHP	
	}
?>
