<?PHP
	session_start();

	/* fichier de modif de son compte utilisateur	*/
	
	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		
	
	$login =  $_SESSION['login'];
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");

	

	/**************** MODIFICATION ********************/	
	
	$nom 		= addslashes(utf8_decode(urldecode($_POST ['nom'])));
	$password	= $_POST ['password'];
	$mail  		= $_POST ['mail'];
	$skin  		= $_POST ['skin'];
	$page   	= $_POST ['page'];
	$mailing   	= $_POST ['mailing'];
	
	$mailing = $mailing == "on" ? 1 : 0 ;

	$req_modif_user = "UPDATE users SET user_nom='$nom', user_password='$password', user_mail='$mail', user_skin='$skin', user_accueil='$page', user_mailing=$mailing WHERE user_logon='$login'";
	$con_gespac->Execute ( $req_modif_user );
	
	// On log la requête SQL
	$log->Insert( $req_modif_user );
	
	$log_texte = "Le compte $nom a été modifié";
	
	$req_log_modif_user = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification compte', '$log_texte' );";
	$con_gespac->Execute ( $req_log_modif_user );
	
	// On log la requête SQL
	$log->Insert( $req_log_modif_user );
	
	echo "<br><small>L'utilisateur <b>$nom</b> a bien été modifié...</small>";


?>
