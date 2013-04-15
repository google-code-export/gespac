<script type="text/javascript"> 
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// On route le site vers la page d'affectation des comptes
		window.setTimeout("document.location.href='index.php?page=migusers2'", 1500);
	}
</script>


<div class="entetes" id="entete-migusers">	
	<span class="entetes-titre">MIGRATION DES UTILISATEURS<img class="help-button" src="img/icons/info.png"></span>
	<div class="helpbox">Script permettant de migrer les anciens comptes utilisateurs sur l'architecture AD2008.<br>Il faut un fichier CSV comportant le nom, le prénom, le login et le mot de passe, séparés par des points-virgules et encadrés de double-quotes.<br>Par exemple "LECHAT";"RAOUL";"rlechat";"4511932"</div>
</div>

<div class="spacer"></div>


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

