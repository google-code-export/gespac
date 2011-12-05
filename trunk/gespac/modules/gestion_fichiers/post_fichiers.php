<?PHP

	session_start();

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');
	
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$action = $_GET ["action"];


	/*******************************************************
	*
	*			Suppression d'un fichier
	*
	********************************************************/

if ( $action == 'suppr') {
	
	$id = $_GET ["id"];
	
	// Le fichier à dégommer
	$fichier = $con_gespac->QueryOne ("SELECT fichier_chemin FROM fichiers WHERE fichier_id=$id");
	
	// On test la supression du fichier
	if ( unlink ("../../fichiers/$fichier") ) {
		
		// Suppression du fichier dans la DB
		$rq_suppr_fichier = "DELETE FROM fichiers WHERE fichier_id=$id";
		$con_gespac->Execute($rq_suppr_fichier);
		
		// Logs
		$log_texte = "Suppression du fichier $fichier.";
		$rq = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Suppression fichier', '$log_texte' );";
		$con_gespac->Execute($rq);
	
	} else {
		echo "fichier introuvable...";
	}
	
} else {


	/*******************************************************
	*
	*					Ajout d'un fichier
	*
	********************************************************/
		
	$dossier = '../../fichiers/'; 		// dossier où sera déplacé le fichier
	
	$titre 			= $_POST["titre"];
	$description 	= $_POST["description"];
	$droits 		= $_POST["droits"];
	$user 			= $_SESSION['login'];
	$user_id		= $con_gespac->QueryOne("SELECT user_id FROM users WHERE user_logon='$user'");
	
	$fichier = basename($_FILES['myfile']['name']);
	$extensions = array('.sh', '.bat', '.vbs', '.php', '.js');
	$extension = strrchr($_FILES['myfile']['name'], '.'); 
	
	//Si l'extension n'est pas dans le tableau
	if ( in_array($extension, $extensions) )
		 $erreur = 'Vous ne pouvez pas uploader ce type de fichier ...';
		 
	//Si le fichier existe déjà
	$existe = $con_gespac->QueryOne("SELECT fichier_id FROM fichiers WHERE fichier_chemin='$fichier'");
	if ( $existe )
		$erreur = 'Le fichier existe déjà ...';

	if (!isset($erreur)) {	//S'il n'y a pas d'erreur, on upload

		//On formate le nom du fichier ici...
		$fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
		
		//On upload et on teste si la fonction renvoie TRUE
		if ( move_uploaded_file($_FILES['myfile']['tmp_name'], $dossier . $fichier) ) {
			echo $fichier . " envoyé avec succès !";
			
			
			// ************ Traitement du fichier uploadé *****************

			$req_ajout_fichier = "INSERT INTO fichiers ( fichier_chemin, fichier_description, fichier_droits, user_id ) VALUES ( '$fichier', '$description', '$droits', $user_id );";
			$con_gespac->Execute($req_ajout_fichier);

			//Insertion d'un log
			$log_texte = "Ajout du fichier $fichier";
			$rq = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import IACA', '$log_texte' );";
			$con_gespac->Execute($rq);

?>
			
			<script>window.close();</script>
			
<?PHP
		}
		else	// En cas d'échec d'upload
			echo 'Echec de l\'upload  de ' . $dossier . $fichier;
		 
	} else	// En cas d'erreur dans l'extension
		 echo $erreur;

}
?>
