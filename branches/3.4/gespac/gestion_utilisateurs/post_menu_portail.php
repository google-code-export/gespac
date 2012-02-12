<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");

	$id   			= $_GET ['gradeid'];
	$menu_portail 	= json_encode($_POST);
	
	$req_modif_menu_portail = "UPDATE grades SET grade_menu_portail='$menu_portail' WHERE grade_id=$id";
	$con_gespac->Execute ( $req_modif_menu_portail );
	
	// On log la requête SQL
	$log->Insert( $req_modif_menu_portail );
	
	// log
	$log_texte = "Le menu portail du grade a été modifié";
	
	$req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification Droits', '$log_texte' );";
	$con_gespac->Execute ( $req_log_modif_grade );
	$log->Insert( $req_log_modif_grade );
	
	echo utf8_decode("<br><small>Le menu portail du grade a été modifié...</small>");
	
?>
