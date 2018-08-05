/*
SQLyog Ultimate v12.4.3 (64 bit)
MySQL - 5.7.23-0ubuntu0.16.04.1 : Database - queue
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`queue` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `queue`;

/*Table structure for table `queue` */

DROP TABLE IF EXISTS `queue`;

CREATE TABLE `queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `worker` int(10) unsigned DEFAULT '0' COMMENT 'Assigned Worker',
  `worker_status` tinyint(4) unsigned DEFAULT '0' COMMENT '0:default, 1:working, 2:done',
  `context` varchar(255) DEFAULT 'default',
  `params` text,
  `command` text COMMENT 'Command to execute',
  `job_hash` varchar(200) DEFAULT NULL,
  `job` text,
  `output` text,
  `return_code` tinyint(4) DEFAULT '-1',
  `priority` int(11) DEFAULT '500' COMMENT 'Job priority, the higher the faster it gets done',
  `time_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_completed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_job_hash` (`job_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
