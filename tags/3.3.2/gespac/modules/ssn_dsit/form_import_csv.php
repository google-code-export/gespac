<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	echo "<h2>formulaire de mise à jour des tags DSIT</h2><br>";

?>
		
<script type="text/javascript"> 
	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un délais de 1500ms
		window.setTimeout("$('conteneur').load('gestion_inventaire/voir_materiels.php');", 1500);
		SexyLightbox.close();
	};
	
	
</script>	
		
<form method="POST" action="modules/ssn_dsit/post_import_csv.php" target=_blank enctype="multipart/form-data">
	<center>
	
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier CSV</td>
			<td><input type="file" name="myfile"></td>
		</tr>
	 </table>
	
	 </center>

	<br>
	<br>
	<center>
	<input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();">

	</center>

</FORM>

<br>
<br>
<center>
	<a href='#' onclick='alert("Formalisme pour le fichier CSV d`import : \n \"no_serie1\";\"no_dsit1\" \n \"no_serie2\";\"no_dsit\" \n \"no_serie3\";\"no_dsit3\" \n ... ");'>AIDE</a>
</center>
	
