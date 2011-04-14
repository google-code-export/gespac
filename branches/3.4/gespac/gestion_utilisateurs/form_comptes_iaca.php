<script type="text/javascript"> 
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un délais de 1000ms
		window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_utilisateurs.php');", 1000);
		TB_remove();
	}
</script>

<h3>Importation des comptes IACA</h3>

<br>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>


<form method="POST" action="gestion_utilisateurs/post_comptes_iaca.php" target=_blank enctype="multipart/form-data">
<!--<form method="POST" action="gestion_utilisateurs/post_comptes_iaca.php" enctype="multipart/form-data">-->
	 <!-- On limite le fichier à 10000Ko -->
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

<center>
	<a href='#' onclick='alert("Importation des comptes IACA profs : \n - Ouvrir IACA \n - Selectionnez le groupe des profs (UAI_P) \n - OUTILS -> Exporter les comptes \n - Decocher `premiere ligne avec noms des champs` \n - Separateur `virgule` \n - Entourer les champs guillemets \n - Cocher `NOM COMPLET` \n - Cocher `NOM OUVERTURE DE SESSION EN MAJUSCULES` \n - Cocher `MOT DE PASSE` \n - Il faut les champs dans cet ordre (utilisez le bouton MONTER) \n - Faire OK, enregistrer en CSV \n - N`importez que le groupe des profs \n");'>AIDE</a>
</center>






