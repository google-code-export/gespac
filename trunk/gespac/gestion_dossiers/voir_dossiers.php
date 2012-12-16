<?PHP
	session_start();

/*

	Page de visualisation des dossiers
	

*/


	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-03-03#", $_SESSION['droits']);
		
	
	$filtre = $_GET["filter"];	
	$showclos = $_GET["showclos"];	
?>


<div class="entetes" id="entete-dossiers">	

	<span class="entetes-titre">LES DOSSIERS<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Cette page permet de gérer les dossiers, leur création, modification et suppression.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_dossiers/form_dossiers.php?height=750&width=750&id=-1' rel='slb_dossiers' title='Ajouter un dossier'> <img src='img/icons/add.png'></a>";?></span>
		
		<span class="option"><?PHP 
			if (!$showclos) echo "<a href='index.php?page=dossiers&showclos=1' title='Montrer les dossiers clos'> <img src='img/icons/eye.png'></a>";
			else echo "<a href='index.php?page=dossiers' title='Cacher les dossiers clos'> <img src='img/icons/eye.png'></a>";?></span>
			
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'dossiers_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 
	$liste_dossiers = $con_gespac->QueryAll ("SELECT dossiers.dossier_id as dossier_id, dossier_type, dossier_mat, txt_date, txt_etat, txt_texte FROM dossiers, dossiers_textes WHERE dossiers.dossier_id=dossiers_textes.dossier_id GROUP BY dossiers.dossier_id ORDER BY dossier_id DESC;");
		
	echo "<table id='dossiers_table'>";
	
		echo "<th>&nbsp;</th>";
		echo "<th>dossier</th>";
		echo "<th>date</th>";
		echo "<th>type</th>";
		echo "<th>etat</th>";
		echo "<th>commentaire</th>";
		echo "<th>&nbsp;</th>";
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		
		$compteur = 0;
		
		foreach ( $liste_dossiers as $dossier) {
			
			$dossier_id 	= $dossier['dossier_id'];
			$dossier_type 	= stripslashes($dossier['dossier_type']);
			$dossier_mat 	= $dossier['dossier_mat'];
			$date_ouverture = date ("d-m-Y H:i", strtotime($dossier['txt_date']));
			$txt_texte 		= stripslashes($dossier['txt_texte']);
			
			$last_etat		= $con_gespac->QueryOne("SELECT txt_etat FROM dossiers_textes WHERE dossier_id=$dossier_id ORDER BY txt_date DESC");
			
			// Voir aussi les dossiers clos
			if ( $showclos || $last_etat <> "clos" ) {
							
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
				
				// Le dossier
				
				echo "<tr id='tr_$dossier_id' class=$tr_class>";
					echo "<td width=20px><a href='#' onclick=\"toggleDossier($dossier_id);\"><img src='img/deplier.png'></a></td>";
					echo "<td width=20px>$dossier_id</td>";
					echo "<td width=100px>$date_ouverture</td>";
					echo "<td width=60px>$dossier_type</td>";
					echo "<td width=60px class='td_$last_etat'>$last_etat</td>";
					echo "<td>$txt_texte</td>";
					echo "<td width=20px><a href='gestion_dossiers/liste_materiels.php?height=480&width=500&dossier=$dossier_id' rel='slb_dossiers' title='Liste des matériels du dossier $dossier_id'><img src='img/outils.png'></a></td>";
					if ( $E_chk && $last_etat<>"clos") echo "<td width=20px><a href='#' onclick=\"AffichePage('conteneur', 'gestion_dossiers/form_dossiers.php?id=$dossier_id');\"> <img src='img/write.png'> </a></td>";
					else echo "<td>&nbsp;</td>";
					if ( $E_chk && $last_etat<>"clos") echo "<td width=20px><a href='#' onclick=\"validation_suppr_dossier('$dossier_id');\"> <img src='img/delete.png'> </a></td>";
					else echo "<td>&nbsp;</td>";
				
				echo "</tr>";
				
				
				
				// 	Les pages du dossier 	 	 	 	 
				$page_dossier = $con_gespac->QueryAll ("SELECT txt_id, txt_date, txt_texte, txt_etat, users.user_nom FROM dossiers_textes, users WHERE dossier_id=$dossier_id AND txt_user=user_id");

				echo "<tr id='dossiers$dossier_id' style='display:none' class='inner_tr'>";
				
				echo "<td colspan=9 class='inner_td'>";
					echo "<table class='innertable'>";

						foreach ( $page_dossier as $page) {
							
							$txt_id 	= $page['txt_id'];
							$txt_date 	= date ("d-m-Y H:i", strtotime($page['txt_date']));
							$txt_texte 	= stripcslashes($page['txt_texte']);
							$txt_etat 	= $page['txt_etat'];
							if (strtoupper($_SESSION['grade']) == 'ATI' || strtoupper($_SESSION['grade']) == 'ROOT') {
								$user_nom 	= $page['user_nom'];
							} else {
								$user_nom 	= 'Anonyme';
							} 
							
							
							echo "<tr>";
								echo "<td class='td_$txt_etat'>$txt_etat</td>";
								echo "<td class='td_$txt_etat'>$txt_date</td>";
								echo "<td class='td_$txt_etat'>$user_nom</td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td colspan=4>$txt_texte</td>";
							echo "</tr>";
							
							//echo "<tr><td colspan=4 style='border:none;background-color:white;'>&nbsp;</td></tr>";
							
						}
						
					
					echo "</table>";
				echo "</td></tr>";
				
				$compteur++;
			}
			

		}
		
		
		echo "</table>";
	
?>


<script>
	
	window.addEvent('domready', function(){ 
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_dossiers'});	
	});
	
	// Fonction de validation de la suppression d'une marque
	function validation_suppr_dossier (id) {
	
		var valida = confirm('Voulez-vous vraiment supprimer le dossier ' + id + ' ?\n ATTENTION, toutes les pages du dossier seront détruites !');
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
			/*	poste la page en ajax	*/
			$('target').load("gestion_dossiers/post_dossiers.php?action=suppr&id=" + id);
			/*	on recharge la page au bout de 1000ms	*/
			window.setTimeout("$('conteneur').load('gestion_dossiers/voir_dossiers.php');", 1000);
		}
	
	}
	
	// Montre / cache les pages d'un dossier
	function toggleDossier(dossier) {
		if ( $('dossiers' + dossier).style.display == "none" ) {
			$('dossiers' + dossier).style.display = "";
			$('tr_' + dossier).style.borderStyle = "solid";
			$('tr_' + dossier).style.borderBottom = "none";
			$('tr_' + dossier).style.borderColor = "blue";
			$('tr_' + dossier).style.borderWidth = "3px";
			
		}
		else {
			$('dossiers' + dossier).style.display = "none";
			$('tr_' + dossier).style.border = "none";
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
			
			if (table.rows[r].className != 'inner_tr') {
			
				for (var i = 0; i < words.length; i++) {
					if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
						displayStyle = '';
					}	
					else {	// on masque les rows qui ne correspondent pas
						displayStyle = 'none';
						break;
					}
				}
				
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;	
		}
		
	}	
	
	
	
</script>
