<?PHP
	session_start();
	
	/* 	
	 	fichier de visualisation des utilisateurs :
		vue de la db gespac avec tous les users du parc
	*/


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-06-01#", $_SESSION['droits']);
	
	
?>

<div class="entetes" id="entete-utilisateurs">	

	<span class="entetes-titre">LES UTILISATEURS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des utilisateurs du parc.<br>On peut gérer ici les skins, le mailing et les grades des utilisateurs.</div>

	<span class="entetes-options">

		<span class="option"><?PHP if ( $E_chk ) { echo "<span id='nb_selectionnes' title=\"nombre d'utilisateurs sélectionnés\"></span>"; echo "<span id='modif_selection' style='display:none;'> <a href='gestion_utilisateurs/form_utilisateurs.php?height=200&width=640&action=modlot' rel='slb_users' title='modifier selection'> <img src='" . ICONSPATH . "modif1.png'></a></span>"; }?>  </span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_utilisateurs/form_utilisateurs.php?height=300&width=640&id=-1' rel='slb_users' title=\"ajout d'un utilisateur\"><img src='" . ICONSPATH . "add.png'></a>";?></span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'user_table');" type="text" value=<?PHP echo $_GET['filter'];?>> 
				<span id="nb_filtre" title="nombre d'utilisateurs affichés"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_utilisateurs = $con_gespac->QueryAll ( "SELECT user_nom, user_logon, user_password, grade_nom, user_mail, user_id, user_skin, user_mailing, users.est_modifiable FROM users, grades WHERE users.grade_id=grades.grade_id ORDER BY user_nom" );

?>
		
	<input type=hidden name='users_a_poster' id='users_a_poster' value=''>


	<center>
	<br>
	<table class="tablehover" id="user_table">
		<th> <input type=checkbox id=checkall onclick="checkall('user_table');" > </th>
		<th>Nom</th>
		<th>Logon</th>
		<th>Grade</th>
		<th>Mail</th>
		<th>Skin</th>
		<th>Mailing</th>
		
		<?PHP	
		if ( $E_chk ) echo "<th>&nbsp</th><th>&nbsp</th>";
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_utilisateurs as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
					$nom 			= $record['user_nom'];
					$logon 			= $record['user_logon'];
					$password 		= $record['user_password'];
					$grade			= $record['grade_nom'];
					$mail 			= $record['user_mail'];
					$id				= $record['user_id'];
					$skin			= $record['user_skin'];
					$mailing		= $record['user_mailing'];
					$est_modifiable	= $record['est_modifiable'];
					
					$mailing_chk = $mailing == 1 ? "<img src='img/ok.png' height=16px width=16px>" : "";
					
					
					// test si la machine est prétée ou pas
					$rq_machine_pretee = $con_gespac->QueryAll ( "SELECT mat_id FROM materiels WHERE user_id=$id AND user_id<>1" );
					$mat_id = @$rq_machine_pretee['mat_id'];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
							
					if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
							$id_pret = 0;
						} else {	// la machine est prêtée ($mat_id existe)
							$id_pret = 1;
					}
					
					
					echo "<tr id=tr_id$id class=$tr_class>";
					
					// on affiche pas la checkbox pour les comptes dont le champ "est_modifiable" est TRUE
					if ( $E_chk && $est_modifiable) {
						echo "<td> <input type=checkbox name=chk indexed=true id='chk$id' value='$id' onclick=\"select_cette_ligne('$id', $compteur); \"> </td>";	
					} else {
						echo "<td>&nbsp</td>";
					}
					
					
					echo $chk_box;
					echo "<td> $nom </td>";
					echo "<td> $logon </td>";
					echo "<td> $grade </td>";
					echo "<td> $mail </td>";
					echo "<td> $skin </td>";
					echo "<td> $mailing_chk </td>";
					
					if ( $E_chk && $est_modifiable) {
						
						echo "<td><a href='gestion_utilisateurs/form_utilisateurs.php?height=300&width=640&id=$id&action=mod' rel='slb_users' title='Formulaire de modification de l`utilisateur $nom'><img src='" . ICONSPATH . "edit.png'> </a></td>";
						echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_user('$id', '$nom', this.parentNode.parentNode.rowIndex, $id_pret);\">	<img src='" . ICONSPATH . "delete.png' title='supprimer $nom'>	</a> </td>";
							
					} else {
						echo "<td>&nbsp</td>";
						echo "<td>&nbsp</td>";
					
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>



<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_users'});
	});

	// Filtre rémanent
	filter ( $('filt'), 'user_table' );
	
	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression d'un user
	//
	// *********************************************************************************
	
	function validation_suppr_user (id, nom, row, id_pret) {
		
		if ( id_pret == 0 ) {
		
			var valida = confirm('Voulez-vous vraiment supprimer l\'utilisateur "' + nom + '" ?');
			// si la réponse est TRUE ==> on lance la page post_marques.php
			if (valida) {
				
				/* On déselectionne les lignes cocchées */
				select_cette_ligne ( id, row, 0 );
							
				$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
				$('target').load("gestion_utilisateurs/post_utilisateurs.php?action=suppr&id=" + id);
				window.setTimeout("document.location.href='index.php?page=utilisateurs&filter=" + $('filt').value + "'", 1500);
				
			}
		} else {
			alert('L\'utilisateur a une machine en prêt. Merci de la rendre avant suppression !');
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
	
	
	// *********************************************************************************
	//
	//				Selection/déselection de toutes les rows
	//
	// *********************************************************************************	
	
	
	function checkall(_table) {
		var table = $(_table);	// le tableau du matériel
		var checkall_box = $('checkall');	// la checkbox "checkall"
		
		for ( var i = 1 ; i < table.rows.length ; i++ ) {

			var lg = table.rows[i].id;					// le tr_id (genre tr115)
			var id = lg.replace("tr_id","");
		
			if (lg != "tr_id1") {
			
				if (checkall_box.checked == true) {
					$('chk' + id).checked = true;
					select_cette_ligne( lg.substring(5), i, 1 )					//on selectionne la ligne et on ajoute l'index
				} else {
					$('chk' + id).checked = false;
					select_cette_ligne( lg.substring(5), i, 0 )					//on déselectionne la ligne et on la retire de l'index
				}
			}
			
		}
		
	}
	
	
	// *********************************************************************************
	//
	//				Ajout des index pour postage sur clic de la checkbox
	//
	// *********************************************************************************	
	 
	function select_cette_ligne( tr_id, num_ligne, check ) {

		var chaine_id = $('users_a_poster').value;
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
					if ( table_id.contains(tr_id) ) { // la valeur existe dans la liste on le supprime donc le tr_id de la liste
						table_id.erase(tr_id);
						// alternance des couleurs calculée avec la parité
						if ( num_ligne % 2 == 0 ) li.className="tr1"; else li.className="tr2";
					}
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
			$('users_a_poster').value = table_id.join(";");

			if ( $('users_a_poster').value != "" ) {
				$('modif_selection').setStyle("display", "inline");
				$('nb_selectionnes').setStyle("display", "inline");
				$('nb_selectionnes').innerHTML = table_id.length-1;	// On entre le nombre de machines sélectionnées	

			} else { 
				$('modif_selection').setStyle("display", "none");
				$('nb_selectionnes').setStyle("display", "none");
			}
			
			
		}
	}
	
	
	
</script>

