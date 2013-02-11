// *********************************************************************************
//
//		FONCTIONS GENERALES
//
// *********************************************************************************

	// désactive postage sur touche entrée
	function disableEnterKey(e){
		var key = e.which;

		if(key == 13) return false;
		else return true;
	};


window.addEvent('domready', function(){
	
	// On implémente la fonctionnalité show/hide/toggle
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
	toggleAffichage(1000);


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
		
		// La barre d'entête
		$$(".entetes").setStyle("width", $("contenu").getStyle('width'));
		$$(".entetes-titre").setStyle("width", "auto");		// règle le problème sous firefox : width:auto ne marche pas depuis le css pour une raison étrange ...
		$$(".entetes-options").setStyle("width", "auto");
		
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
		toggleAffichage(1000);
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
