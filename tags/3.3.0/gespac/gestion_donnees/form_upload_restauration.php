<?PHP

	/*
		formulaire de restauration de base de données
		permet de restaurer une base qui a été dumpée auparavant,
	*/
	
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

?>

<script type="text/javascript"> 
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un délais de 1000ms
		window.setTimeout("$('conteneur').load('./');", 1000);
		TB_remove();
	}
</script>

<h3>Restauration de la base GESPAC</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<form action="post_restauration_DB.php" method="POST" target=_blank enctype="multipart/form-data">
	 <!-- On limite le fichier à 10000Ko -->
     <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier SQL : </td>
			<td><input type="file" name="myfile" ></td>
		</tr>
		<tr>
			<td colspan=2><br><input type="submit" name="envoyer" value="Restaurer la base GESPAC" onclick="refresh_quit();"></td>
		</tr>
	 </table>
	 </center>
      
</form>
