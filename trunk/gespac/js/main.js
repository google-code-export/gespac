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
		//$(div_dest).set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET � POST (en effet, avec GET il r�cup�re la totalit� du tableau get en param�tres pour ne pas d�passer la taille maxi d'une url)
		//$(div_dest).load(page);
	};
	
	
/*
* @name: resizeContent
* @param : rien
* @return : rien
* @description : Permet le redimensionnement du contenu de la page � la vol�e  
* @reference : toutes les pages
*/

function resizeContent() {
	//$('conteneur').style.height = window.getSize().y -240 + 'px';
}
