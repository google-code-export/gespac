<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');		
	include_once ('../../class/Log.class.php');		
	
	
	// Cnx � la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");


	$id     	= $_GET ['gradeid'];
	$menu = json_encode($_POST);
	
	$req_modif_droits = "UPDATE grades SET grade_menu='$menu' WHERE grade_id=$id";
	$con_gespac->Execute ( $req_modif_droits );
	$log->Insert( $req_modif_droits );
	
	// [BUG=>la requ�te est nok] Insertion d'un log
	$log_texte = "Les droits du grade ont �t� modifi�s";
	
	$req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification Droits', '$log_texte' );";
	$con_gespac->Execute ( $req_log_modif_grade );
	$log->Insert( $req_log_modif_grade );
	
	echo "<br><small>Les droits ont �t� modifi�s...</small>";
	
?>
