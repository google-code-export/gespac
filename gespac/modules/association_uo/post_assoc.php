<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Log.class.php');
	include_once ('../../../class/Sql.class.php');

	
	// Log des requêtes SQL
	$log = new Log ("../../dump/log_sql.sql");
	

	foreach ($_POST as $key=>$value) {
		
		if ($value <> "") {
			
			$value = utf8_encode($value);
			
			// On récupère l'id
			
			$key = preg_replace("#param#", "", $key);
			
			$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );
			
			$salle = $con_gespac->QueryOne ( "SELECT salle_nom FROM salles WHERE salle_id=$key" );

			$snapin_nom = "AIC " . strtoupper($salle);
			
			$con_gespac->Close();
			
			$con_fog 		= new Sql ( $host, $user, $pass, $fog );
			// On vérifie si le snapin existe dans fog
			$existe = $con_fog->QueryOne ( "SELECT sID FROM snapins WHERE sName = '$snapin_nom' " );
			
			// le snapin n'existe pas
			if ( !$existe) {
				
				$snapin_desc = utf8_decode("Intégration au domaine généré par gespac pour la salle " . $salle);
				$snapin_path = "/opt/fog/snapins/aic.exe";
				
				$sql = "INSERT INTO snapins (sName, sDesc, sFilePath, sArgs, sCreator, sReboot) VALUES ('$snapin_nom', '$snapin_desc', '$snapin_path', '$value', 'ati', '0');";
				$snapin = $con_fog->Execute ( $sql );
				
				$log->Insert($sql);
		
				echo "Création du snapin $snapin_nom <br>";
				
			}
			else {
				echo "Le snapin $snapin_nom existe déjà.<br>";
			}
			
			$con_fog->Close();
			
		}
		
	}


?>
