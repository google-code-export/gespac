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


window.addEvent('domready', function(){
	
	///////////////////////////////////////////
	//		AFFICHE L'AIDE EN LIGNE
	///////////////////////////////////////////

	$$('.help-button').addEvent('click', function(e)  {
		
		if ($$(".helpbox").getStyle("display") == "none")
			$$(".helpbox").setStyle("display", "inline");
		else
			$$(".helpbox").setStyle("display", "none");
	});
	
	
	window.addEvent('resize', function(){
	
		// On ajuste la taille de la barre d'ent�te
		$("entete-materiels").style.width = $("contenu").getStyle('width');
		
		if (window.getSize().x < 950 ) {
			console.log ("redim pliz !");
		}
	
		/*
		
			si la taille de la fen�tre passe sous 950px
			- on masque le menu
			- on colle un bouton genre mouseover, affiche le menu
			- on vire le margin-left du div page
			- on r�ajuste la taille de la barre d'ent�te
		
		*/
		
	});
	
});
