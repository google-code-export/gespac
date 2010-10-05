<?PHP
	
	/* fichier de visualisation des utilisateurs :
	
		view de la db gespac avec tous les users du parc
	*/
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	
?>


<h3>Visualisation des utilisateurs</h3>


<script type="text/javascript" src="server.php?client=all"></script>

<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP 

	// adresse de connexion à la base de données
	$dsn_gespac 	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_utilisateurs = $db_gespac->queryAll ( "SELECT user_nom, user_logon, user_password, user_niveau, user_mail, user_id, user_skin FROM users ORDER BY user_nom" );

?>
	
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center><small>Filtrer :</small> <input name="filt" onkeyup="filter(this, 'user_table', '1')" type="text"></center>
	</form>
	
<?PHP
	// Ajout d'un utilisateur
	echo "<a href='gestion_utilisateurs/form_utilisateurs.php?height=280&width=640&id=-1' rel='sexylightbox' title='ajout d un utilisateur'> <img src='img/add.png'>Ajouter un utilisateur </a>";
?>

	<center>
	<br>
	<table class="tablehover" id="user_table" width=800>
		<th>Nom</th>
		<th>Logon</th>
		<th>Grade</th>
		<th>Mail</th>
		<th>Skin</th>
		<th>&nbsp</th>
		<th>&nbsp</th>
		
		
		<?PHP	
			
			$compteur = 0;
			// On parcourt le tableau
			foreach ( $liste_des_utilisateurs as $record ) {
				
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				echo "<tr class=$tr_class>";
						
					$nom 		= $record[0];
					$logon 		= $record[1];
					$password 	= $record[2];
					$niveau		= $record[3];
					$mail 		= $record[4];
					$id			= $record[5];
					$skin		= $record[6];
					
					
					// test si la machine est prétée ou pas
					$rq_machine_pretee = $db_gespac->queryAll ( "SELECT mat_id FROM materiels WHERE user_id=$id AND user_id<>1" );
					$mat_id = @$rq_machine_pretee[0][0];	// crado : le @ permet de ne pas afficher d'erreur si la requete ne renvoie rien. A modifier, évidement
							
					if ( !isset($mat_id) ) {	// la machine n'est pas prêtée ($mat_id n'existe pas)
							$id_pret = 0;
						} else {	// la machine est prêtée ($mat_id existe)
							$id_pret = 1;
					}
					
					switch ($niveau) {
						case 0 : $niveau = "ROOT";
						break;
					
						case 1 : $niveau = "ATI";
						break;
						
						case 2 : $niveau = "TICE";
						break;
						
						case 3 : $niveau = "Professeur";
						break;
						
						case 9 : $niveau = "Autre...";
						break;
					}
					
					echo "<td> $nom </td>";
					echo "<td> $logon </td>";
					echo "<td> $niveau </td>";
					echo "<td> $mail </td>";
					echo "<td> $skin </td>";
					
					if ( $id == 1 ) {
						$modif_user = "<td><img src='img/write.png' style=display:none></td>";
					} else {
						$modif_user = "<td><a href='gestion_utilisateurs/form_utilisateurs.php?height=270&width=640&id=$id' rel='sexylightbox' title='Formulaire de modification de l`utilisateur $nom'><img src='img/write.png'> </a></td>";
					}
					
					echo $modif_user;
					echo "<td width=20 align=center> <a href='#' onclick=\"javascript:validation_suppr_user($id, '$nom', this.parentNode.parentNode.rowIndex, $id_pret);\">	<img src='img/delete.png' title='supprimer $nom'>	</a> </td>";
				
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	
	</center>
	
	
<?PHP

	echo "<a href='gestion_utilisateurs/form_utilisateurs.php?height=280&width=640&id=-1' rel='sexylightbox' title='ajout d un utilisateur'> <img src='img/add.png'>Ajouter un utilisateur </a>";

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
	
	function validation_suppr_user (id, nom, row, id_pret) {
	
		if ( id == 1 ) {
			alert("IMPOSSIBLE de supprimer l'utilisateur ATI ! ");
		} else {
			
			if ( id_pret == 0 ) {
		
				var valida = confirm('Voulez-vous vraiment supprimer l\'utilisateur "' + nom + '" ?');
				// si la réponse est TRUE ==> on lance la page post_marques.php
				if (valida) {
							
					/*	supprimer la ligne du tableau	*/
					document.getElementById('user_table').deleteRow(row);
					/*	poste la page en ajax	*/
					HTML_AJAX.replace("target", "gestion_utilisateurs/post_utilisateurs.php?action=suppr&id=" + id);
				}
			} else {
				alert('L\'utilisateur a une machine en prêt. Merci de la rendre avant suppression !');
			}
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

