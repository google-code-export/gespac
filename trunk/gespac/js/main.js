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
