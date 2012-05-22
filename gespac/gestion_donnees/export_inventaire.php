<?PHP

/*	CREATION DU FICHIER D'EXPORT INVENTAIRE	*/

include_once ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
include ('../../version'); //on flag la version de gespac dans l'export

// adresse de connexion à la base de données
$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

// cnx à la base de données OCS
$db_gespac 	= & MDB2::factory($dsn_gespac);

// stockage des lignes retournées par sql dans un tableau (je ne récupère que le matos associé à une marque)
$liste_export = $db_gespac->queryAll ( "
select college.clg_uai, clg_nom, clg_cp, clg_ville, salle_nom, mat_nom, mat_etat, mat_origine, marque_type, marque_stype, marque_marque, marque_model, mat_dsit, mat_serial, salle_vlan, salle_etage, salle_batiment, clg_site_web, clg_site_grr 
from college, salles, materiels, marques

where 
	college.clg_uai = salles.clg_uai AND
	salles.salle_id = materiels.salle_id AND
	marques.marque_id = materiels.marque_id
" );


	
$filename = "inv_" . $liste_export[0][1] . "_" . $liste_export[0][3] . "_" . $liste_export[0][0] . "_gespac_".$version.".csv";
//On formate le nom du fichier ici histoire de pas avoir de caractères zarb'
$filename = strtr($filename, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);


$fp = fopen('../dump/' .$filename, 'w+');

// ENTETES
fputcsv($fp, array('clg_uai', 'clg_nom', 'clg_cp', 'clg_ville', 'salle_nom', 'mat_nom', 'etat', 'origine', 'type', 'stype', 'marque', 'modele', 'inventaire', 'lastcome', 'fidele',  'serial', 'vlan', 'etage', 'batiment'), ',' );

foreach ($liste_export as $record) {
	$clg_uai 	= mb_strtoupper($record[0]);
	$clg_nom  	= strtr(mb_strtoupper($record[1]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$clg_cp 	= mb_strtoupper($record[2]);
	$clg_ville 	= strtr(mb_strtoupper($record[3]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$salle_nom 	= strtr(mb_strtoupper($record[4]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$mat_nom 	= mb_strtoupper($record[5]);
	$etat 		= strtr(mb_strtoupper($record[6]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$origine 	= mb_strtoupper($record[7]);
	$type 		= mb_strtoupper($record[8]);
	$stype 		= mb_strtoupper($record[9]);
	$marque 	= mb_strtoupper($record[10]);
	$modele 	= mb_strtoupper($record[11]);
	$dsit 		= mb_strtoupper($record[12]);
	$serial 	= mb_strtoupper($record[13]);
	$vlan 		= mb_strtoupper($record[14]);
	$etage 		= strtr(mb_strtoupper($record[15]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$batiment 	= strtr(mb_strtoupper($record[16]), 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$web 		= mb_strtoupper($record[17]);
	$grr 		= mb_strtoupper($record[18]);

	
	//Partie fidelité OCS :-(

	// adresse de connexion à la base de données
	$dsn_ocsweb 	= 'mysql://'. $user .':' . $pass . '@localhost/'.$ocsweb;

	// cnx à la base de données OCS
	$db_ocsweb 	= & MDB2::factory($dsn_ocsweb);
	$liste_export_ocs = $db_ocsweb->queryALL ("select LASTCOME, FIDELITY from hardware, bios where bios.HARDWARE_ID=hardware.ID AND bios.SSN = '$serial'");
	if (!$liste_export_ocs) {
		$last='matériel non présent dans OCS'; $fidele='0';
	}//du fait du MAX(LASTCOME) cette ligne ne marche pas...
	else {
		foreach ($liste_export_ocs as $record_ocs) {
			$last = ($record_ocs[0]);
			$fidele =($record_ocs[1]);
		}
		
	}
	$db_ocsweb->disconnect();


    
	fputcsv($fp, array($clg_uai, $clg_nom, $clg_cp, $clg_ville, $salle_nom, $mat_nom, $etat, $origine, $type, $stype, $marque, $modele, $dsit, $last, $fidele, $serial, $vlan, $etage, $batiment), ',');
	//fputcsv( $out, array("" . $mac . "", '"' . $name . '"'), ',');
}

fclose($fp);
//fclose($out);

$db_gespac->disconnect();



echo "<center><h1><a href='./dump/$filename'>Fichier CSV inventaire</a></h1></center>";

?>
