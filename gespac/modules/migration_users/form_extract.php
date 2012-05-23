<script type="text/javascript"> 
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un délais de 1000ms
		SexyLightbox.close();
		window.setTimeout("$('conteneur').load('modules/migration_users/form_migration_users.php');", 1500);
	}
</script>

<h3>Migration des comptes utilisateurs sur l'architecture AD 2008</h3>
<small><i>Les logins et mots de passe iaca sont mis à jour dans l'architecture AD2008. Ce module permet de mettre à jour les comptes dans Gespac.<br>Il faut transformer le fichier d'extraction en csv, séparé par des points-virgules.</i></small>
	
<br>
<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>


<form method="POST" action="modules/migration_users/post_extract.php" target=_blank enctype="multipart/form-data">

     <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier IACA</td>
			<td><input type="file" name="myfile"></td>
		</tr>
		<tr>
			<td colspan=2><br><input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();"></td>
		</tr>
	 </table>
	 </center>
      
</form>

