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


Entre les soussign�s, 


- Le Coll�ge <b><?PHP echo $clg_nom;?> </b>, repr�sent� par son chef d'�tablissement, 
- MME, MELLE, MR <b><?PHP echo $user_nom;?></b><br>
Affect�(e) au coll�ge en qualit� de ............................. mati�re .............................

Il a �t� convenu et arr�t� ce qui suit :

<u>Article 1er :</u> Objet

Le Coll�ge pr�te � l'utilisateur qui l'accepte, pour l'ann�e scolaire 20__-20__ et pour toute la dur�e de son
affectation dans l'�tablissement un ordinateur portable et accessoires correspondants, r�f�renc� sous le num�ro
d'inventaire suivant: 


				<h3><b><center><?PHP echo $mat_dsit. "(" . $mat_serial . ")";?></center></b></h3>

<u>Article 2 :</u> Propri�t� des biens

Conform�ment aux dispositions de l'article L-421-17 du Code de l'Education, ce mat�riel est la propri�t� du
coll�ge qui assure la mise en �uvre de la garantie contractuelle. 


<u>Article 3 :</u> Utilisation du mat�riel 


Le mat�riel mis � la disposition de l'utilisateur est uniquement destin� � un usage p�dagogique et �ducatif dans
le cadre des enseignements organis�s par le Coll�ge.
Le Coll�ge ne pourra donc �tre tenu pour responsable, � quelque titre que ce soit, pour toute utilisation autre,
quelle qu'elle soit, qui pourrait �tre faite par l'utilisateur, et pour tous les dommages qui pourraient en
r�sulter (atteinte aux droits de tiers, infractions diverses par exemple).
En tout �tat de cause, l'utilisateur reste responsable de l'usage fait du mat�riel � titre priv�.

<u>Article 4 :</u> Obligations de l'utilisateur 
L'utilisateur est tenu d'avertir le coll�ge en cas de vol ou perte du mat�riel et de produire une d�claration de
police dans le cas o� le vol ou la perte aurait lieu hors du coll�ge.
De m�me, en cas de panne, de dysfonctionnement ou de dommage caus� au mat�riel, l'utilisateur doit ramener le
mat�riel au coll�ge afin de permettre la mise en �uvre �ventuelle de la garantie contractuelle.
Dans l'hypoth�se o� l'utilisateur quitterait d�finitivement le coll�ge, le mat�riel devrait �tre restitu�.

Dans le cas d'une multi affectation dans plusieurs coll�ges du d�partement des Bouches du Rh�ne, l'utilisateur
d�clare ne pas b�n�ficier d'un mat�riel similaire pr�t� dans un autre coll�ge.
Les utilisateurs quittant d�finitivement le Coll�ge devront au pr�alable sauvegarder leur donn�es personnelles.

La pr�sente convention prend effet d�s sa signature par les parties concern�es. 


Fait � <?PHP echo $clg_ville;?>, le <?PHP echo date(d."/".m."/".y);?>

LE CHEF D'ETABLISSEMENT 							L'UTILISATEUR
</pre>