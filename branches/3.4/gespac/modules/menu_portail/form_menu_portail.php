<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	// cnx à gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
?>

<script type="text/javascript"> 


	window.addEvent('domready', function(){
		
		// MOTEUR AJAX
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (pour les url trop longues)
					window.setTimeout("$('conteneur').load('modules/menu_portail/voir_menu_portail.php');", 1500);
					SexyLightbox.close();
				}
			
			}).send(this.toQueryString());
		});		
	});

	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un délais de 1500ms
		window.setTimeout("$('conteneur').load('modules/menu_portail/voir_menu_portail.php');", 1500);
		SexyLightbox.close();
	}
	
		
	// Validation du formulaire
	function validation () {

		var bt_submit = $("bt_submit");
		var mp_nom   = $("mp_nom").value;
		var mp_url   = $("mp_url").value;

		if (mp_nom == "" || mp_url == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	}
	
	
	// Donne le focus au premier champ du formulaire
	$('mp_nom').focus();
	
</script>

<?PHP

	$id = $_GET['id'];


	/*******************************
	*
	*			MODIFICATION 
	* 
	********************************/

	if ($id <> '-1' ) {

		// Requete pour récupérer les données des champs pour le user à modifier
		$item_a_modifier = $con_gespac->queryRow ( "SELECT mp_id, mp_nom, mp_url FROM menu_portail WHERE mp_id=$id" );		
		
		// valeurs à affecter aux champs
		$mp_id 		= $item_a_modifier[0];
		$mp_nom	 	= $item_a_modifier[1];
		$mp_url		= $item_a_modifier[2];

		
?>		
	
		<form action="modules/menu_portail/post_menu_portail.php?action=mod&id=<?PHP echo $id; ?>" method="post" name="post_form" id="post_form">
			<center>
					
			<table width=400 align=center cellpadding=10px>

				<tr>
					<TD>nom</TD> 
					<TD><input name="mp_nom" id="mp_nom" type="text" value="<?PHP echo $mp_nom;?>" onkeyup="validation();" ></TD>
				</tr>
				
				<tr>
					<TD>Url</TD>
					<TD><input name="mp_url" id="mp_url" type="text" value="<?PHP echo $mp_url;?>" onkeyup="validation();" ></TD>
				</tr>

			</table>
			


			<br>
			<br>
			<center>
			<input type="submit" name="envoyer" id="bt_submit" value="Envoyer">

			</center>
			
		</FORM>
	
		


<?PHP		
	}
	
	
	/*******************************
	*
	*			CREATION 
	* 
	********************************/
	
	

	else {
?>
	
				
		<form method="POST" action="modules/menu_portail/post_menu_portail.php?action=add" target=_blank enctype="multipart/form-data">
			<center>
					
			<table width=400 align=center cellpadding=10px>

				<tr>
					<TD>nom</TD> 
					<TD><input name="mp_nom" id="mp_nom" type="text" onkeyup="validation();" ></TD>
				</tr>
				
				<tr>
					<TD>Url</TD>
					<TD><input name="mp_url" id="mp_url" type="text" onkeyup="validation();" ></TD>
				</tr>

			
			
			<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
				<tr>
					<td>Icone (png)</td>
					<td><input type="file" name="myfile"></td>
				</tr>
			 </table>
			 </center>



			<br>
			<br>
			<center>
			<input type="submit" name="envoyer" id="bt_submit" value="Envoyer" onclick="refresh_quit();" disabled />

			</center>

		</FORM>

<?PHP } ?>			
