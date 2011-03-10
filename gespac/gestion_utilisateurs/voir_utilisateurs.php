<?PHP
	session_start();
	
	/* fichier de visualisation des utilisateurs :
	
		view de la db gespac avec tous les users du parc
	*/
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-06-01#", $_SESSION['droits']);
	
	
?>


<h3>Visualisation des utilisateurs</h3>


<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_utilisateurs = $db_gespac->queryAll ( "SELECT user_nom, user_logon, user_password, grade_nom, user_mail, user_id, user_skin, user_mailing FROM users, grades WHERE users.grade_id=grades.grade_id ORDER BY user_nom" );

?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'user_table', '1')" type="text"></center>
	</form>
	
	<input type=hidden name='users_a_poster' id='users_a_poster' value=''>
	
<?PHP
	// Ajout d'un utilisateur
	if ( $E_chk )  {
		echo "<a href='gestion_utilisateurs/form_utilisateurs.php?height=300&width=640&id=-1' rel='slb_users' title='ajout d un utilisateur'> <img src='img/add.png'>Ajouter un utilisateur </a>";
		echo "<span id='modif_selection' style='display:none; float:right; margin-right:20px'><a href='gestion_utilisateurs/form_utilisateurs.php?height=200&width=640&action=modlot' rel='slb_users' title='modifier selection'> <img src='img/write.png'>Modifier le lot</a> <span id='nb_selectionnes'></span></span>";
	}
?>

	<center>
	<br>
	<table class="tablehover" id="user_table" width=800>
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
						
					$nom 		= $record[0];
					$logon 		= $record[1];
					$password 	= $record[2];
					$grade		= $record[3];
					$mail 		= $record[4];
					$id			= $record[5];
					$skin		= $record[6];
					$mailing	= $record[7];
					
					$mailing_chk = $mailing == 1 ? "<img src='img/ok.png' height=16px width=16px>" : "";
					
					
					// test si la machine est prétée ou pas
					$rq_machine_pretee = $db_gespac->queryAll ( "SELECT mat_id FROM materiels WHERE user_id=$id AND user_id<>1" );
					$mat_id = @$rq_machine_pretee[0][0];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
							
					if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
							$id_pret = 0;
						} else {	// la machine est prêtée ($mat_id existe)
							$id_pret = 1;
					}
					
					
					echo "<tr id=tr_id$id class=$tr_class>";
					
					// on affiche pas la checkbox pour le compte ati (pas modifiable)
					if ( $E_chk ) {
						if ( $logon == "ati" ) {
							$chk_box = "<td>&nbsp</td>";
						} else {
							$chk_box = "<td> <input type=checkbox name=chk indexed=true value='$id' onclick=\"select_cette_ligne('$id', $compteur); \"> </td>";	
						}
					}
					
					echo $chk_box;
					echo "<td> $nom </td>";
					echo "<td> $logon </td>";
					echo "<td> $grade </td>";
					echo "<td> $mail </td>";
					echo "<td> $skin </td>";
					echo "<td> $mailing_chk </td>";
					
					if ( $E_chk ) {
						if ( $logon == "ati" ) {
							$modif_user = "<td><img src='img/write.png' style=display:none></td>";
							$suppr_user = "<td><img src='img/delete.png' style=display:none></td>";
						} else {
							$modif_user = "<td><a href='gestion_utilisateurs/form_utilisateurs.php?height=300&width=640&id=$id&action=mod' rel='slb_users' title='Formulaire de modification de l`utilisateur $nom'><img src='img/write.png'> </a></td>";
							$suppr_user = "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_user($id, '$nom', this.parentNode.parentNode.rowIndex, $id_pret);\">	<img src='img/delete.png' title='supprimer $nom'>	</a> </td>";
						}
						
						echo $modif_user;
						echo $suppr_user; 				
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP
	if ( $E_chk )
		echo "<a href='gestion_utilisateurs/form_utilisateurs.php?height=300&width=640&id=-1' rel='slb_users' title='ajout d un utilisateur'> <img src='img/add.png'>Ajouter un utilisateur </a>";

	// On se déconnecte de la db
	$db_gespac->disconnect();
?>



<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_users'});
	});

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	
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
							
				/*	supprimer la ligne du tableau	*/
				document.getElementById('user_table').deleteRow(row);
				/*	poste la page en ajax	*/
				$('target').load("gestion_utilisateurs/post_utilisateurs.php?action=suppr&id=" + id);
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
	
	
	// *********************************************************************************
	//
	//				Selection/déselection de toutes les rows
	//
	// *********************************************************************************	
	
	
	function checkall(_table) {
		var table = $(_table);	// le tableau du matériel
		var checkall_box = $('checkall');	// la checkbox "checkall"
		
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

		var chaine_id = $('users_a_poster').value;
		var table_id = chaine_id.split(";");

		var nb_selectionnes = $('nb_selectionnes');
		
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
			$('users_a_poster').value = table_id.join(";");

			if ( $('users_a_poster').value != "" ) {
				$('modif_selection').style.display = "";

			} else { 
				$('modif_selection').style.display = "none";
			}
		}
	}
	
	
	
</script>

