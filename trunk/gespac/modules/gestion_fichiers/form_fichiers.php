<!--	DIV target pour Ajax	-->
<div id="target"></div>

<?PHP

$id = $_GET['id'];

if ( $id == -1 ) {

?>
	<h3>Ajouter un fichier</h3>

	<br>
	
	<form method="POST" action="modules/gestion_fichiers/post_fichiers.php?action=creation" target=_blank enctype="multipart/form-data">

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
		
		<b>droits</b> <input type=hidden id="droits" name="droits" value="00">
		 
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

<?PHP
}


// MODIFICATION

if ( $id <> -1 ) {

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$fichier = $con_gespac->QueryRow ('SELECT * FROM fichiers WHERE fichier_id='.$id);
	
	$fic_id 	= $fichier[0];
	$fic_chemin = $fichier[1];
	$fic_desc 	= $fichier[2];
	$fic_droits = $fichier[3];

?>
	<h3>Modifier un fichier</h3>

	<br>

	<script>AffectDroits("<?PHP echo $fic_droits;?>");</script>
	
	<form method="POST" action="modules/gestion_fichiers/post_fichiers.php?action=mod&id=<?PHP echo $fic_id; ?>" name="post_form" id="post_form">

		<center>
		<table width=400 align=center cellpadding=10px>
			
			<tr>
				<td>Fichier</td>
				<td><input type="text" disabled name="myfile" value=<?PHP echo $fic_chemin;?> size=27></td>
			</tr>
			
			<tr>
				<td>Description</td>
				<td><textarea name="description"><?PHP echo $fic_desc;?></textarea> </td>
			</tr>
			

		</table>
		 
		<br>
		
		<b>droits</b> <input type=hidden id="droits" name="droits" value=<?PHP echo $fic_droits;?>>
		 
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
		 
		</center>
		  
	</form>

<?PHP
	}
?>




<script type="text/javascript"> 

	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		
		if ( $('post_form') ) {
			$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
				new Event(e).stop();
				new Request({

					method: this.method,
					url: this.action,

					onSuccess: function(responseText, responseXML, filt) {
						$('target').set('html', responseText);
						$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
						SexyLightbox.close();
					}
				
				}).send(this.toQueryString());
			});			
		}
	
		// Sur click des checkboxes pour les droits
	
		$('grade_ecriture').addEvent ('click', function(e) {
			
			if ( $('grade_ecriture').checked == true ) {
				$('grade_lecture').checked=true;
			}
			else {
				$('tous_ecriture').checked=false;
			}
			
			CalcDroits();
		});

		$('grade_lecture').addEvent ('click', function(e) {
			if ( $('grade_lecture').checked == false ) {
				$('grade_ecriture').checked=false;
				$('tous_ecriture').checked=false;
				$('tous_lecture').checked=false;
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
	
	}); // EOFn on domready



	function refresh_quit () {
		// lance la fonction avec un délais de 1500ms
		window.setTimeout("$('conteneur').load('modules/gestion_fichiers/voir_fichiers.php');", 1500);
		SexyLightbox.close();
	}
	
	// Permet de mettre une valeur numérique aux cases cochées
	function CalcDroits () {
		var chiffre1 = "0";
		var chiffre2 = "0";
		
		if ( $("grade_lecture").checked == true ) chiffre1="1";
		if ( $("grade_ecriture").checked == true ) chiffre1="2";
		
		if ( $("tous_lecture").checked == true ) chiffre2="1";
		if ( $("tous_ecriture").checked == true ) chiffre2="2";
		
		$("droits").value = chiffre1+chiffre2;
	}
	
	// Permet de cocher les cases en fonction d'une valeur numérique
	function AffectDroits (valeur) {

			if ( valeur == "00") {
				$("grade_lecture").checked = false; $("grade_ecriture").checked = false; $("tous_lecture").checked = false;	$("tous_ecriture").checked = false; }
			if ( valeur == "10") {
				$("grade_lecture").checked = true; $("grade_ecriture").checked = false;	$("tous_lecture").checked = false; $("tous_ecriture").checked = false; }	
			if ( valeur == "11") {
				$("grade_lecture").checked = true; $("grade_ecriture").checked = false;	$("tous_lecture").checked = true; $("tous_ecriture").checked = false; }
			if ( valeur == "20") {
				$("grade_lecture").checked = true; $("grade_ecriture").checked = true; $("tous_lecture").checked = false; $("tous_ecriture").checked = false; }	
			if ( valeur == "21") {
				$("grade_lecture").checked = true; $("grade_ecriture").checked = true; $("tous_lecture").checked = true; $("tous_ecriture").checked = false; }			
			if ( valeur == "22") {
				$("grade_lecture").checked = true; $("grade_ecriture").checked = true; $("tous_lecture").checked = true; $("tous_ecriture").checked = true; }
	}
	
	
</script>

