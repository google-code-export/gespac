<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Log.class.php');	
	include_once ('../../class/Sql.class.php');		
	
	
	// Cnx à la base
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	$log = new Log ("../dump/log_sql.sql");


	$id     	= $_GET ['gradeid'];
	$menu = json_encode($_POST);
	
	$req_modif_droits = "UPDATE grades SET grade_menu='$menu' WHERE grade_id=$id";
	$con_gespac->Execute ( $req_modif_droits );
	$log->Insert( $req_modif_droits );
	
	// [BUG=>la requête est nok] Insertion d'un log
	$log_texte = "Les droits du grade ont été modifiés";
	
	$req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification Droits', '$log_texte' );";
	$con_gespac->Execute ( $req_log_modif_grade );
	$log->Insert( $req_log_modif_grade );
	
	echo "Les droits ont été modifiés...";
	
?>
