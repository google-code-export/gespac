<?PHP 


/*

	menu en haut de l'écran
	La gestion des droits est activée de cette façon :
	On a stocké dans un variable de session la liste des pages autorisées en lecture et écriture
	chaque page est identifiée par un code par exemple 02-01 ou 03-02.
	Les codes sont gérés par le fichier menu.txt.
	les fonctions checkdroit et checkalldroits permettent respectivement de tester si un item ou une
	liste d'items est en lecture ou pas.
	On dessine ou pas l'item en fonction de la valeur retournée

*/

?>
	
	<div id="menu-global">

		<div class="menu-block" id="menu-accueil">
			<div class="menu-titre" id="entete-menu-accueil"><img src="img/icons/accueil.png">ACCUEIL</div>
			<div class="menu-items">
				<a href="index.php?page=accueil"><div class="menu-item">Retour à l'accueil</div></a>
				<a href="../index.php"><div class="menu-item">Retour au portail</div></a>
			</div>
		</div>

		
		<div class="menu-block" id="menu-inventaire">
			<div class="menu-titre" id="entete-menu-inventaire"><img src="img/icons/inventaire.png">INVENTAIRE</div>
			<div class="menu-items">
				<a href="index.php?page=materiels"><div class="menu-item">matériels</div></a>
				<a href="index.php?page=marques"><div class="menu-item">marques</div></a>
				<a href="index.php?page=salles"><div class="menu-item">salles</div></a>
			</div>
		</div>
		
		
		<div class="menu-block" id="menu-dossiers">
			<div class="menu-titre" id="entete-menu-dossiers"><img src="img/icons/dossiers.png">DOSSIERS</div>
			<div class="menu-items">
				<a href="index.php?page=dossiers"><div class="menu-item">Gérer les dossiers</div></a>
			</div>
		</div>
	
	
		<div class="menu-block" id="menu-prets">
			<div class="menu-titre" id="entete-menu-prets"><img src="img/icons/pret.png">PRETS</div>
			<div class="menu-items">
				<a href="index.php?page=prets"><div class="menu-item">Gérer les prêts</div></a>
			</div>
		</div>
		
		
		<div class="menu-block" id="menu-users">
			<div class="menu-titre" id="entete-menu-users"><img src="img/icons/users.png">UTILISATEURS</div>
			<div class="menu-items">
				<a href="index.php?page=utilisateurs"><div class="menu-item">Visualiser les utilisateurs</div></a>
				<a href="index.php?page=grades"><div class="menu-item">Visualiser les grades</div></a>
				<a href="index.php?page=importiaca"><div class="menu-item">Importer les comptes IACA</div></a>
				<a href="index.php?page=moncompte"><div class="menu-item">Modifier mon compte</div></a>
			</div>
		</div>
		
		<div class="menu-block" id="menu-modules">
			<div class="menu-titre" id="entete-menu-modules"><img src="img/icons/modules.png">MODULES</div>
			<div class="menu-items">
				<a href="index.php?page=recapfog"><div class="menu-item">Récapitulatif FOG</div></a>
				<a href="index.php?page=wol"><div class="menu-item">Wake On Lan</div></a>
				<a href="index.php?page=exportsperso"><div class="menu-item">Export Perso</div></a>
				<a href="index.php?page=taginventaire"><div class="menu-item">MAJ No Inventaire</div></a>
				<a href="index.php?page=imagefog"><div class="menu-item">Images Fog</div></a>
				<a href="index.php?page=modportail"><div class="menu-item">Menu portail</div></a>
				<a href="index.php?page=gestfichiers"><div class="menu-item">Gestionnaire de fichiers</div></a>
				<a href="index.php?page=migfog"><div class="menu-item">Migration Fog</div></a>
				<a href="index.php?page=migdossiers"><div class="menu-item">Migration dossiers</div></a>
				<a href="index.php?page=geninventaire"><div class="menu-item">Générer Inventaire</div></a>
				<a href="index.php?page=migusers"><div class="menu-item">Migration Utilisateurs</div></a>
				<a href="index.php?page=aic"><div class="menu-item">Création AIC</div></a>
			</div>
		</div>
			
		<div class="menu-block" id="menu-info">
			<div class="menu-titre" id="entete-menu-info"><img src="img/icons/info.png">INFO</div>
			<div class="menu-items">
				<a href="index.php?page=college"><div class="menu-item">Fiche collège</div></a>
				<a href="index.php?page=rss"><div class="menu-item">Flux RSS</div></a>
				<a href="index.php?page=statbat"><div class="menu-item">Stats bâtons</div></a>
				<a href="index.php?page=statparc"><div class="menu-item">Stats utilisation du parc</div></a>
				<a href="index.php?page=infoserveur"><div class="menu-item">Info serveur</div></a>
			</div>
		</div>
		
		<div class="menu-block" id="menu-donnees">
			<div class="menu-titre" id="entete-menu-donnees"><img src="img/icons/data.png">DONNEES</div>
			<div class="menu-items">
				<a href="index.php?page=importocs"><div class="menu-item">Importer DB OCS</div></a>
				<a href="index.php?page=exports"><div class="menu-item">Exports</div></a>
				<a href="index.php?page=dumpgespac"><div class="menu-item">Dump base GESPAC</div></a>
				<a href="index.php?page=dumpocs"><div class="menu-item">Dump base OCS</div></a>
				<a href="index.php?page=logs"><div class="menu-item">Voir les Logs</div></a>
				<a href="index.php?page=importcsv"><div class="menu-item">Importer CSV</div></a>
			</div>
		</div>
	
	</div>
	

	<script type="text/javascript">
		
		// Activation des icones sur clic
		function change_icon_onclick (div) {	
			// on désactive tous les boutons
			
			if ($('accueil')) $('accueil').className = "accueil";
			if ($('inventaire')) $('inventaire').className = "inventaire";
			if ($('demandes')) $('demandes').className = "demandes";
			if ($('donnees')) $('donnees').className = "donnees";
			if ($('prets')) $('prets').className = "prets";
			if ($('utilisateurs')) $('utilisateurs').className = "utilisateurs";
			if ($('plugins')) $('plugins').className = "plugins";
			if ($('info')) $('info').className = "info";
			
			// On active le bon bouton
			$(div).className = div + "-clicked";
		}
		


		window.addEvent('domready', function(){
			
			// Quand on clique sur une image
			$$('.menu-titre').addEvent('click', function(el)  {
				$$('.menu-items').setStyle("display", "none");	// On masque tout
				$(this).getParent().getChildren(".menu-items").setStyle("display", "block");	// On affiche le set d'items
			});
		
		});
		
	</script>
