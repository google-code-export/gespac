<?PHP
	session_start();
?>


<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!--	CODAGE	-->
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 

	
<?php

	// lib
	require_once ('../../fonctions.php');
	require_once ('../../config/pear.php');
	include_once ('../../config/databases.php');
	
	$E_chk = preg_match ("#E-08-02#", $_SESSION['droits']);
	

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	if ( !$handle = fopen("../../dump/flux.txt", "r") ) {
		echo "Le fichier flux.txt ne peut pas �tre ouvert (peut �tre qu'il n'existe pas ?)<br><br>";
		$fichier_existe = false;
	}
	else {
		$row = 0;
		
		// apr�s suppression, le onchange ne marche plus !
		
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
