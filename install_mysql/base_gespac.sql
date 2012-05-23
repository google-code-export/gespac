SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


CREATE SCHEMA `gespac` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `gespac`;

CREATE TABLE `basedoc` (
  `doc_id` int(11) NOT NULL auto_increment,
  `doc_titre` varchar(100) default NULL,
  `doc_date_creat` datetime default NULL,
  `doc_date_modif` datetime default NULL,
  `doc_texte` text,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`doc_id`),
  KEY `FK_basedoc_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `college` (
  `clg_uai` varchar(10) NOT NULL,
  `clg_nom` varchar(255) default NULL,
  `clg_ati` varchar(255) default NULL,
  `clg_ati_mail` varchar(255) default NULL,
  `clg_adresse` varchar(255) default NULL,
  `clg_cp` varchar(20) default NULL,
  `clg_ville` varchar(255) default NULL,
  `clg_tel` varchar(20) default NULL,
  `clg_fax` varchar(20) default NULL,
  `clg_site_web` varchar(255) default NULL,
  `clg_site_grr` varchar(255) default NULL,
  PRIMARY KEY  (`clg_uai`),
  UNIQUE KEY `clg_uai` (`clg_uai`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `demandes` (
  `dem_id` int(11) NOT NULL auto_increment,
  `dem_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `dem_text` text,
  `dem_etat` varchar(100) NOT NULL,
  `dem_type` varchar(30) NOT NULL,
  `user_demandeur_id` int(11) default NULL,
  `user_intervenant_id` int(11) default NULL,
  `mat_id` int(11) default NULL,
  `salle_id` int(11) default NULL,
  PRIMARY KEY  (`dem_id`),
  KEY `FK_demandes_user_demandeur_id` (`user_demandeur_id`),
  KEY `FK_demandes_mat_id` (`mat_id`),
  KEY `FK_demandes_salle_id` (`salle_id`),
  KEY `FK_demandes_user_intervenant_id` (`user_intervenant_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `demandes_textes` (
  `txt_id` int(11) NOT NULL auto_increment,
  `txt_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `txt_etat` varchar(30) NOT NULL,
  `txt_texte` text NOT NULL,
  `dem_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`txt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `etats` (
  `etat` varchar(255) NOT NULL,
  PRIMARY KEY  (`etat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `interventions` (
  `interv_id` int(11) NOT NULL auto_increment,
  `interv_date` timestamp NULL default CURRENT_TIMESTAMP,
  `interv_cloture` timestamp NULL default NULL,
  `interv_text` text,
  `dem_id` int(11) default NULL,
  `salle_id` int(11) default NULL,
  `mat_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`interv_id`),
  KEY `FK_interventions_dem_id` (`dem_id`),
  KEY `FK_interventions_salle_id` (`salle_id`),
  KEY `FK_interventions_mat_id` (`mat_id`),
  KEY `FK_interventions_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_type` varchar(30) NOT NULL,
  `log_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `log_texte` text NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `marques` (
  `marque_id` int(11) NOT NULL auto_increment,
  `marque_marque` varchar(255) NOT NULL,
  `marque_model` varchar(255) default NULL,
  `marque_type` varchar(255) default NULL,
  `marque_stype` varchar(255) NOT NULL,
  `marque_suppr` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`marque_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `materiels` (
  `mat_id` int(11) NOT NULL auto_increment,
  `mat_nom` varchar(255) default NULL,
  `mat_dsit` varchar(100) default NULL,
  `mat_serial` varchar(100) default NULL,
  `mat_mac` varchar(17) default NULL,
  `mat_etat` varchar(100) default 'Fonctionnel',
  `mat_origine` varchar(7) NOT NULL,
  `salle_id` int(11) NOT NULL default '1',
  `user_id` int(11) default '1',
  `marque_id` int(11) default NULL,
  `mat_suppr` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`mat_id`),
  UNIQUE KEY `mat_serial` (`mat_serial`),
  KEY `FK_materiels_salle_id` (`salle_id`),
  KEY `FK_materiels_user_id` (`user_id`),
  KEY `FK_materiels_marque_id` (`marque_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `origines` (
  `origine` varchar(20) NOT NULL default 'INCONNUE',
  PRIMARY KEY  (`origine`),
  UNIQUE KEY `origine` (`origine`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `salles` (
  `salle_id` int(11) NOT NULL auto_increment,
  `salle_nom` varchar(80) default NULL,
  `salle_vlan` varchar(30) default NULL,
  `salle_etage` varchar(30) default NULL,
  `salle_batiment` varchar(30) default NULL,
  `clg_uai` varchar(10) default NULL,
  `salle_suppr` tinyint(1) NOT NULL default '0',
  `est_modifiable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`salle_id`),
  UNIQUE KEY `salle_nom` (`salle_nom`),
  KEY `FK_salles_clg_uai` (`clg_uai`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_nom` varchar(255) default NULL,
  `user_logon` varchar(20) NOT NULL,
  `user_password` varchar(15) default NULL,
  `grade_id` int(11) default '3',
  `user_skin` varchar(150) NOT NULL default 'cg13',
  `user_accueil` varchar(255) NOT NULL default 'modules/stats/csschart.php',
  `user_mail` varchar(100) NOT NULL,
  `user_mailing` tinyint(1) NOT NULL default '0',
  `user_suppr` tinyint(1) NOT NULL,
  `est_modifiable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_logon` (`user_logon`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE `correspondances` (
  `corr_id` int(11) NOT NULL auto_increment,
  `corr_marque_ocs` varchar(255) NOT NULL,
  `corr_type` varchar(255) NOT NULL,
  `corr_stype` varchar(255) NOT NULL,
  `corr_marque` varchar(255) NOT NULL,
  `corr_modele` varchar(255) NOT NULL,
  PRIMARY KEY  (`corr_id`),
  UNIQUE KEY `corr_marque_ocs` (`corr_marque_ocs`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `grades` (
  `grade_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `grade_nom` varchar(255) NOT NULL,
  `grade_menu` text NOT NULL,
  `grade_menu_portail` text NOT NULL,
  `est_modifiable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`grade_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `menu_portail` (
  `mp_id` int(11) NOT NULL AUTO_INCREMENT,
  `mp_nom` varchar(255) NOT NULL,
  `mp_url` varchar(255) NOT NULL,
  `mp_icone` varchar(255) NOT NULL,
  `est_modifiable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`mp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

CREATE TABLE IF NOT EXISTS `dossiers` (
  `dossier_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dossier_type` varchar(255) NOT NULL,
  `dossier_mat` text NOT NULL,
  `dossier_mailing` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`dossier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dossiers_textes` (
  `txt_id` int(11) NOT NULL AUTO_INCREMENT,
  `dossier_id` int(11) NOT NULL,
  `txt_user` int(11) NOT NULL,
  `txt_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `txt_texte` text NOT NULL,
  `txt_etat` varchar(255) NOT NULL,
  PRIMARY KEY (`txt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `droits` (
  `droit_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `droit_index` varchar(5) NOT NULL,
  `droit_titre` varchar(255) NOT NULL,
  `droit_page` varchar(255) NOT NULL,
  `droit_etendue` tinyint(1) NOT NULL DEFAULT '1',
  `droit_description` varchar(255) NOT NULL,
  PRIMARY KEY (`droit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `fichiers` (
  `fichier_id` int(11) NOT NULL AUTO_INCREMENT,
  `fichier_chemin` varchar(255) NOT NULL,
  `fichier_description` text NOT NULL,
  `fichier_droits` varchar(2) NOT NULL DEFAULT '00',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`fichier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dossiers_types` (
  `type` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO users (user_id, user_nom, user_logon, user_password, grade_id, user_skin, user_accueil, user_mail, user_mailing, user_suppr, est_modifiable) VALUES (1, 'ati', 'ati', 'azerty', 1, 'cg13', 'modules/stats/csschart.php', '', 1, 0, 0);

INSERT IGNORE INTO `etats` (`etat`) VALUES
('ATTENTE SAV'),
('AUTRES'),
('CASSE'),
('DEPLOIEMENT EN COURS'),
('ENVOYE SAV'),
('FONCTIONNEL'),
('NON DEBALLE'),
('PANNE'),
('PERDU'),
('REPARATION PAR L`ATI'),
('VOLE');

INSERT IGNORE INTO `dossiers_types` (`type`) VALUES
('REPARATION'),
('INSTALLATION'),
('USAGE'),
('FORMATION');

INSERT IGNORE INTO `origines` (`origine`) VALUES
('CLG2003'),
('CLG2004'),
('CLG2005'),
('CLG2006'),
('CLG2007'),
('CLG2008'),
('CLG2009'),
('CLG2010'),
('CLG2011'),
('CLG2012'),
('CLG2013'),
('CLG2014'),
('CLG2015'),
('DOT2003'),
('DOT2004'),
('DOT2005'),
('DOT2006'),
('DOT2007'),
('DOT2008'),
('DOT2009'),
('DOT2010'),
('DOT2011'),
('DOT2012'),
('DOT2013'),
('DOT2014'),
('DOT2015'),
('INCONNUE'),
('MUT2009'),
('MUT2010'),
('MUT2011'),
('MUT2012');

INSERT INTO `grades` (`grade_id`, `grade_nom`, `grade_menu`, `grade_menu_portail`, `est_modifiable` ) VALUES
(1, 'root', '','{"item1":"on"}', '0'),
(3, 'PROFESSEURS', '{"L-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","L-06-04":"on","E-06-04":"on","L-08-01":"on"}','{"item1":"on"}', '1'),
(4, 'INTENDANCE', '','{"item1":"on"}', '1'),
(5, 'DIRECTION', '','{"item1":"on"}', '1'),
(6, 'TICE', '{"L-02-01":"on","L-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","L-05-01":"on","L-06-01":"on","L-06-04":"on","E-06-04":"on","L-07-02":"on","E-07-02":"on","L-08-01":"on","L-08-02":"on","L-08-04":"on","E-08-04":"on"}','{"item1":"on"}', '1'),
(7, 'VIE SCOLAIRE', '','{"item1":"on"}', '1'),
(8, 'ADMINISTRATIF', '','{"item1":"on"}', '1'),
(2, 'ATI', '{"L-01-01":"on","E-01-01":"on","L-02-01":"on","E-02-01":"on","L-02-02":"on","E-02-02":"on","L-02-03":"on","E-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","E-03-02":"on","L-04-01":"on","E-04-01":"on","L-04-02":"on","E-04-02":"on","L-04-03":"on","E-04-03":"on","L-04-04":"on","E-04-04":"on","L-04-05":"on","E-04-05":"on","L-04-06":"on","E-04-06":"on","L-05-01":"on","E-05-01":"on","L-06-01":"on","E-06-01":"on","L-06-02":"on","E-06-02":"on","L-06-03":"on","E-06-03":"on","L-06-04":"on","E-06-04":"on","L-07-01":"on","E-07-01":"on","L-07-02":"on","E-07-02":"on","L-07-03":"on","E-07-03":"on","L-07-04":"on","E-07-04":"on","L-07-05":"on","E-07-05":"on","L-08-01":"on","E-08-01":"on","L-08-02":"on","E-08-02":"on","L-08-03":"on","E-08-03":"on","L-08-04":"on","E-08-04":"on","L-08-05":"on","E-08-05":"on"}','{"item1":"on"}', '1');

INSERT INTO `menu_portail` (`mp_id`, `mp_nom`, `mp_url`, `mp_icone`, `est_modifiable`) VALUES
(2, 'FOG', 'http://gespac/fog', 'fog.png', '0'),
(3, 'OCS', 'http://gespac/ocsreports', 'ocs.png', '0'),
(4, 'RESTAURATION', './gespac/gestion_donnees/form_upload_restauration.php', 'database.png', '0');

INSERT IGNORE INTO `droits` (`droit_id`, `droit_index`, `droit_titre`, `droit_page`, `droit_etendue`, `droit_description`) VALUES
(1, '01-01', 'Retour au portail', 'index.php', 0, 'Affiche le menu de retour au portail.'),
(2, '02-01', 'Visualiser les matériels', 'gestion_inventaire/voir_materiels.php', 1, 'Voir/Créer des matériels dans inventaire'),
(3, '02-02', 'Visualiser les marques', 'gestion_invetaire/voir_marques.php', 1, 'Voir/Créer des marques'),
(4, '02-03', 'Visualiser les salles', 'gestion_invetaire/voir_salles.php', 1, 'Voir/Créer des salles'),
(5, '03-01', 'Old dossiers', 'gestion_demandes/voir_demandes.php', 1, 'Voir/Créer des dossiers'),
(6, '03-02', 'Old interventions', 'gestion_demandes/voir_interventions.php', 1, 'Voir/Créer des interventions'),
(7, '03-03', 'Dossiers', 'gestion_dossiers/voir_dossiers.php', 1, 'Voir/Créer des dossiers'),
(8, '03-04', 'Créer des interventions', '', 0, 'Autoriser la création des interventions et la cloture des dossiers.'),
(9, '04-01', 'Importer DB OCS', 'gestion_donnees/voir_ocs_db.php', 1, 'Voir/Importer la base OCS.'),
(10, '04-02', 'Exports', 'gestion_donnees/exports.php', 0, 'Afficher la page des exports.'),
(11, '04-03', 'Dump base GESPAC', 'gestion_donnees/dump_db_gespac.php', 0, 'Autoriser le dump de la base Gespac.'),
(12, '04-04', 'Dump base OCS', 'gestion_donnees/dump_db_ocs.php', 0, 'Autoriser le dump de la base OCS.'),
(13, '04-05', 'Voir les Logs', 'gestion_donnees/voir_logs.php', 1, 'Voir/Vider les logs'),
(14, '04-06', 'Importer CSV', 'gestion_inventaire/form_import_csv.php', 0, 'Importer un fichier CSV de matériels.'),
(15, '05-01', 'Prêts', 'gestion_prets/voir_prets.php', 1, 'Voir/Prêter/Rendre un matériel.'),
(16, '06-01', 'Visualiser les utilisateurs', 'gestion_utilisateurs/voir_utilisateurs.php', 1, 'Voir, créer ou modifier un utilisateur.'),
(17, '06-02', 'Visualiser les grades', 'gestion_utilisateurs/voir_grades.php', 1, 'Voir, créer ou modifier un grade et gérer les droits.'),
(18, '06-03', 'Importer les comptes IACA', 'gestion_utilisateurs/form_comptes_iaca.php', 0, 'Import des comptes IACA.'),
(19, '06-04', 'Modifier mon compte', 'gestion_utilisateurs/form_utilisateur_personnel.php', 0, 'Modifier son propre compte.'),
(20, '07-01', 'Récapitulatif FOG', 'modules/fog/recap_fog.php', 0, 'Afficher un récapitulatif Fog'),
(21, '07-02', 'Wake On Lan', 'modules/wol/voir_liste_wol.php', 0, 'Autoriser le WAKE ON LAN.'),
(22, '07-03', 'Export Perso', 'modules/export/export_perso.php', 0, 'Permet les exports personnalisés.'),
(23, '07-04', 'MAJ tags DSIT', 'modules/ssn_dsit/form_import_csv.php', 0, 'Mise à jour des numéros inventaire par le numéro de série.'),
(24, '07-05', 'Images Fog', 'modules/image_fog/voir_liste.php', 1, 'Clonage direct par Fog.'),
(25, '07-06', 'Menu portail', 'modules/menu_portail/voir_menu_portail.php', 1, 'Voir, créer ou modifier le menu du portail.'),
(26, '07-07', 'Gestionnaire de fichiers', 'modules/gestion_fichiers/voir_fichiers.php', 1, 'Voir, créer ou modifier des fichiers.'),
(27, '07-08', 'Migration Fog','modules/migration_fog/voir_migration.php', 1, 'Permet de migrer les noms de machine de Gespac à Fog.'),
(28, '07-09', 'Migration dossiers','modules/migration_dossiers/migration_dossiers.php', 0, 'Permet de migrer les dossiers vers le nouveau système.'),
(29, '07-10', 'Générer Inventaire','modules/generate_inv/voir_generate.php', 1, 'Permet de générer des numéros inventaire pour les matériels sans plaque.'),
(30, '08-01', 'Fiche collège', 'gestion_college/voir_college.php', 1, 'Voir ou modifier la fiche du collège.'),
(31, '08-02', 'Flux RSS', 'modules/rss/rss.php', 1, 'Voir, ajouter ou modifier un flux RSS.'),
(32, '08-03', 'Stats camemberts', 'modules/stats/camembert.php', 0, 'Voir les stats camembert.'),
(33, '08-04', 'Stats bâtons', 'modules/stats/csschart.php', 0, 'Voir les stats bâtons.'),
(34, '08-05', 'Stats utilisation du parc', 'modules/stats/utilisation_parc.php', 0, 'Voir les stats du parc.'),
(35, '08-06', 'Info serveur', 'modules/infoserveur/infoserveur.php', 0, 'Voir les info du serveur.'),
(36, '07-12', 'Migration Utilisateurs', 'modules/migration_users/voir_migration_users.php', 1, 'permet de migrer les mots de passe et login des users dans la nouvelle archi');
