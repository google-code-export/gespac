<?PHP
	
	/* fichier de visualisation des logs des prets :
	
		view de la db gespac avec tous le matos pr�t� et rendu
		avec possibilit� de r��diter une convention
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

		var valida = confirm ("La suppression des logs va ex�cuter un dump automatique dans le fichier DUMP_LOGS.CSV. MERCI DE V�RIFIER QUE VOTRE FICHIER DUMP_LOGS.CSV N'EST PAS OUVERT !");
		
		// si la r�ponse est TRUE ==> on lance la page post_logs.php
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
	
	// adresse de connexion � la base de donn�es
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_prets 
	// SAlle_id = 3 (� la fin de la rq) parce que 3 correspond � la salle "PRETS"
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
					case "Suppression mat�riel"		: 	$td_color = "#ff7b7b";	break;
					case "Suppression marque"  		: 	$td_color = "#ff7b7c";	break;
					case "Suppression salle"   		: 	$td_color = "#ff7b7d";	break;
					case "Suppression compte"  		: 	$td_color = "#ff7b7e";	break;
					case "Cr�ation compte"	   		: 	$td_color = "#b3fffe";	break;
					case "Cr�ation salle"	   		: 	$td_color = "#b3ffff";	break;
					case "Cr�ation marque"	   		: 	$td_color = "#b3fffd";	break;
					case "Cr�ation mat�riel"   		: 	$td_color = "#b3fffc";	break;
					case "Cr�ation coll�ge"		   	: 	$td_color = "#b3fffb";	break;
					case "Cr�ation demande"		   	: 	$td_color = "#b3fffa";	break;
					case "Modification compte"	   	: 	$td_color = "#9aff9f";	break;
					case "Modification coll�ge"	   	: 	$td_color = "#9aff9e";	break;
					case "Modification salle"	   	: 	$td_color = "#9aff9d";	break;
					case "Modification mat�riel"   	: 	$td_color = "#9aff9c";	break;
					case "Modification marque"	   	: 	$td_color = "#9aff9b";	break;
					case "Affectation salle"	   	: 	$td_color = "#ffd20f";	break;
					case "Dump GESPAC"			   	: 	$td_color = "#c6baff";	break;
					case "Dump OCS"				   	: 	$td_color = "#c6bafe";	break;
					case "Dump LOGS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import OCS"			   	: 	$td_color = "#c6bafd";	break;
					case "Import IACA"			   	: 	$td_color = "#c6bafc";	break;
					case "Import CSV"			   	: 	$td_color = "#f1ff73";	break;
					case "Etat demande"			   	: 	$td_color = "#c6bafb";	break;
					case "Pr�t�"				   	: 	$td_color = "#f1ff73";	break;
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