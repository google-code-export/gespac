<?PHP
	session_start();
	
	/* fichier de visualisation des grades :
	
		vue de la db gespac avec tous les grades du parc
	*/
	

	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-06-02#", $_SESSION['droits']);
	

?>

<div class="entetes" id="entete-grades">	

	<span class="entetes-titre">LES GRADES<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Les grades sont des groupes d'utilisateurs.<br>A chaque grade on peut affecter une liste de droits en lecture/écriture sur les pages ainsi que le contenu du portail.</div>

	<span class="entetes-options">

		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_utilisateurs/form_grades.php?action=add' class='editbox' title=\"Ajouter un grade\"><img src='" . ICONSPATH . "add.png'></a>";?></span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'grades_table');" type="text" value=<?PHP echo $_GET['filter'];?>>  <span id="filtercount" title="Nombre de lignes filtrées"></span>
				<span id="nb_filtre" title="nombre d'utilisateurs affichés"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_grades = $con_gespac->QueryAll ( "SELECT grade_id, grade_nom, grade_menu, est_modifiable FROM grades ORDER BY grade_nom" );

?>
	
	<center>

	<table class="bigtable hover" id="grades_table">
		<th>Nom</th>
		
		<?PHP	
		
			if ( $E_chk ) echo "<th>&nbsp</th><th>&nbsp</th><th>&nbsp</th><th>&nbsp</th>";
			
			foreach ( $liste_des_grades as $record ) {

				echo "<tr>";
						
					$grade_id 			= $record['grade_id'];
					$grade_nom 			= $record['grade_nom'];
					$grade_menu 		= $record['grade_menu'];
					$est_modifiable 	= $record['est_modifiable'];
					
					$nb_users_du_grade = $con_gespac->QueryOne ( "SELECT count(*) as compte FROM users WHERE grade_id=$grade_id" );
					
					echo "<td><a href='gestion_utilisateurs/voir_membre_grade.php?maxheight=650&grade_id=$grade_id' class='infobox' title='membres du grade $grade_nom'>$grade_nom</a> [" . $nb_users_du_grade ."] </td>";
				
					if ( $E_chk && $est_modifiable ) {
						echo "<td width=20><a href='gestion_utilisateurs/form_menu_portail.php?maxheight=650&id=$grade_id' class='editbox' title='Modification du menu portail du grade $grade_nom'><img src='" . ICONSPATH . "home.png'> </a></td>";
						echo "<td width=20><a href='gestion_utilisateurs/form_droits.php?maxheight=650&id=$grade_id' class='editbox' title='Modification des droits du grade $grade_nom'><img src='" . ICONSPATH . "unlocked.png'> </a></td>";
						echo "<td width=20><a href='gestion_utilisateurs/form_grades.php?id=$grade_id&action=mod' class='editbox' title='Modification du grade $grade_nom'><img src='" . ICONSPATH . "edit.png'> </a></td>";
						echo "<td width=20 align=center> <a href='gestion_utilisateurs/form_grades.php?id=$grade_id&action=del' class='editbox' title='Supprimer $grade_nom'>	<img src='" . ICONSPATH . "delete.png'>	</a> </td>";
					} else {
						echo"<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>
						<td width=20>&nbsp</td>";
					}
					
				echo "</tr>";
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close ();
?>



<script type="text/javascript">

	// Filtre rémanent
	filter ( $('#filt').val(), 'grades_table' );
	
	
</script>

