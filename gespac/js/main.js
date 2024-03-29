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


	// Pour parser la chaine get en js
	function getQueryVariable(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split('&');
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split('=');
			if (decodeURIComponent(pair[0]) == variable) {
				return decodeURIComponent(pair[1]);
			}
		}
	}


window.addEvent('domready', function(){
	
	// On impl�mente la fonctionnalit� show/hide/toggle
	Element.implement({
		//implement show
		show: function() {
			this.setStyle('display','');
		},
		//implement hide
		hide: function() {
			this.setStyle('display','none');
		},
		//implement toggle
		toggle: function() {
			if (this.getStyle('display')=='none')
				this.setStyle('display','block');
			else
				this.setStyle('display','none');
		}
	});

	
	
	
	
	// init l'affichage
	toggleAffichage(1100);


	/////////////////////////////////////////////////////////////
	//	Change l'affichage en fonction de la taille de la fenetre
	/////////////////////////////////////////////////////////////
	function toggleAffichage(size) {
		if (window.getSize().x < size ) {
			$('menu').setStyle("display", "none");
			$('menu').setStyle("border", "1px solid black");
			$('contenu').setStyle("margin-left", "10px");
			$('toggle-menu').setStyle("display", "block");
		}
		else {
			$('menu').setStyle("display", "block");
			$('menu').setStyle("border", "0px");
			$('contenu').setStyle("margin-left", "230px");
			$('toggle-menu').setStyle("display", "none");
		}
		
		// La hauteur du menu principal
		$('menu').setStyle("max-height", window.getSize().y-100 + "px");
		
		// La barre d'ent�te
		//$$(".entetes").setStyle("width", $("contenu").getStyle('width'));
		//$$(".entetes-titre").setStyle("width", "auto");		// r�gle le probl�me sous firefox : width:auto ne marche pas depuis le css pour une raison �trange ...
		//$$(".entetes-options").setStyle("width", "auto");
		
	}
	
	
	///////////////////////////////////////////
	//		AFFICHE L'AIDE EN LIGNE
	///////////////////////////////////////////
	$$('.help-button').addEvent('click', function(e)  {
		
		if ($$(".helpbox").getStyle("display") == "none")
			$$(".helpbox").setStyle("display", "inline");
		else
			$$(".helpbox").setStyle("display", "none");
	});
	
	
	///////////////////////////////////////////
	//	Si la taille de la page est trop juste
	///////////////////////////////////////////
	window.addEvent('resize', function(){	
		toggleAffichage(1100);
	});
	
	
	///////////////////////////////////////////
	//	toggle du menu quand la page est trop petite
	///////////////////////////////////////////
	$('toggle-menu').addEvent('click', function(e)  {
		if ($("menu").getStyle("display") == "none")
			$('menu').setStyle("display", "block");
		else
			$('menu').setStyle("display", "none");
	});
		
	
});
