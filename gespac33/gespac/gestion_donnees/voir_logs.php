<?PHP
	
	/* fichier de visualisation des logs des prets :
	
		view de la db gespac avec tous le matos prêté et rendu
		avec possibilité de rééditer une convention
	*/
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
?>


<h3>Suivi des logs</h3>

<br>


<script type="text/javascript">	
	
	// init de la couleur de fond
	document.getElementById('conteneur').style.backgroundColor = "#fff";
	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression des logs
	//
	// *********************************************************************************
	
	function validation_suppr_logs () {

		var valida = confirm ("La suppression des logs va exécuter un dump automatique dans le fichier DUMP_LOGS.CSV. MERCI DE VÉRIFIER QUE VOTRE FICHIER DUMP_LOGS.CSV N'EST PAS OUVERT !");
		
		// si la réponse est TRUE ==> on lance la page post_logs.php
		if (valida) {
			AffichePage('conteneur', 'gestion_donnees/dump_logs.php');
			
			//	poste la page en ajax	
			$("target").load("gestion_donnees/post_logs.php");
			window.setTimeout("$('conteneur').load('gestion_donnees/voir_logs.php');", 1000);
		}
	}		
	
	
	
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
	
</script>	


<?PHP
	
	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_prets 
	// SAlle_id = 3 (à la fin de la rq) parce que 3 correspond à la salle "PRETS"
	$liste_des_prets = $db_gespac->queryAll ( "SELECT log_date, log_type, log_texte FROM logs ORDER BY log_date DESC" );	

?>
	
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'log_prets_table', '1')" type="text"></center>
	</form>
	
	
	<center>
	
	<table id="log_prets_table" width=850>
	
		<th>Type</th>
		<th>Date</th>
		<th>Objet du log</th>
	
		<?PHP	

			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_prets as $record ) {
	
				$date 		= $record[0];
				$type 		= $record[1];
				$texte		= $record[2];

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr3" : "tr4";
						
				// couleur operation
				switch ($type) {
					case "Suppression matériel"		: 	$td_color = "#ff7b7b";	break;
					case "Suppression marque"  		: 	$td_color = "#ff7b7c";	break;
					case "Suppression salle"   		: 	$td_color = "#ff7b7d";	break;
					case "Suppression compte"  		: 	$td_color = "#ff7b7e";	break;
					case "Création compte"	   		: 	$td_color = "#b3fffe";	break;
					case "Création salle"	   		: 	$td_color = "#b3ffff";	break;
					case "Création marque"	   		: 	$td_color = "#b3fffd";	break;
					case "Création matériel"   		: 	$td_color = "#b3fffc";	break;
					case "Création collège"		   	: 	$td_color = "#b3fffb";	break;
					case "Création demande"		   	: 	$td_color = "#b3fffa";	break;
					case "Modification compte"	   	: 	$td_color = "#9aff9f";	break;
					case "Modification collège"	   	: 	$td_color = "#9aff9e";	break;
					case "Modification salle"	   	: 	$td_color = "#9aff9d";	break;
					case "Modification matériel"   	: 	$td_color = "#9aff9c";	break;
					case "Modification marque"	   	: 	$td_color = "#9aff9b";	break;
					case "Affectation salle"	   	: 	$td_color = "#ffd20f";	break;
					case "Dump GESPAC"			   	: 	$td_color = "#c6baff";	break;
					case "Dump OCS"				   	: 	$td_color = "#c6bafe";	break;
					case "Dump LOGS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import OCS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import IACA"			   	: 	$td_color = "#c6bafc";	break;
					case "Import CSV"			   	: 	$td_color = "#f1ff73";	break;
					case "Etat demande"			   	: 	$td_color = "#c6bafb";	break;
					case "Prêté"				   	: 	$td_color = "#f1ff73";	break;
					case "Rendu"				   	: 	$td_color = "#2f7bff";	break;
				}
				
					
				echo "<tr class=$tr_class>";									
					echo "<td bgcolor=$td_color> $type </td>";
					echo "<td> $date </td>";
					echo "<td align=left>&nbsp $texte </td>";
				echo "</tr>";
				
				$compteur++;				
			}
		?>		

	</table>
	
	<form>
		<br>
		<input type=button value="vider les logs" onClick="validation_suppr_logs();">
	</form>
	
	</center>
	
	<br>