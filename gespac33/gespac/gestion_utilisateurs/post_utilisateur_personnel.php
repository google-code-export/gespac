<?PHP
	session_start();

	/* fichier de modif de son compte utilisateur	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	$login =  $_SESSION['login'];
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);

	

	/**************** MODIFICATION ********************/	
	
	$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
	$password	= $_POST ['password'];
	$mail  		= $_POST ['mail'];
	$skin  		= $_POST ['skin'];
	$page   	= $_POST ['page'];
	$mailing   	= $_POST ['mailing'];
	
	$mailing = $mailing == "on" ? 1 : 0 ;

	$req_modif_user = "UPDATE users SET user_nom='$nom', user_password='$password', user_mail='$mail', user_skin='$skin', user_accueil='$page', user_mailing=$mailing WHERE user_logon='$login'";
	$result = $db_gespac->exec ( $req_modif_user );
	
	// On log la requête SQL
	fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
	
	$log_texte = "Le compte $nom a été modifié";
	
	$req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_modif_user );
	
	echo "<br><small>L'utilisateur <b>$nom</b> a bien été modifié...</small>";

	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>