<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	
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
				
		$("conteneur").load("modules/stats/utilisation_parc.php?pc=" + pc_value + "&datefin=" + datefin_value + "&datedebut=" + datedebut_value);
	}
	
	
</script>
	

<?PHP
  
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
		
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

<h3>Utilisation du parc info (cumul des logins)</h3><br>

<center>
	<table width=400 align=center>
		<tr>
			<td>PC</td>
			<td><input type=text id=pc></td>
		</tr>
		<tr>
			<td>Date (aaaa-mm-jj)</td>
			<td>Déb <input type=text id=datedebut SIZE=10 MAXLENGTH=10>
			<br>Fin <input type=text id=datefin SIZE=10 MAXLENGTH=10></td>
		</tr>
		<tr>
			<td colspan=2 align=center><br><input type=button value=Filtrer onclick="filtrer_stat(pc, datedebut, datefin);" ></td>	
		</tr>
	
	</table>
</center>

<!--

	REPARTITION PAR MARQUE !

-->


  <div class="section">
	<?PHP echo "<h3>Filtre : $pc de $datedebut à $datefin</h3><br>"; ?>
	
	Nombre d`identifications par machines sur la période filtrée.<br> 
	
		
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
  
  
  
