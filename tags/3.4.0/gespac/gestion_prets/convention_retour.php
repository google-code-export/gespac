<?PHP
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	// Connexion à la base de données GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$id_conv = $_GET['id_conv'];
	
	if ($id_conv == 1) {
		$matid 	 = $_GET['matid'];
		$userid  = $_GET['userid'];
	
	
		$liste_pour_convention = $con_gespac->QueryRow ( "SELECT clg_nom, mat_serial, mat_dsit, marque_type, marque_model, user_nom, clg_ville FROM materiels, marques, users, college WHERE (materiels.user_id = $userid AND materiels.mat_id = $matid AND materiels.marque_id = marques.marque_id and users.user_id = materiels.user_id)" );

		$clg_nom		= $liste_pour_convention[0]; 
		$mat_serial		= $liste_pour_convention[1]; 
		$mat_dsit		= $liste_pour_convention[2]; 
		$marque_type	= $liste_pour_convention[3]; 
		$marque_model	= $liste_pour_convention[4]; 
		$user_nom		= $liste_pour_convention[5]; 
		$clg_ville		= $liste_pour_convention[6]; 

	
		// On rend ici le matériel et pas dans le fichier post_prets.php car la mise à jour de la table est trop rapide et les données n'existent plus lors de la création de la convention (donc convention vierge)
		$req_rendre_materiel = "UPDATE materiels SET user_id = 1 WHERE mat_id =$matid ;";
		$con_gespac->Execute ( $req_rendre_materiel );

?>
	
	<pre>
	<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

	Le <?PHP echo date(d."/".m."/".y);?> à <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable à l'ATI du collège.

	L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le collège.
	La convention signée pour l'année 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?></b> et le collège <?PHP echo $clg_nom;?> est annulée. 


	<center><?PHP echo $mat_dsit; ?></center>
	 


	Le collège <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






	 
	Exemplaire utilisateur 
	------------------------------------------------------------------------------------------------------------------------


	<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

	Le <?PHP echo date(d."/".m."/".y);?> à <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable à l'ATI du collège.

	L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le collège.
	La convention signée pour l'année 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?><b> et le collège <?PHP echo $clg_nom;?> est annulée. 


	<center><?PHP echo $mat_dsit; ?></center>
	 


	Le collège <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






	 
	Exemplaire collège 

	</pre>
	
<?PHP		
	} else {
		
		
		$matid 	 = $_GET['matid'];
		$userid  = $_GET['userid'];
		$id_conv = $_GET['id_conv'];
		
		// On rend temporairement le matériel à son précédent propriétaire 
		$req_rendre_materiel = "UPDATE materiels SET user_id = $userid WHERE mat_id =$matid ;";
		$con_gespac->Execute ( $req_rendre_materiel );
		
		$liste_pour_convention = $con_gespac->QueryRow ( "SELECT clg_nom, mat_serial, mat_dsit, marque_type, marque_model, user_nom, clg_ville FROM materiels, marques, users, college WHERE (materiels.user_id = $userid AND materiels.mat_id = $matid AND materiels.marque_id = marques.marque_id and users.user_id = materiels.user_id)" );

		$clg_nom		= $liste_pour_convention[0]; 
		$mat_serial		= $liste_pour_convention[1]; 
		$mat_dsit		= $liste_pour_convention[2]; 
		$marque_type	= $liste_pour_convention[3]; 
		$marque_model	= $liste_pour_convention[4]; 
		$user_nom		= $liste_pour_convention[5]; 
		$clg_ville		= $liste_pour_convention[6]; 
		
	

?>

	<pre>
	<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

	Le <?PHP echo date(d."/".m."/".y);?> à <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable à l'ATI du collège.

	L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le collège.
	La convention signée pour l'année 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?></b> et le collège <?PHP echo $clg_nom;?> est annulée. 


	<center><?PHP echo $mat_dsit; ?></center>
	 


	Le collège <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






	 
	Exemplaire utilisateur 
	------------------------------------------------------------------------------------------------------------------------


	<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

	Le <?PHP echo date(d."/".m."/".y);?> à <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable à l'ATI du collège.

	L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le collège.
	La convention signée pour l'année 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?></b> et le collège <?PHP echo $clg_nom;?> est annulée. 


	<center><?PHP echo $mat_dsit; ?></center>
	 


	Le collège <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






	 
	Exemplaire collège 

	</pre>

	<?PHP
		// On rend ici le matériel et pas dans le fichier post_prets.php car la mise à jour de la table est trop rapide et les données n'existent plus lors de la création de la convention (donc convention vierge)
		$req_rendre_materiel = "UPDATE materiels SET user_id = 1 WHERE mat_id =$matid ;";
		$con_gespac->Execute ( $req_rendre_materiel );
	}


	?>
