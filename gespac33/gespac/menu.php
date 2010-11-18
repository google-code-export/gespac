<?PHP 

/*

	menu en haut de l'écran :

*/

/*inventaire
	visualiser les matériels
	visualiser les marques
	visualiser les salles	
demandes
	voir les demandes
	voir les interventions
	Faire une demande
données	
	Import via DB OCS
	Dump base GESPAC
	Dump base OCS
	Logs
prêts
	Prêter ou rendre
utilisateurs	
	Visualiser les users
	Importer les comptes IACA
plugins
	fog
	wol
	stat
collège
info*/

// Gestion de l'affichage des menus en fonction du grade 
$user_grade = $_SESSION ['grade'];

if ( $user_grade < 2 ) { // root + ati

	$accueil		= "";
	$inventaire		= "";
	$demandes		= "";
	$donnees		= "";
	$prets			= "";
	$utilisateurs	= "";
	$plugins		= "";
	$college		= "";
	$recap_fog		= "";
	$info			= "";
	$stats			= "";
	$export			= "";
}

if ( $user_grade == 2 ) { // tice
	$accueil		= "";
	$inventaire		= "";
	$demandes		= "none";
	$donnees		= "none";
	$prets			= "none";
	$utilisateurs	= "";
	$plugins		= "";
	$college		= "";
	$recap_fog		= "none";
	$info			= "";
	$stats			= "";
	$export			= "none";
}

if ( $user_grade > 2 ) { // profs ou autres
	$accueil		= "";
	$inventaire		= "none";
	$demandes		= "";
	$donnees		= "none";
	$prets			= "none";
	$utilisateurs	= "none";
	$plugins		= "none";
	$college		= "none";
	$recap_fog		= "none";
	$info			= "";
	$stats			= "none";
	$export			= "none";
}



