<?php
$path_racine = dirname(__FILE__); 
$path = $path_racine.'/PEAR';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);		
require_once 'MDB2.php';	 
?>
