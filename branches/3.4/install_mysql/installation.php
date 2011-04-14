<?php
	echo '<center>';
	echo 'Mise en place de la nouvelle base GESPAC<br>';

	require_once ('../include/config.php');
 	mysql_select_db($database_gespac, $gespac);
 
 
	$requetes="";
 
	$sql=file("base_gespac.sql"); // on charge le fichier SQL
	foreach($sql as $l){ 
		if (substr(trim($l),0,2)!="--"){
			$requetes .= $l;
		}
	}
 
	$reqs = split(";",$requetes);// on sépare les requêtes
	foreach($reqs as $req){	// et on les éxécute
		if (!mysql_query($req,$gespac) && trim($req)!=""){
			die("ERROR : ".$req); // stop si erreur 
		}
	}
echo "base install&eacute;e<br>
<a href='fin_install.php'>Retour</a>";
?>
