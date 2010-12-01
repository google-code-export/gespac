SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


CREATE SCHEMA IF NOT EXISTS `gespac` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `etats` (
  `etat` varchar(255) NOT NULL,
  PRIMARY KEY  (`etat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `interventions` (
  `interv_id` int(11) NOT NULL auto_increment,
  `interv_date` timestamp NULL default CURRENT_TIMESTAMP,
  `interv_cloture` int(11) default NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `marque_garantie` date NULL,
  `marque_suppr` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`marque_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `materiels` (
  `mat_id` int(11) NOT NULL auto_increment,
  `mat_nom` varchar(255) default NULL,
  `mat_dsit` varchar(100) default NULL,
  `mat_serial` varchar(100) default NULL,
  `mat_mac` varchar(17) NOT NULL,
  `mat_etat` enum('fonctionnel','réparation par ati','hors service','attente SAV','envoyé SAV') default 'fonctionnel',
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

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_nom` varchar(255) DEFAULT NULL,
  `user_logon` varchar(20) NOT NULL,
  `user_password` varchar(15) DEFAULT NULL,
  `user_niveau` int(11) DEFAULT '3',
  `user_mail` varchar(100) NOT NULL,
  `user_skin` varchar(150) NOT NULL DEFAULT 'cg13',
  `user_accueil` varchar(255) NOT NULL,
  `user_menu` varchar(255) NOT NULL,
  `user_suppr` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_logon` (`user_logon`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `correspondances` (
  `corr_id` int(11) NOT NULL AUTO_INCREMENT,
  `corr_marque_ocs` varchar(255) NOT NULL,
  `corr_type` varchar(255) NOT NULL,
  `corr_stype` varchar(255) NOT NULL,
  `corr_marque` varchar(255) NOT NULL,
  `corr_modele` varchar(255) NOT NULL,
  PRIMARY KEY (`corr_id`),
  UNIQUE KEY `corr_marque_ocs` (`corr_marque_ocs`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO users VALUES('1', 'ati', 'ati', 'azerty', '0', '', 'cg13', 'modules/stats/cssschart.php','','0');

INSERT IGNORE INTO `etats` (`etat`) VALUES
('ATTENTE SAV'),
('AUTRES'),
('ENVOYE SAV'),
('FONCTIONNEL'),
('HORS SERVICE'),
('REPARATION PAR ATI');

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
('INCONNUE');


