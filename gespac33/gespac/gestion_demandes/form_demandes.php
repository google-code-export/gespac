<?PHP

	#formulaire de création / modification d'une demande

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

	$id = $_GET['id'];

?>


<!--  SERVEUR AJAX 
<script type="text/javascript" src="server.php?client=all"></script>
-->

<!--	DIV target pour Ajax	-->
<div id="target"></div>


<!--  FONCTIONS JAVASCRIPT -->
<script>

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";

	// Sur changement du type on affiche ou pas la salle et les pc
	function change_type(type) {
		
		var tr_salle = $("tr_salle");
		var tr_pc 	 = $("tr_pc");
		var tr_texte = $("tr_texte");
		
		// Si le type de demande est "installation" ou "reparation"
		if ( type == "installation" || type == "reparation") {
			tr_salle.style.display = "";
			tr_pc.style.display = "none";
			tr_texte.style.display = "none";
		}
		else {	// Si c'est un autre type
			tr_salle.style.display = "none";
			tr_pc.style.display = "none";
			tr_texte.style.display = "";
		}
		
		// Si le type est vide ...
		if ( type == "" ) {
			tr_salle.style.display = "none";
			tr_pc.style.display = "none";
			tr_texte.style.display = "none";
		}
	}

	// Sur changement de la salle on affiche le champ pc et on le remplit avec les pc de la salle <- [AMELIORATION] Ouh ! qu'il est laid ce code !
	function change_salle(salle_id) {
		var tr_pc 			= $("tr_pc");
		var pc_demande 		= $("pc_demande");
		tr_pc.style.display = "";
		
		pc_demande[0] = new Option(">>>Sélectionner un PC<<<","");
		pc_demande[1] = new Option("TOUTE LA SALLE",0);
		<?PHP
		
			// adresse de connexion à la base de données
			$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

			// cnx à la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);

			// requête qui va afficher dans le menu déroulant les pc de la salle selectionnée
			$sql = "SELECT mat_id, mat_nom FROM materiels WHERE salle_id = 1";
			$req_salles_disponibles = $db_gespac->queryAll ( $sql );
						
			$compteur = 2;	// Commence à 2 car on ajoute Sélectionner un PC et toute la salle à 0 et 1
			foreach ( $req_salles_disponibles as $record) { 
			
				$mat_id 	= $record[0];
				$mat_nom 	= $record[1];
			?>
			// On remplit la liste du select en JS
			pc_demande[<?PHP echo $compteur ?>] = new Option("<?PHP echo $mat_nom ?>",<?PHP echo $mat_id ?>);
			
		<?PHP	$compteur++; }	?>
			
	}		
	
	// affichage des anciennes demandes pour ce matériel
	function affiche_ancienne_inter (pc) {
		// a faire en ajax, un peu comme le chainage des CB
	}
	
	// Sur changement du PC on affiche le champ texte 
	function change_pc () {
		var tr_texte = $("tr_texte");
		tr_texte.style.display = "";
	}	
	
	// serveur AJAX mootools pour le chainage des combobox SALLE - PC
	function chainage_salle_pc( select, id, div_id ) {
		
		if ( select.options[0].value == '' ) {
			select.options[0] = null;
		}
	
		var myRequest = new Request(
		{
			url: 'gestion_demandes/chain_salle-mat.php', 
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
		).send('value='+select.value+'&id_to_modify='+id+'&div_id='+div_id);
	}

	
	// serveur AJAX mootools pour le chainage PC - historique des demandes
	function chainage_pc_historique( pc_id, div_id ) {
	
		var salle_id = document.getElementById("salle_demande").value;
	
		var myRequest = new Request(
		{
			url: 'gestion_demandes/chain_pc-histo_demandes.php', 
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
		).send('mat='+pc_id.value+'&salle='+salle_id+'&div_id='+div_id);
	}
	
	
	// affiche / masque l'historique
	function montre_masque_historique( ) {
		var historique = $("historique");
		
		if ( historique.style.display == "none" )
			historique.style.display = "";
		else
			historique.style.display = "none";
	}
	
	
	// *********************************************************************************
	//
	// 		vérouille l'accès au bouton submit si les conditions ne sont pas remplies
	//
	// *********************************************************************************
	
	function validation () {

		var bt_submit  	= $("post_demandes");
		var commentaire	= $("texte_demande").value;
		var type		= $("type_demande").value;
		var salle		= $("salle_demande").value;
		var pc			= $("pc_demande").value;
	
		if (commentaire == "" || type == "" || salle == "" || pc == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	// *************************************************************************************************************
	//
	// 		vérouille l'accès au bouton submit si les conditions ne sont pas remplies pour la réponse à un dossier
	//
	// *************************************************************************************************************
	
	function validation_reponse () {

		var bt_submit  	= $("post_reponse");
		var commentaire	= $("reponse");
		
	
		if (commentaire == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('gestion_demandes/voir_demandes.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});			
	});
</script>

<style>
	td { border : 1px solid #ccc; }
</style>

<?PHP

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	
	if ( $id == -1 ) {	// Création d'une demande
	
		echo "<h2>Créer le dossier</h2><br>";
		
		?>

		<form action="gestion_demandes/post_demandes.php?action=add" method="post" name="post_form" id="post_form">

			<center>
			<table width=500>

				<tr id="tr_type">
					<TD>Type :</TD>
					<TD><select id="type_demande" name="type_demande" onChange="change_type(type_demande.value);$('salle_demande').value=''; $('pc_demande').value='';validation();">
							<option selected value=""> >>> Type de Demande <<< </option>
							<option value="installation">installation</option>
							<option value="reparation">réparation</option>
							<option value="usages">usages</option>
							<option value="formation">formation</option>
							<option value="autre">Autre...</option>
						</select>
					</TD>
				</tr>
				
				<tr id="tr_salle" style='display:none'>
					<TD>Salle</TD>
					<TD><select id="salle_demande" name="salle_demande" onChange="chainage_salle_pc(this, 'pc_demande', 'tr_pc') ; $('pc_demande').value = ''; validation();">
							<option selected value=''> >>>Sélectionner une salle<<< </option>
							<?PHP
								// requête qui va afficher dans le menu déroulant les salles saisies dans la table 'salles' sauf les salles MATERIEL VOLE et D3E
								$req_salles_disponibles = $db_gespac->queryAll ( "SELECT salle_nom, salle_id FROM salles WHERE NOT salle_nom='PRETS' AND NOT salle_nom='MATERIEL VOLE'ORDER BY salle_nom" );
								foreach ( $req_salles_disponibles as $record) { 
								
									$salle_nom 	= $record[0];
									$salle_id 	= $record[1];
								?>
									<option value="<?PHP echo $salle_id ?>"><?PHP echo $salle_nom ?></option>
							<?PHP
								}
							?>
						</select>
					</TD>
				</tr>
				
				<tr id="tr_pc" style='display:none'>
					<TD>PC</TD> 
					<TD><select id="pc_demande" name="pc_demande" onChange="change_pc();chainage_pc_historique(this,'historique'); validation();"></select>
					</TD>
				</tr>

				<TR id="tr_texte" style='display:none'>
					<TD>Problème</TD>
					<TD><textarea cols=45 rows=15 id="texte_demande" name="texte_demande" onkeyup="validation();"></textarea></TD>
				</TR>
				
			</table>
	
			<?PHP 
				$grade_user = $_SESSION ['grade'];
				
				// L'utilisateur ATI peut créer directement l'intervention
				if ( $grade_user <=2 ) { ?>
					<br>
					<br>
				 Créer directement l'intervention <input type=checkbox name=creat_inter id=creat_inter>
			<?PHP } ?>
			
			<br>
			<br>
				<input type=submit id=post_demandes value='Envoyer la demande' disabled>

			</center>

		</FORM>
		
		<div id=historique></div>
	<?PHP	
	} else {
			
		$req_info_demande = $db_gespac->queryAll ( "SELECT dem_id, dem_date, dem_text, dem_etat, user_demandeur_id, user_intervenant_id, user_nom, dem_type FROM demandes, users WHERE demandes.user_demandeur_id=users.user_id AND dem_id=$id ORDER BY dem_date" );
		
		$dem_id 				= $req_info_demande[0][0];
		$dem_date 				= $req_info_demande[0][1];
		$dem_text 				= $req_info_demande[0][2];
		$dem_etat 				= $req_info_demande[0][3];
		$user_demandeur_id 		= $req_info_demande[0][4];
		$user_intervenant_id 	= $req_info_demande[0][5];
		$user_demandeur_nom		= $req_info_demande[0][6];
		$dem_type				= $req_info_demande[0][7];

		
		// On récupère la salle et le materiel si c'est une installation ou une reparation
		if ( $dem_type == "installation" || $dem_type == "reparation" ) {
			$rq_extraction_salle_mat = $db_gespac->queryAll ( "SELECT demandes.mat_id, demandes.salle_id, salle_nom FROM demandes, salles, users WHERE salles.salle_id=demandes.salle_id AND demandes.user_demandeur_id=users.user_id AND dem_id=$dem_id" );

			$mat_id 	= $rq_extraction_salle_mat [0][0];
			$salle_id 	= $rq_extraction_salle_mat [0][1];
			$salle_nom 	= $rq_extraction_salle_mat [0][2];
			
			// On récupère le nom du matériel
			if ( $mat_id <> 0) {
				$liste_nom_materiel = $db_gespac->queryAll ( "SELECT mat_nom FROM materiels WHERE mat_id=$mat_id" );
				$mat_nom = $liste_nom_materiel[0][0];
			}
			else {	$mat_nom = "TOUS";	}
			
		} else {
			$mat_nom = "NA";
			$salle_nom = "NA";
		}
		
		
			
		echo "<h2>Répondre au dossier <b>$dem_id</b> créé le : $dem_date</h2><br>";
		
		echo "	<center>
				<table width=700px>
					<th>Etat</th>
					<th>Type</th>
					<th>Demandeur</th>
					<th>Salle</th>
					<th>Matériel</th>
					
					<tr>
						<td>$dem_etat</td>
						<td>$dem_type</td>
						<td>$user_demandeur_nom</td>
						<td>$salle_nom</td>
						<td>$mat_nom</td>
					</tr>

					<tr>
						<td colspan=7>$dem_text</td>
					</tr>

				</table>
		";
		
		// on gère ici le style:display du div reponse (si le dossier est cloturé, on n'affiche pas le div)
		$montre_reponse = $dem_etat == "cloturer" ? "none" : "";
	?>	

		<br>

		<!-- 	BLOC DE REPONSE A LA DEMANDE	-->	

	
		<div id="reponse" style="display:<?PHP echo $montre_reponse; ?>">
			<form action="gestion_demandes/post_demandes.php?action=mod" method="post" name="post_form" id="post_form">
				
				<input type=hidden name="dossier" value= <?PHP echo $id;?> >
				<input type=hidden name="salle" value= <?PHP echo $salle_id;?> >
				<input type=hidden name="mat" value= <?PHP echo $mat_id;?> >
				
				<textarea name="reponse" cols=65 rows=10 onkeyup="validation_reponse();" ></textarea>
				<br>
				
				<label>Changer l'état : </label>
				<select name="etat">
					<option value=rectifier>	Rectifier le dossier	</option>

					<?PHP 
					$grade_user = $_SESSION ['grade'];
					
					if ( $grade_user <=2 ) { ?>
						<option value=precisions>	Demander précisions		</option>
						
						<?PHP
							// Ici il faut voir si le dossier est déjà en cours d'intervention
							$inter_existe = $db_gespac->queryOne ( "SELECT interv_id FROM interventions WHERE dem_id=$id;" );
							if ( !$inter_existe ) {
								echo "<option value=intervention>	Créer l'intervention	</option>";
								echo "<option value=clos>	Clore le dossier				</option>";
							}
							
							/*
							// Si le dossier est en cours d'intervention, on ne peut pas le fermer
							$inter_en_cours = $db_gespac->queryOne ( "SELECT interv_id FROM interventions WHERE dem_id=$id AND interv_cloture='' ;" );
							if ( $inter_en_cours ) echo "<option value=clos>		Clore le dossier		</option>";*/
						?>

						
					<?PHP
					}
					?>

				</select>
				
				<input type=submit id=post_reponse value=poster disabled >
		
			</form>
		
		</div>
		
		<br>
		<small><a href='#' onclick='javascript:montre_masque_historique();'>afficher/masquer l'historique</a></small>
		<br>
		
		
		<!-- 	BLOC HISTORIQUE DU DOSSIER	-->
		<div id="historique" style='padding:10px;'>
			
			<?PHP 
				// historique des demandes
				$historique_demandes = $db_gespac->queryAll ( "SELECT txt_date, txt_texte, user_nom, txt_etat FROM demandes_textes, users WHERE dem_id=$id AND users.user_id=demandes_textes.user_id ORDER BY txt_date DESC;" );
				
				echo "<table style='border: 1px solid #ccc;width:700px;'>
						<th>Date</th>
						<th>Intervenant</th>
						<th>Etat</th>
						<th>commentaire</th>";
			
			
				foreach ( $historique_demandes as $record ) {
				
					$txt_date 	= $record[0];
					$txt_texte 	= $record[1];
					$user_nom 	= $record[2];
					$txt_etat	= $record[3];
					
					echo "
						<tr>
							<td>$txt_date</td>
							<td>$user_nom</td>
							<td>$txt_etat</td>
							<td>$txt_texte</td>
						</tr>";
							
				}
				echo "</table>";
			
			?>
		
				
		</div>

	<?PHP
	}
	?>
