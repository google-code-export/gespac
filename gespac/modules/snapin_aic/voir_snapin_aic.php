<?PHP session_start(); ?>

<!--
	Visualisation des PC à migrer dans FOG
	On sélectionne les PC dans la liste et on met
	à jour dans le post les noms des machines dans FOG.

-->


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	
	// gestion des droits
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-13#", $_SESSION['droits']);


	echo "<h3>Création des snapins pour intégration dans le domaine via client iaca</h3>";
	echo "<br><br><br><br><center>";
	
	if ($E_chk) {
		
		if (file_exists("/opt/fog/snapins/aic.exe")) {
			echo "<a href='#' onclick=\"javascript:AffichePage('conteneur', 'modules/snapin_aic/form_snapin_aic.php');\" >Gérer les snapins AIC</a>";
		} else {
			echo "Le fichier <b>aic.exe</b> (attention à la casse) n'existe pas. Merci de créer un snapin avec ce fichier dans FOG.";
			echo "<br><br>";
			echo "<a href='#' onclick=\"javascript:AffichePage('conteneur', 'modules/snapin_aic/form_snapin_aic.php');\" >Je posterai mon snapin aic.exe plus tard.</a>";
		}
		
	} else {
		echo "vous n'avez pas les droits suffisants.";
	}
	
	echo "<br><br><br><br></center>";
	
	
	
	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	
	// rq pour la liste des PC
	$liste_snapins_fog = $con_fog->QueryAll ("SELECT sID, sName, sDesc, sFilePath, sArgs FROM snapins WHERE sFilePath='/opt/fog/snapins/aic.exe';");
	
			
	/*************************************
	*
	*		LISTE DE SELECTION
	*
	**************************************/

	echo "<table id='association_uo' width=100%>";
	
	$compteur = 0;
	
	echo "
		<th>Snapin</th>
		<th>Arguments</th>
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
		</tr>";

		$compteur++;


		
	}
	
	echo "</table>";	

?>
