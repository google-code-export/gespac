<?php
//Analyse des versions OCS et FOG
	$file_ocs = '/usr/share/ocsinventory-reports/ocsreports/preferences.php';//fichier ou est stocké la version du GUI d'OCS
	if (file_exists($file_ocs)) {
		require_once ('/usr/share/ocsinventory-reports/ocsreports/preferences.php');
		$version_ocs = GUI_VER;//récupère la version du GUI d'OCS
	}
	else {
		$version_ocs = "<font color='red'>Il semblerait qu'OCS ne soit pas installé</font>";}//le fichier n'est pas trouvé
	
	$file_fog = '/var/www/fog/commons/config.php';//fichier ou est stocké la version de FOG
	if (file_exists($file_fog)) {
					require_once ('/var/www/fog/commons/config.php'); 
					$version_fog = FOG_VERSION;//récupère la version de FOG
	}
	else {
		$version_fog = "<font color='red'>Il semblerait que FOG ne soit pas installé</font>";}//Fichier config FOG non trouvé
	
		//Les commandes sous linux
		if (!EXEC('uname -r')) {//on vérifie le système avec une commande sh
			$version_gespac = 'Non déterminé car le système ne semble pas être un Linux';
			$version_sqlgespac = 'Non déterminé car le système ne semble pas être un Linux';
			$version_linux = 'Horreur votre système est un WIN32';//Message un peu dur, mais salutaire!!!
		}
		else {
	        	$version_gespac = EXEC('apt-show-versions gespac');
            		$version_foggespac = EXEC('apt-show-versions fog-gespac');
			$version_serveurgespac = EXEC('apt-show-versions serveur-gespac');
			$version_sqlgespac = EXEC('apt-show-versions sql-gespac');
			$version_linux = EXEC('uname -r');
			$ip_serveur = EXEC('/sbin/ifconfig eth0 | grep Bcast | cut -c 08-35');
		}
?>
<b>Information sur le serveur</b><br>
		<center><img src="./img/serpac.png" WIDTH=90 HEIGHT=90></center><br>
		<table align="center">
			<tr><th align="center" colspan="2">Paquets GESPAC</th></tr>
			<tr><td align="left">GESPAC</td><td align="right"><?php echo $version_gespac;?></td></tr>
			<tr><td align="left">SQL-GESPAC</td><td align="right"><?php echo $version_sqlgespac;?></td></tr>
			<tr><td align="left">FOG-GESPAC</td><td align="right"><?php echo $version_foggespac;?></td></tr>
			<tr><td align="left">SERVEUR-GESPAC</td><td align="right"><?php echo $version_serveurgespac;?></td></tr>
			<tr><th align="center" colspan="2">Systeme</th></tr>
			<tr><td align="left">Adresse IP</td><td align="right"><?php echo $ip_serveur;?></td></tr>
			<tr><td align="left">Signature serveur</td><td align="right"><?php echo $_SERVER['SERVER_SIGNATURE'];?></td></tr>
        		<tr><td align="left">Linux kernel</td><td align="right"><?php echo $version_linux;?></td></tr>
	     		<tr><td align="left">Version PHP</td><td align="right"><?php echo  phpversion();?></td></tr>
			<tr><td align="left">Zend engine version</td><td align="right"><?php echo zend_version();?></td></tr>
			<tr><th align="center" colspan="2">Applications</th></tr>
			<tr><td align="left">Version GUI OCS</td><td align="right"><?php echo $version_ocs;?></td></tr>
			<tr><td align="left">Version FOG</td><td align="right"><?php echo $version_fog;?></td></tr>
			
		</table>
		
<center>
<object type="text/html" data="http://<?php echo $_SERVER['SERVER_ADDR'];?>/viewrx/"  width="100%" height="100%" style="overflow:auto; border: none;">
</center>