<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?PHP
	
	/* fichier de visualisation de l'inventaire :
	
		view de la db gespac avec tous le matos du parc

		combobox filtre ajax pour n'avoir que les imprimantes, que les pc ... 
		Pour chaque matos :
		
			boutons visualisation pour avoir la fiche d�taill�e (�ventuellement avec liste des demandes et des inters, liste des prets ...)
			bouton modification
			bouton suppression avec de belles confirmations
			bouton ajout, avec demande du type, du model et si on peux le pr�ter
			mais checker si le materiel est unique ou pas !!!!!
	
		lors de l'ajout d'un nouveau mat�riel, penser � permettre l'affectation directe � une salle !
	
	*/

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
			
?>

<h3>Visualisation des mat�riels</h3><br>

<!--	Ancre haut de page	-->
<a name="hautdepage"></a>

<!--	DIV target pour Ajax	-->
<div id="target"></div>



<?PHP

	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	
	// stockage des lignes retourn�es par sql dans un tableau nomm� liste_des_materiels
	$liste_des_materiels = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) ORDER BY mat_nom" );

?>
	<!-- onclick="$('filt').value = '/ecran';filter($('filt').value);"-->
	<!-- 	bouton pour le filtrage du tableau	-->
	<form>
		<center>
		<div id="ligne-filtre">
			<small>Filtrer <a href="#" title="Cherchez dans une colonne pr�cise avec le s�parateur deux points (CDI:1 pour la premi�re colonne, CDI:0 pour tout le tableau) " onclick="alert('Cherchez dans une colonne pr�cise avec le s�parateur deux points (CDI:1 pour la premi�re colonne, CDI:0 pour tout le tableau) \n Le filtre d`exclusion permet de ne pas s�lectionner une valeur particuli�re.\n Ainsi `CDI:1 / ecran:1` permet de selectionner tout le mat�riel appel� CDI mais pas les �crans CDI.');">[?]</a>:</small> 
			<input name="filt" id="filt" onKeyPress="return disableEnterKey(event)" type="text" value=<?PHP echo $_GET['filter']; ?> >
			<span id="nb_filtre"></span>
			
			<span id="liste_filtres" style=''><small>filtres perso</small>
				<span id="filtres_perso">
					<a href="#" id="filter_ecrans" title="ecran">Seulement les �crans</a><br>
					<a href="#" id="filter_noecrans" title="/ecran">Pas les �crans</a><br>
					<a href="#" id="filter_ssnnc" title="NC:4">Serial NC</a><br>
					<a href="#" id="filter_ssnrand" title="RAND:4">Serial RAND</a><br>
				</span>
			</span>
		</div>
			
		</center>
	</form>

	<div id='tableau'>Chargement des donn�es ...</div>

	<center><a href="#hautdepage"><img src="./img/up.png" title="Retourner en haut de page"></a></center><br>
	
<?PHP
	// On se d�connecte de la db
	$db_gespac->disconnect();
?>

</body>


<script type="text/javascript">	

	// init de la couleur de fond
	$('conteneur').style.backgroundColor = "#fff";

	window.addEvent('domready', function(){
	
		// fonction de filtrage
		function filter (phrase) {
			$("tableau").load("gestion_inventaire/voir_materiels_table.php?filter=" + phrase);
		};
			

		// Tamporisation du filtre + envoi
		$('filt').addEvent('keyup', function(el)  {
			 el.stop();
			 if($defined(this.timer))
				 $clear(this.timer);
			 this.timer = (function() { filter($('filt').value); }).delay(1000);
		});
		
			
		// Menu des filtres pr�programm�s
		$('liste_filtres').addEvent('mouseenter', function(el)  {
			el.stop();
			$('filtres_perso').style.display = "block";
		});
		
		$('liste_filtres').addEvent('mouseleave', function(el)  {
			el.stop();
			$('filtres_perso').style.display = "none";
		});
		
		
		//**********************************************
		//			Filtres Perso
		//**********************************************
		
		$('filter_ecrans').addEvent('click', function(el)  {
			$('filt').value = "ecran";
			filter ("ecran");
		});
		
		$('filter_noecrans').addEvent('click', function(el)  {
			$('filt').value = "/ecran";
			filter ("/ecran");
		});
		
		$('filter_ssnnc').addEvent('click', function(el)  {
			$('filt').value = "nc:4";
			filter ("nc:4");
		});
		
		$('filter_ssnrand').addEvent('click', function(el)  {
			$('filt').value = "rand:4";
			filter ("rand:4");
		});
		
	
		// oncharge par d�faut TOUS les enregistrements
		filter("<?PHP echo $_GET['filter'];?>");
		
	});
	

	
</script>
