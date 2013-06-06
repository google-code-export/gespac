<div class="entetes" id="entete-importusers">	

	<span class="entetes-titre">IMPORT IACA<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Permet d'importer des comptes avec un fichier CSV. </div>

</div>

<div class="spacer"></div>

<form enctype="multipart/form-data">
     <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier IACA</td>
			<td><input type="file" name="myfile">  </td>
		</tr>
		<tr>
			<td colspan=2><input type="button" name="envoyer" value="Envoyer le fichier"></td>
		</tr>
	 </table>
	 </center>
      
</form>

<br>

<center>Aide <a href="javascript:$('#aide1').toggle('fast');"><b>Infra non migrée</b></a> - <a href="javascript:$('#aide2').toggle('fast');"><b>Infra migrée</b></a></center>



<div id="aide1" style='display:none;'>
	<small>
		<br><b>Infra NON migrée :</b><br>
		Ouvrir IACA et selectionnez le groupe des profs (UAI_P)<br>
		 - OUTILS -> Exporter les comptes <br>
		 - Decocher `premiere ligne avec noms des champs`  <br>
		 - Separateur `virgule`  <br>
		 - Entourer les champs guillemets  <br>
		 - Cocher `NOM COMPLET`  <br>
		 - Cocher `NOM OUVERTURE DE SESSION EN MAJUSCULES`  <br>
		 - Cocher `MOT DE PASSE`  <br>
		 - Il faut les champs dans cet ordre (utilisez le bouton MONTER)  <br>
		 - Faire OK, enregistrer en CSV  <br>
		 - N`importez que le groupe des profs
	</small>
</div>


<div id="aide2" style='display:none;'>
	<small>
		<br><b>Infra migrée :</b><br>
		Ouvrir IACA et selectionnez le groupe des profs (UAI_P)<br>
		 - OUTILS -> Exporter les comptes <br>
		 - Decocher `premiere ligne avec noms des champs`  <br>
		 - Separateur `virgule`  <br>
		 - Entourer les champs guillemets  <br>
		 - Cocher `NOM COMPLET`  <br>
		 - Cocher `NOM OUVERTURE DE SESSION EN MAJUSCULES`  <br>
		 - Cocher `MOT DE PASSE`  <br>
		 - Il faut les champs dans cet ordre (utilisez le bouton MONTER)  <br>
		 - Faire OK, enregistrer en CSV  <br>
		 - N`importez que le groupe des profs
	</small>
</div>


<script>
	
//---------------------------------------- Post file par Ajax (fonction par olanod : http://stackoverflow.com/users/931340/olanod)
$(':button').click(function(){
    var formData = new FormData($('form')[0]);
    $.ajax({
        url: 'gestion_utilisateurs/post_comptes_iaca.php',  //server script to process data
        type: 'POST',
        xhr: function() {  // custom xhr
            var myXhr = $.ajaxSettings.xhr();
            return myXhr;
        },
		// Data du formulaire
        data: formData,
        //Options to tell JQuery not to process data or worry about content-type
        cache: false,
        contentType: false,
        processData: false,
        complete : function(res) {
			$('#targetback').show(); $('#target').show();
			$('#target').html(res.responseText);
			window.setTimeout("document.location.href='index.php?page=utilisateurs'", 2500);
		}
    });
});

</script>
