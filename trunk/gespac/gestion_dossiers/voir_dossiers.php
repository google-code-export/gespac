<?PHP
	
	session_start();

/*

	Page de visualisation des dossiers
	

*/


	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');




	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-03-03#", $_SESSION['droits']);
	
?>

<h3>Visualisation des dossiers</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<?PHP 
	if ( $E_chk ) echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_dossiers/form_dossiers.php?id=-1');\"> <img src='img/add.png'>Créer un dossier </a>"; 
		
	$liste_dossiers = $con_gespac->QueryAll ("SELECT dossiers.dossier_id as dossier_id, dossier_type, dossier_mat, txt_date, txt_etat, txt_texte FROM dossiers, dossiers_textes WHERE dossiers.dossier_id = dossiers_textes.dossier_id AND txt_etat='ouverture';");
	
	echo "<table id='dossiers_table' width='900px'>";
	
		echo "<th>&nbsp;</th>";
		echo "<th>date</th>";
		echo "<th>type</th>";
		echo "<th>etat</th>";
		echo "<th>commentaire</th>";
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		
		$compteur = 0;
		
		foreach ( $liste_dossiers as $dossier) {
			
			$dossier_id 	= $dossier['dossier_id'];
			$dossier_type 	= stripslashes($dossier['dossier_type']);
			$dossier_mat 	= $dossier['dossier_mat'];
			$date_ouverture = date ("d-m-Y H:i", strtotime($dossier['txt_date']));
			$txt_texte 		= stripslashes($dossier['txt_texte']);
			
			$last_etat		= $con_gespac->QueryOne("SELECT txt_etat FROM dossiers_textes WHERE dossier_id=$dossier_id ORDER BY txt_date DESC");
			
			// alternance des couleurs
			$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
			
			// Le dossier
			
			echo "<tr id='tr_$dossier_id' class=$tr_class>";
				echo "<td width=20px><a href='#' onclick=\"toggleDossier($dossier_id);\"><img src='img/deplier.png'></a></td>";
				echo "<td width=100px>$date_ouverture</td>";
				echo "<td width=60px>$dossier_type</td>";
				echo "<td width=60px>$last_etat</td>";
				echo "<td>$txt_texte</td>";
				if ( $E_chk ) echo "<td width=20px><a href='#' onclick=\"AffichePage('conteneur', 'gestion_dossiers/form_dossiers.php?id=$dossier_id');\"> <img src='img/write.png'> </a></td>";
			
			echo "</tr>";
					
			
			// 	Les pages du dossier 	 	 	 	 
			$page_dossier = $con_gespac->QueryAll ("SELECT txt_id, txt_date, txt_texte, txt_etat, users.user_nom FROM dossiers_textes, users WHERE dossier_id=$dossier_id AND txt_user=user_id");

			echo "<tr id='dossiers$dossier_id' style='display:none' class='inner_tr'>";
			
			echo "<td colspan=6 class='inner_td'>";
				echo "<table class='innertable'>";
					echo "<th>utilisateur</th>";
					echo "<th>date</th>";
					echo "<th>etat</th>";
				
				
					foreach ( $page_dossier as $page) {
						
						$txt_id 	= $page['txt_id'];
						$txt_date 	= date ("d-m-Y H:i", strtotime($page['txt_date']));
						$txt_texte 	= $page['txt_texte'];
						$txt_etat 	= $page['txt_etat'];
						$user_nom 	= $page['user_nom'];
					
						
						echo "<tr>";
							echo "<td>$user_nom</td>";
							echo "<td>$txt_date</td>";
							echo "<td>$txt_etat</td>";
						echo "</tr>";
						
						echo "<tr>";
							echo "<td colspan=4>$txt_texte</td>";
						echo "</tr>";
						
					}
					
				
				echo "</table>";
			echo "</td></tr>";

			$compteur++;

		}
		
		
		echo "</table>";
	
?>


<script>
	
	// Montre / cache les pages d'un dossier
	function toggleDossier(dossier) {
		if ( $('dossiers' + dossier).style.display == "none" ) {
			$('dossiers' + dossier).style.display = "";
			//$('tr_' + dossier).className = "selectedrow";
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
	
	
</script>
