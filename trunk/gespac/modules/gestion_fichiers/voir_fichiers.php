<?PHP
	
	session_start();

/*

	Page de visualisation des dossiers
	

*/


	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');




	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-07#", $_SESSION['droits']);
	
?>

<h3>Visualisation des fichiers</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<?PHP 
	if ( $E_chk ) echo "<a href='modules/gestion_fichiers/form_fichiers.php?height=320&width=640' rel='slb_fichiers' title='Ajout fichier'>	Ajouter un fichier	</a>"; 
		
	$liste_fichiers = $con_gespac->QueryAll ("SELECT * FROM fichiers;");
	
	echo "<table id='fichiers_table' width='900px'>";
	
		echo "<th>fichier</th>";
		echo "<th>description</th>";
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		
		$compteur = 0;
		
		foreach ( $liste_fichiers as $fichier) {
			
			$fichier_id 	= $fichier['fichier_id'];
			$fichier_chemin = stripslashes($fichier['fichier_chemin']);
			$fichier_desc 	= stripslashes($fichier['fichier_description']);
			

			// alternance des couleurs
			$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
			
			// Le dossier
			
			echo "<tr class=$tr_class>";
				echo "<td width='200px'><a href='fichiers/$fichier_chemin' target=_blank>$fichier_chemin</a></td>";
				echo "<td>$fichier_desc</td>";
				if ( $E_chk ) echo "<td width=20px><a href='#' onclick=\"validation_suppr_fichier($fichier_id, '$fichier_chemin');\"> <img src='img/delete.png'> </a></td>";
				else echo "<td>&nbsp;</td>";
			
			echo "</tr>";
	
			echo "</td></tr>";

			$compteur++;

		}
			
		echo "</table>";
	
?>


<script>
	
	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_fichiers'});
	});
	
	
	
	// *********************************************************************************
	//
	//			Fonction de validation de la suppression d'un matériel
	//
	// *********************************************************************************

	function validation_suppr_fichier (id, fichier) {
			
		var valida = confirm('Voulez vous supprimer le fichier ' + fichier + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_materiels.php
		if (valida) {
			
			//	poste la page en ajax
			$('target').load("modules/gestion_fichiers/post_fichiers.php?action=suppr&id=" + id);
			
			// lance la fonction avec un délais de 1500ms
			window.setTimeout("$('conteneur').load('modules/gestion_fichiers/voir_fichiers.php');", 1500);
	
		}
	}

			
</script>
