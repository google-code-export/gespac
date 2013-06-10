<script type="text/javascript"> 


	$(function(){
		// **************************************************************** POST AJAX FORMULAIRES
		$("#post_form").click(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
			
			if ( validForm() == true) {
			
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
					$('#target').html(msg);
				 });
			}			 
		});	
	});

</script>



<div class="entetes" id="entete-exportperso">	

	<span class="entetes-titre">EXPORT PERSONNALISE<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Permet de créer un fichier CSV avec des champs personnalisés de la base Gespac.</div>

</div>

<div class="spacer"></div>

<form action="modules/export/post_export_perso.php" method="post" name="post_form" id="formulaire">

	<input type=checkbox class=chkbox id=mat_nom> Nom du matériel <br>
	<input type=checkbox class=chkbox id=mat_dsit> Numéro d'inventaire <br>
	<input type=checkbox class=chkbox id=mat_serial> Numéro de série <br>
	<input type=checkbox class=chkbox id=mat_etat> Etat du matériel<br>
	<input type=checkbox class=chkbox id=mat_origine> Origine du matériel <br>

	<input type=checkbox class=chkbox id=salle_nom> Salle du matériel <br>

	<input type=checkbox class=chkbox id=marque_type> Type du matériel <br>
	<input type=checkbox class=chkbox id=marque_stype> Sous-type du matériel <br>
	<input type=checkbox class=chkbox id=marque_marque> Marque du matériel <br>
	<input type=checkbox class=chkbox id=marque_model> Modèle du matériel <br>

	<input type=checkbox class=chkbox id=user_nom> Prêté à <br>

	<br>

	<br><br>

	<div id="log"></div>

	<input type=hidden name=rqsql id=rqsql size=150>
	<input type="submit" onclick="add_field()" value="Lancer l'export personnalisé" id="post_form">
</form>


<script>

	function add_field () {
	
		var query_select = "";
		var query_from = "materiels";
		var query_where = "";
		var thereis_salles = false;
		var thereis_marques = false;
		var thereis_users = false;
		var nb_champs = 0;
		
		$('.chkbox').each(function (item) {
		
			// Si la case en question est cochée
			if ( $(this).prop("checked") == true  ) {
			
				// On incrémente le nombre de champs
				nb_champs++;
				
				// On rajoute à la partie query le champ courant
				query_select += $(this).prop("id") + ",";
				
				// si c'est un champ de la table "salles"
				if ( $(this).prop("id") == "salle_nom" ) {	thereis_salles = true; }
				
				// si c'est un champ de la table "marques"
				if ( $(this).prop("id") == "marque_type" || $(this).prop("id") == "marque_stype" || $(this).prop("id") == "marque_marque" || $(this).prop("id") == "marque_model") { thereis_marques = true; }
				
				// si c'est un champ de la table "users"
				if ( $(this).prop("id") == "user_nom" ) {	thereis_users = true;	}
			} 

		})
		
		// Si jamais il y a des champs de la table "salles" on rajoute au FROM et au WHERE
		if ( thereis_salles ) {
			query_where += "materiels.salle_id = salles.salle_id AND ";
			query_from += ",salles";
		}
		
		// Si jamais il y a des champs de la table "marques" on rajoute au FROM et au WHERE
		if ( thereis_marques ) {		
			query_where += "materiels.marque_id = marques.marque_id AND ";
			query_from += ",marques";
		}

		// Si jamais il y a des champs de la table "users" on rajoute au FROM et au WHERE
		if ( thereis_users ) {		
			query_where += "materiels.user_id = users.user_id AND ";
			query_from += ",users";
		}
		

		if ( query_select != "" ) {
			// on vire la dernière virgule
			query_select = query_select.substr(0, query_select.length-1);
			// on rajoute SELECT
			query_select = "SELECT " + query_select; 
		}
		
		if ( query_from != "" ) {
			query_from = " FROM " + query_from; }

		if ( query_where != "" ) { 
			// on vire le dernier AND
			query_where = query_where.substr(0, query_where.length-5);
			// on rajoute WHERE
			query_where = " WHERE " + query_where; 
		}
		
		// On affiche
		if ( nb_champs > 0)	$('#rqsql').val(query_select + query_from + query_where);
		else $('#log').html("Faudrait au moins cocher une case");

	}
	
	
</script>
