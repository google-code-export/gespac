<?PHP
	session_start();
	
	/* fichier de visualisation des prets :
	
		view de la db gespac avec tous le matos du parc qui peut être prêté UNIQUEMENT
	*/


	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-05-01#", $_SESSION['droits']);
	
?>


<div class="entetes" id="entete-prets">	

	<span class="entetes-titre">LE PRET de MATERIEL<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer le prêt du matériel du parc.<br>Le matériel doit être dans la salle PRETS pour être affiché ici.</div>

	<span class="entetes-options">
		
		<span class="option">
			<!--	FORMULAIRE DE PRET AUX USERS 	-->
			<form id=elements_selectionnes method="post">
				
				<?PHP
				if ( $E_chk ) {
				?>
				
				
				<!--	ID du pret à poster	-->
				<input type=hidden name=pret_a_poster id=pret_a_poster value=''>					
				
				
				
				<!--------------------------------------------------------------------
				!		PARTIE POUR PRETER UN MATERIEL 
				--------------------------------------------------------------------->
				
				
				<div id="preter" style="display:none; text-align:center" > 
					Prêter à : &nbsp
					
					<select id=user_select>
				
					<?PHP 
						// Pour le remplissage de la combobox des user pour l'affectation du matériel prêté
							
						// stockage des lignes retournées par sql dans un tableau nommé combo_des_users
						$combo_des_users = $con_gespac->QueryAll ( "SELECT user_id, user_nom FROM users ORDER BY user_nom;" );
									
						foreach ($combo_des_users as $combo_option ) {
						
							$option_id 		= $combo_option['user_id'];
							$option_user 	= $combo_option['user_nom'];
												
							echo "<option value=$option_id name=$option_user> $option_user </option>";
						}
					?>
					
					</select>
					
					<?PHP echo "<a id='preter_bt' href='gestion_prets/form_prets.php?action=pret&mat=0&user=0' class='editbox' title='PRETER le matériel'><img src='" . ICONSPATH . "refresh.png'></a> "; ?>
					
				</div>
				
				
				<!--------------------------------------------------------------------
				!					PARTIE POUR RENDRE UN MATERIEL 
				--------------------------------------------------------------------->
				
				<div id="rendre" style="display:none; text-align:center">		
					<?PHP echo "<a id='rendre_bt' href='gestion_prets/form_prets.php?action=rendre&mat=0' class='editbox' title='RENDRE le matériel'><img src='" . ICONSPATH . "refresh.png'></a>"; ?>
				</div>				
				<?PHP } // fin test de droit sur le prêt ?>
				
			</form>
		</span>
		<span class="option">
			<!-- 	bouton pour le filtrage du tableau	-->
			<form id="filterform"> 
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'prets_table');" type="text" value=<?PHP echo $_GET['filter'];?>> 
				<span id="filtercount" title="Nombre de lignes filtrées"></span>
			</form>
			
		</span>
	</span>

</div>

<div class="spacer"></div>


<?PHP

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac ) ;

	// liste des matos dans la salle "prets"
	$liste_des_prets = $con_gespac->QueryAll ( "SELECT mat_nom, mat_serial, marque_type, marque_model, salle_nom, user_nom, mat_id, materiels.salle_id, materiels.user_id, mat_dsit, mat_etat FROM materiels, marques, salles, users WHERE ( materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id and materiels.user_id=users.user_id and salles.salle_nom='PRETS'	) ORDER BY mat_nom" );	

?>

	
	<center>
	
	<table class="bigtable alternate hover" id="prets_table">
	
	<?PHP
		if ($E_chk) echo "<th> &nbsp </th>";
	?>
		<th>Nom</th>
		<th>DSIT</th>
		<th>Type</th>
		<th>Modèle</th>
		<th>Etat</th>
		<th>Prêté à...</th>
		<th style="display:none"></th>
	
		<?PHP	

			// On parcourt le tableau
			foreach ( $liste_des_prets as $record ) {
								
				$mat 		= $record['mat_nom'];
				$serial 	= $record['mat_serial'];
				$type 		= $record['marque_type'];
				$model 		= $record['marque_model'];
				$salle 		= $record['salle_nom'];
				$user	 	= $record['user_nom'];
				$mat_id		= $record['mat_id'];
				$salle_id	= $record['salle_id'];
				$user_id	= $record['user_id'];
				$inventaire	= $record['mat_dsit'];
				$etat		= $record['mat_etat'];
				
				echo "<tr id='tr$mat_id'>";			
						
					// couleurs et noms					
					if ( $user_id == 1 ) {
						$apreter_color = "#36F572";
						$user = "DISPONIBLE";
					} else { $apreter_color = "#F57236"; }
					
					if ( $E_chk ) {
						echo "<td> <input type='radio' name='radio' class='radio' value='$mat_id'> </td>";
					}
					
					echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$mat&mat_ssn=$serial' class='infobox' title='Fiche du matériel $mat'>$mat</a> </td>";
					
					echo "<td class='inventaire'> $inventaire </td>";
					echo "<td class='type'> $type </td>";
					echo "<td class='model'> $model </td>";
					echo "<td class='etat'> $etat </td>";
					echo "<td bgcolor=$apreter_color><a href='gestion_prets/convention_pret.php?matid=$mat_id&userid=$user_id' target=_blank> $user </a></td>";
					
					echo "<td style='display:none' class='nom'>$mat</td>"; //permet de récupérer juste le nom de la machine pour les fonctions JS de prêt et rendu des machines
					echo "<td style='display:none' class='user' id='user$mat_id'>$user</td>"; //permet de récupérer juste le nom de l'utilisateur
		
				echo "</tr>";
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>



<script type="text/javascript">

	
	// Filtre rémanent
	filter ( $('#filt').val(), 'prets_table' );
	
	
	$(function() {	
		
		// ----------------------------------------------- Sur modification du combobox des users
		
		$('#user_select').change(function(){
				
			$('#preter_bt').attr("href", function(i,a){			
				var str = a.replace( /(mat=)[0-9]+/ig, '$1'+ $('#pret_a_poster').val() );
				var str = str.replace( /(user=)[0-9]+/ig, '$1'+ $('#user_select').val() );
				return str;
			});	
		});
		
		
		
		// ----------------------------------------------- Sur choix d'une ligne de matériel
		
		$('.radio').click(function(e){
			
			$('#pret_a_poster').val($(this).val());	// ID du matériel à prêter
				
		
			// On se base sur la valeur du champ caché 'user' pour afficher PRETER ou RENDRE
			if ( $('#user' + $(this).val()).html() == 'DISPONIBLE' ) {	
				$('#rendre').hide();
				$('#preter').show();
				
				$('#preter_bt').attr("href", function(i,a){			
					var str = a.replace( /(mat=)[0-9]+/ig, '$1'+ $('#pret_a_poster').val() );
					var str = str.replace( /(user=)[0-9]+/ig, '$1'+ $('#user_select').val() );
					return str;
				});	
				
			} else {
				$('#rendre').show();
				$('#preter').hide();
				
				$('#rendre_bt').attr("href", function(i,a){			
					var str = a.replace( /(mat=)[0-9]+/ig, '$1'+ $('#pret_a_poster').val() );
					return str;
				});	
				
			}	
	
		});
	
	});
	
	
</script>

