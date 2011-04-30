<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
require_once = "gespac/config/databases.php";
$hostname_gespac = $host;
$database_gespac = $gespac;
$username_gespac = $user;
$password_gespac = $pass;
$gespac = mysql_pconnect($hostname_gespac, $username_gespac, $password_gespac) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
