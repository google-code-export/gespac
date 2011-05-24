<?php
//Ce fichier de configuration sert pour la création de la base de donnée et aussi la lecture pour rechercher la présence de OCS & FOG & GESPAC
require_once ('gespac/config/databases.php');//on écrit maintenant les informations de configuration dans gespac/config/databases.php
$hostname_gespac = $host;
$database_gespac = $gespac;
$username_gespac = $user;
$password_gespac = $pass;
$gespac = mysql_pconnect($hostname_gespac, $username_gespac, $password_gespac) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
