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
  PRIMARY KEY (`grade_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `menu_portail` (
  `mp_id` int(11) NOT NULL AUTO_INCREMENT,
  `mp_nom` varchar(255) NOT NULL,
  `mp_url` varchar(255) NOT NULL,
  `mp_icone` varchar(255) NOT NULL,
  PRIMARY KEY (`mp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

INSERT INTO users VALUES('1', 'ati', 'ati', 'azerty', '0', '', 'cg13', 'modules/stats/cssschart.php','','0', '0');

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

INSERT INTO `grades` (`grade_id`, `grade_nom`, `grade_menu`, `grade_menu_portail`) VALUES
(1, 'root', '','{"item1":"on"}'),
(3, 'PROFESSEURS', '{"L-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","L-06-04":"on","E-06-04":"on","L-08-01":"on"}','{"item1":"on"}'),
(4, 'INTENDANCE', '','{"item1":"on"}'),
(5, 'DIRECTION', '','{"item1":"on"}'),
(6, 'TICE', '{"L-02-01":"on","L-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","L-05-01":"on","L-06-01":"on","L-06-04":"on","E-06-04":"on","L-07-02":"on","E-07-02":"on","L-08-01":"on","L-08-02":"on","L-08-04":"on","E-08-04":"on"}','{"item1":"on"}'),
(7, 'VIE SCOLAIRE', '','{"item1":"on"}'),
(8, 'ADMINISTRATIF', '','{"item1":"on"}'),
(2, 'ATI', '{"L-01-01":"on","E-01-01":"on","L-02-01":"on","E-02-01":"on","L-02-02":"on","E-02-02":"on","L-02-03":"on","E-02-03":"on","L-03-01":"on","E-03-01":"on","L-03-02":"on","E-03-02":"on","L-04-01":"on","E-04-01":"on","L-04-02":"on","E-04-02":"on","L-04-03":"on","E-04-03":"on","L-04-04":"on","E-04-04":"on","L-04-05":"on","E-04-05":"on","L-04-06":"on","E-04-06":"on","L-05-01":"on","E-05-01":"on","L-06-01":"on","E-06-01":"on","L-06-02":"on","E-06-02":"on","L-06-03":"on","E-06-03":"on","L-06-04":"on","E-06-04":"on","L-07-01":"on","E-07-01":"on","L-07-02":"on","E-07-02":"on","L-07-03":"on","E-07-03":"on","L-07-04":"on","E-07-04":"on","L-07-05":"on","E-07-05":"on","L-08-01":"on","E-08-01":"on","L-08-02":"on","E-08-02":"on","L-08-03":"on","E-08-03":"on","L-08-04":"on","E-08-04":"on","L-08-05":"on","E-08-05":"on"}','{"item1":"on"}');

INSERT INTO `menu_portail` (`mp_id`, `mp_nom`, `mp_url`, `mp_icone`) VALUES
(2, 'FOG', 'http://gespac/fog', 'fog.png'),
(3, 'OCS', 'http://gespac/ocsreports', 'ocs.png'),
(4, 'RESTAURATION', './gespac/gestion_donnees/form_upload_restauration.php', 'database.png');

