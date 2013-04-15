<?PHP
	
	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...	

	// Connexion &agrave; la base de donn&eacute;es GESPAC
	$con_gespac = new Sql($host, $user, $pass, $gespac);

	$matid 	= $_GET['matid'];
	$userid = $_GET['userid'];

	
	$liste_pour_convention = $con_gespac->QueryRow ( "SELECT clg_nom, mat_serial, mat_dsit, marque_type, marque_model, user_nom, clg_ville FROM materiels, marques, users, college WHERE (materiels.user_id = $userid AND materiels.mat_id = $matid AND materiels.marque_id = marques.marque_id and users.user_id = materiels.user_id)" );

	$clg_nom		= stripslashes($liste_pour_convention[0]); 
	$mat_serial		= $liste_pour_convention[1]; 
	$mat_dsit		= $liste_pour_convention[2]; 
	$marque_type	= $liste_pour_convention[3]; 
	$marque_model	= $liste_pour_convention[4]; 
	$user_nom		= stripslashes($liste_pour_convention[5]); 
	$clg_ville		= stripslashes($liste_pour_convention[6]); 

?>

<pre>
<Center>
	ANNEE SCOLAIRE 20__/20__ 

	<u>CONVENTION BIPARTITE DE PRET</u>

	COLLEGE / MEMBRE DE L'EQUIPE PEDAGOGIQUE
</center>


Entre les soussign&eacute;s, 


- Le Coll&egrave;ge <b><?PHP echo $clg_nom;?> </b>, repr&eacute;sent&eacute; par son chef d'&eacute;tablissement, 
- MME, MR <b><?PHP echo $user_nom;?></b><br>
Affect&eacute;(e) au coll&egrave;ge en qualit&eacute; de ............................. mati&egrave;re .............................

Il a &eacute;t&eacute; convenu et arr&ecirc;t&eacute; ce qui suit :

<u>Article 1er :</u> Objet

Le Coll&egrave;ge pr&ecirc;te &agrave; l'utilisateur qui l'accepte, pour l'ann&eacute;e scolaire 20__-20__ et pour toute la dur&eacute;e de son
affectation dans l'&eacute;tablissement un ordinateur portable et accessoires correspondants, r&eacute;f&eacute;renc&eacute; sous le num&eacute;ro
d'inventaire suivant: 


				<h3><b><center><?PHP echo $mat_dsit. "(" . $mat_serial . ")";?></center></b></h3>

<u>Article 2 :</u> Propri&eacute;t&eacute; des biens

Conform&eacute;ment aux dispositions de l'article L-421-17 du Code de l'Education, ce mat&eacute;riel est la propri&eacute;t&eacute; du
coll&egrave;ge qui assure la mise en oeuvre de la garantie contractuelle. 


<u>Article 3 :</u> Utilisation du mat&eacute;riel 


Le mat&eacute;riel mis &agrave; la disposition de l'utilisateur est uniquement destin&eacute; &agrave; un usage p&eacute;dagogique et &eacute;ducatif dans
le cadre des enseignements organis&eacute;s par le Coll&egrave;ge.
Le Coll&egrave;ge ne pourra donc &ecirc;tre tenu pour responsable, &agrave; quelque titre que ce soit, pour toute utilisation autre,
quelle qu'elle soit, qui pourrait &ecirc;tre faite par l'utilisateur, et pour tous les dommages qui pourraient en
r&eacute;sulter (atteinte aux droits de tiers, infractions diverses par exemple).
En tout &eacute;tat de cause, l'utilisateur reste responsable de l'usage fait du mat&eacute;riel &agrave; titre priv&eacute;.

<u>Article 4 :</u> Obligations de l'utilisateur 
L'utilisateur est tenu d'avertir le coll&egrave;ge en cas de vol ou perte du mat&eacute;riel et de produire une d&eacute;claration de
police dans le cas où le vol ou la perte aurait lieu hors du coll&egrave;ge.
De m&ecirc;me, en cas de panne, de dysfonctionnement ou de dommage caus&eacute; au mat&eacute;riel, l'utilisateur doit ramener le
mat&eacute;riel au coll&egrave;ge afin de permettre la mise en oeuvre &eacute;ventuelle de la garantie contractuelle.
Dans l'hypoth&egrave;se où l'utilisateur quitterait d&eacute;finitivement le coll&egrave;ge, le mat&eacute;riel devrait &ecirc;tre restitu&eacute;.

Dans le cas d'une multi affectation dans plusieurs coll&egrave;ges du d&eacute;partement des Bouches du Rhône, l'utilisateur
d&eacute;clare ne pas b&eacute;n&eacute;ficier d'un mat&eacute;riel similaire pr&ecirc;t&eacute; dans un autre coll&egrave;ge.
Les utilisateurs quittant d&eacute;finitivement le Coll&egrave;ge devront au pr&eacute;alable sauvegarder leur donn&eacute;es personnelles.

La pr&eacute;sente convention prend effet d&egrave;s sa signature par les parties concern&eacute;es. 


Fait &agrave; <?PHP echo $clg_ville;?>, le <?PHP echo date(d."/".m."/".y);?>

LE CHEF D'ETABLISSEMENT 							L'UTILISATEUR
</pre>
