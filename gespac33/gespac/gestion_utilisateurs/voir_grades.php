<?PHP
	session_start();
	
	/* fichier de visualisation des grades :
	
		vue de la db gespac avec tous les grades du parc
	*/
	
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	$E_chk = preg_match ("#E-06-02#", $_SESSION['droits']);		

?>


<h3>Visualisation des grades</h3>


<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 

	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_grades = $db_gespac->queryAll ( "SELECT grade_id, grade_nom, grade_menu, grade_niveau FROM grades ORDER BY grade_nom" );

?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'grades_table', '1')" type="text"></center>
	</form>
	
<?PHP
	// Ajout d'un grade
	if ( $E_chk )
		echo "<a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=-1' rel='sexylightbox' title='ajouter un grade'> <img src='img/add.png'>Ajouter un grade </a>";
?>

	<center>
	<br>
	<table class="tablehover" id="grades_table" width=600>
		<th>Niveau</th>
		<th>Nom</th>
		
		<?PHP	
		
			if ( $E_chk ) echo "<th>&nbsp</th><th>&nbsp</th><th>&nbsp</th>";
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_grades as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
						
					$grade_id 		= $record[0];
					$grade_nom 		= $record[1];
					$grade_menu 	= $record[2];
					$grade_niveau	= $record[3];
					
					$nb_users_du_grade = $db_gespac->queryOne ( "SELECT count(*) as compte FROM users WHERE grade_id=$grade_id" );
					
					echo "<td width=20> $grade_niveau </td>";
					echo "<td><a href='gestion_utilisateurs/voir_membre_grade.php?height=480&width=640&grade_id=$grade_id' rel='sexylightbox' title='membres du grade $grade_nom'>$grade_nom</a> [" . $nb_users_du_grade ."] </td>";
				
					if ( $E_chk ) {
						if ( $grade_nom <> "root" ) {
							echo "<td width=20><a href='gestion_utilisateurs/form_droits.php?height=650&width=640&id=$grade_id' rel='sexylightbox' title='Formulaire de modification des droits du grade $grade_nom'><img src='img/key.png'> </a></td>";
							echo "<td width=20><a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=$grade_id' rel='sexylightbox' title='Formulaire de modification du grade $grade_nom'><img src='img/write.png'> </a></td>";
							echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_grade($grade_id, '$grade_nom', this.parentNode.parentNode.rowIndex);\">	<img src='img/delete.png' title='supprimer $grade_nom'>	</a> </td>";
						} else {
							echo"<td width=20>&nbsp</td>
							<td width=20>&nbsp</td>
							<td width=20>&nbsp</td>";
						}
					}
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP

	// Ajout d'un grade
	if ( $E_chk )
		echo "<a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=-1' rel='sexylightbox' title='ajouter un grade'> <img src='img/add.png'>Ajouter un grade </a>";
	
	// On se déconnecte de la db
	$db_gespac->disconnect();
?>



<script type="text/javascript">
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages'});
	});
</script>

<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression d'un user
	//
	// *********************************************************************************
	
	function validation_suppr_grade (id, nom, row) {
	
		var valida = confirm('Voulez-vous vraiment supprimer le grade "' + nom + '" ?');
		// si la réponse est TRUE ==> on lance la page post_grades.php
		if (valida) {		
			/*	supprimer la ligne du tableau	*/
			$('grades_table').deleteRow(row);
			/*	poste la page en ajax	*/
			$('target').load("gestion_utilisateurs/post_grades.php?action=suppr&id=" + id);
		}
	}	

	
	
	// *********************************************************************************
	//
	//				Fonction de filtrage des tables
	//
	// *********************************************************************************

	function filter (phrase, _id){

		var words = phrase.value.toLowerCase().split(" ");
		var table = $(_id);
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

