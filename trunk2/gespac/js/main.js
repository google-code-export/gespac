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
	
	//	Pour afficher une page	dans un div particulier
	function AffichePage(div_dest, page) {
		$(div_dest).set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres pour ne pas dépasser la taille maxi d'une url)
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


$(function () {
		
	// init l'affichage
	toggleAffichage(1100);


	/////////////////////////////////////////////////////////////
	//	Change l'affichage en fonction de la taille de la fenetre
	/////////////////////////////////////////////////////////////
	function toggleAffichage(size) {
		if ($(window).width() < size ) {
			$('#menu').hide();
			$('#menu').css("border","1px solid black");
			$('#contenu').css("margin-left","10px");
			$('#toggle-menu').show();
		}
		else {
			$('#menu').show();
			$('#menu').css("border","0px");
			$('#contenu').css("margin-left","230px");
			$('#toggle-menu').hide();
		}
		
		// La hauteur du menu principal
		$('#menu').css("max-height", $(window).height()-100 + "px");
		
		// La barre d'entête
		//$$(".entetes").setStyle("width", $("contenu").getStyle('width'));
		//$$(".entetes-titre").setStyle("width", "auto");		// règle le problème sous firefox : width:auto ne marche pas depuis le css pour une raison étrange ...
		//$$(".entetes-options").setStyle("width", "auto");
		
	}
	
	
	///////////////////////////////////////////
	//		AFFICHE L'AIDE EN LIGNE
	///////////////////////////////////////////
	$('.help-button').click( function(e)  {
		
		if ($(".helpbox").is(':visible'))
			$(".helpbox").fadeOut();
		else
			$(".helpbox").fadeIn();
	});
	
	
	///////////////////////////////////////////
	//	Si la taille de la page est trop juste
	///////////////////////////////////////////
	$(window).resize( function(){	
		toggleAffichage(1100);
	});
	
	
	///////////////////////////////////////////
	//	toggle du menu quand la page est trop petite
	///////////////////////////////////////////
	$('#toggle-menu').click( function()  {
		$('#menu').toggle("slide");
	});
		
	
});
