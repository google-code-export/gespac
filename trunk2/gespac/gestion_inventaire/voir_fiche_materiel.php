<!--

	Cette page est chargée lorsqu'on clique sur le nom d'un materiel dans la page voir_materiels.php

-->


<?PHP

	// lib
	require_once ('../fonctions.php');
	include_once ('../config/databases.php');
	include_once ('../../class/Sql.class.php');
	
	// le ssn est le champ charnière pour récupérer les informations des différentes bases
	$mat_ssn = $_GET ['mat_ssn'];
	// le nom va servir pour la fiche fog ; en effet, la machine peut exister si elle n'a pas de numéro de série
	$mat_nom = $_GET ['mat_nom'];
	
	

	/*******************************************
	*
	*				BASE OCS
	*
	********************************************/
	
	// On regarde si la base OCS existe car dans le cas de sa non existance la page ne s'affiche pas
	
	$con_ocs = new Sql($host, $user, $pass, $ocsweb);

	if( $con_ocs->Exists() ){
		
		// RQ POUR INFO OCS
		$materiel_ocs    = $con_ocs->QueryRow ( "SELECT NAME, USERDOMAIN, OSNAME, OSCOMMENTS, PROCESSORT, MEMORY, FIDELITY, USERID, SMANUFACTURER, SMODEL, SSN, networks.HARDWARE_ID as hid, hardware.ID as id FROM hardware, bios, networks WHERE bios.SSN = '$mat_ssn' AND bios.HARDWARE_ID = hardware.id AND networks.HARDWARE_ID = hardware.id;" );
		$materiel_ocs_id = $materiel_ocs[12];
	
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
			// RQ POUR INFO cartes rzo
			$rq_cartes_reseaux = $con_ocs->QueryAll ( "SELECT MACADDR, SPEED FROM networks WHERE HARDWARE_ID = " . $materiel_ocs[11] );
			// RQ POUR liste logiciels
			$rq_liste_logiciels = $con_ocs->QueryAll ( "SELECT softwares.Name as name FROM softwares , hardware WHERE softwares.hardware_id = " . $materiel_ocs_id . " AND hardware.id = " . $materiel_ocs_id . " AND NOT softwares.Name LIKE '% Windows XP %' ");
		}

	}
	
	/*******************************************
	*
	*		BASE GESPAC
	*
	********************************************/
	
	// cnx gespac
	$con_gespac = new Sql($host, $user, $pass, $gespac);
	
	$materiel_gespac = $con_gespac->QueryRow ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id as salleid, mat_origine, mat_mac, user_logon FROM materiels, marques, salles, users WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND users.user_id = materiels.user_id AND mat_serial='$mat_ssn') ORDER BY mat_nom" );

	
	
	/*******************************************
	*
	*		BASE FOG
	*
	********************************************/
	
	//On vérifie l'existance de la base FOG même souci que OCS plantage de la page.
	$con_fog = new Sql($host, $user, $pass, $fog);
	
	if($con_fog->Exists()) {
	
		$host_id = $con_fog->QueryOne ( "SELECT hostID FROM hosts, inventory WHERE hosts.hostID = inventory.iHostID AND inventory.iSysserial='$mat_ssn';" );	
		$host_nom_id = $con_fog->QueryOne ( "SELECT hostID FROM hosts WHERE hosts.hostName='$mat_nom';" );
		

		if ( $host_id ) { 
			
			$message_fog = "";
			
			$image_associee = $con_fog->QueryOne ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $host_id");
			$rq_groupe_associe = $con_fog->QueryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $host_id");
			$rq_liste_snapins = $con_fog->queryAll ( "SELECT sName FROM snapinAssoc, snapins WHERE sID=saSnapinID AND saHostID='$host_id';" );
			
			$image_fog  = $image_associee ? $image_associee : "Pas d'image associée";
			$groupe_fog = $rq_groupe_associe ? $rq_groupe_associe : "Pas de groupe associé";
			$snapin_fog = $rq_liste_snapins ? $rq_liste_snapins : "Pas de snapin associé";

		} else if ($host_nom_id) { // on vérifie en passant par le nom de la machine qu'il y ait au moins un résultat
			
			// on avertit qu'il peut y avoir des erreurs dans les informations
			$message_fog = "Attention ! La recherche étant basée sur le nom, il peut y avoir des erreurs sur les données suivantes !";
			
			$image_associee = $con_fog->QueryOne ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $host_nom_id");
			$rq_groupe_associe = $con_fog->QueryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $host_nom_id");		
			$rq_liste_snapins = $con_fog->QueryAll ( "SELECT sName FROM snapinAssoc, snapins WHERE sID=saSnapinID AND saHostID='$host_nom_id';" );
			
			$image_fog  = $image_associee ? $image_associee : "Pas d'image associée";
			$groupe_fog = $rq_groupe_associe ? $rq_groupe_associe : "Pas de groupe associé";
			$snapin_fog = $rq_liste_snapins ? $rq_liste_snapins : "Pas de snapin associé";

		} else {
			$message_fog = "Ce matériel n'existe pas dans FOG.";
		}
	}
	else {$message_fog = "Base FOG non présente";}
	

	// OCS
	if($con_ocs->Exists()) {	
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
			$NAME 			= $materiel_ocs[0]; 
			$USERDOMAIN 	= $materiel_ocs[1];  
			$OSNAME 		= $materiel_ocs[2];  
			$OSCOMMENTS 	= $materiel_ocs[3];
			$PROCESSORT 	= $materiel_ocs[4]; 
			$MEMORY 		= $materiel_ocs[5]; 
			$FIDELITY 		= $materiel_ocs[6]; 
			$USERID 		= $materiel_ocs[7];
			$SMANUFACTURER 	= $materiel_ocs[8];
			$SMODEL 		= $materiel_ocs[9];
			$SSN 			= $materiel_ocs[10]; 
			$HARDWARE_ID	= $materiel_ocs[11];
		} else {
			$message_ocs = "Ce matériel n'existe pas dans OCS.";//Base OCS existe mais materiel non
		}
	} else {$message_ocs = "Base OCS non présente";}//Base OCS absente on affiche ce message dans la liste


	////////////////////////////////////
	//			PARTIE GESPAC
	////////////////////////////////////
	
	$mat_nom	= $materiel_gespac[0];
	$dsit		= $materiel_gespac[1];
	$serial		= $materiel_gespac[2];
	$etat		= $materiel_gespac[3];
	$marque		= $materiel_gespac[4];
	$model		= $materiel_gespac[5];
	$type		= $materiel_gespac[6];
	$stype		= $materiel_gespac[7];
	$mat_id		= $materiel_gespac[8];
	$salle_nom	= $materiel_gespac[9];
	$salle_id	= $materiel_gespac[10];
	$origine	= $materiel_gespac[11]; 
	$mat_mac	= $materiel_gespac[12];
	$user		= $materiel_gespac[13];
	
	if ($salle_nom == 'PRETS') {
		if ($user == 'ati') {
			$font_color = "#18c900";
			$pret = "DISPONIBLE";
		} else {
			$font_color = "#0BAFF0";
			$pret = "$user";
		}
	} else {
		$font_color = "#FF0000";
		$pret = "INDISPONIBLE";
	}
	
