<?php
	echo '<center>';
	echo 'Mise en place de la nouvelle base GESPAC<br>';

	require_once ('../gespac/config/databases.php');
	$ressource = mysql_pconnect($host, $user, $pass) or trigger_error(mysql_error(),E_USER_ERROR); 
	
 	mysql_select_db($gespac, $ressource);
 
 
	$requetes="";
 
	$sql=file("base_gespac.sql"); // on charge le fichier SQL
	foreach($sql as $l){ 
		if (substr(trim($l),0,2)!="--"){
			$requetes .= $l;
		}
	}
 
	$reqs = explode(";",$requetes);// on sépare les requêtes
	foreach($reqs as $req){	// et on les éxécute
		if (!mysql_query($req,$ressource) && trim($req)!=""){
			die("ERROR : ".$req); // stop si erreur 
		}
	}
echo "base install&eacute;e<br>
<a href='fin_install.php'>Retour</a>";
?>
