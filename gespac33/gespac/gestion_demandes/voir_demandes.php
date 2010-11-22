<?PHP

/*

	- user fait une demande
		-> dem_etat passe de "" � "� r�pondre"
		-> envoi d'un mail � l'ati avec le contenu de la demande
	
	- l'ati ou autre r�pond � la demande
		-> il peut demander une pr�cision sur la demande
		-> il peut d�cliner la demande 
			-> expliquer la r�ponse
			-> cloture de la demande
				-> changement de l'�tat � cloturer
		-> il peut accepter la demande
			-> changement d'�tat � "en cours d'inter"


	- les demandes sont entass�es dans le champ dem_text
		-> c'est plus lourd � g�rer qu'une table des textes associ�e � la table des demandes, mais bon, ca suffira pour le moment 

		
	- La cr�ation de la demande par un user :
		-> type de la demande
			* installation
			* r�paration
			* usages
			* formation
		-> si installation ou r�paration on demande
			* la salle 
			* l'ordi (mettre dans le menu une ligne "toute la salle")
			
*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...

?>

<h3>Visualisation des dossiers</h3>

<script type="text/javascript" src="server.php?client=all"></script>

<!--	DIV target pour Ajax	-->
<div id="target"></div>


	<?PHP 

		// adresse de connexion � la base de donn�es
		$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

		// cnx � la base de donn�es GESPAC
		$db_gespac 	= & MDB2::factory($dsn_gespac);

		$liste_des_demandes = $db_gespac->queryAll ( "SELECT dem_id, dem_date, dem_text, dem_etat, dem_type, user_demandeur_id, user_intervenant_id, user_nom FROM demandes, users WHERE demandes.user_demandeur_id=users.user_id ORDER BY dem_date DESC" );
		
	?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'demandes_table', '1')" type="text"></center>
	</form>
	
	<center><small><a href='#' id="masque_montre" onclick="montre_masque_dossiers_clotures();" title="masque">masquer/montrer les dossiers clos</a></small></center>
	
	<?PHP echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/form_demandes.php?id=-1');\"> <img src='img/add.png'>Ouvrir un dossier </a>"; ?>

	<center>
	
	<table class="tablehover" id="demandes_table" width=800>
	
		<th>num</th>
		<th>date</th>
		<th>�tat</th>
		<th>type</th>
		<th>demandeur</th>
		<th>salle</th>
		<th>mat</th>		
		<th>texte</th>
		<th>&nbsp</th>
		
		<?PHP	
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_demandes as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
				
					$dem_id 				= $record[0];
					$dem_date 				= $record[1];
					$dem_text 				= $record[2];
					$dem_etat 				= $record[3];
					$dem_type 				= $record[4];
					$user_demandeur_id 		= $record[5];
					$user_intervenant_id 	= $record[6];
					$user_demandeur_nom		= $record[7];
					
					
					// On r�cup�re la salle et le materiel si c'est une installation ou une reparation
					if ( $dem_type == "installation" || $dem_type == "reparation" ) {
						$rq_extraction_salle_mat = $db_gespac->queryAll ( "SELECT demandes.mat_id, demandes.salle_id, salle_nom FROM demandes, salles, users WHERE salles.salle_id=demandes.salle_id AND demandes.user_demandeur_id=users.user_id AND dem_id=$dem_id" );

						$mat_id 	= $rq_extraction_salle_mat [0][0];
						$salle_id 	= $rq_extraction_salle_mat [0][1];
						$salle_nom 	= $rq_extraction_salle_mat [0][2];
						
						// On r�cup�re le nom du mat�riel
						if ( $mat_id <> 0) {
							$liste_nom_materiel = $db_gespac->queryAll ( "SELECT mat_nom FROM materiels WHERE mat_id=$mat_id" );
							$mat_nom = $liste_nom_materiel[0][0];
						}
						else {	$mat_nom = "TOUS";	}
						
					} else {
						$mat_nom = "NA";
						$salle_nom = "NA";
					}
					
					
					// On change la couleur quand le dossier est clos et on masque la case de modification
					if ( $dem_etat == "clos" ) {
						$etat_couleur = "#F57236";
						$hidemodif = "none";
					} else {
						$etat_couleur = "";
						$hidemodif = "";
					}
					
														
					echo "<td> <a href='gestion_demandes/voir_dossier.php?height=480&width=640&id=$dem_id' rel='sexylightbox' title='voir le dossier $dem_id'> <img src='img/loupe.gif'>$dem_id</a> </td>";					
					echo "<td> $dem_date </td>";
					echo "<td bgcolor=$etat_couleur> $dem_etat </td>";
					echo "<td> $dem_type </td>";
					echo "<td> $user_demandeur_nom </td>";
					echo "<td> $salle_nom  </td>";
					echo "<td> $mat_nom </td>";
					echo "<td> $dem_text </td>";
					echo "<td width=20 align=center> <a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/form_demandes.php?id=$dem_id');\" style='display:$hidemodif;'>	<img src='img/write.png' title='g�rer la demande $dem_id'>	</a> </td>";
				
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP

	echo "<a href='#' onclick=\"AffichePage('conteneur', 'gestion_demandes/form_demandes.php?id=-1');\"> <img src='img/add.png'>Ouvrir un dossier </a>";

	// On se d�connecte de la db
	$db_gespac->disconnect();

?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages'});
	});
</script>

<script type="text/javascript">	
	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		var elements_liste = "";
				
		for (var r = 1; r < table.rows.length; r++){
			
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0) {	// la phrase de recherche est reconnue
					displayStyle = '';
				}	
				else {	// on masque les rows qui ne correspondent pas
					displayStyle = 'none';
					break;
				}
			}
			
			// Affichage on / off en fonction de displayStyle
			table.rows[r].style.display = displayStyle;	
		}
	}	

		
	
	// *********************************************************************************
	//
	//				Masque / Montre les dossiers clotur�s
	//
	// *********************************************************************************

	function montre_masque_dossiers_clotures () {

		var table = document.getElementById("demandes_table");
		var lien = document.getElementById("masque_montre");
		
		if ( lien.title == "masque" ) {		// on masque les rows "cloturer" 
			
			lien.title = "affiche"
			
			for (var r = 1; r < table.rows.length; r++) {
				
				if (table.rows[r].cells[2].innerHTML == " clos " )	// attention aux espaces avant et apr�s !
					displayStyle = "none";			
				else
					displayStyle = "";

				// Affichage on / off en fonction de displayStyle
				table.rows[r].style.display = displayStyle;	
			}
		
		} else {	// On affiche toutes les rows
			
			lien.title = "masque"
			
			for (var r = 1; r < table.rows.length; r++) {
				// Affichage de toutes les rows du tableau
				table.rows[r].style.display = "";	
			}
		}

	}	
</script>
