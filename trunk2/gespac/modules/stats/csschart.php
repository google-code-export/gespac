<?PHP

	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	// nb de matériels
	$nb_mat = $con_gespac->QueryOne ( "SELECT count(mat_nom) FROM materiels" );

?>


<div class="entetes" id="entete-statbat">	
	<span class="entetes-titre">LES STATISTIQUES BATONS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet l'affichage les statistiques de répartition par marque, par salle et par état de fonctionnement du parc. </div>
</div>


<!--

	REPARTITION PAR MARQUE !

-->


  <div class="section">
    <h2>Répartition par marque</h2>

    <ul class="chartlist">

	<?PHP
		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste = $con_gespac->QueryAll ("SELECT CONCAT(marque_marque, ' ', marque_model) as mat, COUNT(mat_nom) as compte FROM marques, materiels WHERE materiels.marque_id = marques.marque_id GROUP BY mat");
						
		foreach ($liste as $record) {
		
			$marque	= $record['mat'];
			$val	= $record['compte'];
			$pc 	= ceil(($val / $nb_mat) * 90);
		

		
			$marque = $marque == " " ? "NC" : $record['mat'];
				
			echo "<li>";
				// label
				echo "<a>$marque</a>";
				// nb d'éléments
				echo "<span class='count'>$val</span>";
				// row coloriée
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
    <h2>Répartition par salle</h2>

    <ul class="chartlist">

	<?PHP
		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste = $con_gespac->QueryAll ( "SELECT salle_nom, count( mat_nom ) as compte FROM materiels, salles WHERE materiels.salle_id = salles.salle_id GROUP BY salle_nom" );
		
		foreach ($liste as $record) {
		
			$salle	= $record['salle_nom'];
			$val	= $record['compte'];
			$pc 	= ceil(($val / $nb_mat) * 90);
			
			
			echo "<li>";
				// label
				echo "<a>$salle</a>";
				// nb d'éléments
				echo "<span class='count'>$val</span>";
				// row coloriée
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
		// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
		$liste = $con_gespac->QueryAll ( "SELECT mat_etat, count( mat_etat ) as compte FROM materiels GROUP BY mat_etat" );

		foreach ($liste as $record) {
		
			$etat	= $record['mat_etat'];
			$val	= $record['compte'];
			$pc 	= ceil(($val / $nb_mat) * 90);
			
			
			echo "<li>";
				// label
				echo "<a>$etat</a>";
				// nb d'éléments
				echo "<span class='count'>$val</span>";
				// row coloriée
				echo "<span class='index' style='width: $pc%'>($pc %)</span>";
			echo "</li>";
		}
	?>

    </ul>
  </div>
  
  

