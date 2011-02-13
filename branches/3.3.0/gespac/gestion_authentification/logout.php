<?php
	session_start() ;
	//destruction de toutes les variable de sessions
	session_unset() ;
	//destruction de la session
	session_destroy() ;

	header("Location: ../../index.php") ;
	
?>