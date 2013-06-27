<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Log.class.php');
	include_once ('../../../class/Sql.class.php');

	
	// Log des requêtes SQL
	$log = new Log ("../../dump/log_sql.sql");

	
	$action = $_GET['action'];
	
	//-------------------------------------- @@ SUPPRESSION
	if ($action == 'suppr') {
	
		$id = $_GET['id'];
	
		$con_fog = new Sql ( $host, $user, $pass, $fog );
		
		$sql = "DELETE FROM snapins WHERE sID = $id;";
		$snapin = $con_fog->Execute ( $sql );
		
		echo "Le snapin a été supprimé dans FOG.";
	
	}
	
	
	
	//-------------------------------------- @@ CREATION
	if ($action == 'add') {

		$nom_uo = escapeSql($_POST['nom_uo']);
		$param = escapeSql($_POST['param']);


		$snapin_nom = "AIC " . strtoupper($nom_uo);

		
		$con_fog 		= new Sql ( $host, $user, $pass, $fog );
		// On vérifie si le snapin existe dans fog
		$existe = $con_fog->QueryOne ( "SELECT sID FROM snapins WHERE sName = '$snapin_nom' " );
		
		// le snapin n'existe pas
		if ( !$existe) {
			
			$snapin_desc = utf8_decode("Intégration au domaine généré par gespac pour UO " . $nom_uo);
			$snapin_path = "/opt/fog/snapins/aic.exe";
			
			$sql = "INSERT INTO snapins (sName, sDesc, sFilePath, sArgs, sCreator, sReboot) VALUES ('$snapin_nom', '$snapin_desc', '$snapin_path', '$param', 'ati', '0');";
			$snapin = $con_fog->Execute ( $sql );
			
			$log->Insert($sql);

			echo "Création du snapin $snapin_nom <br>";
			
		}
		else {
			echo "Le snapin $snapin_nom existe déjà.<br>";
		}
		
		$con_fog->Close();
	
	
	}

?>
