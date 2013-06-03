// *********************************************************************************
//
//		FONCTIONS GENERALES
//
// *********************************************************************************

/*
* @name: AffichePage
* @param : touche frappée
* @return : rien
* @description : désactive postage sur touche entrée
* @reference : toutes les pages avec un filtre
*/	
function disableEnterKey(e){
	var key = e.which;

	if(key == 13) return false;
	else return true;
};
	
/*
* @name: AffichePage
* @param : string:variable, string:page
* @return : rien
* @description : Permet d'afficher dans un div le contenu d'une page (genre ajax)
* @reference : ???? encore utile ???
*/
function AffichePage(div_dest, page) {
	$(div_dest).set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres pour ne pas dépasser la taille maxi d'une url)
	$(div_dest).load(page);
};


/*
* @name: getQueryVariable
* @param : string:variable
* @return : string:valeur de la variable
* @description : Permet de récupérer la valeur d'une variable de l'url
* @reference : menu.php
*/
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
		
		
/*
* @name: filter
* @param : string:phrase, string:tableid
* @return : rien
* @description : Permet de filtrer une table et éventuellement de marquer le nombre de lignes filtrées
* @reference : Presque toutes les pages
*/
function filter (phrase, tableid){
	
	var data = phrase.split(" ");
	var cells=$("#" + tableid + " td");
				
	if(data != "") {
		// On cache toutes les lignes
		cells.parent("tr").hide();
		// puis on filtre pour n'afficher que celles qui répondent au critère du filtre
		cells.filter(function() {
			return $(this).text().toLowerCase().indexOf(data) > -1;
		}).parent("tr").show();		
	} else {
		// On montre toutes les lignes
		cells.parent("tr").show();
	}
	
	if ($("#filtercount")) $("#filtercount").html( $("#" + tableid + " tr:visible").length -1 );
}	


/*
* @name: validation
* @param : queud
* @return : true si le formulaire est valide, sinon ... false
* @description : Permet de valider les input ayant la classe "valid" dans un formulaire
* @reference : Presque toutes les pages formulaires
*/

function validForm () {
	
	var valid = true;
	$('.validInfo').html("");
	
	$('.valid').each (function(){
		
		// test sur champ vide
		if ($(this).hasClass("nonvide")) {	
			if ( $(this).val() == "" ) {
				valid=false;
				$(this).after("<span class='validInfo'>*non vide </span>");
			}
		}
		
		
		// test sur uai
		if ($(this).hasClass("uai") && $(this).val() != "") {	
			if ( $(this).val().match(/^[0-9]{7}[A-Z]{1}$/) == null ) {
				valid=false;
				$(this).after("<span class='validInfo'>*uai en majuscules </span>");
			}
		}
		
		// test sur majuscules
		if ($(this).hasClass("caps") && $(this).val() != "") {	
			if ( $(this).val().match(/[A-Z0-9]/) == null ) {
				valid=false;
				$(this).after("<span class='validInfo'>*en majuscules </span>");
			}
		}
		
		// test sur mail
		if ($(this).hasClass("mail") && $(this).val() != "") {	
			if ( $(this).val().match(/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/) == null ) {
				valid=false;
				$(this).after("<span class='validInfo'>*mail invalide </span>");
			}
		}		
		
		// test sur url
		if ($(this).hasClass("url") && $(this).val() != "") {	
			if ( $(this).val().match(/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-=?]*)*\/?/) == null ) {
				valid=false;
				$(this).after("<span class='validInfo'>*url invalide </span>");
			}
		}	
		
		// test sur mac
		if ($(this).hasClass("mac") && $(this).val() != "") {	
			if ( $(this).val().match(/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i) == null ) {
				valid=false;
				$(this).after("<span class='validInfo'>*mac invalide </span>");
			}
		}		
	});
	
	return valid;	
}

	
	
$(function () {
	
	// init l'affichage
	toggleAffichage(1100);



	/*
	* @name: toggleAffichage
	* @param : int:size
	* @return : rien
	* @description : Change l'affichage en fonction de la taille de la fenetre (fait disparaitre le menu)
	* @reference : index
	*/
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
	

	// **************************************************************** AFFICHE L'AIDE EN LIGNE
	$('.help-button').click( function(e)  {
		$(".helpbox").toggle("fade");
	});
	
	
	// **************************************************************** Si la taille de la page est trop juste
	$(window).resize( function(){	
		toggleAffichage(1100);
	});
	
	
	// **************************************************************** toggle du menu quand la page est trop petite
	$('#toggle-menu').click( function()  {
		$('#menu').toggle("slide");
	});
	
		
	// **************************************************************** CREATION / EDITION DANS UNE DIALOGBOX
	$('a.editbox').click(function(){

		var url = this.href;
		var title = this.title;
		
		var width = "auto";
		var height = "auto";
		var maxheight = "auto";
		var modal = false;
		
		if (url.match(/[&|?]width=([^&]+)/)) width = url.match(/[&|?]width=([^&]+)/)[1];
		if (url.match(/[&|?]height=([^&]+)/)) height = url.match(/[&|?]height=([^&]+)/)[1];
		if (url.match(/[&|?]maxheight=([^&]+)/)) maxheight = url.match(/[&|?]maxheight=([^&]+)/)[1];
		if (url.match(/[&|?]modal=([^&]+)/)) modal = url.match(/[&|?]modal=([^&]+)/)[1];
							
		var dialog = $("#dialog");
		if ($("#dialog").length == 0) {	dialog = $('<div id="dialog" style="display:hidden"></div>').appendTo('body');	} 

		// load remote content
		dialog.load(
			url,
			{},
			function(responseText, textStatus, XMLHttpRequest) {
				dialog.dialog({	title:title, width:width, height:height, modal:modal, stack: false});
				dialog.css('maxHeight', maxheight + "px"); //on applique une hauteur maximum
			}
		);
		
		return false;	//on ne suit pas le lien cliquable
		
	});
	
	
	// **************************************************************** CREATION / EDITION DANS UNE DIALOGBOX
	$('a.infobox').click(function(){

		var url = this.href;
		var title = this.title;
		
		var width = "auto";
		var height = "auto";
		var maxheight = "auto";
		var modal = false;
		
		if (url.match(/[&|?]width=([^&]+)/)) width = url.match(/[&|?]width=([^&]+)/)[1];
		if (url.match(/[&|?]height=([^&]+)/)) height = url.match(/[&|?]height=([^&]+)/)[1];
		if (url.match(/[&|?]maxheight=([^&]+)/)) maxheight = url.match(/[&|?]maxheight=([^&]+)/)[1];
		if (url.match(/[&|?]modal=([^&]+)/)) modal = url.match(/[&|?]modal=([^&]+)/)[1];
							
		var infobox = $("#infobox");
		if ($("#infobox").length == 0) {	infobox = $('<div id="infobox" style="display:hidden"></div>').appendTo('body');	} 

		// load remote content
		infobox.load(
			url,
			{},
			function(responseText, textStatus, XMLHttpRequest) {
				infobox.dialog({	title:title, width:width, height:height, modal:modal, stack: false});
				infobox.css('maxHeight', maxheight + "px"); //on applique une hauteur maximum
			}
		);
		
		return false;	//on ne suit pas le lien cliquable
		
	});

	
	
});