?>

	<script type="text/javascript">
		window.addEvent('domready', function(){ 
			SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages'});
		});
	</script>
  
	<script>
		function change_icon_onclick (div) {
			
			// on désactive tous les boutons
			$('accueil').className = "accueil";
			$('inventaire').className = "inventaire";
			$('demandes').className = "demandes";
			$('donnees').className = "donnees";
			$('prets').className = "prets";
			$('utilisateurs').className = "utilisateurs";
			$('plugins').className = "plugins";
			//$('college').className = "college";
			$('info').className = "info";
			
			// On active le bon bouton
			$(div).className = div + "-clicked";
		}
	</script>

	
	<div id="main">
		<ul id="nav" class="dropdown dropdown-horizontal">
			
			<li class='dir'><a style='display:<?PHP echo $accueil;?>' href="#" onclick="AffichePage('conteneur', 'accueil.php?droit=');change_icon_onclick('accueil');"><div id="accueil" class="accueil" title="accueil"></div></a>
				<ul>
					<li class='item'><a href="../index.php">	Retour au portail	</a></li>
				</ul>
			</li>

			<li class='dir'><a style='display:<?PHP echo $inventaire;?>' href="#" onclick="AffichePage('conteneur', 'gestion_inventaire/voir_materiels.php');change_icon_onclick('inventaire');"><div id="inventaire" class="inventaire" title="inventaire"></div></a>
				<ul>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_inventaire/voir_materiels.php');change_icon_onclick('inventaire');">	Visualiser les matériels	</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_inventaire/voir_marques.php');change_icon_onclick('inventaire');">	Visualiser les marques		</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_inventaire/voir_salles.php');change_icon_onclick('inventaire');">	Visualiser les salles		</a></li>
				</ul>
			</li>

			<li class='dir'><a style='display:<?PHP echo $demandes;?>' href="#" onclick="AffichePage('conteneur', 'gestion_demandes/voir_demandes.php');change_icon_onclick('demandes');"><div id="demandes" class="demandes" title="demandes et interventions"></div></a>
				<ul>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_demandes/voir_demandes.php');change_icon_onclick('demandes');">		Voir les dossiers	</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_demandes/voir_interventions.php');change_icon_onclick('demandes');">	Voir les interventions	</a></li>
				</ul>
			</li>
			
			<li class='dir'><a style='display:<?PHP echo $donnees;?>' href="#"><div id="donnees" class="donnees" title="gestion des imports et exports de données"></div></a>
				<ul>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_donnees/voir_ocs_db.php');change_icon_onclick('donnees');">		Importer DB OCS		</a></li>			
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_donnees/exports.php');change_icon_onclick('donnees');">			Exports </a></li>			
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_donnees/dump_db_gespac.php');change_icon_onclick('donnees');">	Dump base GESPAC	</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_donnees/dump_db_ocs.php');change_icon_onclick('donnees');">		Dump base OCS		</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_donnees/voir_logs.php');change_icon_onclick('donnees');">		Voir les Logs		</a></li>
					<li class='item'><a href='gestion_inventaire/form_import_csv.php?height=600&width=640' rel='sexylightbox' title='Import machines CSV'>	Importer CSV	</a></li>				
				</ul>
			</li>
			
			<li class='dir'><a style='display:<?PHP echo $prets;?>' href="#" onclick="AffichePage('conteneur', 'gestion_prets/voir_prets.php');change_icon_onclick('prets');"><div id="prets" class="prets" title="preter ou rendre un matériel"></div></a></li>
				<ul>
					<li class='item'><a style='display:<?PHP echo $prets;?>' href="#" onclick="AffichePage('conteneur', 'gestion_prets/voir_prets.php');change_icon_onclick('prets');"><div id="prets" class="prets" title="preter ou rendre un matériel"></div></a></li>
				</ul>
			<li class='dir'><a style='display:<?PHP echo $utilisateurs;?>' href="#" onclick="AffichePage('conteneur', 'gestion_utilisateurs/voir_utilisateurs.php');change_icon_onclick('utilisateurs');"><div id="utilisateurs" class="utilisateurs" title="gestion des utilisateurs"></div></a>
				<ul>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_utilisateurs/voir_utilisateurs.php');change_icon_onclick('utilisateurs');">	Visualiser les utilisateurs		</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'gestion_utilisateurs/voir_grades.php');change_icon_onclick('utilisateurs');">	Visualiser les grades		</a></li>
					<li class='item'><a href='gestion_utilisateurs/form_comptes_iaca.php?height=200&width=640' rel='sexylightbox' title='Import des comptes IACA'>	Importer les comptes IACA	</a></li>
					<li class='item'><a href='gestion_utilisateurs/form_utilisateur_personnel.php?height=300&width=640' rel='sexylightbox' title='Modifier mon compte'>	Modifier mon compte	</a></li>
				</ul>
			</li>
			
			<li class='dir'><a style='display:<?PHP echo $plugins;?>' href="#"><div id="plugins" class="plugins" title="modules et extensions"></div></a>
				<ul>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'modules/fog/recap_fog.php');change_icon_onclick('plugins');">Récapitulatif FOG</a></li>
					<li class='item'><a href="#" onclick="AffichePage('conteneur', 'modules/wol/voir_liste_wol.php');change_icon_onclick('plugins');">Wake On Lan</a></li>
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'modules/export/export_perso.php');change_icon_onclick('info');">Export Perso</a></li>
				</ul>
			</li>
			
			<li class='dir'><a style='display:<?PHP echo $info;?>' href="#" onclick="AffichePage('conteneur', 'info.php');change_icon_onclick('info');"><div id="info" class="info" title="info"></div></a>
				<ul>
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'gestion_college/voir_college.php'); change_icon_onclick('info');">Fiche collège</a></li>
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'modules/rss/rss.php'); change_icon_onclick('info');">Flux RSS</a></li>	
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'modules/stats/graph.php');change_icon_onclick('info');">Stats camemberts</a></li>
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'modules/stats/csschart.php');change_icon_onclick('info');">Stats bâtons</a></li>
					<li class='item'><a style='display:<?PHP echo $stats;?>' href="#" onclick="AffichePage('conteneur', 'modules/stats/utilisation_parc.php');change_icon_onclick('info');">Stats utilisation du parc</a></li>	
				</ul>
			</li>

		</ul>
	</div>

	