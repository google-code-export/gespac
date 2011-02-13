<!--	DIV target pour Ajax	-->
<div id="target"></div>

		<!--	CODAGE	-->
		<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
		<!--	CSS	-->
		<link rel="stylesheet" href="css/smoothbox.css" type="text/css" media="screen" />
		<!--	JS	-->
		<script type="text/javascript" src="js/smoothbox.js"></script> 
		<!-- 	AJAX	-->
		<script type="text/javascript" src="server.php?client=all"></script>



	<?php
	
		// lib
		
		
		
		require_once ('../../fonctions.php');
		require_once ('../../config/pear.php');
		include_once ('../../config/databases.php');
	
		header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
		
		if ( !$handle = fopen("../../dump/flux.txt", "r") ) {
			echo "Le fichier flux.txt ne peut pas être ouvert (peut être qu'il n'existe pas ?)<br><br>";
			$fichier_existe = false;
		}
		else {
			$row = 0;
			
			// après suppression, le onchange ne marche plus !
			
			echo "<select id='select_flux' onchange=\"HTML_AJAX.replace('flux', 'modules/rss/rss_flux.php?page=' + this.value);  \">";
			
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
				HTML_AJAX.replace('flux', 'modules/rss/rss_flux.php?page=0');
			</script>
		
		<?PHP
		}
		
		
		echo "<a href='modules/rss/form_rss.php?height=190&width=640&action=ajout' class='smoothbox' title='Ajouter un flux'> &nbsp <img src='img/add.png'>Ajouter un flux </a>";

		// si le fichier flux n'existe pas, on ne permet pas la suppression (et la suppression de quoi d'abord ?)
		if ( $row > 0 )
			echo "<a href='#' onclick='supprimer_flux( $(\"select_flux\").value);'> &nbsp <img src='img/delete.png'>Supprimer ce flux </a>";
			
	?>	
	
	<br><br>
	
	<div id='flux'></div>
	
	
	<script>

		// Permet de supprimer un flux
		function supprimer_flux (ligne) {
			
			var valida = confirm('Voulez-vous vraiment supprimer ce flux ?');
			
			if ( valida ) {
				HTML_AJAX.replace('target', 'modules/rss/post_rss.php?action=suppr&id=' + ligne);
				HTML_AJAX.replace('conteneur', 'modules/rss/rss.php');
			}
		}
		
		
			
	</script>
