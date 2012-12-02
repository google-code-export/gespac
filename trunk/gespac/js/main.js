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
