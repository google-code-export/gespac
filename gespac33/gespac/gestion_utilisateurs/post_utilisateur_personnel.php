<?PHP
	session_start();

	/* fichier de modif de son compte utilisateur	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	$login =  $_SESSION['login'];
	
	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac	= 'mysql://'. $user .':' . $pass . '@localhost/gespac';	
	
	// cnx � la base de donn�es GESPAC
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
	
	// On log la requ�te SQL
	fwrite($fp, date("Ymd His") . " " . $req_modif_user."\n");
	
	$log_texte = "Le compte $nom a �t� modifi�";
	
	$req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_modif_user );
	
	echo "<br><small>L'utilisateur <b>$nom</b> a bien �t� modifi�...</small>";

	
	// Je ferme le fichier  de log sql
	fclose($fp);

?>