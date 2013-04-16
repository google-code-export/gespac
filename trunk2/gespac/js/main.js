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
	
		
		
	/////////////////////////////////////////////////////////////
	//			Fonction de filtrage des tables
	/////////////////////////////////////////////////////////////
	
	function filter (phrase, tableid){
		
		if(typeof phrase.value != 'undefined') {
			var data = phrase.value.split(" ");
			var cells=$("#" + tableid + " td");
						
			if(data != "") {
				// On cache toutes les lignes
				cells.parent("tr").hide("fast");
				// puis on filtre pour n'afficher que celles qui répondent au critère du filtre
				cells.filter(function() {
					return $(this).text().toLowerCase().indexOf(data) > -1;
				}).parent("tr").show("fast");	
			} else {
				// On montre toutes les lignes
				cells.parent("tr").show("fast");
			}

			// Si il existe on remplit le filtercount (même si pour le moment il a un coup de retard, sans que je me l'explique)
			if ($("#filtercount")) $("#filtercount").html($("#" + tableid + " tr:visible").length -1);			
		}
	}	
	
	
$(function () {

	// custom css expression for a case-insensitive contains()
	jQuery.expr[':'].Contains = function(a, i, m) {
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
	};
		
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
		$(".helpbox").toggle("fade");
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
	
		
	// **************************************************************** CREATION / EDITION DANS UNE DIALOGBOX
	$('a.editbox').click(function(){

		var url = this.href;
		var title = this.title;
		
		var width = "auto";
		var height = "auto";
		var modal = false;
		
		if (url.match(/width=([^&]+)/)) width = url.match(/width=([^&]+)/)[1];
		if (url.match(/height=([^&]+)/)) height = url.match(/height=([^&]+)/)[1];
		if (url.match(/modal=([^&]+)/)) modal = url.match(/modal=([^&]+)/)[1];
							
		var dialog = $("#dialog");
		if ($("#dialog").length == 0) {	dialog = $('<div id="dialog" style="display:hidden"></div>').appendTo('body');	} 

		// load remote content
		dialog.load(
			url,
			{},
			function(responseText, textStatus, XMLHttpRequest) {
				dialog.dialog({	title:title, width:width, height:height, modal:modal, stack: false});
			}
		);
		
		return false;	//on ne suit pas le lien cliquable
		
	});

	
	
});
