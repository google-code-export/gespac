<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères
	
	// lib
	require_once ('../fonctions.php');
	require_once ('../config/pear.php');
	include_once ('../config/databases.php');
	
	
	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;	
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::connect($dsn_gespac);


	$id     	= $_GET ['gradeid'];
	$menu = json_encode($_POST);
	
	$req_modif_droits = "UPDATE grades SET grade_menu='$menu' WHERE grade_id=$id";
	$result = $db_gespac->exec ( $req_modif_droits );
	
	// On log la requête SQL
	fwrite($fp, date("Ymd His") . " " . $req_modif_droits."\n");
	
	// [BUG=>la requête est nok] Insertion d'un log
	$log_texte = "Les droits du grade ont été modifiés";
	
	$req_log_modif_grade = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Modification Droits', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_modif_grade );
	
	echo "<br><small>Les droits ont été modifiés...</small>";

	
	// Je ferme le fichier  de log sql
	fclose($fp);
	
?>