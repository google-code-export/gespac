<?PHP


	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');


	$dossier_id = $_GET["dossier"];

	$con_gespac = new Sql($host, $user, $pass, $gespac);



	echo "<p><h3>Matériels du dossier $dossier_id</h3></p>";
	
	$dossier_courant_mat = $con_gespac->QueryOne("SELECT dossier_mat FROM dossiers WHERE dossier_id=$dossier_id");
	
	$arr_dossier_courant_mat = explode(";", $dossier_courant_mat);

	echo "<table width=100%>";
		echo "<th>Matériel</th>";
		echo "<th>Type</th>";
		echo "<th>Salle</th>";
	
	foreach ($arr_dossier_courant_mat as $mat) {
		
		if ($mat <> '') {
			$mat = $con_gespac->QueryRow ("Select mat_id, mat_nom, marque_type, salle_nom FROM materiels, marques, salles WHERE materiels.marque_id=marques.marque_id AND materiels.salle_id=salles.salle_id AND mat_id = $mat");
			
			$mat_nom = $mat[1];
			$mat_type = $mat[2];
			$mat_salle = $mat[3];
			
			echo "<tr><td>$mat_nom</td><td>$mat_type</td><td>$mat_salle</td></tr>";
		}
		
	}
	
	echo "</table>";
	

?>
