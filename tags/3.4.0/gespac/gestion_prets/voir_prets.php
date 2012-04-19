<?PHP
	session_start();
	
	/* fichier de visualisation des prets :
	
		view de la db gespac avec tous le matos du parc qui peut être prêté UNIQUEMENT
	*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	
	
	
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-05-01#", $_SESSION['droits']);
	
?>

<!--	Ancre haut de page	-->
<a name="hautdepage"></a>

<h3>Visualisation des matériels disponibles au prêt</h3><br>


<!--	DIV target pour Ajax	-->
<div id="target"></div>


<?PHP

	// cnx à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac ) ;

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_prets 
	// SAlle_id = 3 (à la fin de la rq) parce que 3 correspond à la salle "PRETS"
	$liste_des_prets = $con_gespac->QueryAll ( "SELECT mat_nom, mat_serial, marque_type, marque_model, salle_nom, user_nom, mat_id, materiels.salle_id, materiels.user_id, mat_dsit, mat_etat FROM materiels, marques, salles, users WHERE ( materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id and materiels.user_id=users.user_id and materiels.salle_id=3	) ORDER BY mat_nom" );	

?>
	
	<!--<span style="float:right; margin-right:80px"><a href="#basdepage"><img src="./img/down.png" title="Aller en bas de page"></a></span>-->
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'prets_table', '1')" type="text"></center>
	</form>
	
	<center>
	
	<table class="tablehover" id="prets_table" width=800>
	
	<?PHP
		if ($E_chk) echo "<th> &nbsp </th><th> &nbsp </th>";
	?>
		<th>Nom</th>
		<th>DSIT</th>
		<th>Type</th>
		<th>Modèle</th>
		<th>Etat</th>
		<th>Prêté à...</th>
		<th style="display:none"></th>
	
		<?PHP	
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_prets as $record ) {
				// On écrit les lignes en brut dans la page html

				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
						
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
					
					
					
					
					// couleurs et noms					
					if ( $user_id == 1 ) {
						$apreter_color = "#36F572";
						$user = "DISPONIBLE";
					} else { $apreter_color = "#F57236"; }
					
					if ( $E_chk ) {
						echo "<td> <input type=radio name=radio value='$mat_id' onclick=\"select_cette_ligne('$mat_id', $user_id, this.parentNode.parentNode.rowIndex); bas_de_page(this.parentNode.parentNode.rowIndex);\"> </td>";
						echo "<td> <a href='#basdepage' class='bdp' id='bdp$compteur' style='display:none;'><img src='./img/down.png' title='Aller en bas de page' align=left></a></td>";
					}
					
					echo "<td> <a href='gestion_inventaire/voir_fiche_materiel.php?height=500&width=640&mat_nom=$mat&mat_ssn=$serial' rel='slb_prets title='Caractéristiques de $mat'>$mat</a> </td>";
					
					//echo "<td> $serial </td>";
					echo "<td> $inventaire </td>";
					echo "<td> $type </td>";
					echo "<td> $model </td>";
					echo "<td> $etat </td>";
					echo "<td bgcolor=$apreter_color><a href='gestion_prets/convention_pret.php?matid=$mat_id&userid=$user_id' target=_blank> $user </a></td>";
					
					echo "<td style=display:none>$mat</td>"; //permet de récupérer juste le nom de la machine pour les fonctions JS de prêt et rendu des machines
		
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	<!--	Ancre bas de page	-->
	<a name="basdepage"></a>
	<br>


	<!--	FORMULAIRE DE PRET AUX USERS 	-->
	
	<form id=elements_selectionnes method="post">
		
		<?PHP
		if ( $E_chk ) {
		?>
		
		<input type=hidden name=pret_a_poster id=pret_a_poster value=''>	<!--	ID du pret à poster	-->
		<input type=hidden name=row_table id=row_table value=''>			<!--	ROW du pret à poster	-->
		<input type=hidden name=select_user id=select_user value=''>		<!--	USER_ID du pret à poster	-->
		
		
		
		
		<!--------------------------------------------------------------------
		!		PARTIE POUR PRETER UN MATERIEL 
		--------------------------------------------------------------------->
		
		
		<div id="preter" style="visibility:hidden; text-align:center" > 
			Préter à : &nbsp
			
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
			
		
			<?PHP echo "<input type=button value='PRETER LE MATERIEL' onclick=\"javascript:validation_preter_materiel(pret_a_poster.value, user_select.value, row_table.value);\"> "; ?>
			
		</div>
		
		
		<!--------------------------------------------------------------------
		!					PARTIE POUR RENDRE UN MATERIEL 
		--------------------------------------------------------------------->
		
		<div id="rendre" style="visibility:hidden; text-align:center">		
			<?PHP echo "<input type=button value='RENDRE LE MATERIEL' onclick=\"javascript:validation_rendre_materiel(pret_a_poster.value, select_user.value, row_table.value);\"> "; ?>
		</div>
		<center><a href="#hautdepage"><img src="./img/up.png" title="Retourner en haut de page"></a></center><br>
		
		<?PHP } // fin test de droit sur le prêt ?>
		
	</form>

	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>



<script type="text/javascript">

	window.addEvent('domready', function(){
	  SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_prets'});
	});

		
	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
	
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
	
	
	
	// *********************************************************************************
	//
	//				Ajout des index pour postage sur clic de la radiobox
	//
	// *********************************************************************************	
	 
	function select_cette_ligne( id, userid, row ) {

		$('pret_a_poster').value = id;		
		$('row_table').value = row;	// row du tableau à modifier
		$('select_user').value = userid;	// userid du matos à modifier
		
		if ( userid == 1 ) {	// On se base sur la valeur USER_ID de root
			$('rendre').style.visibility = "hidden";
			$('preter').style.visibility = "";
			
		} else {
			
			$('rendre').style.visibility = "";
			$('preter').style.visibility = "hidden";
		}			

	}
	
	
	
	// *********************************************************************************
	//
	//				PRETER UN MATERIEL
	//
	// *********************************************************************************	
	 
	function validation_preter_materiel( matid, userid, row ) {
		
		var mat_nom = $('prets_table').rows[row].cells[8].innerHTML;
		var mat_etat = $('prets_table').rows[row].cells[6].innerHTML;
		
		var user_selected_id = $('user_select').selectedIndex;
		var user_selected_text = $('user_select').options[user_selected_id].text;	
		
		var valida = confirm('Voulez-vous vraiment prêter le matériel ' + mat_nom + ' qui est en état '+ mat_etat + ' à ' + user_selected_text + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
			
				//	poste la page en ajax	
				$("target").load("gestion_prets/post_prets.php?action=preter&matid=" + matid + "&userid=" + userid);
			
				// recharge la page
				$('conteneur').load("gestion_prets/voir_prets.php");
			
		}
	}


	
	
	// *********************************************************************************
	//
	//				RENDRE UN MATERIEL
	//
	// *********************************************************************************	
	 
	function validation_rendre_materiel( matid, userid, row ) {
		
		var mat_nom = $('prets_table').rows[row].cells[8].innerHTML;
	
		var valida = confirm('Voulez-vous vraiment rendre le matériel ' + mat_nom + " ?");
		
		// si la réponse est TRUE ==> on lance la page post_marques.php
		if (valida) {
					
			//	poste la page en ajax
			$("target").load("gestion_prets/post_prets.php?action=rendre&matid=" + matid + "&userid=" + userid);
			
			// recharge la page avec un délais de 1000ms
			window.setTimeout("$('conteneur').load('gestion_prets/voir_prets.php');", 1000);
		}
	}
	
	// *********************************************************************************
	//
	//				Bas de page
	//
	// *********************************************************************************
	
	function bas_de_page (row) {
	
		$$('.bdp').each(function(item) {
			item.style.display = "none";
		})

		var div = 'bdp' + (row - 1);
		$(div).style.display = "";
	}
	
</script>

