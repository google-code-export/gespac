<?PHP
	
	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)


	// adresse de connexion à la base de données
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// options facultatives de cnx à la db
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL,);

	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac, $options);


	$matid 	= $_GET['matid'];
	$userid = $_GET['userid'];

	
	$liste_pour_convention = $db_gespac->queryAll ( "SELECT clg_nom, mat_serial, mat_dsit, marque_type, marque_model, user_nom, clg_ville FROM materiels, marques, users, college WHERE (materiels.user_id = $userid AND materiels.mat_id = $matid AND materiels.marque_id = marques.marque_id and users.user_id = materiels.user_id)" );

	$clg_nom		= stripslashes($liste_pour_convention[0][0]); 
	$mat_serial		= $liste_pour_convention[0][1]; 
	$mat_dsit		= $liste_pour_convention[0][2]; 
	$marque_type	= $liste_pour_convention[0][3]; 
	$marque_model	= $liste_pour_convention[0][4]; 
	$user_nom		= stripslashes($liste_pour_convention[0][5]); 
	$clg_ville		= stripslashes($liste_pour_convention[0][6]); 

?>

<pre>
<Center>
	ANNEE SCOLAIRE 20__/20__ 

	<u>CONVENTION BIPARTITE DE PRET</u>

	COLLEGE / MEMBRE DE L'EQUIPE PEDAGOGIQUE
</center>


Entre les soussignés, 


- Le Collège <b><?PHP echo $clg_nom;?> </b>, représenté par son chef d'établissement, 
- MME, MELLE, MR <b><?PHP echo $user_nom;?></b><br>
Affecté(e) au collège en qualité de ............................. matière .............................

Il a été convenu et arrêté ce qui suit :

<u>Article 1er :</u> Objet

Le Collège prête à l'utilisateur qui l'accepte, pour l'année scolaire 20__-20__ et pour toute la durée de son
affectation dans l'établissement un ordinateur portable et accessoires correspondants, référencé sous le numéro
d'inventaire suivant: 


				<h3><b><center><?PHP echo $mat_dsit. "(" . $mat_serial . ")";?></center></b></h3>

<u>Article 2 :</u> Propriété des biens

Conformément aux dispositions de l'article L-421-17 du Code de l'Education, ce matériel est la propriété du
collège qui assure la mise en œuvre de la garantie contractuelle. 


<u>Article 3 :</u> Utilisation du matériel 


Le matériel mis à la disposition de l'utilisateur est uniquement destiné à un usage pédagogique et éducatif dans
le cadre des enseignements organisés par le Collège.
Le Collège ne pourra donc être tenu pour responsable, à quelque titre que ce soit, pour toute utilisation autre,
quelle qu'elle soit, qui pourrait être faite par l'utilisateur, et pour tous les dommages qui pourraient en
résulter (atteinte aux droits de tiers, infractions diverses par exemple).
En tout état de cause, l'utilisateur reste responsable de l'usage fait du matériel à titre privé.

<u>Article 4 :</u> Obligations de l'utilisateur 
L'utilisateur est tenu d'avertir le collège en cas de vol ou perte du matériel et de produire une déclaration de
police dans le cas où le vol ou la perte aurait lieu hors du collège.
De même, en cas de panne, de dysfonctionnement ou de dommage causé au matériel, l'utilisateur doit ramener le
matériel au collège afin de permettre la mise en œuvre éventuelle de la garantie contractuelle.
Dans l'hypothèse où l'utilisateur quitterait définitivement le collège, le matériel devrait être restitué.

Dans le cas d'une multi affectation dans plusieurs collèges du département des Bouches du Rhône, l'utilisateur
déclare ne pas bénéficier d'un matériel similaire prêté dans un autre collège.
Les utilisateurs quittant définitivement le Collège devront au préalable sauvegarder leur données personnelles.

La présente convention prend effet dès sa signature par les parties concernées. 


Fait à <?PHP echo $clg_ville;?>, le <?PHP echo date(d."/".m."/".y);?>

LE CHEF D'ETABLISSEMENT 							L'UTILISATEUR
</pre>