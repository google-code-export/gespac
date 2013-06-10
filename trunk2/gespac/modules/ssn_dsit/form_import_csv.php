<div class="entetes" id="entete-taginventaire">	

	<span class="entetes-titre">MISE A JOUR des NUMEROS INVENTAIRE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Permet de mettre à jour les numéros d'inventaire de la base en fonction du numéro de série du matériel.</div>

</div>

<div class="spacer"></div>



<form enctype="multipart/form-data">
     <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	 <center>
	 <table width=400 align=center cellpadding=10px>
		<tr>
			<td>Fichier CSV</td>
			<td><input type="file" name="myfile">  </td>
		</tr>
		<tr>
			<td colspan=2><input type="button" name="envoyer" value="Envoyer le fichier"></td>
		</tr>
	 </table>
	 </center>
      
</form>

<br>

<center>
	
	<b>Formalisme du fichier à constituer :</b><br>
	"no_serie1";"no_dsit1"<br>
	"no_serie2";"no_dsit2"<br>
	"no_serie3";"no_dsit3"<br>
	"no_serie4";"no_dsit4"<br>
	...
	
	
</center>


<script>
	
//---------------------------------------- Post file par Ajax (fonction par olanod : http://stackoverflow.com/users/931340/olanod)
$(':button').click(function(){
    var formData = new FormData($('form')[0]);
    $.ajax({
        url: 'modules/ssn_dsit/post_import_csv.php',  //server script to process data
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
			window.setTimeout("document.location.href='index.php?page=taginventaire'", 2500);
		}
    });
});

</script>
