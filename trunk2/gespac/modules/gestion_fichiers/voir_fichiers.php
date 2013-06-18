<?PHP
	
	session_start();

/*

	Page de visualisation des dossiers
	

*/

	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-07#", $_SESSION['droits']);
	
?>


<div class="entetes" id="entete-gestfichiers">	

	<span class="entetes-titre">GESTIONNAIRE DE FICHIERS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Le gestionnaire de fichier permet de poster et partager des fichiers entre utilisateurs.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='modules/gestion_fichiers/form_fichiers.php?maxheight=650&action=add' class='editbox' title='Ajout fichier'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'fichiers_table');" type="text" value=<?PHP echo $_GET['filter'];?>><span id="filtercount" title="Nombre de lignes filtrées"></span></form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 

	$liste_fichiers = $con_gespac->QueryAll ("SELECT * FROM fichiers;");
	
	echo "<table id='fichiers_table' class='bigtable hover'>";
	
		echo "<th>fichier</th>";
		echo "<th>description</th>";
		echo "<th>proprietaire</th>";
		echo "<th>&nbsp;</th>";	
		echo "<th>&nbsp;</th>";	
		
		$compteur = 0;
		
		foreach ( $liste_fichiers as $fichier) {
			
			$fichier_id 	= $fichier['fichier_id'];
			$user_id 		= $fichier['user_id'];
			$droits 		= $fichier['fichier_droits'];
			$fichier_chemin = stripslashes($fichier['fichier_chemin']);
			$fichier_desc 	= stripslashes($fichier['fichier_description']);
			$proprio_nom 	= $con_gespac->QueryOne ("SELECT user_nom FROM users WHERE user_id = $user_id;");
			$proprio_grade	= $con_gespac->QueryOne ("SELECT grade_nom FROM grades, users WHERE users.grade_id = grades.grade_id AND user_id = $user_id;");
			$proprio_login	= $con_gespac->QueryOne ("SELECT user_logon FROM users WHERE user_id = $user_id;");
			
			
			$lecture = false;
			$ecriture = false;
			
			// On teste les droits
			
			// Si 00 -> juste le root et le propriétaire
			// Si 10 -> lecture au groupe
			// Si 11 -> lecture pour tout le monde
			// Si 20 -> lecture et écriture au groupe
			// Si 21 -> lecture et écriture au groupe, lecture seule à tout le monde
			// Si 22 -> lecture et écriture à tout le monde
			// 01 et 02 impossible (le groupe est inclus dans tout le monde)
						
			if ( strtoupper($proprio_login) == strtoupper($_SESSION['login']) || $_SESSION['grade'] == 'root' ) {
				$lecture = true; 
				$ecriture = true;
			}
			else {
			
				if ( $droits == '10' ) {  
					$lecture = $_SESSION['grade'] == $proprio_grade ? true : false ;	// Si le grade du propriétaire est le même que l'utilsateur courant lecture ok
					$ecriture = false; 
				}	
				
				if ( $droits == '20' ) {  
					
					if ($_SESSION['grade'] == $proprio_grade) {
						$lecture = true;
						$ecriture = false; 
					}		
				}
								
				if ( $droits == '21' ) {  
					$ecriture = $_SESSION['grade'] == $proprio_grade ? true : false ;	// Si le grade du propriétaire est le même que l'utilsateur courant ecriture ok
					$lecture = true; 
				}
				
				if ( $droits == '22' ) { $lecture = true; $ecriture = true;	}
				
				if ( $droits == '11' ) { $lecture = true; $ecriture = false; }		
				
	
			}
			
					
			// alternance des couleurs
			$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
			
			// Le dossier
			
			if ( $lecture ) {
				echo "<tr class=$tr_class>";
					echo "<td width='200px'><a href='fichiers/$fichier_chemin' target=_blank>$fichier_chemin</a></td>";
					echo "<td>$fichier_desc</td>";
					echo "<td>$proprio_nom</td>";
					
					if ( $ecriture && $E_chk ) { // Il faut avoir les droits en écriture sur le fichier ET les droits d'écriture par l'administrateur
						echo "<td width=20px><a href='modules/gestion_fichiers/form_fichiers.php?action=mod&id=$fichier_id' class='editbox' title='Modifier un fichier'> <img src='img/write.png'> </a></td>";
						echo "<td width=20px><a href='modules/gestion_fichiers/form_fichiers.php?action=del&id=$fichier_id' class='editbox' title='Supprimer un fichier'> <img src='img/delete.png'> </a></td>";
					} else {
						echo "<td>&nbsp;</td>";
						echo "<td>&nbsp;</td>";
					}
					
				echo "</tr>";
		
			}

			$compteur++;

		}
			
		echo "</table>";
	
?>


<script>
		
	
	// *********************************************************************************
	//
	//			Fonction de validation de la suppression d'un matériel
	//
	// *********************************************************************************

	function validation_suppr_fichier (id, fichier) {
			
		var valida = confirm('Voulez vous supprimer le fichier ' + fichier + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_materiels.php
		if (valida) {
			
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("modules/gestion_fichiers/post_fichiers.php?action=suppr&id=" + id);
			window.setTimeout("document.location.href='index.php?page=gestfichiers'", 1500);		
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
