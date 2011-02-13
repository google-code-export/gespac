<script type="text/javascript">	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
</script>

<?php
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	
	
	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	
	
	$base = "gespac";

	// nom du fichier dump
	$dumpfile = $base. "-sqldump-".date("Ymd-His").".sql"; 
	
	// création du fichier dump dans le dossier dump
	file_put_contents( "../dump/" . $dumpfile, dump_base($host, $user, $pass, $base) );
	
	// On écrit des choses interessantes ici ...
	echo "<center><h2>Création du fichier dump de la base GESPAC dans le dossier dump du site ...";
	echo "<br>";
	echo "Pour le voir cliquez >> <a href='../gespac/dump/$dumpfile' target=_blank> $dumpfile </a> << </H2></center>";
	
	//Insertion d'un log
	$log_texte = "Le fichier $dumpfile a été créé";
		
	$req_log_dump_gespac = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Dump GESPAC', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_dump_gespac );

?>