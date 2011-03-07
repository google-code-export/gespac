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
  
	//header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
	
	require("camembert.php"); 		// on charge la classe camembert
		
	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	
	
	/********************************
	*
	*			ETAT DU PARC
	*
	*********************************/
	
	
	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_etats = $db_gespac->queryAll ( "SELECT mat_etat, count( mat_etat ) FROM materiels GROUP BY mat_etat" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_etats as $record) {
	
		$etat	= $record[0];
		$val	= $record[1];
	
		$camembert->add_tab( $val, $etat );
	}
	
	
	// Facultatif, les donnees sont triees dans l'ordre decroissant
	$camembert->trier_tab(); 
	// $camembert->affiche_tab(); // Debug

	// 1er argument (2 ou 3 pour la 2D ou la 3D) - 2eme argument hauteur en pixel de l'effet 3D (mettre quelque chose meme pour la 2D)
	$camembert->stat2png(3, 15, "Etat du matériel du parc", "etat"); 

	
	/********************************
	*
	*		Répartition par salle
	*
	*********************************/
	
	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_salles = $db_gespac->queryAll ( "SELECT salle_nom, count( mat_nom ) FROM materiels, salles WHERE materiels.salle_id = salles.salle_id GROUP BY salle_nom" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_salles as $record) {
	
		$salle	= $record[0];
		$val	= $record[1];
	
		$camembert->add_tab( $val, $salle );
	}
	
	$camembert->trier_tab(); // tri
	$camembert->stat2png(3, 15, "Répartition du parc par salle", "salles"); // 3 pour 3d, 15 pour l'épaisseur du clacos, titre, nom du fichier

	
	/********************************
	*
	*		Répartition par modele
	*
	*********************************/
	
	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_salles = $db_gespac->queryAll ( "select CONCAT(marque_type, ' ',marque_stype, ' ', marque_marque, ' ', marque_model) as mat, COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id = marques.marque_id GROUP BY mat" );

	// instantiation
	$camembert = new camembert(); 
	
	foreach ($liste_des_salles as $record) {
	
		$marque	= $record[0];
		$val	= $record[1];
	
		$camembert->add_tab( $val, $marque );
	}
	
	$camembert->trier_tab(); // tri
	$camembert->stat2png(3, 15, "Répartition du parc par modele", "modeles"); // 3 pour 3d, 15 pour l'épaisseur du clacos, titre, nom du fichier

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
