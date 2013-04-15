<?PHP


	/* 
		Permet le clonage des machines
	*/


	// lib
	require_once ('../../fonctions.php');
	include_once ('../../config/databases.php');
	include_once ('../../../class/Sql.class.php');

	$action = $_GET ['action'];
	
	
	if ( $action == 'unicast' ) {
		
		$id = $_GET ['id']; 		
		
		// cnx à gespac
		$con_gespac = new Sql($host, $user, $pass, $gespac);
	
		$materiel = $db_gespac->queryRow ( "SELECT mat_nom, mat_mac FROM materiels WHERE mat_id = $id" );
		$mat_nom = $materiel[0];
		$mat_mac = $materiel[1];
		$mat_mac_with_dash = "01-" . preg_replace("[:]", "-", $mat_mac);
		
			

		// cnx à fog
		$con_fog = new Sql($host, $user, $pass, $fog);
		
		// On récupère les infos serveur	
		$FOG_TFTP_FTP_USERNAME 		= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_TFTP_FTP_USERNAME';");
		$FOG_TFTP_PXE_KERNEL 		= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_TFTP_PXE_KERNEL';");
		$FOG_PXE_BOOT_IMAGE 		= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_PXE_BOOT_IMAGE';");
		$FOG_KERNEL_RAMDISK_SIZE 	= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_KERNEL_RAMDISK_SIZE';");
		$FOG_PXE_IMAGE_DNSADDRESS 	= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_PXE_IMAGE_DNSADDRESS';");
		$FOG_TFTP_HOST 				= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_TFTP_HOST';");
		$FOG_WEB_HOST 				= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_WEB_HOST';");
		$FOG_WEB_ROOT 				= $con_fog->queryOne ("SELECT settingValue FROM globalSettings WHERE settingKey='FOG_WEB_ROOT';");
		$hostOS 					= $con_fog->queryOne ("SELECT hostOS FROM hosts WHERE hostName='$mat_nom';");
		$imagePath 					= $con_fog->queryOne ("SELECT imagePath FROM images, hosts WHERE imageID=hostImage AND hosts.hostName = '$mat_nom'");
	
	
		// création du fichier
		$fichier = "	# Généré par GESPAC WEB  
		
						DEFAULT $FOG_TFTP_FTP_USERNAME
							
						LABEL $FOG_TFTP_FTP_USERNAME
							
						kernel $FOG_TFTP_PXE_KERNEL
							
						append initrd=$FOG_PXE_BOOT_IMAGE  root=/dev/ram0 rw ramdisk_size=$FOG_KERNEL_RAMDISK_SIZE ip=dhcp dns=$FOG_PXE_IMAGE_DNSADDRESS type=down img=$imagePath mac=$mat_mac ftp=$FOG_TFTP_HOST storage=$FOG_TFTP_HOST:/images/ web=$FOG_WEB_HOST$FOG_WEB_ROOT osid=$hostOS  imgType=mps keymap=azerty shutdown= loglevel=4   fdrive=";
		
		
		// On créé le fichier au bon endroit (/tftboot/pxelinux.cfg/)
		$path = "/var/www/";
		exec ("echo -n \"" . $fichier . "\" >> " . $path . $mat_mac_with_dash );
		
		// et on réveille la machine ...
		//exec ("sudo wakeonlan $mat_mac");
		
	}
	
	if ( $action == 'multicast' ) {
	
	}
	
	

?>