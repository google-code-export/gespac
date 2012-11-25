<?PHP
	session_start();
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>
	
<?php

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	

	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-08-02#", $_SESSION['droits']);
	
	
	if ( !$handle = fopen("../../dump/flux.txt", "r") ) {
		echo "Le fichier flux.txt ne peut pas être ouvert (peut être qu'il n'existe pas ?)<br><br>";
		$fichier_existe = false;
	}
	else {
		$row = 0;
		
		// après suppression, le onchange ne marche plus !
		
		echo "<select id='select_flux' onchange=\"$('flux').load('modules/rss/rss_flux.php?page=' + this.value);  \">";
		
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				
				$line[$row][0] = $data[0];	
				$line[$row][1] = $data[1];		
				
				echo "<option value=$row>" . $line[$row][0] . "</option>";

				$row++;
			}
		
		echo "</select>";
	
		fclose ($handle);
		
?>
		
		<script>					
			// on charge le premier flux
			$('flux').load('modules/rss/rss_flux.php?page=0');
		</script>
	
	<?PHP
		}
		
		if ( $E_chk ) {
			echo "<a href='modules/rss/form_rss.php?height=190&width=640&action=ajout' rel='slb_rss' title='Ajouter un flux'> &nbsp <img src='img/add.png'>Ajouter un flux </a>";

			// si le fichier flux n'existe pas, on ne permet pas la suppression (et la suppression de quoi d'abord ?)
			if ( $row > 0 )
				echo "<a href='#' onclick='supprimer_flux( $(\"select_flux\").value);'> &nbsp <img src='img/delete.png'>Supprimer ce flux </a>";
		}
	?>	

<br><br>

<div id='flux'></div>


<script type="text/javascript"> 

	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_rss'});
	}); 


	// Permet de supprimer un flux
	function supprimer_flux (ligne) {
		
		var valida = confirm('Voulez-vous vraiment supprimer ce flux ?');
		
		if ( valida ) {
			$('target').load('modules/rss/post_rss.php?action=suppr&id=' + ligne);
			$('conteneur').load('modules/rss/rss.php');
		}
	};
	
</script>
