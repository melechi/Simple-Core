create database if not exists `s3core`;
USE `s3core`;
/*Table structure for table `s3core_account` */
DROP TABLE IF EXISTS `s3core_account`;
CREATE TABLE `s3core_account` (
  `account_id` int(10) NOT NULL auto_increment,
  `account_username` varchar(255) NOT NULL default '',
  `account_password` varchar(255) NOT NULL default '',
  `account_email` varchar(255) NOT NULL default '',
  `account_confirmationcode` varchar(255) NOT NULL default '',
  `account_status` int(2) NOT NULL default '0',
  `account_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `account_lastlogin` timestamp NULL default NULL,
  `account_lastip` varchar(15) NOT NULL default '',
  `account_privs` set('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62','63') default NULL,
  `account_namespace` varchar(255) default NULL,
  PRIMARY KEY  (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

/*Trigger structure for table `s3core_account` */

DELIMITER $$

/*!50003 DROP TRIGGER /*!50114 IF EXISTS */ `delete_s3core_account` */$$

/*!50003 CREATE TRIGGER `delete_s3core_account` AFTER DELETE ON `s3core_account` FOR EACH ROW BEGIN	INSERT LOW_PRIORITY INTO s3core_deleted_records_log SET `table`='account',`id_key`=OLD.account_id;END */$$


DELIMITER ;

/*Table structure for table `s3core_category` */

DROP TABLE IF EXISTS `s3core_category`;

CREATE TABLE `s3core_category` (
  `category_id` int(10) NOT NULL auto_increment,
  `category_parentid` int(10) NOT NULL,
  `category_namespace` varchar(64) NOT NULL,
  `category_name` varchar(256) NOT NULL,
  `category_safename` varchar(256) NOT NULL default '',
  `category_description` varchar(512) NOT NULL,
  `category_status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

/*Table structure for table `s3core_deleted_records_log` */

DROP TABLE IF EXISTS `s3core_deleted_records_log`;

CREATE TABLE `s3core_deleted_records_log` (
  `table` enum('account') NOT NULL,
  `id_key` int(11) NOT NULL,
  `when` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s3core_emailtemplates` */

DROP TABLE IF EXISTS `s3core_emailtemplates`;

CREATE TABLE `s3core_emailtemplates` (
  `emailtemplates_id` int(10) NOT NULL auto_increment,
  `emailtemplates_active` int(1) NOT NULL default '1',
  `emailtemplates_name` varchar(255) NOT NULL default '',
  `emailtemplates_description` varchar(255) NOT NULL default '',
  `emailtemplates_from` varchar(255) NOT NULL default '',
  `emailtemplates_subject` varchar(255) NOT NULL default '',
  `emailtemplates_text` longtext NOT NULL,
  `emailtemplates_html` longtext NOT NULL,
  PRIMARY KEY  (`emailtemplates_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

/*Table structure for table `s3core_session` */

DROP TABLE IF EXISTS `s3core_session`;

CREATE TABLE `s3core_session` (
  `session_id` int(10) NOT NULL auto_increment,
  `session_key` varchar(64) NOT NULL default '',
  `session_ip` varchar(15) NOT NULL default '',
  `session_touched` int(10) NOT NULL default '0',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

/*Table structure for table `s3core_session_var` */

DROP TABLE IF EXISTS `s3core_session_var`;

CREATE TABLE `s3core_session_var` (
  `session_var_id` int(20) NOT NULL auto_increment,
  `session_var_sessionid` int(20) NOT NULL default '0',
  `session_var_key` varchar(255) NOT NULL default '',
  `session_var_val` text NOT NULL,
  PRIMARY KEY  (`session_var_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;