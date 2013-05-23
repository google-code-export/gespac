<?PHP

	#formulaire pour prêter et rendre du matériel

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');		

	
	$action = $_GET['action'];

	
	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ($host, $user, $pass, $gespac);

	
	// *********************************************************************************
	//
	//			Formulaire pour PRETER
	//
	// *********************************************************************************
	
	
	if ( $action == 'pret' ) {
		
		$userid = $_GET['user'];
		$matid = $_GET['mat'];
		
		$user_nom = $con_gespac->QueryOne('SELECT user_nom FROM users WHERE user_id = ' . $userid);
		$mat = $con_gespac->Queryrow('SELECT mat_nom, mat_etat FROM materiels WHERE mat_id =' . $matid);
		$mat_nom = $mat[0];
		$mat_etat = $mat[1];
		
		echo "<center>Voulez vous PRETER <br><br> le matériel <b>$mat_nom</b> <br><br> à <b>$user_nom</b> ?";
		
		if ($mat_etat <> 'FONCTIONNEL') echo "<br><br> <span style='color:red;font-size:125%;'>ATTENTION ! Le matériel est en état : <b>$mat_etat</b></span>";
		
		?>	
		
		<center><br><br>
		<form action="gestion_prets/post_prets.php?action=preter" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $matid;?>" name="mat_id">
			<input type=hidden value="<?PHP echo $userid;?>" name="user_id">
			<input type=submit value='PRETER' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
		<?PHP	
			
	}
	
	
	
	
	// *********************************************************************************
	//
	//			Formulaire pour RENDRE
	//
	// *********************************************************************************
	
	
	if ( $action == 'rendre' ) {
		
		$matid = $_GET['mat'];
		
		
		$mat = $con_gespac->Queryrow("SELECT mat_nom, user_nom, materiels.user_id FROM materiels, users WHERE materiels.user_id = users.user_id AND mat_id =" . $matid);
		$mat_nom = $mat[0];
		$user_nom = $mat[1];
		$user_id = $mat[2];
		
		echo "<center>Voulez vous RENDRE <br><br> le matériel <b>$mat_nom</b> <br><br>prêté à <b>$user_nom</b> ?";
		
		?>	
		
		<center><br>
		<form action="gestion_prets/post_prets.php?action=rendre" method="post" name="post_form" id='formulaire'>
			<input type=hidden value="<?PHP echo $matid;?>" name="mat_id">
			<input type=hidden value="<?PHP echo $user_id;?>" name="user_id">
			<input type=submit value='RENDRE' id="post_form">
			<input type=button onclick="$('#dialog').dialog('close');" value='Annuler'>
		</form>
		</center>
		
		<?PHP
	}
	
?>



<script>
	
	/******************************************
	*
	*		AJAX
	*
	*******************************************/

	
	$(function() {	
	
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
		
			// Permet d'avoir les données à envoyer
			var dataString = $("#formulaire").serialize();
			
			// action du formulaire
			var url = $("#formulaire").attr( 'action' );
			
			var request = $.ajax({
				type: "POST",
				url: url,
				data: dataString,
				dataType: "html"
			 });
			 
			 request.done(function(msg) {
				$('#dialog').dialog('close');
				$('#targetback').show(); $('#target').show();
				$('#target').html(msg);
				window.setTimeout("document.location.href='index.php?page=prets&filter=" + $('#filt').val() + "'", 1500);
			 });
			 
		});	
	});
	
</script>
