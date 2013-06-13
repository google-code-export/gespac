<?PHP

/*	CREATION DU FICHIER D'EXPORT INVENTAIRE	*/


	// lib
	require_once ('../config/databases.php');
	require_once ('../fonctions.php');
	include_once ('../../class/Sql.class.php');	
	include ('../../version'); //on flag la version de gespac dans l'export
	
	// Connexion à la base de données GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
	$liste_export = $con_gespac->QueryAll ( "
	select college.clg_uai as clg_uai, clg_nom, clg_cp, clg_ville, salle_nom, mat_nom, mat_etat, mat_origine, marque_type, marque_stype, marque_marque, marque_model, mat_dsit, mat_serial, salle_vlan, salle_etage, salle_batiment, clg_site_web, clg_site_grr 
	from college, salles, materiels, marques

	where 
		college.clg_uai = salles.clg_uai AND
		salles.salle_id = materiels.salle_id AND
		marques.marque_id = materiels.marque_id
	" );

$con_gespac->Close();
		
	$filename = "inv_" . $liste_export[0]["clg_nom"] . "_" . $liste_export[0]["clg_ville"] . "_" . $liste_export[0]["clg_uai"] . "_gespac_".$version.".csv";
	//On formate le nom du fichier ici histoire de pas avoir de caractères zarb'
	$filename = strtr($filename, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);


	$fp = fopen('../dump/' .$filename, 'w+');

	// ENTETES
	fputcsv($fp, array('clg_uai', 'clg_nom', 'clg_cp', 'clg_ville', 'salle_nom', 'mat_nom', 'etat', 'origine', 'type', 'stype', 'marque', 'modele', 'inventaire', 'lastcome', 'fidele',  'serial', 'vlan', 'etage', 'batiment'), ',' );

	foreach ($liste_export as $record) {
		$clg_uai 	= mb_strtoupper($record['clg_uai']);
		$clg_nom  	= strtr(mb_strtoupper($record['clg_nom']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$clg_cp 	= mb_strtoupper($record['clg_cp']);
		$clg_ville 	= strtr(mb_strtoupper($record['clg_ville']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$salle_nom 	= strtr(mb_strtoupper($record['salle_nom']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$mat_nom 	= mb_strtoupper($record['mat_nom']);
		$etat 		= strtr(mb_strtoupper($record['mat_etat']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$origine 	= mb_strtoupper($record['mat_origine']);
		$type 		= mb_strtoupper($record['marque_type']);
		$stype 		= mb_strtoupper($record['marque_stype']);
		$marque 	= mb_strtoupper($record['marque_marque']);
		$modele 	= mb_strtoupper($record['marque_model']);
		$dsit 		= mb_strtoupper($record['mat_dsit']);
		$serial 	= mb_strtoupper($record['mat_serial']);
		$vlan 		= mb_strtoupper($record['salle_vlan']);
		$etage 		= strtr(mb_strtoupper($record['salle_etage']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$batiment 	= strtr(mb_strtoupper($record['salle_batiment']), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$web 		= mb_strtoupper($record['clg_site_web']);
		$grr 		= mb_strtoupper($record['clg_site_grr']);

		
		//Partie fidelité OCS :-(

		// Connexion à la base de données GESPAC
		$con_ocsweb = new Sql ( $host, $user, $pass, $ocsweb );
		
		if ($con_ocsweb->Exists()) {
			$liste_export_ocs = $con_ocsweb->QueryAll ("select LASTCOME, FIDELITY from hardware, bios where bios.HARDWARE_ID=hardware.ID AND bios.SSN = '$serial'");
			if (!$liste_export_ocs) {
				$last='matériel non présent dans OCS'; $fidele='0';
			}//du fait du MAX(LASTCOME) cette ligne ne marche pas...
			else {
				foreach ($liste_export_ocs as $record_ocs) {
					$last = ($record_ocs['LASTCOME']);
					$fidele =($record_ocs['FIDELITY']);
				}				
			}
		}
		
		//demande etude imprimante reseaux ou pas
		if ($type == 'IMPRIMANTE') { 
			if ($modele[strlen($modele)-1] == 'N') {$stype = $stype.'_RX';}//si le modèle imprimante contient à la fin un N est bien c'est une imprimante RX normalement et on ajout _RX à la fin du sous type
		} 
		
		fputcsv($fp, array($clg_uai, $clg_nom, $clg_cp, $clg_ville, $salle_nom, $mat_nom, $etat, $origine, $type, $stype, $marque, $modele, $dsit, $last, $fidele, $serial, $vlan, $etage, $batiment), ',');
		
	}

	fclose($fp);

	echo "<center><h1><a href='./dump/$filename'>Fichier CSV inventaire</a></h1></center>";

?>
