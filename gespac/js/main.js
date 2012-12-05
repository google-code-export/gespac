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
	
	///////////////////////////////////////////
	//		AFFICHE L'AIDE EN LIGNE
	///////////////////////////////////////////
	
	$$(".helpbox").setStyle("display", "none");
	
	$$('.help-button').addEvent('click', function(e)  {
		
		if ($$(".helpbox").getStyle("display") == "none")
			$$(".helpbox").setStyle("display", "");
		else
			$$(".helpbox").setStyle("display", "none");
	});
	
});
