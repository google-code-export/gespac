<?PHP
session_start();

	/*
		PAGE 07-11
	
		Visualisation des salles
		
		bouton ajouter une salle
		
		sur chaque salle possibilité de la modifier
		
		de la supprimer en faisant gaffe à bien rebalancer TOUTES les machines dans la salle de stockage
	
	*/


	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
		
	// si le grade du compte est root, on donne automatiquement les droits d'accès en écriture. Sinon, on teste si le compte a accès à la page.
	$E_chk = ($_SESSION['grade'] == 'root') ? true : preg_match ("#E-07-11#", $_SESSION['droits']);

	
	echo "<h3>Création des snapins pour intégration dans le domaine via client iaca.</h3>";
	echo "<br><small><i>Tout d'abord, créez vos UO sur le contrôleur de domaine puis créez les snapins avec cette interface et enfin associez ces snapins aux groupes dans fog.</small></i>";
	echo "<br><br><br><br>";
		
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";


	
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

<center>

<!-- 	bouton pour le filtrage du tableau	-->
<form id="filterform">
	<small>Filtrer :</small> <input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" onkeyup="filter(this, 'salle_table');" type="text" value=<?PHP echo $_GET['filter'];?> >
</form>

<center>
	
	Sélectionnez votre bassin :
	
	<select id='bassin'>
		<option value="AI" selected>Aix</option>
		<option value="AR">Arles</option>
		<option value="IS">Istres</option>
		<option value="MA">Marignane Vitrolles</option>
		<option value="MC">Marseille Centre</option>
		<option value="ME">Marseille Aubagne La Ciotat</option>
		<option value="MN">Marseille Nord et Etoile Sud</option>
	</select>
	
	
</center>

<?PHP 

	// Connexion à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	// stockage des lignes retournées par sql dans un tableau nommé avec originalité "array" (mais "tableau" peut aussi marcher)
	$liste_des_salles = $con_gespac->QueryAll ( "SELECT salle_id, salle_nom, salle_vlan, salle_etage, salle_batiment, est_modifiable FROM salles ORDER BY salle_nom" );

	$uai = $con_gespac->QueryOne ( "SELECT clg_uai FROM college" );
	
	echo "<input type=hidden id='uai' value=$uai>";


?>


<form action="modules/association_uo/post_assoc.php" method="post" name="post_form" id="post_form">
	
	<br>
	<br>
	
	<input type="submit" value="Créer les Snapins dans FOG">
	
	
	<center>
	<br>
	<table class="tablehover" width=800 id='salle_table'>
		<th>&nbsp</th>
		<th>Nom</th>
		<th>Paramètres</th>
		<th>&nbsp</th>	
		
		<?PHP	

			//$option_id = 0;
			$compteur = 0;
			// On parcourt le tableau
			foreach ($liste_des_salles as $record ) {
							
				// alternance des couleurs
				$tr_class = ($compteur % 2) == 0 ? "tr1" : "tr2";
						
				$id		 		= $record['salle_id'];
				$nom	 		= $record['salle_nom'];
				$vlan 			= $record['salle_vlan'];
				$etage 			= $record['salle_etage'];
				$batiment 		= $record['salle_batiment'];
				$est_modifiable = $record['est_modifiable'];
				
				echo "<tr class=$tr_class id='ligne$id'>";
					
					// valeur nominale pour la checkbox
					$chkbox_state = $apreter == 1 ? "checked" : "unchecked";
					
					// On récupère la valeur inverse pour la poster
					$change_apreter = $apreter == 1 ? 0 : 1;

					
					echo "<td> <input type=checkbox id='chk$id' class='chk'> </td>";
					echo "<td>$nom</td>";

					echo "<td><textarea id='param$id' name='param$id' readonly style='height:18px;width:320px;'></textarea></td>";
					
					echo "<td align=left class='params'>

						<div id='paramdiv$id' class='paramdiv' style='display:none;'>		
							<input type=hidden id='salle$id' value='$nom'>
							<input type=checkbox id='e$id'>Messages d'erreur<br>
							<input type=checkbox id='u$id'>Login précédent<br>
							<input type=checkbox id='m$id' checked>Verrouillage MAJ<br>
							<input type=checkbox id='c$id' checked>Fenêtre Ctrl Alt Suppr<br>
							<input type=checkbox id='s$id'>Synchronisation<br>
							<input type=checkbox id='r$id' checked>Reboot<br>
							<input type=checkbox id='p$id' checked>Poste Fixe<br>
						</div>
					</td>";	
					
				echo "</tr>";
				
				$compteur++;
			}
		?>		

	</table>
	</center>
	
	</form>
	
	<br>
	

