<!--


Cette page est chargée lorsqu'on clique sur le nom d'un materiel dans la page voir_materiels.php

les données sont récupérées dans la base OCS.

Attention pour les adresses MAC : le code ne sort que la première valeur ! A MODIIIIIIFFFFIIIIER !
Pour faire la distinction WIFI / ETHERNET, utiliser networks.SPEED de OCSWEB !



-->


<?PHP

	header("Content-Type:text/html; charset=iso-8859-1" ); 	// règle le problème d'encodage des caractères

	include ('../config/databases.php');	// fichiers de configuration des bases de données
	include ('../config/pear.php');			// fichiers de configuration des lib PEAR (setinclude + packages)
	//include ('../../include/config.php'); 	//on récupère les variables pour le test des bases OCS et FOG
	
	// le ssn est le champ charnière pour récupérer les informations des différentes bases
	$mat_ssn = $_GET ['mat_ssn'];
	// le nom va servir pour la fiche fog ; en effet, la machine peut exister si elle n'a pas de numéro de série
	$mat_nom = $_GET ['mat_nom'];
	
	
?>

	<!--	DIV target pour Ajax	-->
	<div id="target"></div>

<?PHP


	/*******************************************
	*
	*				BASE OCS
	*
	********************************************/
	
	// On regarde si la base OCS existe car dans le cas de sa non existance la page ne s'affiche pas
	$link_bases = mysql_pconnect('localhost', 'root', $pass);//connexion à la base de donnée	
	if(!mysql_select_db($ocsweb, $link_bases)) { echo "BASE OCS introuvable";}
	else {
		// adresse de connexion à la base de données	
		$dsn_ocs	= 'mysql://'. $user .':' . $pass . '@localhost/' . $ocsweb;
		
		// cnx à la base de données GESPAC
		$db_ocs 	= & MDB2::factory($dsn_ocs);
		
		// RQ POUR INFO OCS
		$materiel_ocs    = $db_ocs->queryAll ( "SELECT NAME, USERDOMAIN, OSNAME, OSCOMMENTS, PROCESSORT, MEMORY, FIDELITY, USERID, SMANUFACTURER, SMODEL, SSN, networks.HARDWARE_ID, hardware.ID FROM hardware, bios, networks WHERE bios.SSN = '$mat_ssn' AND bios.HARDWARE_ID = hardware.id AND networks.HARDWARE_ID = hardware.id;" );
		$materiel_ocs_id = $materiel_ocs[0][12];
		
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
			// RQ POUR INFO cartes rzo
			$rq_cartes_reseaux = $db_ocs->queryAll ( "SELECT MACADDR, SPEED FROM networks WHERE HARDWARE_ID = " . $materiel_ocs[0][11] );
			// RQ POUR liste logiciels
			$rq_liste_logiciels = $db_ocs->queryAll ( "SELECT softwares.Name FROM softwares , hardware WHERE softwares.hardware_id = " . $materiel_ocs[0][12] . " AND hardware.id = " . $materiel_ocs[0][12] . " AND NOT softwares.Name LIKE '% Windows XP %' ");
		}
		
		// On se déconnecte de la db ocs
		$db_ocs->disconnect();
	}
	
	/*******************************************
	*
	*		BASE GESPAC
	*
	********************************************/
	
	// adresse de connexion à la base de données	
	$dsn_gespac     = 'mysql://'. $user . ':' . $pass . '@localhost/' . $gespac;
	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);
	$materiel_gespac = $db_gespac->queryAll ( "SELECT mat_nom, mat_dsit, mat_serial, mat_etat, marque_marque, marque_model, marque_type, marque_stype, mat_id, salle_nom, salles.salle_id, mat_origine, mat_mac, user_logon FROM materiels, marques, salles, users WHERE (materiels.marque_id=marques.marque_id and materiels.salle_id=salles.salle_id AND users.user_id = materiels.user_id AND mat_serial='$mat_ssn') ORDER BY mat_nom" );
	
	// On se déconnecte de la db gespac
	$db_gespac->disconnect();
	
	
	/*******************************************
	*
	*		BASE FOG
	*
	********************************************/
	
	//On vérifie l'existance de la base FOG même souci que OCS plantage de la page.
	if(!mysql_select_db($fog, $link_bases)) {$message_fog = "Base FOG non présente";}
	else {
	
		// adresse de connexion à la base de données
		$dsn_fog	= 'mysql://'. $user .':' . $pass . '@localhost/' . $fog;
	
		// cnx à la base de données FOG
		$db_fog 	= & MDB2::factory($dsn_fog);
	
		$rq_hotes_fog = $db_fog->queryAll ( "SELECT hostID FROM hosts, inventory WHERE hosts.hostID = inventory.iHostID AND inventory.iSysserial='$mat_ssn';" );
		$host_id = $rq_hotes_fog[0][0];
	
		$rq_nom_hotes_fog = $db_fog->queryAll ( "SELECT hostID FROM hosts WHERE hosts.hostName='$mat_nom';" );
		$host_nom_id = $rq_nom_hotes_fog[0][0];
	

		if ( $host_id ) { 
			
			$message_fog = "";
			
			$rq_image_associee = $db_fog->queryAll ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $host_id");
			$image_associee = $rq_image_associee[0][0];
			
			$rq_groupe_associe = $db_fog->queryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $host_id");
			$groupe_associe = $rq_groupe_associe[0][0];
			
			$rq_liste_snapins = $db_fog->queryAll ( "SELECT sName FROM snapinAssoc, snapins WHERE sID=saSnapinID AND saHostID='$host_id';" );
			
			$image_fog  = (!empty($image_associee)) ? $image_associee : "Pas d'image associée";
			$groupe_fog = (!empty($groupe_associe)) ? $groupe_associe : "Pas de groupe associé";

		} else if ($host_nom_id) { // on vérifie en passant par le nom de la machine qu'il y ait au moins un résultat
			
			// on avertit qu'il peut y avoir des erreurs dans les informations
			$message_fog = "Attention ! La recherche étant basée sur le nom, il peut y avoir des erreurs sur les données suivantes !";
			
			$rq_image_associee = $db_fog->queryAll ("SELECT imageName FROM images, hosts WHERE imageID=hostImage AND hosts.hostID = $host_nom_id");
			$image_associee = $rq_image_associee[0][0];
			
			
			$rq_groupe_associe = $db_fog->queryAll ("SELECT groupName FROM groups, groupMembers, hosts WHERE groupMembers.gmHostID = hosts.hostID AND groups.groupID = groupMembers.gmGroupID AND hosts.hostID = $host_nom_id");
			$groupe_associe = $rq_groupe_associe[0][0];
			
			
			$rq_liste_snapins = $db_fog->queryAll ( "SELECT sName FROM snapinAssoc, snapins WHERE sID=saSnapinID AND saHostID='$host_nom_id';" );
			
			$image_fog  = (!empty($image_associee)) ? $image_associee : "Pas d'image associée";
			$groupe_fog = (!empty($groupe_associe)) ? $groupe_associe : "Pas de groupe associé";

		} else {
			
			$message_fog = "Ce matériel n'existe pas dans FOG.";
			$image_associee  = "Pas d'image associée";
			$groupe_associee = "Pas de groupe associé";
		}
		
		// On se déconnecte de la db fog
		$db_fog->disconnect();
	}
	
	
	// OCS
	if(!mysql_select_db('ocsweb', $link_bases)) {$message_ocs = "Base OCS non présente";}//Base OCS absente on affiche ce message dans la liste
	else {
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
			$NAME 			= $materiel_ocs[0][0]; 
			$USERDOMAIN 	= $materiel_ocs[0][1];  
			$OSNAME 		= $materiel_ocs[0][2];  
			$OSCOMMENTS 	= $materiel_ocs[0][3];
			$PROCESSORT 	= $materiel_ocs[0][4]; 
			$MEMORY 		= $materiel_ocs[0][5]; 
			$FIDELITY 		= $materiel_ocs[0][6]; 
			$USERID 		= $materiel_ocs[0][7];
			$SMANUFACTURER 	= $materiel_ocs[0][8];
			$SMODEL 		= $materiel_ocs[0][9];
			$SSN 			= $materiel_ocs[0][10]; 
			$HARDWARE_ID	= $materiel_ocs[0][11];
		} else {
			$message_ocs = "Ce matériel n'existe pas dans OCS.";//Base OCS existe mais materiel non
		}
	}


	// GESPAC
	$mat_nom	= $materiel_gespac[0][0];
	$dsit		= $materiel_gespac[0][1];
	$serial		= $materiel_gespac[0][2];
	$etat		= $materiel_gespac[0][3];
	$marque		= $materiel_gespac[0][4];
	$model		= $materiel_gespac[0][5];
	$type		= $materiel_gespac[0][6];
	$stype		= $materiel_gespac[0][7];
	$mat_id		= $materiel_gespac[0][8];
	$salle_nom	= $materiel_gespac[0][9];
	$salle_id	= $materiel_gespac[0][10];
	$origine	= $materiel_gespac[0][11]; 
	$mat_mac	= $materiel_gespac[0][12];
	$user		= $materiel_gespac[0][13];
	
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
	echo "<H1> $NAME </H1>";
	
		echo "<TABLE width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=8><b>GESPAC</b><HR></TD>";
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
	
		echo "<TABLE width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=10><b>FOG</b><HR></TD>";
		echo "</TR>";
		
		echo "<TR>";
			echo "<TD COLSPAN=10><font color=red><b>$message_fog</b></font></TD>";
		echo "</TR>";
		
		
		
		if ($host_id) {
			echo "<TR>";
				echo "<TD>Image associée</TD>";
				echo "<TD>$image_fog</TD>";
			echo "</TR>";
			
			echo "<TR>";
				echo "<TD>Groupe associé</TD>";
				echo "<TD>$groupe_fog</TD>";
			echo "</TR>";
		
			foreach ($rq_liste_snapins as $record) {
				$nom_snapin = $record[0];
				
				echo "<TR>";
					echo "<TD>Snapin associé</TD>";
					echo "<TD>$nom_snapin</TD>";
				echo "</TR>";
			}
		} elseif ($host_nom_id) {
		
				echo "<TR>";
				echo "<TD>Image associée</TD>";
				echo "<TD>$image_fog</TD>";
			echo "</TR>";
			
			echo "<TR>";
				echo "<TD>Groupe associé</TD>";
				echo "<TD>$groupe_fog</TD>";
			echo "</TR>";
		
			foreach ($rq_liste_snapins as $record) {
				$nom_snapin = $record[0];
				
				echo "<TR>";
					echo "<TD>Snapin associé</TD>";
					echo "<TD>$nom_snapin</TD>";
				echo "</TR>";
			}
		}
		
			
	echo "</TABLE>";
	
	echo "<br>";
	echo "<br>";
	
	echo "<TABLE width=550>";
		echo "<TR>";
		echo "<TD COLSPAN=10><b>OCS</b><HR></TD>";
		echo "</TR>";
		
		echo "<TR>";
			echo "<TD COLSPAN=10><font color=red><b>$message_ocs</b></font></TD>";
		echo "</TR>";
		
		if ( $materiel_ocs_id ) {	// si le matériel existe dans ocs
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
				$SPEED   = $record[1];
				$MACADDR = $record[0];
				
				$select = ($mat_mac == $MACADDR) ? "checked" : "";
				
				echo "<TR>";
					echo "<TD>Adresse MAC $SPEED</TD>";
					echo "<TD>
							<input type='radio' name='mac' id='chk_$MACADDR' value=$MACADDR $select onclick=\"choix_mac($mat_id, '$MACADDR');\"> $MACADDR
						</TD>
						"; //création d'un bouton radio à côté de chaque adresse mac
				echo "</TR>";
			
			}
			echo "</form>";
			
			$count_logiciels = count($rq_liste_logiciels);
			
			$bg_color_logiciel = "#e1ffd3";
			echo "<TR>";
				echo "<TD rowspan=$count_logiciels bgcolor=$bg_color_logiciel><b>Logiciels installés</b></TD>";
			$i = 0;
			foreach ($rq_liste_logiciels as $record) {
				$NAME = stripcslashes(urldecode(utf8_decode($record[0])));
				$bg_color_image = $bg_color_image == "#e1ffd3" ? "#ffffff" : "#e1ffd3";
				if ( $i == 0 ) {
					echo "<td bgcolor=$bg_color_image>$NAME</td></tr>";
				} else {
					echo "<tr bgcolor=$bg_color_image><td bgcolor=$bg_color_image>$NAME</td></tr>";
				}

				$i++;		
			}
			echo "</TR>";
		
		}
		
	echo "</TABLE>";


	echo "</CENTER>";
	

?>

<script type="text/javascript">	
	
	function choix_mac(mat_id, mac) {
		// Submit le formulaire après clic du bouton radio
		$('target').load("gestion_inventaire/post_materiels.php?action=mod_mac&mat_id=" + mat_id + "&mac=" + mac);

	}

</script>
