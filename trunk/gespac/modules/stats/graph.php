<script type="text/javascript">	
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
</script>
	
	
<!--	DIV target pour Ajax	-->
<div id="target"></div>
	

<?PHP
  
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache"); 
  
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	require("camembert.php"); 		// on charge la classe camembert
		
	// cnx � gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	
	
	/********************************
	*
	*			ETAT DU PARC
	*
	*********************************/
	
	
	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
	$liste_des_etats = $con_gespac->QueryAll ( "SELECT mat_etat, count( mat_etat ) as compte FROM materiels GROUP BY mat_etat" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_etats as $record) {
	
		$etat	= $record['mat_etat'];
		$val	= $record['compte'];
	
		$camembert->add_tab( $val, $etat );
	}
	
	
	// Facultatif, les donnees sont triees dans l'ordre decroissant
	$camembert->trier_tab(); 
	// $camembert->affiche_tab(); // Debug

	// 1er argument (2 ou 3 pour la 2D ou la 3D) - 2eme argument hauteur en pixel de l'effet 3D (mettre quelque chose meme pour la 2D)
	$camembert->stat2png(3, 15, "Etat du mat�riel du parc", "etat"); 

	
	/********************************
	*
	*		R�partition par salle
	*
	*********************************/
	
	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
	$liste_des_salles = $con_gespac->QueryAll ( "SELECT salle_nom, count( mat_nom ) as compte FROM materiels, salles WHERE materiels.salle_id = salles.salle_id GROUP BY salle_nom" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_salles as $record) {
	
		$salle	= $record['salle_nom'];
		$val	= $record['compte'];
	
		$camembert->add_tab( $val, $salle );
	}
	
	$camembert->trier_tab(); // tri
	$camembert->stat2png(3, 15, "R�partition du parc par salle", "salles"); // 3 pour 3d, 15 pour l'�paisseur du clacos, titre, nom du fichier

	
	/********************************
	*
	*		R�partition par modele
	*
	*********************************/
	
	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
	$liste_des_salles = $con_gespac->QueryAll ( "select CONCAT(marque_type, ' ',marque_stype, ' ', marque_marque, ' ', marque_model) as mat, COUNT(mat_nom) as compte FROM marques, materiels WHERE materiels.marque_id = marques.marque_id GROUP BY mat" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_salles as $record) {
	
		$marque	= $record['mat'];
		$val	= $record['compte'];
	
		$camembert->add_tab( $val, $marque );
	}
	
	$camembert->trier_tab(); // tri
	$camembert->stat2png(3, 15, "R�partition du parc par modele", "modeles"); // 3 pour 3d, 15 pour l'�paisseur du clacos, titre, nom du fichier

?>

		<br>
		<center>
			<img src='./dump/etat.png' style="border-color:#FF0000">
		</center>
		
		<br>
		<center>
			<img src='./dump/salles.png' style="border-color:#FF0000">
		</center>
		
		<br>
		<center>
			<img src='./dump/modeles.png' style="border-color:#FF0000">
		</center>
