<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script type="text/javascript">	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
</script>
	

<?PHP
  
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
		
	// adresse de connexion � la base de donn�es
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// nb de mat�riels
	$rq_nb_mat = $db_gespac->queryAll ( "SELECT count( mat_nom ) FROM materiels" );
	$nb_mat = $rq_nb_mat[0][0];
?>

<!--

	REPARTITION PAR MARQUE !

-->


  <div class="section">
    <h2>R�partition par marque</h2>

    <ul class="chartlist">

	<?PHP
		// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
		$liste = $db_gespac->queryAll ( "select CONCAT(marque_marque, ' ', marque_model) as mat, COUNT(mat_nom) FROM marques, materiels WHERE materiels.marque_id = marques.marque_id GROUP BY mat" );
						
		foreach ($liste as $record) {
		
			$marque	= $record[0];
			$val	= $record[1];
			$pc 	= ceil(($val / $nb_mat) * 90);
		
			$marque = $marque == " " ? "NC" : $record[0];
				
			echo "<li>";
				// label
				echo "<a>$marque</a>";
				// nb d'�l�ments
				echo "<span class='count'>$val</span>";
				// row colori�e
				echo "<span class='index' style='width: $pc%'>($pc %)</span>";
			echo "</li>";
		}
	?>

    </ul>
  </div>
  
  
  
  
<!--

	REPARTITION PAR SALLE !

-->


  <div class="section">
    <h2>R�partition par salle</h2>

    <ul class="chartlist">

	<?PHP
		// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
		$liste = $db_gespac->queryAll ( "SELECT salle_nom, count( mat_nom ) FROM materiels, salles WHERE materiels.salle_id = salles.salle_id GROUP BY salle_nom" );
		
		foreach ($liste as $record) {
		
			$salle	= $record[0];
			$val	= $record[1];
			$pc 	= ceil(($val / $nb_mat) * 90);
			
			
			echo "<li>";
				// label
				echo "<a>$salle</a>";
				// nb d'�l�ments
				echo "<span class='count'>$val</span>";
				// row colori�e
				echo "<span class='index' style='width: $pc%'>($pc %)</span>";
			echo "</li>";
		}
	?>

    </ul>
  </div>
  
  
  
   
<!--

	ETAT du parc !

-->


  <div class="section">
    <h2>Etat du parc</h2>

    <ul class="chartlist">

	<?PHP
		// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
		$liste = $db_gespac->queryAll ( "SELECT mat_etat, count( mat_etat ) FROM materiels GROUP BY mat_etat" );

		foreach ($liste as $record) {
		
			$etat	= $record[0];
			$val	= $record[1];
			$pc 	= ceil(($val / $nb_mat) * 90);
			
			
			echo "<li>";
				// label
				echo "<a>$etat</a>";
				// nb d'�l�ments
				echo "<span class='count'>$val</span>";
				// row colori�e
				echo "<span class='index' style='width: $pc%'>($pc %)</span>";
			echo "</li>";
		}
	?>

    </ul>
  </div>
  
  

