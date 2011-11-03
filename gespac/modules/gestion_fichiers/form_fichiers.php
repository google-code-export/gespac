<h3>Ajouter un fichier</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<form method="POST" action="modules/gestion_fichiers/post_fichiers.php" target=_blank enctype="multipart/form-data">
<!--<form method="POST" action="gestion_utilisateurs/post_comptes_iaca.php" enctype="multipart/form-data">-->
	 <!-- On limite le fichier à 10000Ko -->
     <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		
		<tr>
			<td>Fichier</td>
			<td><input type="file" name="myfile"></td>
		</tr>
		
		<tr>
			<td>Description</td>
			<td><textarea name="description"></textarea> </td>
		</tr>
		
		<tr>
			<td colspan=2><br><input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();"></td>
		</tr>
	 </table>
	 
	 <br>
	 
	 <small>fichier limité à 10Mio.</small>
	 
	 </center>
      
</form>


<script type="text/javascript"> 
	function refresh_quit () {
		// lance la fonction avec un délais de 1500ms
		window.setTimeout("$('conteneur').load('modules/gestion_fichiers/voir_fichiers.php');", 1500);
		SexyLightbox.close();
	}
</script>

