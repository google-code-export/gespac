<script type="text/javascript"> 

	/******************************************
	*
	*		AJAX
	*
	*******************************************/
	
	window.addEvent('domready', function(){
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
					window.setTimeout("$('conteneur').load('modules/export/post_export_perso.php');", 1500);
				}
			
			}).send(this.toQueryString());
		});			
	});


</script>

<?PHP header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères ?>

<div id="target"></div>

<!--  SERVEUR AJAX 
<script type="text/javascript" src="server.php?client=all"></script>-->
<h3>Export personnalisé</h3><br>

<form action="modules/export/post_export_perso.php" method="post" name="post_form" id="post_form">

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

	<input type=hidden name=rqsql id=rqsql />
	<input type=submit onclick="add_field()" value="Lancer l'export personnalisé">
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
		
		$$('.chkbox').each(function (item) {
		
			// Si la case en question est cochée
			if ( $(item.id).checked ) {
			
				// On incrémente le nombre de champs
				nb_champs++;
				
				// On rajoute à la partie query le champ courant
				query_select += item.id + ",";
				
				// si c'est un champ de la table "salles"
				if ( item.id == "salle_nom" ) {	thereis_salles = true; }
				
				// si c'est un champ de la table "marques"
				if ( item.id == "marque_type" || item.id == "marque_stype" || item.id == "marque_marque" || item.id == "marque_model") { thereis_marques = true; }
				
				// si c'est un champ de la table "users"
				if ( item.id == "user_nom" ) {	thereis_users = true;	}
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
		if ( nb_champs > 0)	$('rqsql').value = query_select + query_from + query_where;
		else $('log').innerHTML = "Faudrait au moins cocher une case";

	}
	
	
</script>