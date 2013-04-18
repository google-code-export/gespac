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
		<div class="menu-titre" id="entete-menu-accueil"><img src="<?PHP echo ICONSPATH . "accueil.png";?>">ACCUEIL</div>
		<div class="menu-items">
			<a href="index.php?page=accueil"><div class="menu-item" id='accueil'>Retour à l'accueil</div></a>
			<a href="../index.php"><div class="menu-item">Retour au portail</div></a>
		</div>
	</div>

	
	<div class="menu-block" id="menu-inventaire">
		<div class="menu-titre" id="entete-menu-inventaire"><img src="<?PHP echo ICONSPATH . "inventaire.png";?>">INVENTAIRE</div>
		<div class="menu-items">
			<a href="index.php?page=materiels"><div class="menu-item" id='materiels' >matériels</div></a>
			<a href="index.php?page=marques"><div class="menu-item" id='marques'>marques</div></a>
			<a href="index.php?page=salles"><div class="menu-item" id='salles'>salles</div></a>
		</div>
	</div>
	
	
	<div class="menu-block" id="menu-dossiers">
		<div class="menu-titre" id="entete-menu-dossiers"><img src="<?PHP echo ICONSPATH . "dossiers.png";?>">DOSSIERS</div>
		<div class="menu-items">
			<a href="index.php?page=dossiers"><div class="menu-item" id='dossiers'>Gérer les dossiers</div></a>
		</div>
	</div>


	<div class="menu-block" id="menu-prets">
		<div class="menu-titre" id="entete-menu-prets"><img src="<?PHP echo ICONSPATH . "pret.png";?>">PRETS</div>
		<div class="menu-items">
			<a href="index.php?page=prets"><div class="menu-item" id='prets'>Gérer les prêts</div></a>
		</div>
	</div>
	
	
	<div class="menu-block" id="menu-users">
		<div class="menu-titre" id="entete-menu-users"><img src="<?PHP echo ICONSPATH . "users.png";?>">UTILISATEURS</div>
		<div class="menu-items">
			<a href="index.php?page=utilisateurs"><div class="menu-item" id='utilisateurs'>Visualiser les utilisateurs</div></a>
			<a href="index.php?page=grades"><div class="menu-item" id='grades'>Visualiser les grades</div></a>
			<a href="index.php?page=importiaca"><div class="menu-item" id='importiaca'>Importer les comptes IACA</div></a>
			<a href="index.php?page=moncompte"><div class="menu-item" id='moncompte'>Modifier mon compte</div></a>
		</div>
	</div>
	
	<div class="menu-block" id="menu-modules">
		<div class="menu-titre" id="entete-menu-modules"><img src="<?PHP echo ICONSPATH . "modules.png";?>">MODULES</div>
		<div class="menu-items">
			<a href="index.php?page=recapfog"><div class="menu-item" id='recapfog'>Récapitulatif FOG</div></a>
			<a href="index.php?page=wol"><div class="menu-item" id='wol'>Wake On Lan</div></a>
			<a href="index.php?page=exportsperso"><div class="menu-item" id='exportsperso'>Export Perso</div></a>
			<a href="index.php?page=taginventaire"><div class="menu-item" id='taginventaire'>MAJ No Inventaire</div></a>
			<a href="index.php?page=imagefog"><div class="menu-item" id='imagefog'>Images Fog</div></a>
			<a href="index.php?page=modportail"><div class="menu-item" id='modportail'>Menu portail</div></a>
			<a href="index.php?page=gestfichiers"><div class="menu-item" id='gestfichiers'>Gestionnaire de fichiers</div></a>
			<a href="index.php?page=migfog"><div class="menu-item" id='migfog'>Migration Fog</div></a>
			<a href="index.php?page=migdossiers"><div class="menu-item" id='migdossiers'>Migration dossiers</div></a>
			<a href="index.php?page=geninventaire"><div class="menu-item" id='geninventaire'>Générer Inventaire</div></a>
			<a href="index.php?page=migusers"><div class="menu-item" id='migusers'>Migration Utilisateurs</div></a>
			<a href="index.php?page=aic"><div class="menu-item" id='aic'>Création AIC</div></a>
		</div>
	</div>
		
	<div class="menu-block" id="menu-info">
		<div class="menu-titre" id="entete-menu-info"><img src="<?PHP echo ICONSPATH . "info.png";?>">INFO</div>
		<div class="menu-items">
			<a href="index.php?page=college"><div class="menu-item" id='college'>Fiche collège</div></a>
			<a href="index.php?page=rss"><div class="menu-item" id='rss'>Flux RSS</div></a>
			<a href="index.php?page=statbat"><div class="menu-item" id='statbat'>Stats bâtons</div></a>
			<a href="index.php?page=statparc"><div class="menu-item" id='statparc'>Stats utilisation du parc</div></a>
			<a href="index.php?page=infoserveur"><div class="menu-item" id='infoserveur'>Info serveur</div></a>
		</div>
	</div>
	
	<div class="menu-block" id="menu-donnees">
		<div class="menu-titre" id="entete-menu-donnees"><img src="<?PHP echo ICONSPATH . "data.png";?>">DONNEES</div>
		<div class="menu-items">
			<a href="index.php?page=importocs"><div class="menu-item" id='importocs'>Importer DB OCS</div></a>
			<a href="index.php?page=exports"><div class="menu-item" id='exports'>Exports</div></a>
			<a href="index.php?page=dumpgespac"><div class="menu-item" id='dumpgespac'>Dump base GESPAC</div></a>
			<a href="index.php?page=dumpocs"><div class="menu-item" id='dumpocs'>Dump base OCS</div></a>
			<a href="index.php?page=logs"><div class="menu-item" id='logs'>Voir les Logs</div></a>
			<a href="index.php?page=importcsv"><div class="menu-item" id='importcsv'>Importer CSV</div></a>
		</div>
	</div>

</div>


<script type="text/javascript">
	$(function () {
		// Quand on clique sur une image
		$('.menu-titre').click( function(el)  {
			$('.menu-items').hide("fast");	// On masque tout
			$(this).parent().children(".menu-items").show("fast");	// On affiche le set d'items
		});
		
		var page = getQueryVariable("page");
		if (page) {
			$("#" + page).parent().parent().show();	// On affiche le set d'items
			$("#" + page).toggleClass('menu-current');
		}
	});
</script>
