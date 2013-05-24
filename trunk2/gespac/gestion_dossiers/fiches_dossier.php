<?PHP
	session_start();

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');


	$dossier_id = $_GET["dossier"];

	$con_gespac = new Sql($host, $user, $pass, $gespac);

	// 	Les pages du dossier 	 	 	 	 
	$page_dossier = $con_gespac->QueryAll ("SELECT txt_id, txt_date, txt_texte, txt_etat, users.user_nom FROM dossiers_textes, users WHERE dossier_id=$dossier_id AND txt_user=user_id");

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
		
		echo "<table class='smalltable' width=500>";
			echo "<tr>";
				echo "<td class='td_$txt_etat'>$txt_etat</td>";
				echo "<td class='td_$txt_etat'>$txt_date</td>";
				echo "<td class='td_$txt_etat'>$user_nom</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=3>$txt_texte</td>";
			echo "</tr>";
		echo "</table><br>";
		
	}
	
?>
