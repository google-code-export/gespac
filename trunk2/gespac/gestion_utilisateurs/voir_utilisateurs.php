<?PHP
	session_start();
	
	/* 	
	 	fichier de visualisation des utilisateurs :
		vue de la db gespac avec tous les users du parc
	*/


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-06-01#", $_SESSION['droits']);
	
	
?>

<div class="entetes" id="entete-utilisateurs">	

	<span class="entetes-titre">LES UTILISATEURS<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des utilisateurs du parc.<br>On peut gérer ici les skins, le mailing et les grades des utilisateurs.</div>

	<span class="entetes-options">

		<span class="option"><?PHP if ( $E_chk ) { echo "<span id='nb_selectionnes' title=\"nombre d'utilisateurs sélectionnés\"></span>"; echo "<span id='modif_selection' style='display:none;'> <a href='gestion_utilisateurs/form_utilisateurs.php?height=200&width=640&action=modlot' rel='slb_users' title='modifier selection'> <img src='" . ICONSPATH . "modif1.png'></a></span>"; }?>  </span>
		
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_utilisateurs/form_utilisateurs.php?action=add' class='editbox' title='Ajouter un utilisateur'><img src='" . ICONSPATH . "add.png'></a>";?></span>
		
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform">
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'user_table');" type="text" value=<?PHP echo $_GET['filter'];?>> <span id="filtercount" title="Nombre de lignes filtrées"></span>
			</form>
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP 

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_utilisateurs = $con_gespac->QueryAll ( "SELECT user_nom, user_logon, user_password, grade_nom, user_mail, user_id, user_skin, user_mailing, users.est_modifiable FROM users, grades WHERE users.grade_id=grades.grade_id ORDER BY user_nom" );

?>
		
	<input type=hidden name='id_a_poster' id='id_a_poster' value=''>


	<center>

	<table class="bigtable hover" id="user_table">
		<th> <input type=checkbox id='checkall' > </th>
		<th>Nom</th>
		<th>Logon</th>
		<th>Grade</th>
		<th>Mail</th>
		<th>Skin</th>
		<th>Mailing</th>
		
		<?PHP	
		if ( $E_chk ) echo "<th>&nbsp</th><th>&nbsp</th>";

			foreach ( $liste_des_utilisateurs as $record ) {
				
						
				$nom 			= $record['user_nom'];
				$logon 			= $record['user_logon'];
				$password 		= $record['user_password'];
				$grade			= $record['grade_nom'];
				$mail 			= $record['user_mail'];
				$id				= $record['user_id'];
				$skin			= $record['user_skin'];
				$mailing		= $record['user_mailing'];
				$est_modifiable	= $record['est_modifiable'];
				
				$mailing_chk = $mailing == 1 ? "<img src='img/ok.png' height=16px width=16px>" : "";
					
				
				// test si la machine est prétée ou pas
				$rq_machine_pretee = $con_gespac->QueryAll ( "SELECT mat_id FROM materiels WHERE user_id=$id AND user_id<>1" );
				$mat_id = @$rq_machine_pretee['mat_id'];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
				
				// TODO : Virer la partie check des machines prêtées
				if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
						$id_pret = 0;
					} else {	// la machine est prêtée ($mat_id existe)
						$id_pret = 1;
				}
				

				// on affiche pas la checkbox pour les comptes dont le champ "est_modifiable" est TRUE
				if ( $E_chk && $est_modifiable) {
					echo "<tr id='tr_id$id' class='tr_modif'>";
						echo "<td> <input type=checkbox name=chk indexed=true id='$id' value='$id' class='chk_line'> </td>";	
				} else {
					echo "<tr id='tr_id$id' class='tr_nonmodif'>";
						echo "<td>&nbsp</td>";
				}
				
				
				echo $chk_box;
				echo "<td> $nom </td>";
				echo "<td> $logon </td>";
				echo "<td> $grade </td>";
				echo "<td> $skin </td>";
				echo "<td> $mail </td>";
				echo "<td> $mailing_chk </td>";
				
				if ( $E_chk && $est_modifiable) {
					echo "<td><a href='gestion_utilisateurs/form_utilisateurs.php?id=$id&action=mod' class='editbox' title='Modifier un utilisateur'><img src='" . ICONSPATH . "edit.png'> </a></td>";
					echo "<td width=20 align=center> <a href='gestion_utilisateurs/form_utilisateurs.php?action=del&id=$id' class='editbox' title='Supprimer un utilisateur'>	<img src='" . ICONSPATH . "delete.png' title='supprimer $nom'>	</a> </td>";
				} else {
					echo "<td>&nbsp</td>";
					echo "<td>&nbsp</td>";
				}
				
				echo "</tr>";

			}
		?>		

	</table>
	
	<br>
	
	</center>
	
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>



<script type="text/javascript">
	
	
	$(function(){
	
	
		//--------------------------------------- Selection d'une ligne
		
		$('.chk_line').click(function(){
			
			var id = $(this).attr('id');
			
			if ( $(this).is(':checked') ){		
				$('#id_a_poster').val( $('#id_a_poster').val() + ";" + id );
				$("#tr_id" + id).addClass("selected");
			}
			else {
				$('#id_a_poster').val( $('#id_a_poster').val().replace(";" + id + ";", ";") );	// Supprime la valeur au milieu de la chaine
				var re = new RegExp(";" + id + "$", "g"); $('#id_a_poster').val( $('#id_a_poster').val().replace(re, "") );			// Supprime la valeur en fin de la chaine
				$("#tr_id" + id).removeClass("selected");
				$('#checkall').prop("checked", false);
			}
			
			// On affiche les boutons
			if ( $('#id_a_poster').val() != "" ) {
				$('#modif_selection').show();	$('#affect_selection').show();				
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
			} else { 
				$('#modif_selection').hide(); $('#affect_selection').hide(); $('#nb_selectionnes').hide();
			}
			
		});
		
		
		
		//--------------------------------------- Selection de toutes les lignes
		
		$('#checkall').click(function(){
			
			if ( $('#checkall').is(':checked') ){		
				
				$('.chk_line:visible').prop("checked", true);	// On coche toutes les cases visibles

				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line:visible').each (function(){$('#id_a_poster').val( $('#id_a_poster').val() + ";" + $(this).attr('id') );	});	// On alimente le input à poster
				
				$('#modif_selection').show(); $('#affect_selection').show();		// On fait apparaitre les boutons
				$('#nb_selectionnes').show(); $('#nb_selectionnes').html( $('.chk_line:checked').length + ' sélectionné(s)');
				$('.tr_modif:visible').addClass("selected");	// On colorie toutes les lignes	visibles
			}
			else {
				$('#id_a_poster').val("");	// On vide les matos à poster
				$('.chk_line').prop("checked", false);	// On décoche toutes les cases
				$('.tr_modif').removeClass("selected");	// On vire le coloriage de toutes les lignes	
				$('#modif_selection').hide();	$('#rename_selection').hide(); $('#affect_selection').hide(); $('#nb_selectionnes').hide();
			}			
		});	
		
		
	});
	
	

	// Filtre rémanent
	filter ( $('#filt').val(), 'user_table' );
	

</script>

