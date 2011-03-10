<?PHP
	
	include ('../config/databases.php');	// fichiers de configuration des bases de donn�es
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)


	// adresse de connexion � la base de donn�es
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// options facultatives de cnx � la db
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL,);

	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);


	$matid 	= $_GET['matid'];
	$userid = $_GET['userid'];

	
	$liste_pour_convention = $db_gespac->queryAll ( "SELECT clg_nom, mat_serial, mat_dsit, marque_type, marque_model, user_nom, clg_ville FROM materiels, marques, users, college WHERE (materiels.user_id = $userid AND materiels.mat_id = $matid AND materiels.marque_id = marques.marque_id and users.user_id = materiels.user_id)" );

	$clg_nom		= $liste_pour_convention[0][0]; 
	$mat_serial		= $liste_pour_convention[0][1]; 
	$mat_dsit		= $liste_pour_convention[0][2]; 
	$marque_type	= $liste_pour_convention[0][3]; 
	$marque_model	= $liste_pour_convention[0][4]; 
	$user_nom		= $liste_pour_convention[0][5]; 
	$clg_ville		= $liste_pour_convention[0][6]; 

	
	// On rend ici le mat�riel et pas dans le fichier post_prets.php car la mise � jour de la table est trop rapide et les donn�es n'existent plus lors de la cr�ation de la convention (donc convention vierge)
	$req_rendre_materiel = "UPDATE materiels SET user_id = 1 WHERE mat_id =$matid ;";
	$result = $db_gespac->exec ( $req_rendre_materiel );
?>

<pre>
<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

Le <?PHP echo date(d."/".m."/".y);?> � <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable � l'ATI du coll�ge.

L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le coll�ge.
La convention sign�e pour l'ann�e 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?></b> et le coll�ge <?PHP echo $clg_nom;?> est annul�e. 


<center><?PHP echo $mat_dsit; ?></center>
 


Le coll�ge <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






 
Exemplaire utilisateur 
------------------------------------------------------------------------------------------------------------------------


<center><h2>Bon de restitution de l'ordinateur portable</h2></center> 

Le <?PHP echo date(d."/".m."/".y);?> � <?PHP echo $clg_ville;?>, MME ou M <b><?PHP echo $user_nom;?></b> a rendu son portable � l'ATI du coll�ge.

L'ordinateur <b><?PHP echo $mat_dsit; ?></b> est maintenant pris en charge par le coll�ge.
La convention sign�e pour l'ann�e 20__/20__ entre MME ou M <b><?PHP echo $user_nom;?><b> et le coll�ge <?PHP echo $clg_nom;?> est annul�e. 


<center><?PHP echo $mat_dsit; ?></center>
 


Le coll�ge <b><?PHP echo $clg_nom;?></b>                                              MME ou M <b><?PHP echo $user_nom;?></b>






 
Exemplaire coll�ge 

</pre>