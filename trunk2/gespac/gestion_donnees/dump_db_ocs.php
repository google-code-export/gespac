<div class="entetes" id="entete-dumpocs">	

	<span class="entetes-titre">DUMP BASE OCS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Permet la création d'un dump de la base OCS.</div>

</div>

<div class="spacer"></div>

<?php

	// Connexion à la base de données OCS
	$con_ocs = new Sql ( $host, $user, $pass, $ocsweb );


	if ($con_ocs->Exists()) {

		// nom du fichier dump
		$dumpfile = "dump-$ocsweb-".date("Ymd-His").".sql"; 
		
		// création du fichier dump dans le dossier dump
		file_put_contents( "dump/" . $dumpfile, dump_base($host, $user, $pass, $ocsweb) );
		
		// On écrit des choses interessantes ici ...
		echo "<center><h2>Création du fichier dump de la base OCS dans le dossier dump du site ...";
		echo "<br>";
		echo "Pour le voir cliquez >> <a href='../gespac/dump/$dumpfile' target=_blank> $dumpfile </a> << </H2></center>";
		
		//Insertion d'un log
		$log_texte = "Le fichier $dumpfile a été créé";
		
		$req_log_dump_ocs = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Dump OCS', '$log_texte' );";
		$result = $con_ocs->Execute ( $req_log_dump_ocs );

	}
	else {
		echo "<center><h2 style='color:red;'>La base OCS ne semble pas joignable. Impossible de créer un dump de cette base.</h2></center>";
	}
?>
