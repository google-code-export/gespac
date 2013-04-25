<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="js/jqplot.pieRenderer.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/jquery.jqplot.css" />

<style>
	.piechart{width:800px; height:800px;}
	.bt{cursor:pointer;display:inline;}

	#contenu{width:900px;height:900px;}
	
</style>


<?PHP

	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);

?>


<div class="entetes" id="entete-statbat">	
	<span class="entetes-titre">LES STATISTIQUES CAMEMBERTS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet l'affichage les statistiques de répartition par marque, par salle et par état de fonctionnement du parc. </div>
</div>

<div class="spacer"></div>


<div id="bt_etats" class="bt">[ETATS]</div>
<div id="bt_salles" class="bt">[SALLES]</div>
<div id="bt_modeles" class="bt">[MODELES]</div>

<br>

<?PHP

	/*************************************
			REPARTITION PAR ETAT !
	*************************************/

	$etats = $con_gespac->QueryAll ( "SELECT mat_etat, count( mat_etat ) as compte FROM materiels GROUP BY mat_etat" );

	foreach ( $etats as $record) {
		
		$key = $record["mat_etat"];
		$value = $record["compte"];
		
		$data_etats .= "['$key', $value],";

	}
	// On vire la dernière virgule
	$data_etats = preg_replace("[,$]", "", $data_etats);
	
		
	/*************************************
			REPARTITION PAR SALLES !
	*************************************/
	
	$salles = $con_gespac->QueryAll ( "SELECT salle_nom, count( mat_nom ) as compte FROM materiels, salles WHERE materiels.salle_id = salles.salle_id GROUP BY salle_nom" );

	foreach ( $salles as $record) {
		
		$key = $record["salle_nom"];
		$value = $record["compte"];
		
		$data_salles .= "['$key', $value],";

	}
	// On vire la dernière virgule
	$data_salles = preg_replace("[,$]", "", $data_salles);

	
	/*************************************
			REPARTITION PAR MODELES !
	*************************************/
	
	$modeles = $con_gespac->QueryAll ( "select CONCAT(marque_type, ' ', marque_marque, ' ', marque_model) as mat, COUNT(mat_nom) as compte FROM marques, materiels WHERE materiels.marque_id = marques.marque_id GROUP BY mat" );

	foreach ( $modeles as $record) {
		
		$key = $record["mat"];
		$value = $record["compte"];
		
		$data_modeles .= "['$key', $value],";

	}
	// On vire la dernière virgule
	$data_modeles = preg_replace("[,$]", "", $data_modeles);

	
	
?>

<div id="pie-etats" class="piechart"></div>
<div id="pie-salles" class="piechart"></div>
<div id="pie-modeles" class="piechart"></div>




<script>
	$(document).ready(function(){

		var data_modeles = [<?PHP echo $data_modeles;?> ];
		var plotmodeles = jQuery.jqplot ('pie-modeles', [data_modeles], { 
			title: 'répartition par MODELES',
			seriesDefaults: {
				renderer: jQuery.jqplot.PieRenderer, 
				rendererOptions: {
					showDataLabels: true
					}
				}, 
			legend: { show:true, location: 'ne', placement:"insideGrid" },
			highlighter: {
				show: true,
				formatString:'%s<br>%d matériel(s)', 
				tooltipLocation:'se', 
				useAxesFormatters:false,
				}	
			}
		);
	
	
		var data_etats = [<?PHP echo $data_etats;?> ];
		var plotetats = jQuery.jqplot ('pie-etats', [data_etats], { 
			title: 'répartition par ETATS',
			seriesDefaults: {
				renderer: jQuery.jqplot.PieRenderer, 
				rendererOptions: {
					showDataLabels: true
					}
				}, 
			legend: { show:true, location: 'ne', placement:"insideGrid" },
			highlighter: {
				show: true,
				formatString:'%s<br>%d matériel(s)', 
				tooltipLocation:'se', 
				useAxesFormatters:false,
				}	
			}
		);

		  
		var data_salles = [<?PHP echo $data_salles;?> ];
		var plotsalles = jQuery.jqplot ('pie-salles', [data_salles], 
		{ 
			title: 'répartition par SALLES',
			seriesDefaults: {
				renderer: jQuery.jqplot.PieRenderer, 
				rendererOptions: {showDataLabels: true}
			}, 
				legend: { show:true, location: 'ne', placement:"insideGrid" },
				highlighter: {
					show: true,
					formatString:'%s<br>%d matériel(s)', 
					tooltipLocation:'se', 
					useAxesFormatters:false,
				}
			}
		);
		
		
		
		

		$(".piechart").hide();
		$("#pie-etats").show();
	
		$('#bt_etats').click(function(){
			$(".piechart").hide();
			$("#pie-etats").toggle();
		});
		
		$('#bt_salles').click(function(){
			$(".piechart").hide();
			$("#pie-salles").toggle();
		});
		
		$('#bt_modeles').click(function(){
			$(".piechart").hide();
			$("#pie-modeles").toggle();
		});

	
	});

</script>