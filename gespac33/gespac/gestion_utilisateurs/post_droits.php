<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// r�gle le probl�me d'encodage des caract�res
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// on ouvre un fichier en �criture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion � la base de donn�es	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx � la base de donn�es GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);


	$id     	= $_GET ['gradeid'];
	$menu = json_encode($_POST);
	
	$req_modif_droits = "UPDATE grades SET grade_menu='$menu' WHERE grade_id=$id";
	$result = $db_gespac->exec ( $req_modif_droits );
	
	// On log la requ�te SQL
	fwrite($fp, date("Ymd His") . " " . $req_modif_droits."\n");
	
	// [BUG=>la requ�te est nok] Insertion d'un log
	$log_texte = "Les droits du grade ont �t� modifi�s";
	
	$req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification Droits', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_modif_grade );
	
	echo "<br><small>Les droits ont �t� modifi�s...</small>";

	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
?>