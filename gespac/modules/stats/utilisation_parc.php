<script type="text/javascript">	

	// filtrer les stats
	function filtrer_stat (pc, datedebut, datefin) {
		var pc_value = $('pc').value
		var datedebut_value = $('datedebut').value
		var datefin_value = $('datefin').value
		/*
		(datedebut_value == '') ? ();
		? ();
		*/
		
		if (datedebut_value == '') 
			datedebut_value='1976-09-16';
		if (datefin_value == '')
			datefin_value='2050-01-01';
				
		document.location.href="index.php?page=statparc&pc=" + pc_value + "&datefin=" + datefin_value + "&datedebut=" + datedebut_value;
	}
	
	
</script>


<div class="entetes" id="entete-statparc">	
	<span class="entetes-titre">UTILISATION DU PARC<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Statistique du nombre d'identifications par machines sur le parc.<br>Les statistiques sont tirées du module userTracking de FOG.</div>

	<span class="entetes-options">

		<span class="option"><input type=text id=pc placeholder="nom machine"></span>
		<span class="option"><input type=text id=datedebut placeholder="début (aaaa-mm-jj)"></span>
		<span class="option"><input type=text id=datefin placeholder="fin (aaaa-mm-jj)"></span>
		<span class="option"><input type=button value=Filtrer onclick="filtrer_stat(pc, datedebut, datefin);" ></span>
		
	</span>

</div>

<div class="spacer"></div>
	

<?PHP

	// cnx à fog
	$con_fog = new Sql($host, $user, $pass, $fog);
	
	$pc = $_GET['pc'];
	$datedebut = $_GET['datedebut'];
	$datefin = $_GET['datefin'];
	
	if ( !isset($pc) && !isset($datedebut) && !isset($datefin) ) {
		$sql = "select hostName, count(*) as compte from userTracking, hosts WHERE utHostID=hostID group by utHostID order by hostName";
	} else {
		$sql = "select hostName, count(*) as compte from userTracking, hosts WHERE utHostID=hostID AND utDate>'$datedebut' AND utDate<'$datefin' AND hostName LIKE '%$pc%' group by utHostID order by hostName";
	}
	
?>



<!--

	REPARTITION PAR MARQUE !

-->


  <div class="section">
	<?PHP
		if ( $_GET['datedebut'] ) echo "<h3>Filtre : $pc de $datedebut à $datefin</h3><br>"; 
	?>
	
		
    <ul class="microchart">

	<?PHP

		$liste_max = $con_fog->QueryAll ( "select count(*) as maxi from userTracking, hosts WHERE utHostID=hostID group by utHostID order by hostName" );
		$maxi = max($liste_max);
		$maxi = $maxi['maxi'];

		$liste = $con_fog->QueryAll ( $sql );
				
		foreach ($liste as $record) {
		
			$mat	= $record['hostName'];
			$val	= $record['compte'];
			$pc 	= ceil(($val / $maxi) * 90);
		
			echo "<li>";
				// label
				echo "<a>$mat</a>";
				// nb d'éléments
				echo "<span class='count'>$val</span>";
				// row coloriée
				echo "<span class='index' style='width: $pc%'>($pc %)</span>";
			echo "</li>";
		}
	?>

    </ul>
  </div>
  
  
  
