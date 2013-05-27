<?PHP
	session_start();

/*

	Page de visualisation des dossiers
	

*/


	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-03-03#", $_SESSION['droits']);
		
	
	$filtre = $_GET["filter"];	
	$showclos = $_GET["showclos"];	
?>


<div class="entetes" id="entete-dossiers">	

	<span class="entetes-titre">LES DOSSIERS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer les dossiers, leur création, modification et suppression.</div>

	<span class="entetes-options">
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_dossiers/form_dossiers.php?action=add' class='editbox' title='Ajouter un dossier'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		
		<span class="option"><?PHP 
			if (!$showclos) echo "<a href='index.php?page=dossiers&showclos=1' title='Montrer les dossiers clos'> <img src='" . ICONSPATH . "eye.png'></a>";
			else echo "<a href='index.php?page=dossiers' title='Cacher les dossiers clos'> <img src='" . ICONSPATH . "eye.png'></a>";?></span>
			
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'dossiers_table');" type="text" value=<?PHP echo $_GET['filter'];?>> </form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 
	$liste_dossiers = $con_gespac->QueryAll ("SELECT dossiers.dossier_id as dossier_id, dossier_type, dossier_mat, txt_date, txt_etat, txt_texte FROM dossiers, dossiers_textes WHERE dossiers.dossier_id=dossiers_textes.dossier_id GROUP BY dossiers.dossier_id ORDER BY dossier_id DESC;");
		
	echo "<table id='dossiers_table' class='bigtable hover alternate'>";
	
		echo "<th>dossier</th>";
		echo "<th>date</th>";
		echo "<th>type</th>";
		echo "<th>etat</th>";
		echo "<th>commentaire</th>";
		echo "<th>&nbsp;</th>";
		echo "<th>&nbsp;</th>";
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		if ( $E_chk ) echo "<th>&nbsp;</th>";	
		

		foreach ( $liste_dossiers as $dossier) {
			
			$dossier_id 	= $dossier['dossier_id'];
			$dossier_type 	= stripslashes($dossier['dossier_type']);
			$dossier_mat 	= $dossier['dossier_mat'];
			$date_ouverture = date ("d-m-Y H:i", strtotime($dossier['txt_date']));
			$txt_texte 		= stripslashes($dossier['txt_texte']);
			
			$last_etat		= $con_gespac->QueryOne("SELECT txt_etat FROM dossiers_textes WHERE dossier_id=$dossier_id ORDER BY txt_date DESC");
			
			// Voir aussi les dossiers clos
			if ( $showclos || $last_etat <> "clos" ) {
				
				// Le dossier dans son dernier état
				
				echo "<tr id='tr_$dossier_id'>";
					echo "<td width=20px>$dossier_id</td>";
					echo "<td width=100px>$date_ouverture</td>";
					echo "<td width=60px>$dossier_type</td>";
					echo "<td width=60px class='td_$last_etat'>$last_etat</td>";
					echo "<td>$txt_texte</td>";
					echo "<td width=20px><a href='gestion_dossiers/fiches_dossier.php?maxheight=600&dossier=$dossier_id' class='infobox' title='Fiches du dossier $dossier_id'><img src='" . ICONSPATH . "icon_eye.png'></a></td>";
					echo "<td width=20px><a href='gestion_dossiers/liste_materiels.php?maxheight=600&dossier=$dossier_id' class='editbox' title='Liste des matériels du dossier $dossier_id'><img src='" . ICONSPATH . "list.png'></a></td>";
					if ( $E_chk && $last_etat<>"clos") echo "<td width=20px><a href='gestion_dossiers/form_dossiers.php?id=$dossier_id&action=mod' class='editbox' title='Modifier le dossier $dossier_id'> <img src='" . ICONSPATH . "edit.png'> </a></td>";
					else echo "<td>&nbsp;</td>";
					if ( $E_chk ) echo "<td width=20px><a href='gestion_dossiers/form_dossiers.php?action=del&id=$dossier_id' class='editbox' title='Supprimer un dossier'> <img src='" . ICONSPATH . "delete.png'> </a></td>";
					else echo "<td>&nbsp;</td>";
				
				echo "</tr>";
			}
		
		}
		
		echo "</table>";
	
?>

<script type="text/javascript">

	// Filtre rémanent	
	filter ( $('#filt').val(), 'dossiers_table' );
	
</script>
