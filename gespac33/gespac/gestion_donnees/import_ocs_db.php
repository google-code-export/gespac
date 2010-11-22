<script>
	// Montre ou cache les logs
	function showlogs (log) {
		if ( $(log).style.display == "" )
			$(log).style.display = "none";
		else
			$(log).style.display = "";
	}
</script>

<?PHP

	function vd ($var) { return "<pre>" . var_dump ($var) . "</pre>"; }

	// on ouvre un fichier en écriture pour les log sql
	$fp = fopen('../dump/log_sql.sql', 'a+');

	//log
	$big_log = "";
	$marques_ajoutees = "";
	$marques_deja_presentes = "";
	$materiels_ajoutes = "";
	$materiels_maj = "";
	$materiels_deja_presents = "";
	$ecrans_deja_presents = "";
	$ecrans_ajoutes = "";
	
	$marques_creees = array();	// Permet de comptabiliser les marques nouvellement créées (je sais c'est pas super propre, mais il est 21h30 et c'est vendredi soir)
	

	include ('../includes.php');	// fichier contenant les fonctions, la config pear, les mdp databases ...
	
	// adresse de connexion à la base de données
	$dsn_ocs 	= 'mysql://'. $user .':' . $pass . '@localhost/' . $ocsweb;
	$dsn_gespac     = 'mysql://'. $user .':' . $pass . '@localhost/' . $gespac;

	// cnx à la base de données OCS
	$db_ocs 	= & MDB2::factory($dsn_ocs);
	
	// stockage des lignes retournées par sql dans un tableau
	$liste_marques_ocs  		= $db_ocs->queryAll ( "SELECT DISTINCT smanufacturer, smodel FROM bios;" );
	$liste_marques_ecrans_ocs   = $db_ocs->queryAll ( "SELECT DISTINCT manufacturer, caption FROM monitors WHERE serial <> '';" );
	$liste_hardware_ocs 		= $db_ocs->queryAll ( "SELECT hardware.id, name, smanufacturer, smodel, ssn, macaddr, speed FROM hardware, bios, networks where hardware.id=bios.hardware_id AND hardware.id=networks.hardware_id ORDER BY name;" );
	
	$liste_monitors_ocs 		= $db_ocs->queryAll ( "SELECT DISTINCT serial, manufacturer, caption, name from hardware, monitors WHERE serial <> '' AND hardware.id = monitors.hardware_id;" );

	
	// cnx à la base de données GESPAC
	$db_gespac 	= & MDB2::factory($dsn_gespac);

	$liste_marques_gespac		= $db_gespac->queryAll ( "SELECT marque_id, CONCAT(TRIM(marque_marque), ' ', TRIM(marque_model)) FROM marques;" );
	$liste_hardware_gespac  	= $db_gespac->queryAll ( "SELECT mat_id, mat_nom, mat_serial, mat_mac, marque_id FROM materiels;" );
	
	//$liste_correspondances 		= $db_gespac->queryAll ( "SELECT corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances;" );

		
	// On se déconnecte de la db
	$db_ocs->disconnect();
	$db_gespac->disconnect();
	
	
	// on trouve une valeur dans une table à partir de son ssn et du numéro du champ à extraire
	function search_in_array($needle, $haystack, $field=0) {
		foreach($haystack as $key=>$value) { // association à chaque élément du tableau en entrée d'un autre tableau d'éléments. (permet de palier au manquement de la fonction in_array de php)
			$current_key=$key;
			
			// si la valeur cherchée correspond à la valeur de la clé courante ou, si la valeur est tableau, réutiliser la fonction dessus (plus facile en fn recursive) 
			if($needle===$value OR (is_array($value) && search_in_array($needle,$value))) {
				return $value[$field];
			}
		}
		return false;
	} 

		
	/***********************************************
	*	 	Pour chaque PC de la base OCS
	***********************************************/
	foreach ($liste_hardware_ocs as $hardware_ocs) {
		
		$id_ocs 	= $hardware_ocs[0];
		$nom_ocs 	= $hardware_ocs[1];
		$marque_ocs = $hardware_ocs[2];
		$modele_ocs = $hardware_ocs[3];
		$ssn_ocs 	= $hardware_ocs[4];
		$mac_ocs	= $hardware_ocs[5];
		$speed_ocs 	= $hardware_ocs[6];
		
		$marque_et_model_ocs = $marque_ocs . " " . $modele_ocs;
		
		// l'id du matériel gespac en fonction du ssn du matériel OCS
		$gespac_matid_from_ocs_ssn = search_in_array($ssn_ocs, $liste_hardware_gespac);
		

		// Debut du test d'existence de la marque et du modele dans la table marques de gespac
		
			/*
				En résumé : on cherche à savoir si la marque ocs existe déjà dans la base gespac.
				On prend le couple marque/modele OCS et on teste son existance dans la table des correspondances
				Si le couple existe alors on cherche à savoir si les champs corr_marque et corr_modele existent dans la table des marques de gespac,
				sinon c'est que la correspondance n'existe pas et qu'on a dû insérer dans la table des marques le couple marque/modele de ocs.
			*/
			
			// cnx à la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);
			
			// La liste des correspondances
			$liste_correspondances 		= $db_gespac->queryAll ( "SELECT corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances WHERE corr_marque_ocs = '$marque_ocs" . " " . "$modele_ocs';" );
			// Si la correspondance existe on teste avec les champs corr_marque et corr_modele
			if ( $liste_correspondances ) {
				$marque = $liste_correspondances[0][3];
				$modele = $liste_correspondances[0][4];
				$famille = $liste_correspondances[0][1];
				$sfamille = $liste_correspondances[0][2];
			}
			// sinon on utilise les valeurs de OCS
			else {
				$marque = $marque_ocs;
				$modele = $modele_ocs;
				$famille = "PC";
				$sfamille = "DESKTOP";
			}
						
			// On teste maintenant si la marque existe dans gespac et on récupère son id (le champ de test est la concaténation de la marque et du modele)
			$gespac_marqueid_from_ocs_marque_modele = find_marque_id($marque . " " . $modele, $liste_marques_gespac);

		// Fin du test d'existence de la marque et du modele dans la table marques de gespac
		
		
		
		
		
		
		/******************************************************
		*	 	Le matériel n'existe pas dans la base gespac
		*******************************************************/
		if ( $gespac_matid_from_ocs_ssn == false ) {
			$biglog .= " -> Je n'ai pas $nom_ocs (ssn : $ssn_ocs) dans ma base gespac.<br>";
			
			// le matériel n'existe pas dans gespac et sa marque non plus
			if ( $gespac_marqueid_from_ocs_marque_modele == false ) {
				
				$biglog .= "la marque OCS $marque &nbsp $modele du matériel ocs $nom_ocs n'est pas dans ma base gespac <br>";
				
				$biglog .= "Création de la marque avec pour paramètres : PC, DESKTOP, $marque, $modele<br>";
				
				$quadruplet = $famille . $sfamille . $marque . $modele;
				
				if ( !in_array ($quadruplet, $marques_creees) ) {
					$req_insert_marque = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model ) VALUES ('$famille', '$sfamille', '$marque', '$modele' )";
					$result = $db_gespac->exec ( $req_insert_marque );
				
					// Pas très propre => A recoder :
					// La marque une fois créée n'est pas comptabilisé dans la liste [gespac_marqueid_from_ocs_marque_modele]
					// Je créé donc un tableau contenant toutes les marques que j'ajoute afin de ne pas créer plusieurs fois la même marque.
					
					$quadruplet = $famille . $sfamille . $marque . $modele;
					array_push($marques_creees, $quadruplet); 
					
					$marques_ajoutees .= "Ajout de la marque et du modèle <b>$marque &nbsp $modele</b>.<br>";
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_marque."\n");
				}
				else {
					// On log que la marque vient d'être créée
					$biglog .= "La marque $famille / $sfamille / $marque / $modele a déjà été créée. On ne la recrée pas ...<br>";
				}
				
				$biglog .= "On récupère le marque_id de la marque nouvellement créée.<br>";
				$id_marque_gespac = $db_gespac->queryAll ( "SELECT marque_id FROM marques WHERE marque_type='$famille' AND marque_stype='$sfamille' AND marque_marque='$marque' AND marque_model='$modele';" );
				$id_nouvelle_marque = $id_marque_gespac[0][0];
								
				// Si le matériel OCS n'a pas de ssn
				if ( $ssn_ocs == "") {
					$rand_ssn_ocs = "RAND" . rand(0, 99999);
					$biglog .= "On met à jour le matériel ocs ($id_ocs) avec le SSN aléatoire $rand_ssn_ocs ";
					$req_update_ssn_ocs = "UPDATE ocsweb.bios SET SSN='$rand_ssn_ocs' WHERE HARDWARE_ID=$id_ocs";
					$result = $db_ocs->exec ( $req_update_ssn_ocs );
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
					
					$biglog .= "Création du matériel $nom_ocs de ssn $rand_ssn_ocs avec pour marque_id le résultat de la ligne [SQL2] <br>";
					$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('$nom_ocs', '$rand_ssn_ocs', $id_nouvelle_marque, '$mac_ocs')";
					$result = $db_gespac->exec ( $req_insert_materiel_gespac );
					
					$materiels_ajoutes .= "Création du matériel <b>$nom_ocs</b> de ssn <b>$rand_ssn_ocs</b> avec pour marque et modèle <b>$marque &nbsp $modele</b><br>";
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
					
				} 
				else {
					$biglog .= "Création du matériel $nom_ocs de ssn $ssn_ocs avec pour marque_id le résultat de la ligne [SQL2] <br>";
					$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('$nom_ocs', '$ssn_ocs', $id_nouvelle_marque, '$mac_ocs')";
					$result = $db_gespac->exec ( $req_insert_materiel_gespac );
					
					$materiels_ajoutes .= "Création du matériel <b>$nom_ocs</b> de ssn <b>$ssn_ocs</b> avec pour marque et modèle <b>$marque &nbsp $modele</b><br>";
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
						
				}
			}
			// le matériel n'existe pas dans gespac mais sa marque est dans la base
			else {
				$biglog .= "la marque OCS <b>$marque &nbsp $modele</b> du matériel ocs $nom_ocs est dans ma base gespac à l'indice $gespac_marqueid_from_ocs_marque_modele<br>";
				
				$marques_deja_presentes .= "La marque OCS <b>$marque &nbsp $modele</b> du matériel ocs <b>$nom_ocs</b> est dans ma base Gespac<br>";
				
				// Si le matériel OCS n'a pas de ssn
				if ( $ssn_ocs == "") {
					$rand_ssn_ocs = "RAND" . rand(0, 99999);
					$biglog .= "On met à jour le matériel ocs ($id_ocs) avec le SSN aléatoire $rand_ssn_ocs ";
					$req_update_ssn_ocs = "UPDATE ocsweb.bios SET SSN='$rand_ssn_ocs' WHERE HARDWARE_ID=$id_ocs";
					$result = $db_ocs->exec ( $req_update_ssn_ocs );
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $rq_MAJ_nom_materiel_gespac."\n");
					
					$biglog .= "Création du matériel $nom_ocs de ssn $rand_ssn_ocs avec pour marque_id $gespac_marqueid_from_ocs_marque_modele <br>";
					$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('$nom_ocs', '$rand_ssn_ocs', $gespac_marqueid_from_ocs_marque_modele, '$mac_ocs')";
					$result = $db_gespac->exec ( $req_insert_materiel_gespac );
					
					$materiels_ajoutes .= "Création du matériel <b>$nom_ocs</b> de ssn <b>$rand_ssn_ocs</b> avec pour marque et modèle <b>$marque &nbsp $modele</b><br>";
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
				} 
				else { //Le matériel OCS a un ssn
					$biglog .= "Création du matériel $nom_ocs de ssn $ssn_ocs avec pour marque_id $gespac_marqueid_from_ocs_marque_modele <br>";
					$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('$nom_ocs', '$ssn_ocs', $gespac_marqueid_from_ocs_marque_modele, '$mac_ocs')";
					$result = $db_gespac->exec ( $req_insert_materiel_gespac );
					
					$materiels_ajoutes .= "Création du matériel <b>$nom_ocs</b> de ssn <b>$ssn_ocs</b> avec pour marque et modèle <b>$marque &nbsp $modele</b><br>";
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
				}
			}
			$biglog .= "----------------<br>";			
			
		} // end of "Le matériel n'existe pas dans la base"
		
		/******************************************************
		*	 	Le matériel existe dans la base gespac
		*******************************************************/
		else {
			$biglog .= " -> J'ai le matériel $nom_ocs ($ssn_ocs) dans ma base gespac.<br>";
			
			$biglog .= "On récupère la dernière version de l'inventaire de la machine ocs $id_ocs.<br>";	
			$db_ocs = & MDB2::factory($dsn_ocs);
			//$dernier_inventaire_ocs	= $db_ocs->queryAll ( "SELECT NAME, MAX(LASTDATE) FROM hardware WHERE ID=$id_ocs GROUP BY NAME;" );	
			$dernier_inventaire_ocs	= $db_ocs->queryAll ( "SELECT NAME, LASTDATE, MAX(LASTDATE) FROM hardware, bios WHERE ssn='$ssn_ocs' AND hardware.id=bios.hardware_id GROUP BY NAME;" );	
			$db_ocs->disconnect();			
						
			$nom_hardware_gespac = search_in_array ($ssn_ocs, $liste_hardware_gespac, 1);
			
			$biglog .= "Le nom de la machine ocs $id_ocs (ssn : $ssn_ocs) lors de son dernier inventaire (" . $dernier_inventaire_ocs[0][1] . ") : " . $dernier_inventaire_ocs[0][0] . " celui de gespac est : $nom_hardware_gespac<br>";	
			
			
			if ( $dernier_inventaire_ocs[0][0] <>  $nom_hardware_gespac) {
				$biglog .= "On met à jour le matériel GESPAC numéro $gespac_matid_from_ocs_ssn avec le nouveau nom OCS : " . $dernier_inventaire_ocs[0][0] . "<br>";
					$db_gespac 	= & MDB2::factory($dsn_gespac);
					$rq_MAJ_nom_materiel_gespac	= "UPDATE materiels SET mat_nom='" . $dernier_inventaire_ocs[0][0] . "' WHERE mat_id=$gespac_matid_from_ocs_ssn" ;
					$result = $db_gespac->exec ( $rq_MAJ_nom_materiel_gespac );
					
					$materiels_maj .= "Mise à jour du matériel GESPAC <b>$nom_hardware_gespac</b> avec le nouveau nom OCS : <b>" . $dernier_inventaire_ocs[0][0] . "</b><br>";"";
					
					$db_gespac->disconnect();	
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $rq_MAJ_nom_materiel_gespac."\n");
			}
			else {
				$biglog .= "Le nom du matériel dans gespac et du dernier inventaire ocs est identique <br>";
				$materiels_deja_presents .= "Le matériel <b>$nom_hardware_gespac</b> est présent et à jour dans la base gespac. <br>";
			}

				

			$biglog .= "----------------<br>";			
		
		} 
	} // end of "Pour chaque matériel de la base OCS"
	
	
	
	
	
	/***********************************************
	*	 Pour chaque écran de la base OCS
	***********************************************/
	foreach ($liste_monitors_ocs as $monitors_ocs) {
		$monitor_ssn 		= $monitors_ocs[0];
		$monitor_marque 	= $monitors_ocs[1];
		$monitor_modele 	= $monitors_ocs[2];
		$monitor_pc_name	= $monitors_ocs[3];
		
	
		// Nom du matériel associé au ssn de l'écran (ca permet de tester l'existence du matériel dans gespac)
		$nom_pc_associe = search_in_array($monitor_ssn, $liste_hardware_gespac, 1 );
		
		
		// Debut du test d'existence de la marque et du modele dans la table marques de gespac
		
			/*
				En résumé : on cherche à savoir si la marque ocs existe déjà dans la base gespac.
				On prend le couple marque/modele OCS et on teste son existance dans la table des correspondances
				Si le couple existe alors on cherche à savoir si les champs corr_marque et corr_modele existent dans la table des marques de gespac,
				sinon c'est que la correspondance n'existe pas et qu'on a dû insérer dans la table des marques le couple marque/modele de ocs.
			*/
			
			// cnx à la base de données GESPAC
			$db_gespac 	= & MDB2::factory($dsn_gespac);
			
			// La liste des correspondances
			$liste_correspondances 		= $db_gespac->queryAll ( "SELECT corr_marque_ocs, corr_type, corr_stype, corr_marque, corr_modele FROM correspondances WHERE corr_marque_ocs = '$monitor_marque" . " " . "$monitor_modele';" );

			// Si la correspondance existe on teste avec les champs corr_marque et corr_modele
			if ( $liste_correspondances  ) {
				$marque = $liste_correspondances[0][3];
				$modele = $liste_correspondances[0][4];
			}
			// sinon on utilise les valeurs de OCS
			else {
				$marque = $monitor_marque;
				$modele = $monitor_modele;
			}
			
			
			// On teste maintenant si la marque existe dans gespac et on récupère son id (le champ de test est la concaténation de la marque et du modele)
			$gespac_marqueid_from_ocs_marque_modele = find_marque_id($marque . " " . $modele, $liste_marques_gespac);

		// Fin du test d'existence de la marque et du modele dans la table marques de gespac
	
	
		/******************************************************
		*	 	Le matériel n'existe pas dans la base gespac
		*******************************************************/
		if ( $nom_pc_associe == false ) {
			
			// La marque n'existe pas et le matériel non plus
			if ( $gespac_marqueid_from_ocs_marque_modele == false ) {

				// Si la correspondance existe on insère les champs issus de la table de correspondance sinon on utilise les champs de la base OCS
				if ( $liste_correspondances ) {
					$type = $liste_correspondances[0][1];
					$stype = $liste_correspondances[0][2];
					$marque = $liste_correspondances[0][3];
					$modele = $liste_correspondances[0][4];
				}
				else {
					$type = 'ECRAN';
					$stype = 'CRT17';
					$marque = $monitor_marque;
					$modele = $monitor_modele;
				}
				
				$quadruplet = $type . $stype . $marque . $modele;
				
				if ( !in_array ($quadruplet, $marques_creees) ) {
				
					// On créé la marque
					$biglog .= "Création de la marque : <b>$marque $modele</b> de type $type.<br>";
					$req_insert_marque = "INSERT INTO marques ( marque_type, marque_stype, marque_marque, marque_model ) VALUES ('$type', '$stype', '$marque', '$modele' )";
					$result = $db_gespac->exec ( $req_insert_marque );
					
					
					// Pas très propre => A recoder :
					// La marque une fois créée n'est pas comptabilisé dans la liste [gespac_marqueid_from_ocs_marque_modele]
					// Je créé donc un tableau contenant toutes les marques que j'ajoute afin de ne pas créer plusieurs fois la même marque.
					
					$quadruplet = $type . $stype . $marque . $modele;
					array_push($marques_creees, $quadruplet); 
					
					
					// On log la requête SQL
					fwrite($fp, date("Ymd His") . " " . $req_insert_marque."\n");
					
					$marques_ajoutees .= "Ajout de la marque et du modèle <b>$marque $modele</b> de type $type / $stype.<br>";
				}
				else {
					// On log que la marque vient d'être créée
					$biglog .= "La marque $type / $stype / $marque / $modele a déjà été créée. On ne la recrée pas ...<br>";
				}
				
				// On récupère le id de la marque créée
				$biglog .= "On récupère le marque_id de la marque nouvellement créée.<br>";
				$id_marque_gespac = $db_gespac->queryAll ( "SELECT marque_id FROM marques WHERE marque_marque='$marque' AND marque_model='$modele';" );
				$id_nouvelle_marque = $id_marque_gespac[0][0];
				
				// On insère l'écran
				$biglog .= "Création du matériel Ecran_de_$monitor_pc_name de ssn $monitor_ssn avec pour marque_id $id_nouvelle_marque <br>";
				$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('Ecran_de_$monitor_pc_name', '$monitor_ssn', $id_nouvelle_marque, 'NA')";
				$result = $db_gespac->exec ( $req_insert_materiel_gespac );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
				
				$ecrans_ajoutes .= "Ajout de <b>Ecran_de_$monitor_pc_name</b> de ssn <b>$monitor_ssn</b> et de marque <b>$marque $modele</b><br>";

			}
			
			// La marque existe mais pas le matériel
			else {
			
				// On récupère le id de la marque trouvée
				$biglog .= "On récupère le marque_id de la marque nouvellement créée.<br>";
				$id_marque_gespac = $db_gespac->queryAll ( "SELECT marque_id FROM marques WHERE marque_marque='$marque' AND marque_model='$modele';" );
				$id_nouvelle_marque = $id_marque_gespac[0][0];
				
				// On insère l'écran
				$biglog .= "Création du matériel Ecran_de_$monitor_pc_name de ssn $monitor_ssn avec pour marque_id $id_nouvelle_marque <br>";
				$req_insert_materiel_gespac = "INSERT INTO materiels (mat_nom, mat_serial, marque_id, mat_mac) VALUES ('Ecran_de_$monitor_pc_name', '$monitor_ssn', $id_nouvelle_marque, 'NA')";
				$result = $db_gespac->exec ( $req_insert_materiel_gespac );
				
				// On log la requête SQL
				fwrite($fp, date("Ymd His") . " " . $req_insert_materiel_gespac."\n");
				
				$ecrans_ajoutes .= "Ajout de <b>Ecran_de_$monitor_pc_name</b> de ssn <b>$monitor_ssn</b> et de marque <b>$marque $modele</b><br>";
			}
		
		}
		
		
		/*************************************************************
		* Le matériel existe dans la base gespac donc sa marque aussi
		**************************************************************/		
		else {
			$biglog .= "l'écran de ssn <b>$monitor_ssn</b> est déjà dans Gespac.<br>";
			$ecrans_deja_presents .= "l'écran de ssn <b>$monitor_ssn</b> est déjà dans Gespac.<br>";
		}
		
		
		
		$biglog .= "----------------<br>";			

	} // End pour chaque écran de la base ocs

	
	// Je ferme le fichier  de log sql
	fclose($fp);
	

	//recap et compteurs ! (et on en profite pour ne pas écrire 10 fois qu'une marque est créée par ex)
	
	$unique_marques_ajoutees = array_unique (explode ("<br>", $marques_ajoutees));
	$liste_marques_ajoutees = implode ("<br>", $unique_marques_ajoutees);
	
	$unique_marques_deja_presentes = array_unique (explode ("<br>", $marques_deja_presentes));
	$liste_marques_deja_presentes = implode ("<br>", $unique_marques_deja_presentes);
	
	$unique_materiels_ajoutes = array_unique (explode ("<br>", $materiels_ajoutes));
	$liste_materiels_ajoutes = implode ("<br>", $unique_materiels_ajoutes);
	
	$unique_materiels_maj = array_unique (explode ("<br>", $materiels_maj));
	$liste_materiels_maj = implode ("<br>", $unique_materiels_maj);	
	
	$unique_materiels_deja_presents = array_unique (explode ("<br>", $materiels_deja_presents));
	$liste_materiels_deja_presents = implode ("<br>", $unique_materiels_deja_presents);	
	
	$unique_ecrans_deja_presents = array_unique (explode ("<br>", $ecrans_deja_presents));
	$liste_ecrans_deja_presents = implode ("<br>", $unique_ecrans_deja_presents);	

	$unique_ecrans_ajoutes = array_unique (explode ("<br>", $ecrans_ajoutes));
	$liste_ecrans_ajoutes = implode ("<br>", $unique_ecrans_ajoutes);		

	echo "
		- <a href='#' onclick=\"showlogs('marques_ajoutees');\">nombre de marques ajoutées : " . (count($unique_marques_ajoutees)-1) . " </a><br>
		<div id=marques_ajoutees style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_marques_ajoutees</div>
	";
	echo "
		- <a href='#' onclick=\"showlogs('marques_deja_presentes');\">nombre de marques déjà présentes : " . (count($unique_marques_deja_presentes)-1) . " </a><br>
		<div id=marques_deja_presentes style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_marques_deja_presentes</div>
	";
	echo "		
		- <a href='#' onclick=\"showlogs('materiels_ajoutes');\">nombre de matériels ajoutés : " . (count($unique_materiels_ajoutes)-1) . " </a><br>
		<div id=materiels_ajoutes style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_materiels_ajoutes</div>
	";
	echo "		
		- <a href='#' onclick=\"showlogs('materiels_maj');\">nombre de matériels mis à jour : " . (count($unique_materiels_maj)-1) . " </a><br>
		<div id=materiels_maj style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_materiels_maj</div>
	";
	echo "		
		- <a href='#' onclick=\"showlogs('materiels_deja_presents');\">nombre de matériels déjà présents et à jour : " . (count($unique_materiels_deja_presents)-1) . " </a><br>
		<div id=materiels_deja_presents style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_materiels_deja_presents</div> 
	";
	echo "		
		- <a href='#' onclick=\"showlogs('ecrans_ajoutes');\">nombre d'écrans ajoutés : " . (count($unique_ecrans_ajoutes)-1) . " </a><br>
		<div id=ecrans_ajoutes style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_ecrans_ajoutes</div> 
	";
	echo "		
		- <a href='#' onclick=\"showlogs('ecrans_deja_presents');\">nombre d'écrans déjà présents dans Gespac : " . (count($unique_ecrans_deja_presents)-1) . " </a><br>
		<div id=ecrans_deja_presents style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$liste_ecrans_deja_presents</div> 
	";
	
	// Le gros log
	echo "
		<br><br><a href='#' onclick=\"showlogs('biglog');\">- Montrer les gros logs</a>
		<div id=biglog style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>$biglog</div>
	";
	
	echo "<br><br><a href='#' onclick=\"showlogs('correspondances');\">- Montrer les mises à jour de correspondances</a>
		<div id=correspondances style='background-color:#E3E3E3;border:1px solid black;display:none;padding:5px;'>";
		include ("maj_marques_avec_correspondances.php");
	echo "</div>";
	
	
	
	//Insertion d'un log

	$log_texte =
		"Nombre de marques déjà présentes : " . (count($unique_marques_deja_presentes)-1) . 
		"<br>Nombre de matériels ajoutés : " . (count($unique_materiels_ajoutes)-1) . 
		"<br>Nombre de matériels mis à jour : " . (count($unique_materiels_maj)-1) .
		"<br>Nombre d`écrans ajoutés : " . (count($unique_ecrans_ajoutes)-1);

	$req_log_import_ocs = "INSERT INTO logs ( log_type, log_texte ) VALUES ( 'Import OCS', '$log_texte' );";
	$result = $db_gespac->exec ( $req_log_import_ocs );
	
	
?>

<!--  SERVEUR AJAX -->
<script type="text/javascript" src="server.php?client=all"></script>