echo "<form method='GET' name='frmTest' id='frmTest'>";
	
	echo "<CENTER>";
	
		echo "<TABLE class='smalltable alternate' width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=2><b>GESPAC</b></TD>";
		echo "</TR>";
		echo "<TR>";
			echo "<TD>DSIT</TD>";
			echo "<TD>$dsit</TD>";
		echo "</TR>";
		echo "<TR>";
			echo "<TD>PRET</TD>";
			echo "<TD><FONT COLOR=$font_color><B>$pret</B></FONT></TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>ETAT</TD>";
			echo "<TD>$etat</TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>MARQUE</TD>";
			echo "<TD>$marque</TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>MODELE</TD>";
			echo "<TD>$model</TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>FAMILLE</TD>";
			echo "<TD>$type</TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>SOUS FAMILLE</TD>";
			echo "<TD>$stype</TD>";
		echo "</TR>";		
		echo "<TR>";
			echo "<TD>SALLE</TD>";
			echo "<TD>$salle_nom</TD>";
		echo "</TR>";
		echo "<TR>";
			echo "<TD>ORIGINE</TD>";
			echo "<TD>$origine</TD>";
		echo "</TR>";
	echo "</TABLE>";
	
	
	echo "<br>";
	echo "<br>";
	
	////////////////////////////////////
	//			PARTIE FOG
	////////////////////////////////////
	
	echo "<TABLE class='smalltable alternate' width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=2><b>FOG<br><font color=red>$message_fog</font></b></TD>";
		echo "</TR>";

		if ($image_fog) {
			echo "<TR class='tr1'>";
				echo "<TD>Image associée</TD>";
				echo "<TD>$image_fog</TD>";
			echo "</TR>";
		}
		
		if ($groupe_fog) {
			echo "<TR class='tr2'>";
				echo "<TD>Groupe associé</TD>";
				echo "<TD>";
				foreach ($groupe_fog as $gp) echo $gp['groupName'] . "<br>";
				echo "</TD>";
			echo "</TR>";
		}
		
		if ($snapin_fog) {
			echo "<TR class='tr1'>";
				echo "<TD>Snapin associé</TD>";
				echo "<TD>";
				 foreach ($snapin_fog as $sn) echo $sn['sName'] . "<br>";
			echo "</TR>";
		}
	echo "</TABLE>";

	
	echo "<br>";
	echo "<br>";

	
	echo "<TABLE class='smalltable alternate' width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=10><b>OCS<br><font color=red>$message_ocs</font></b></TD>";
		echo "</TR>";
		
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
		echo "<TR>";
			echo "<TD>NOM</TD>";
			echo "<TD>$NAME</TD>";
		echo "</TR>";
		echo "<TR>";
				echo "<TD>DOMAINE</TD>";
				echo "<TD>$USERDOMAIN</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>OS</TD>";
				echo "<TD>$OSNAME $OSCOMMENTS</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>CPU</TD>";
				echo "<TD>$PROCESSORT</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>RAM</TD>";
				echo "<TD>$MEMORY Mo</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>FIDELITE</TD>";
				echo "<TD>$FIDELITY</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>UTILISATEUR</TD>";
				echo "<TD>$USERID</TD>";
			echo "</TR>";		
			echo "<TR>";
				echo "<TD>MARQUE</TD>";
				echo "<TD>$SMANUFACTURER</TD>";
			echo "</TR>";
			echo "<TR>";
				echo "<TD>MODELE</TD>";
				echo "<TD>$SMODEL</TD>";
			echo "</TR>";
			echo "<TR>";
				echo "<TD>No SERIE</TD>";
				echo "<TD>$SSN</TD>";
			echo "</TR>";
			
			
			foreach ($rq_cartes_reseaux as $record) {
				$SPEED   = $record['SPEED'];
				$MACADDR = $record['MACADDR'];
				
				$select = ($mat_mac == $MACADDR) ? "checked" : "";
				
				echo "<TR>";
					echo "<TD>Adresse MAC $SPEED</TD>";
					echo "<TD>
							<input type='radio' name='mac' id='chk_$MACADDR' value=$MACADDR $select onclick=\"choix_mac($mat_id, '$MACADDR');\"> $MACADDR
						</TD>
						"; //création d'un bouton radio à côté de chaque adresse mac
				echo "</TR>";
			
			}
			

			$count_logiciels = count($rq_liste_logiciels) +1;
			
			$bg_color_logiciel = "#e1ffd3";
			echo "<TR>";
				echo "<TD rowspan=$count_logiciels bgcolor=$bg_color_logiciel><b>Logiciels installés</b></TD>";
			$i = 0;
			foreach ($rq_liste_logiciels as $record) {
				$NAME = stripcslashes($record['name']);
				$bg_color_image = $bg_color_image == "#e1ffd3" ? "#ffffff" : "#e1ffd3";
				if ( $i == 0 ) {
					echo "<tr bgcolor='$bg_color_image'><td bgcolor='$bg_color_image' align='left'>$NAME</td></tr>";
				} else {
					echo "<tr bgcolor='$bg_color_image'><td bgcolor='$bg_color_image' align='left'>$NAME</td></tr>";
				}

				$i++;		
			}
			echo "</TR>";
		
		}
		
	echo "</TABLE>";
	
echo "</form>";

	echo "</CENTER>";
	

?>

<script type="text/javascript">	


	// Submit le formulaire après clic du bouton radio
	function choix_mac(mat_id, mac) {	
		$('#target').load("gestion_inventaire/post_materiels.php?action=mod_mac&mat_id=" + mat_id + "&mac=" + mac);
	}

</script>
