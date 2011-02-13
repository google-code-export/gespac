	
	// Ouvre une page en AJAX. <- Fonction assez triviale
	function OuvrirPage (page) {
		HTML_AJAX.replace("conteneur", page);
	};
	
	
	// *********************************************************************************
	//
	//				désactive postage sur touche entrée
	//
	// *********************************************************************************
	
	function disableEnterKey(e){
		var key = e.which;

		if(key == 13) return false;
		else return true;
	}
	
	
	// css et navigateurs
	/*
	if ( navigator.userAgent.indexOf('MSIE') != -1 )
		document.write('<LINK rel="stylesheet" type="text/css" href="css/style_ie.css">');
	else
		document.write('<LINK rel="stylesheet" type="text/css" href="css/style_ff.css">');
*/