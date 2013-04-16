<div class="entetes" id="entete-taginventaire">	

	<span class="entetes-titre">MISE A JOUR des NUMEROS INVENTAIRE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Permet de mettre à jour les numéros d'inventaire de la base en fonction du numéro de série du matériel.</div>

</div>

<div class="spacer"></div>
		
<form method="POST" action="modules/ssn_dsit/post_import_csv.php" target=_blank enctype="multipart/form-data">
	<center>
	
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier CSV</td>
			<td><input type="file" name="myfile"></td>
		</tr>
	 </table>
	
	<br>
	
	<input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();">

	</center>

</FORM>

<br>
<br>
<center>
	
	<b>Formalisme du fichier à constituer :</b><br>
	"no_serie1";"no_dsit1"<br>
	"no_serie2";"no_dsit2"<br>
	"no_serie3";"no_dsit3"<br>
	"no_serie4";"no_dsit4"<br>
	...
	
	
</center>
	
