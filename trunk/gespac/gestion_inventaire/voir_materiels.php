<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?PHP
	
	/* fichier de visualisation de l'inventaire :
	
		view de la db gespac avec tous le matos du parc

		combobox filtre ajax pour n'avoir que les imprimantes, que les pc ... 
		Pour chaque matos :
		
			boutons visualisation pour avoir la fiche détaillée (éventuellement avec liste des demandes et des inters, liste des prets ...)
			bouton modification
			bouton suppression avec de belles confirmations
			bouton ajout, avec demande du type, du model et si on peux le préter
			mais checker si le materiel est unique ou pas !!!!!
	
		lors de l'ajout d'un nouveau matériel, penser à permettre l'affectation directe à une salle !
	
	*/

			
?>

<!--	Ancre haut de page	-->
<a name="hautdepage"></a>


<div class="entetes" id="entete-materiels">	

	<span class="entetes-titre">LES MATERIELS</span>

	<span class="entetes-options">
		
		<span class="option">
		
		</span>
		
		
		<span class="option">
		<!-- 	bouton pour le filtrage du tableau	-->
		<form>
			<center>
			<div id="ligne-filtre">
				<small><a href="#" title="Cherchez dans une colonne précise avec le séparateur deux points (CDI:1 pour la première colonne, CDI:0 pour tout le tableau) " onclick="alert('Cherchez dans une colonne précise avec le séparateur deux points (CDI:1 pour la première colonne, CDI:0 pour tout le tableau) \n Le filtre d`exclusion permet de ne pas sélectionner une valeur particulière.\n Ainsi `CDI:1 / ecran:1` permet de selectionner tout le matériel appelé CDI mais pas les écrans CDI. \n On peut aussi ajouter des champs avec l`opérateur +. par exemple `cdi:1+fonctionnel:5/ecran:1+d3e:10`.');">[?]</a></small> 
				<input placeholder=" filtrer" name="filt" id="filt" onKeyPress="return disableEnterKey(event)" type="text" value=<?PHP echo $_GET['filter']; ?> >
				<span id="nb_filtre"></span>
				
				<span id="liste_filtres" style=''><small>filtres perso</small>
					<span id="filtres_perso">
						<a href="#" id="filter_ecrans" title="ecran">Seulement les écrans</a><br>
						<a href="#" id="filter_noecrans" title="/ecran">Pas les écrans</a><br>
						<a href="#" id="filter_ssnnc" title="NC:4">Serial NC</a><br>
						<a href="#" id="filter_ssnrand" title="RAND:4">Serial RAND</a><br>
					</span>
				</span>
			</div>
				
			</center>
		</form>
		</span>
	</span>

</div>

<div class=spacer></div>

<?PHP

	// cnx à la base de données GESPAC
	$con_gespac	= new Sql ($host, $user, $pass, $gespac);
	
	// stockage des lignes retournées par sql dans un tableau nommé liste_des_materiels
	$liste_des_materiels = $con_gespac->QueryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, user_nom FROM materiels, marques, salles, users WHERE (materiels.user_id=users.user_id AND materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id) ORDER BY mat_nom" );

?>

	<div id='tableau'>Chargement des données ...</div>

	<center><a href="#hautdepage"><img src="./img/up.png" title="Retourner en haut de page"></a></center><br>
	
<?PHP
	// On se déconnecte de la db
	$con_gespac->Close();
?>

</body>


<script type="text/javascript">	

	window.addEvent('domready', function(){
	
		// fonction de filtrage
		function filter (phrase) {
			$("tableau").load("gestion_inventaire/voir_materiels_table.php?filter=" + encodeURIComponent(phrase) );
		};
			

		// Tamporisation du filtre + envoi
		$('filt').addEvent('keyup', function(el)  {
			 el.stop();
			 if($defined(this.timer))
				 $clear(this.timer);
			 this.timer = (function() { filter($('filt').value); }).delay(1000);
		});
		
			
		// Menu des filtres préprogrammés
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
		
	
		// oncharge par défaut TOUS les enregistrements
		filter("<?PHP echo $_GET['filter'];?>");
		
	});
	

	
</script>