<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>

<script type="text/javascript">
	
	window.addEvent('domready', function(){
	 
		SexyLightbox = new SexyLightBox({color:'black', dir: 'img/sexyimages', find:'slb_salles'});
		
		
		$('post_form').addEvent('submit', function(e) {	//	Pour poster un formulaire
			new Event(e).stop();
			new Request({

				method: this.method,
				url: this.action,

				onSuccess: function(responseText, responseXML, filt) {
					$('target').set('html', responseText);
					//$('conteneur').set('load', {method: 'post'});	//On change la methode d'affichage de la page de GET à POST (en effet, avec GET il récupère la totalité du tableau get en paramètres et lorsqu'on poste la page formation on dépasse la taille maxi d'une url)
				}
			
			}).send(this.toQueryString());
		});	
	
	
		// Pour faire apparaitre les paramètres
		$$(".tablehover tr").addEvent('mouseover', function(e) {
			// juste le id
			var id = this.id.replace("ligne", "");
			
			// Si l'id existe (à cause de la ligne des th qui est aussi dans un tr) et si la case en début de ligne est cochée
			if (id && $("chk" + id).checked )	
				$("paramdiv" + id).style.display = "";	
		});
	
	
		// Pour masquer les paramètres
		$$(".tablehover tr").addEvent('mouseleave', function(e) {
			// juste le id
			var id = this.id.replace("ligne", "");

			if (id)	
				$("paramdiv" + id).style.display = "none";	
		});
	
	
		// fonction de création du paramètre Eumcs
		function eumcs (id) {
			
			var param = "";
			
			if ( $("e" + id).checked ) param = "E";	else param = "e";		
			if ( $("u" + id).checked ) param += "U"; else param += "u";
			if ( $("m" + id).checked ) param += "M"; else param += "m";			
			if ( $("c" + id).checked ) param += "C"; else param += "c";
			if ( $("s" + id).checked ) param += "S"; else param += "s";
			
			return param;		
		}
		
		
		// Créé la ligne complète du paramètre
		function MakeParamLine (id) {
		
			// /OU=OU="CDI",OU="Postes Fixes",OU=Ordinateurs,OU=013XXXXY,OU='BassinXXX',OU=Colleges,DC=ordina13,DC=cg13,DC=fr /YES /CLIENT=eUmCS
			
						
			// Pour la partie eumcs
				var iaca = " /client=" + eumcs(id) ;
			
			// Pour le reboot après intégration
				if ( $("r" + id).checked ) reboot = " /YES";	else reboot = "";
	
			// Partie OU
				var ou = "";
				
				// Pour la salle
				var salle = '/OU=OU="' + $('salle' + id).value + '"';
									
				// Pour la portion postes fixe / Postes mobiles
				if ( $("p" + id).checked ) poste = 'OU="Postes Fixes"';	else poste = 'OU="Portables"';
				
				
				ou = salle + "," + poste + ',OU=Ordinateurs,OU=' +  uai.value + ',OU=' + $('bassin').value + ',OU=Colleges,DC=ordina13,DC=cg13,DC=fr';	
					
			// La ligne entière
			return ou + reboot + iaca;	
			
		}
	
	
	
		// Sur clic d'une checkbox dans la liste des paramètres
		$$(".paramdiv input").addEvent('change', function(e) {
			var id = this.id.substring(1);
			$("param"+id).value = MakeParamLine(id);		
		});
		
	
		
		// Sur clic d'une checkbox en début de ligne
		$$(".chk").addEvent('change', function(e) {
						
			// juste le id
			var id = this.id.replace("chk", "");
			
			if (this.checked) {
				$("param"+id).value = MakeParamLine(id);
				$("param"+id).style.height = "70px";
			}
			else {
				$("param"+id).value = "";
				$("param"+id).style.height = "18px";
			}
		});
		
		
		
		// Sur changement du bassin
		$("bassin").addEvent('change', function(e) {
						
			$$('.chk').each(function (item) {
				
			
			
			// Si la case est cochée
			if ( item.checked ) {
				// juste le id
				var id = item.id.replace("chk", "");
				$("param"+id).value = MakeParamLine(id);
			}
		})	
			
			
		});
		
	
	
	});
	
	

</script>
