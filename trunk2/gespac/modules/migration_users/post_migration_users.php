<?PHP

	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	// cnx à la base de données GESPAC
	$con_gespac = new Sql ( $host, $user, $pass, $gespac );

	$compteur = 0;


	foreach ($_POST as $key=>$value) {

		if ($value <> "inconnu") {
		
			$pass = $con_gespac->QueryOne("SELECT pass FROM table_temp WHERE login='$value'");

			$migration_compte = $con_gespac->Execute("UPDATE users SET user_logon='$value', user_password='$pass' WHERE user_id=$key ");
			
			$compteur++;
		
		}

	}

	// On shoot la table temporaire
	$drop_table_temp = $con_gespac->Execute("DROP TABLE table_temp;"); 

	// On shoot le fichier iaca
	$kill_csv_file = unlink ("../../dump/migration_users_ad2008.csv");		

	echo $compteur . " compte(s) mis à jour !";


?>
