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
	echo "<br><small><i>Tout d'abord, créez vos UO sur le contrôleur de domaine puis créez les snapins avec cette interface et enfin associez ces snapins aux machines dans fog !</small></i>";
	echo "<br><br><br><br>";
	
	if ( $E_chk ) {
		
?>

<!--	DIV target pour Ajax	-->
<div id="target"></div>

<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";
	
</script>


<?PHP 

	// Connexion à la base de données GESPAC
	$con_gespac 	= new Sql ( $host, $user, $pass, $gespac );

	$uai = $con_gespac->QueryOne ( "SELECT clg_uai FROM college" );
	
	echo "<input type=hidden id='uai' value=$uai>";


?>

<center>

<form action="modules/snapin_aic/post_snapin_aic.php?action=add" method="post" name="post_form" id="post_form">
	
	<input type="submit" value="Créer le Snapin dans FOG">
	
		<br>
	<br>
	
	
	<?PHP
	echo "<table class=paramdiv>";
						
					echo "<tr align=left><td>UO *</td><td><input type=text id='nom_uo' name='nom_uo' size=15 required><small>Les OU et sous-OU sont séparées par des virgules.</small></td>";

					echo "<tr align=left><td>PARAMETRES</td>
						<td>
						<div id='paramdiv' class='paramdiv' style='padding:10px;'>		
							<input type=checkbox id='e'>Afficher les messages d'erreur<br>
							<input type=checkbox id='u'>N'affiche pas le dernier login<br>
							<input type=checkbox id='m' checked>Verrouillage MAJ<br>
							<input type=checkbox id='c' checked>Supprime la fenêtre Ctrl Alt Suppr<br>
							<input type=checkbox id='s' checked>Supprime la synchronisation<br>
							<input type=checkbox id='r' checked>Reboot<br>
							<input type=checkbox id='p' checked>Poste Fixe<br>
							<input type=checkbox id='a' checked>Installe le client IACA<br>
						</div>
					</td></tr>";	
					
					echo "<tr align=left><td>Arguments</td><td><textarea id='param' name='param' readonly style='height:60px;width:500px;'></textarea></td></tr>";
				
					
			echo "</table>";
	?>
	
	
	</form>
	
</center>

	

<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
	
	} // End of E_chk
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
	
	
		// fonction de création du paramètre Eumcs
		function eumcs () {
			
			var param = "";
			
			if ( $("e").checked ) param = "E";	else param = "e";		
			if ( $("u").checked ) param += "U"; else param += "u";
			if ( $("m").checked ) param += "M"; else param += "m";			
			if ( $("c").checked ) param += "C"; else param += "c";
			if ( $("s").checked ) param += "S"; else param += "s";
			
			return param;		
		}
		
		
		// Créé la ligne complète du paramètre
		function MakeParamLine () {
		
			// /OU=OU="CDI",OU="Postes Fixes",OU=Ordinateurs,OU=013XXXXY,OU='BassinXXX',OU=Colleges,DC=ordina13,DC=cg13,DC=fr /YES /CLIENT=eUmCS
			
						
			// Pour la partie eumcs
				var iaca = " /client=" + eumcs() ;
			
			// Pour le reboot après intégration
				if ( $("r").checked ) reboot = " /YES";	else reboot = "";
	
			// Partie OU
				var ou = "";
				
				// Pour l'uo
				
				var split_ou = $('nom_uo').value.split (",");	// On commence par spliter par les ","
				split_ou.reverse(); // On inverse le sens du tableau pour avoir les ou les plus profondes d'abord.
				
				var complete_ou = "";
				
				Array.each (split_ou, function (itm) {
					complete_ou = complete_ou + 'OU="' + itm.trim() + '",';
				});
				
				var uo = '/OU=' + complete_ou;
									
				// Pour la portion postes fixe / Postes mobiles
				if ( $("p").checked ) poste = 'OU="Postes Fixes"';	else poste = 'OU="Portables"';
								
				ou = uo + poste + ',OU=Ordinateurs,OU=' +  uai.value + ',OU=Colleges,DC=ordina13,DC=cg13,DC=fr';	

				// Pour la portion installation du client iaca
				if ( $("a").checked ) client = '';	else client = ' /C=N';

					
			// La ligne entière
			return ou + reboot + iaca + client;	
			
		}
	
	
	
		// Sur clic d'une checkbox dans la liste des paramètres
		$$(".paramdiv input").addEvent('change', function(e) {
			$("param").value = MakeParamLine();		
		});
			
	
	});
	
	

</script>
