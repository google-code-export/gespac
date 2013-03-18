<?PHP
	session_start();
	
	/* fichier de visualisation des grades :
	
		vue de la db gespac avec tous les grades du parc
	*/
	
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// si le grade du compte est root, on donne automatiquement les droits d'acc�s en �criture. Sinon, on teste si le compte a acc�s � la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-06-02#", $_SESSION['droits']);
	

?>


<h3>Visualisation des grades</h3>


<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 

	// cnx � la base de donn�es GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
	$liste_des_grades = $con_gespac->QueryAll ( "SELECT grade_id, grade_nom, grade_menu, est_modifiable FROM grades ORDER BY grade_nom" );

?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'grades_table', '1')" type="text"></center>
	</form>
	
<?PHP
	// Ajout d'un grade
	if ( $E_chk )
		echo "<a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=-1' rel='slb_grades' title='ajouter un grade'> <img src='img/add.png'>Ajouter un grade </a>";
?>

	<center>
	<br>
	<table class="tablehover" id="grades_table" width=450>
		<th>Nom</th>
		
		<?PHP	
		
			if ( $E_chk ) echo "<th>&nbsp</th><th>&nbsp</th><th>&nbsp</th><th>&nbsp</th>";
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_grades as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
						
					$grade_id 			= $record['grade_id'];
					$grade_nom 			= $record['grade_nom'];
					$grade_menu 		= $record['grade_menu'];
					$est_modifiable 	= $record['est_modifiable'];
					
					$nb_users_du_grade = $con_gespac->QueryOne ( "SELECT count(*) as compte FROM users WHERE grade_id=$grade_id" );
					
					echo "<td><a href='gestion_utilisateurs/voir_membre_grade.php?height=480&width=640&grade_id=$grade_id' rel='slb_grades' title='membres du grade $grade_nom'>$grade_nom</a> [" . $nb_users_du_grade ."] </td>";
				
					if ( $E_chk && $est_modifiable ) {
						echo "<td width=20><a href='gestion_utilisateurs/form_menu_portail.php?height=450&width=640&id=$grade_id' rel='slb_grades' title='Formulaire de modification du menu portail du grade $grade_nom'><img src='img/home.png'> </a></td>";
						echo "<td width=20><a href='gestion_utilisateurs/form_droits.php?height=650&width=640&id=$grade_id' rel='slb_grades' title='Formulaire de modification des droits du grade $grade_nom'><img src='img/key.png'> </a></td>";
						echo "<td width=20><a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=$grade_id' rel='slb_grades' title='Formulaire de modification du grade $grade_nom'><img src='img/write.png'> </a></td>";
						echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_grade($grade_id, '$grade_nom', this.parentNode.parentNode.rowIndex);\">	<img src='img/delete.png' title='supprimer $grade_nom'>	</a> </td>";
					} else {
						echo"<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>";
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
		echo "<a href='gestion_utilisateurs/form_grades.php?height=200&width=640&id=-1' rel='slb_grades' title='ajouter un grade'> <img src='img/add.png'>Ajouter un grade </a>";
	
	// On se d�connecte de la db
	$con_gespac->Close ();
?>



<script type="text/javascript">
	
	window.addEvent('domready', function(){
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_grades'});
	});

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	
	// *********************************************************************************
	//
	//				Fonction de validation de la suppression d'un user
	//
	// *********************************************************************************
	
	function validation_suppr_grade (id, nom, row) {
	
		var valida = confirm('Voulez-vous vraiment supprimer le grade "' + nom + '" ?');
		// si la r�ponse est TRUE ==> on lance la page post_grades.php
		if (valida) {		
			/*	supprimer la ligne du tableau	*/
			$('grades_table').deleteRow(row);
			/*	poste la page en ajax	*/
			$('target').load("gestion_utilisateurs/post_grades.php?action=suppr&id=" + id);
			window.setTimeout("$('conteneur').load('gestion_utilisateurs/voir_grades.php');", 1500);
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
