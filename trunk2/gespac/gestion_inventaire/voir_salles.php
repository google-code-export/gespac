<?PHP
session_start();

	/*
		PAGE 02-03
	
		Visualisation des salles	
	*/

	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-02-03#", $_SESSION['droits']);
?>

<div class="entetes" id="entete-salles">	

	<span class="entetes-titre">LES SALLES<img class="help-button" src="<?PHP echo ICONSPATH . "info.png";?>"></span>
	<div class="helpbox">Cette page permet de gérer l'ajout, la modification et la suppression des salles du parc.<br>Certaines salles, comme PRETS ou STOCK sont bloquées car elles ont un rôle particulier.</div>

	<span class="entetes-options">
		
		<span class="option" id="viderd3e"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?action=d3e' class='editbox' title='Vider la salle D3E'><img src='" . ICONSPATH . "refresh.png'></a>"; ?></span>
		<span class="option"><?PHP if ( $E_chk ) echo "<a href='gestion_inventaire/form_salles.php?action=add' class='editbox' title='Ajouter une salle'> <img src='" . ICONSPATH . "add.png'></a>";?></span>
		<span class="option">
			<form id="filterform"> <input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this.value, 'salle_table');" type="text" value="<?PHP echo $_GET['filter'];?>"><span id="filtercount" title="Nombre de lignes filtrées"></span></form>
		</span>
	</span>

</div>

<div class="spacer"></div>

<?PHP 

	// Connexion à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_salles = $con_gespac->QueryAll ( "SELECT salle_id, salle_nom, salle_vlan, salle_etage, salle_batiment, est_modifiable FROM salles ORDER BY salle_nom" );

?>
	
	<table class="alternate hover bigtable" id='salle_table'>
		<th>Nom</th>
		<th>VLAN</th>
		<th>Etage</th>
		<th>Bâtiment</th>
				
		
		<?PHP	
			if ($E_chk) echo"<th>&nbsp</th>	<th>&nbsp</th>";

			//$option_id = 0;
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_salles as $record ) {
								
				echo "<tr class='alternate'>";
						
					$id		 		= $record['salle_id'];
					$nom	 		= $record['salle_nom'];
					$vlan 			= $record['salle_vlan'];
					$etage 			= $record['salle_etage'];
					$batiment 		= $record['salle_batiment'];
					$est_modifiable = $record['est_modifiable'];
					
					// valeur nominale pour la checkbox
					$chkbox_state = $apreter == 1 ? "checked" : "unchecked";
					
					// On récupère la valeur inverse pour la poster
					$change_apreter = $apreter == 1 ? 0 : 1;
					
					//faire un queryOne
					$nb_matos_dans_cette_salle 	= $con_gespac->QueryOne ( "SELECT COUNT(*) FROM materiels WHERE salle_id=$id" );
					
					// On affiche le bouton pour vider le D3E que si la salle contient du matos
					if ($nom == "D3E" && $nb_matos_dans_cette_salle <= 0) echo "<script>$('#viderd3e').hide();</script>";
					
					echo "<td><a href='gestion_inventaire/voir_membres_salle.php?salle_id=$id&maxheight=650' class='editbox' title='membres de la salle $nom'>$nom</a> [" . $nb_matos_dans_cette_salle ."] </td>";
					echo "<td>" . $vlan . "</td>";
					echo "<td>" . $etage . "</td>";
					echo "<td>" . $batiment . "</td>";
					
					
					if ( $E_chk && $est_modifiable ) {
						echo "<td class='buttons'><a href='gestion_inventaire/form_salles.php?action=mod&id=$id' class='editbox' title='Modification de la salle $nom'><img src='" . ICONSPATH . "edit.png'> </a></td>";
						echo "<td class='buttons'><a href='gestion_inventaire/form_salles.php?action=del&id=$id' class='editbox' title='Suppression de la salle $nom'>	<img src='" . ICONSPATH . "delete.png'>	</a> </td>";
							
					} else {
						echo "<td>&nbsp</td>	<td>&nbsp</td>";
					}	
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	

<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>

<script type="text/javascript">
		
	// Filtre rémanent
	filter ( $('#filt').val(), 'salle_table' );
	
</script>

