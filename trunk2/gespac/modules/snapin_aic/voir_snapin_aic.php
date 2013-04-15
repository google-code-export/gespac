<?PHP session_start(); ?>

<!--
	Visualisation des PC à migrer dans FOG
	On sélectionne les PC dans la liste et on met
	à jour dans le post les noms des machines dans FOG.

-->


<script>
	// Fonction de validation de la suppression d'une marque
	function validation_suppr(id, nom) {

		var valida = confirm('Voulez-vous vraiment supprimer le snapin ' + nom + ' ?');
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("modules/snapin_aic/post_snapin_aic.php?action=suppr&id=" + id);
			window.setTimeout("document.location.href='index.php?page=aic'", 1500);		
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


<?PHP
	
	// gestion des droits
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-13#", $_SESSION['droits']);

?>


<div class="entetes" id="entete-aic">	

	<span class="entetes-titre">SNAPINS FOG pour AIC<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Afin d'éviter de créer un fichier AIC par OU de l'AD, on peut déployer le même fichier AIC.EXE avec des paramètres.<br>Cette page permet de créer dans fog un snapin paramétré pour intégrer les machines au domaine.</div>

	<span class="entetes-options">
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'association_uo');" type="text"> 
				<span id="nb_filtre" title="nombre de machines affichés"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>

<?PHP

	echo "<center>";
	
	if ($E_chk) {
		
		if (file_exists("/opt/fog/snapins/aic.exe")) {
			echo "<a href='index.php?page=aicform'>Gérer les snapins AIC</a>";
		} else {
			echo "Le fichier <b>aic.exe</b> (attention à la casse) n'existe pas. Merci de créer un snapin avec ce fichier dans FOG.<br>";
			echo "<a href='index.php?page=aicform'>Je posterai mon snapin aic.exe plus tard.</a><br>";
		}
		
	} else {
		echo "Vous n'avez pas les droits suffisants.";
	}
	
	echo "<br><br></center>";
	
	
	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	
	// rq pour la liste des PC
	$liste_snapins_fog = $con_fog->QueryAll ("SELECT sID, sName, sDesc, sFilePath, sArgs FROM snapins WHERE sFilePath='/opt/fog/snapins/aic.exe';");
	
			
	/*************************************
	*
	*		LISTE DE SELECTION
	*
	**************************************/

	echo "<table id='association_uo'>";
	
	$compteur = 0;
	
	echo "
		<th>Snapin</th>
		<th>Arguments</th>
		<th>&nbsp;</th>
	";

	foreach ($liste_snapins_fog as $record) {
		
		$sID		= $record['sID'];
		$sName		= utf8_encode($record['sName']);
		$sDesc 		= utf8_encode($record['sDesc']);
		$sFilePath 	= $record['sFilePath'];
		$sArgs		= $record['sArgs'];
		
					
		// alternance des couleurs
		$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
		

		echo "<tr id=tr_id$hostid  class=$tr_class>";
			
			echo "<td>$sName</td>
			<td>$sArgs</td>
			<td> <a href='#' onclick=\"javascript:validation_suppr($sID, '$sName');\">	<img src='img/delete.png'>	</a> </td>
		</tr>";

		$compteur++;


		
	}
	
	echo "</table>";	

?>
