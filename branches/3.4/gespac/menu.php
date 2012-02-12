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

include_once ('fonctions.php');

?>

	<script type="text/javascript">
		
		window.addEvent('domready', function(){ 
			SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'sexylightbox_menu'});
		});
		
		
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
				
	</script>


	
	<div id="main">
		<ul id="nav" class="dropdown dropdown-horizontal">
			

			<li class='dir'><a href='#' onclick="AffichePage('conteneur', './accueil.php');change_icon_onclick('accueil');"><div id='accueil' class='accueil' title='accueil'></div></a>
				<ul>
					<li class='item'><a href='../index.php'>	Retour au portail	</a></li>
				</ul>
			</li>
			
		<?PHP
			
			
			
			if ( checkalldroits("L-02-01,L-02-02,L-02-03") ) {
				echo "<li class='dir'>"; if (checkdroit("L-02-01") ) echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_inventaire/voir_materiels.php');change_icon_onclick('inventaire');\"><div id='inventaire' class='inventaire' title='inventaire'></div></a>"; else echo "<div id='inventaire' class='inventaire' title='inventaire'></div>";
					echo "<ul>";
						if ( checkdroit("L-02-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_inventaire/voir_materiels.php');change_icon_onclick('inventaire');\">	Visualiser les matériels		</a></li>";
						if ( checkdroit("L-02-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_inventaire/voir_marques.php');change_icon_onclick('inventaire');\">	Visualiser les marques		</a></li>";
						if ( checkdroit("L-02-03") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_inventaire/voir_salles.php');change_icon_onclick('inventaire');\">	Visualiser les salles		</a></li>";
					echo "</ul>
				</li>";
			}
			

			
			if ( checkalldroits("L-03-01,L-03-02, L-03-03") ) {
				echo "<li class='dir'>"; if (checkdroit("L-03-01") ) echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/voir_demandes.php');change_icon_onclick('demandes');\"><div id='demandes' class='demandes' title='demandes et interventions'></div></a>"; else echo "<div id='demandes' class='demandes' title='demandes et interventions'></div>";
					echo "<ul>";
						if (checkdroit("L-03-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/voir_demandes.php');change_icon_onclick('demandes');\">		Voir les dossiers	</a></li>";
						if (checkdroit("L-03-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/voir_interventions.php');change_icon_onclick('demandes');\">	Voir les interventions	</a></li>";
						if (checkdroit("L-03-03") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_dossiers/voir_dossiers.php');change_icon_onclick('demandes');\">	Dossiers beta	</a></li>";
					echo "</ul>
				</li>";
			}
			
			
			
			if ( checkalldroits("L-04-01,L-04-02,L-04-03,L-04-04,L-04-05,L-04-06") ) {
				echo "<li class='dir'><a href='#'><div id='donnees' class='donnees' title='gestion des imports et exports de données'></div></a>";
					echo "<ul>";
						if (checkdroit("L-04-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_donnees/voir_ocs_db.php');change_icon_onclick('donnees');\">		Importer DB OCS		</a></li>";			
						if (checkdroit("L-04-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_donnees/exports.php');change_icon_onclick('donnees');\">			Exports </a></li>";			
						if (checkdroit("L-04-03") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_donnees/dump_db_gespac.php');change_icon_onclick('donnees');\">	Dump base GESPAC	</a></li>";
						if (checkdroit("L-04-04") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_donnees/dump_db_ocs.php');change_icon_onclick('donnees');\">		Dump base OCS		</a></li>";
						if (checkdroit("L-04-05") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_donnees/voir_logs.php');change_icon_onclick('donnees');\">		Voir les Logs		</a></li>";
						if (checkdroit("L-04-06") ) echo "<li class='item'><a href='gestion_inventaire/form_import_csv.php?height=600&width=640' rel='sexylightbox_menu' title='Import machines CSV'>	Importer CSV	</a></li>";				
					echo "</ul>
				</li>";
			}
			
			
			
			if (checkdroit("L-05-01") ) echo "
			<li class='dir'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_prets/voir_prets.php');change_icon_onclick('prets');\"><div id='prets' class='prets' title='preter ou rendre un matériel'></div></a></li>
				<ul>
					<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_prets/voir_prets.php');change_icon_onclick('prets');\"><div id='prets' class='prets' title='preter ou rendre un matériel'></div></a></li>
				</ul>";
			
				
			
			if ( checkalldroits("L-06-01,L-06-02,L-06-03,L-06-04") ) {	
			echo "<li class='dir'>"; if (checkdroit("L-06-01") ) echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_utilisateurs/voir_utilisateurs.php');change_icon_onclick('utilisateurs');\"><div id='utilisateurs' class='utilisateurs' title='gestion des utilisateurs'></div></a>"; else echo "<div id='utilisateurs' class='utilisateurs' title='gestion des utilisateurs'></div>";
				echo "<ul>";
					if (checkdroit("L-06-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_utilisateurs/voir_utilisateurs.php');change_icon_onclick('utilisateurs');\">	Visualiser les utilisateurs		</a></li>";
					if (checkdroit("L-06-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_utilisateurs/voir_grades.php');change_icon_onclick('utilisateurs');\">	Visualiser les grades		</a></li>";
					if (checkdroit("L-06-03") ) echo "<li class='item'><a href='gestion_utilisateurs/form_comptes_iaca.php?height=200&width=640' rel='sexylightbox_menu' title='Import des comptes IACA'>	Importer les comptes IACA	</a></li>";
					if (checkdroit("L-06-04") ) echo "<li class='item'><a href='gestion_utilisateurs/form_utilisateur_personnel.php?height=300&width=640' rel='sexylightbox_menu' title='Modifier mon compte'>	Modifier mon compte	</a></li>";
					echo "</ul>
				</li>";
			}
		
			
			
			if ( checkalldroits("L-07-01,L-07-02,L-07-03,L-07-04,L-07-05,L-07-06,L-07-07,L-07-08,L-07-09") ) {	
			echo "<li class='dir'> <a href='#'><div id='plugins' class='plugins' title='modules et extensions'></div></a>";
				echo "<ul>";
					if (checkdroit("L-07-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/fog/recap_fog.php');change_icon_onclick('plugins');\">Récapitulatif FOG</a></li>";
					if (checkdroit("L-07-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/wol/voir_liste_wol.php');change_icon_onclick('plugins');\">Wake On Lan</a></li>";
					if (checkdroit("L-07-03") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/export/export_perso.php');change_icon_onclick('plugins');\">Export Perso</a></li>";
					if (checkdroit("L-07-04") ) echo "<li class='item'><a href='modules/ssn_dsit/form_import_csv.php?height=250&width=640' rel='sexylightbox_menu' title='MAJ tags DSIT'>	MAJ tags DSIT	</a></li>";				
					//if (checkdroit("L-07-05") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/image_fog/voir_liste.php');change_icon_onclick('plugins');\">Image FOG</a></li>";
					if (checkdroit("L-07-06") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/menu_portail/voir_menu_portail.php');change_icon_onclick('plugins');\">Menu portail</a></li>";
					if (checkdroit("L-07-07") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/gestion_fichiers/voir_fichiers.php');change_icon_onclick('plugins');\">Gestion des fichiers</a></li>";
					if (checkdroit("L-07-08") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/migration_fog/voir_migration.php');change_icon_onclick('plugins');\">Migration Fog</a></li>";
					if (checkdroit("L-07-09") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/migration_dossiers/migration_dossiers.php');change_icon_onclick('plugins');\">Migration Dossiers</a></li>";
					echo "</ul>
				</li>";
			}
			
			
			
			if ( checkalldroits("L-08-01,L-08-02,L-08-03,L-08-04,L-08-05") ) {	
			echo "<li class='dir'><a href='#' onclick=\"AffichePage('conteneur', 'info.php');change_icon_onclick('info');\"><div id='info' class='info' title='info'></div></a>";
				echo "<ul>";
					if (checkdroit("L-08-01") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'gestion_college/voir_college.php'); change_icon_onclick('info');\">Fiche collège</a></li>";
					if (checkdroit("L-08-02") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/rss/rss.php'); change_icon_onclick('info');\">Flux RSS</a></li>";	
					if (checkdroit("L-08-03") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/stats/graph.php');change_icon_onclick('info');\">Stats camemberts</a></li>";
					if (checkdroit("L-08-04") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/stats/csschart.php');change_icon_onclick('info');\">Stats bâtons</a></li>";
					if (checkdroit("L-08-05") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/stats/utilisation_parc.php');change_icon_onclick('info');\">Stats utilisation du parc</a></li>";	
					if (checkdroit("L-08-06") ) echo "<li class='item'><a href='#' onclick=\"AffichePage('conteneur', 'modules/infoserveur/infoserveur.php');change_icon_onclick('info');\">Infos serveur</a></li>";
					echo "</ul>
				</li>";
			}
			
			
	?>

		</ul>
	</div>

	
