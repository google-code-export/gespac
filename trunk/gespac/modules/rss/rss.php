<?PHP
	session_start();


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-08-02#", $_SESSION['droits']);
?>

<script type="text/javascript"> 

	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_rss'});
	}); 
</script>


<div class="entetes" id="entete-statparc">	
	<span class="entetes-titre">FLUX RSS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de s'abonner et de lire des flux RSS/ATOM.</div>

	<span class="entetes-options">
		<?PHP
		
		if ( $handle = @fopen("dump/flux.txt", "r") ) {
			
			$row = 0;
			$current_flux = $_GET['flux'];
			
			echo "<span class=option><select id='select_flux' onchange=\"document.location.href='index.php?page=rss&flux=' + this.value;  \">";
			
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					
					$line[$row][0] = $data[0];	
					$line[$row][1] = $data[1];		
					
					$selected = $row==$current_flux ? " selected" : "" ;
										
					echo "<option value=$row $selected>" . $line[$row][0] . "</option>";

					$row++;
				}
			
			echo "</select></span>";
		
			fclose ($handle);
		}
				
		if ( $E_chk ) {
			// si le fichier flux n'existe pas, on ne permet pas la suppression (et la suppression de quoi d'abord ?)
			if ( $row > 0 )	echo "<span class='option'><a href='#' onclick='supprimer_flux( $(\"select_flux\").value);' title='Supprimer le flux'><img src='" . ICONSPATH . "minus.png'></a></span>";
			
			echo "<span class='option'><a href='modules/rss/form_rss.php?height=210&width=640&action=ajout' rel='slb_rss' title='Ajouter un flux'><img src='" . ICONSPATH . "add.png'></a></span>";
		}
		
		?>

	</span>

</div>

<div class="spacer"></div>


<?PHP	
	// On charge la page des flux
	include ('rss_flux.php');
?>	

<script type="text/javascript"> 

	// Permet de supprimer un flux
	function supprimer_flux (ligne) {
		
		var valida = confirm('Voulez-vous vraiment supprimer ce flux ?');
		
		if ( valida ) {
			$('targetback').setStyle("display","block"); $('target').setStyle("display","block");
			$('target').load("modules/rss/post_rss.php?action=suppr&id=" + ligne);
			window.setTimeout("document.location.href='index.php?page=rss'", 1500);		
		}
	};
	
</script>
