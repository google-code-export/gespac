<?php
        
        include ('./gespac/config/databases.php');      // fichiers de configuration des bases de données
        include ('./gespac/fonctions.php');				// fichier contenant les fonctions utilisées dans le reste des scripts
        include ('./gespac/config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)

        session_start();

        // adresse de connexion à la base de données
        $dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

        // cnx à la base de données GESPAC
        $db_gespac      = & MDB2::factory($dsn_gespac);
        
        $message = 'En attente de connexion...';
		
		#*************************************************************************************
		#
		# 				Vérification de l'existence de la table 'users'	et du compte ati 
		#
		#*************************************************************************************
		
		$req_users_existe = "SHOW TABLES FROM gespac LIKE 'users'";
		$result 		 = $db_gespac->queryAll($req_users_existe);
		
		if (!$result) { //dans ce cas, la table 'users' n'existe pas
		
			//on crée la table user avec le compte 'ati'
			$req_creation_table_users = "CREATE TABLE IF NOT EXISTS `users` (
									  `user_id` int(11) NOT NULL AUTO_INCREMENT,
									  `user_nom` varchar(255) DEFAULT NULL,
									  `user_logon` varchar(20) NOT NULL,
									  `user_password` varchar(15) DEFAULT NULL,
									  `user_mail` varchar(100) NOT NULL,
									  `user_skin` varchar(150) NOT NULL DEFAULT 'cg13',
									  `user_accueil` varchar(255) NOT NULL,
									  `user_menu` varchar(255) NOT NULL,
									  `user_suppr` tinyint(1) NOT NULL,
									  PRIMARY KEY (`user_id`),
									  UNIQUE KEY `user_logon` (`user_logon`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
									
			$req_creation_compte_ati = "INSERT INTO users VALUES('1', 'ati', 'ati', 'azerty', 'cg13', 'modules/stats/csschart.php', '1', 'gespac13@free.fr','1','0');";
			
			//on exécute les requêtes ci-dessus
			$result_creation_table = $db_gespac->exec($req_creation_table_users);
			$result_creation_compte_ati = $db_gespac->exec($req_creation_compte_ati);
			
			
		} else { //la table 'users' existe
		
			//on vérifie si le compte ati existe et si il a bien l'identifiant 1
			$req_ati_existe = $db_gespac->queryAll("SELECT user_logon FROM users WHERE user_id=1");
			
			if ($req_ati_existe[0][0] == 'ati') { // si l'user_id (qui est égal à 1) a pour nom ati, on peut sortir (le compte existe et a le bon id) 
				
			} else { 
				//requête pour mettre à jour le compte avec un id différent de 1
				$user_logon = $req_ati_existe[0][0];
				
				if ($user_logon != '') {
					//on génére aléatoirement un numéro d'id qu'on affectera au compte
					$id = rand(800, 1000);
					
					$maxuserid = $db_gespac->queryOne("SELECT max( user_id ) as maxuserid FROM users"); 
					$maxuserid ++;
				
					$req_maj_id = "UPDATE users SET user_id = $maxuserid WHERE user_logon = '$user_logon';";
					$result_maj_id = $db_gespac->exec($req_maj_id);
					
					//on crée ensuite le compte ati
					$req_creation_compte_ati = "INSERT INTO users VALUES('1', 'ati', 'ati', 'azerty', 'cg13', 'modules/stats/csschart.php', '1', 'gespac13@free.fr','1','0');";
					$result_creation_compte_ati = $db_gespac->exec($req_creation_compte_ati);
					
					
				} else {
				
					// il n'y a pas de compte avec l'id à 1 : on crée le compte ati
					$req_creation_compte_ati = "INSERT INTO users VALUES('1', 'ati', 'ati', 'azerty', 'cg13', 'modules/stats/csschart.php', '1', 'gespac13@free.fr','1','0');";
					$result_creation_compte_ati = $db_gespac->exec($req_creation_compte_ati);
				}
			}
			
		}
		
		
        if (!empty($_POST['login']) AND !empty($_POST['passwd'])) {

			$login  = mysql_real_escape_string(htmlentities($_POST['login']));
			$_SESSION['login'] = $login;
			$log_session = $_SESSION['login'];
	   
			$passwd = mysql_real_escape_string(htmlentities($_POST['passwd']));
			$_SESSION['passwd'] = $passwd;
			$pass_session = $_SESSION['passwd'];
	   
	   
			$req_select_comptes  = "SELECT * FROM users WHERE user_logon='$log_session' AND user_password='$pass_session'";
			$result = $db_gespac->queryAll($req_select_comptes);
	  
			if (!$result) {
			
				//destruction de toutes les variable de sessions
				session_unset() ;
				//destruction de la session
				session_destroy() ;
				//header ("Location: ./index.php");
				
				$message = 'Nom d`utilisateur et/ou mot de passe incorrect !';
			} else {
					
				// extraction de données pour les mettre en variables de sessions
				$user = $_SESSION ['login'];
				$rq_session_user = $db_gespac->queryRow ( "SELECT user_skin, grade_menu, grade_nom, grade_menu_portail FROM users, grades WHERE users.grade_id=grades.grade_id AND user_logon='$user' " );
				$_SESSION ['skin'] 			= $rq_session_user[0];             
				$_SESSION ['droits'] 		= $rq_session_user[1];
				$_SESSION ['grade']  		= $rq_session_user[2];
				$_SESSION ['menu_portail']  = $rq_session_user[3];
				
				header ("Location: ./index.php");
				break;
			}
        }
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
   <head>
        <title>GESPAC -> GEStion du PArc des Collèges</title>
   </head>
                        
	<body>

		<form action="<?PHP echo $_SERVER['PHP_SELF']; ?>" method="post">
				
			<table align="center" style="border:0px" >
				<tr>
					<td align="left">Utilisateur : </td><td><input type="text" name="login" size="20" maxlength="20" /></td>
				</tr>
				<tr>
					<td align="left">Mot de passe :</td><td> <input type="password" name="passwd" size="20" maxlength="20" /></td>
				</tr>
				<tr>
					<td></td><td><br><input type="submit" value="> Se connecter <" /></td>
				</tr>
			</table>
		
				<p><?PHP echo $message;?></p>
		</form>
			
		<?php
			$db_gespac->disconnect();
		?>
			
	</body>
        
</html>
