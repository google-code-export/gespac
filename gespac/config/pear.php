<?PHP

		set_include_path(get_include_path() . ";C:\wamp\bin\php\php5.2.9-2\PEAR ; C:\Program Files\wamp\bin\php\php5.2.8\PEAR");	// inclusion du chemin pour PEAR sous windows
		set_include_path(get_include_path() . ";C:\Program Files\wamp\bin\php\php5.2.9-2\PEAR ; C:\Program Files\wamp\bin\php\php5.2.8\PEAR");	// inclusion du chemin pour PEAR sous windows
		set_include_path(get_include_path() . ";/usr/share/php");    // inclusion du chemin pour PEAR sous linux

		require_once 'MDB2.php';
	 

?>