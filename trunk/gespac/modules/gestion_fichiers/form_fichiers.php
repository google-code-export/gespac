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
		

	</table>
	 
	<br>
	
	droits <input type=hidden id="droits" name="droits" value="00">
	 
	<table width=50px>
		<td>&nbsp;</td>
		<td>lecture</td>
		<td>écriture</td>
		<tr>
			<td>grade</td>
			<td><input type=checkbox id=grade_lecture></td>
			<td><input type=checkbox id=grade_ecriture></td>
		</tr>
		<tr>
			<td>tous</td>
			<td><input type=checkbox id=tous_lecture></td>
			<td><input type=checkbox id=tous_ecriture></td>
		</tr>
	</table>
	 
	<br>
	
	<br>
	<input type="submit" name="envoyer" value="Envoyer le fichier" onclick="refresh_quit();">
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
	
	function CalcDroits () {
		var chiffre1 = "0";
		var chiffre2 = "0";
		
		if ( $("grade_lecture").checked == true ) chiffre1="1";
		if ( $("grade_ecriture").checked == true ) chiffre1="2";
		
		if ( $("tous_lecture").checked == true ) chiffre2="1";
		if ( $("tous_ecriture").checked == true ) chiffre2="2";
		
		$("droits").value = chiffre1+chiffre2;
	}
	
	window.addEvent('domready', function(){
		
		$('grade_ecriture').addEvent ('click', function(e) {
			
			if ( $('grade_ecriture').checked == true ) {
				$('grade_lecture').checked=true;
			}
			
			CalcDroits();
		});

		$('grade_lecture').addEvent ('click', function(e) {
			
			if ( $('grade_lecture').checked == false ) {
				$('grade_ecriture').checked=false;
			}
			
			CalcDroits();
		});

		$('tous_lecture').addEvent ('click', function(e) {
			
			if ( $('tous_lecture').checked == false ) {
				$('tous_ecriture').checked=false;
				$('grade_lecture').checked=false;
				$('grade_ecriture').checked=false;
			}
			else {
				$('grade_lecture').checked=true;
			}
			
			CalcDroits();
		});		
		
		$('tous_ecriture').addEvent ('click', function(e) {
			
			if ( $('tous_ecriture').checked == true ) {
				$('tous_lecture').checked=true;
				$('grade_lecture').checked=true;
				$('grade_ecriture').checked=true;
			}
			else {
				$('grade_ecriture').checked=false;
			}
			
			CalcDroits();
			
		});
		
		
	});
	
	
</script>

