
// *********************************************************************************
//
//	On d�marre l'�couteur d'�v�nements pour le postage des formulaires
//
// *********************************************************************************


/*
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({
				method: this.method,
				url: this.action,
				onSuccess: function(responseText, responseXML) {
					$('target').set('html', responseText);
					window.setTimeout("$('conteneur').load('gestion_college/voir_college.php');", 1500);
				}
			}).send(this.toQueryString());
		});	


*/


// *********************************************************************************
//
//		FONCTIONS GENERALES
//
// *********************************************************************************

	// d�sactive postage sur touche entr�e
	function disableEnterKey(e){
		var key = e.which;

		if(key == 13) return false;
		else return true;
	};

	//	Pour afficher une page	dans un div particulier
	function AffichePage(div_dest, page) {
		$(div_dest).set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres pour ne pas d�passer la taille maxi d'une url)
		$(div_dest).load(page);
	};

		window.addEvent('domready', function(){
	
// *********************************************************************************
//
//		PAGES GESTION_COLLEGE
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES GESTION_DEMANDES
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES GESTION_DONNEES
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES GESTION_INVENTAIRE
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES GESTION_PRETS
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES GESTION_UTILISATEURS
//
// *********************************************************************************


// *********************************************************************************
//
//		PAGES RSS (PLUGINS)
//
// *********************************************************************************
	
	// v�rouille l'acc�s au bouton submit si les conditions ne sont pas remplies
	function validation () {

		var bt_submit = $("post_flux");
		var nom = $("nom").value;
		var url = $("url").value;
		
		if (nom == "" || url == "") {
			bt_submit.disabled = true;
		} else {
			bt_submit.disabled = false;
		}
	};
	
	// ferme la smoothbox et rafraichis la page
	function refresh_quit () {
		// lance la fonction avec un d�lais de 1500ms
		window.setTimeout("$('conteneur').load('modules/rss/rss.php');", 1500);
		TB_remove();
	};
	
	// On soumet le formulaire pour l'ajout d'un flux RSS
	$('post_add_flux_rss').addEvent('submit', function(e) {	//	Pour poster un formulaire
		new Event(e).stop();
		new Request({
			method: this.method,
			url: this.action,
			onSuccess: function(responseText, responseXML) {
				$('target').set('html', responseText);
				window.setTimeout("$('conteneur').load('gestion_college/voir_college.php');", 1500);
			}
		}).send(this.toQueryString());
	});	

// *********************************************************************************
//
//		PAGES EXPORTS (PLUGINS)
//
// *********************************************************************************
	

// *********************************************************************************
//
//		PAGES STATS (PLUGINS)
//
// *********************************************************************************
	

// *********************************************************************************
//
//		PAGES WOL (PLUGINS)
//
// *********************************************************************************


//---------------------------------------------------------------------------------------------------------------------------------
	});	// Fin de l'�couteur