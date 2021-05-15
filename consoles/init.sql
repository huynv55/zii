/*
MySQL Database Init Blog

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001
*/

DROP DATABASE IF EXISTS blog;

CREATE DATABASE blog;

USE blog;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auths
-- ----------------------------
DROP TABLE IF EXISTS `auths`;
CREATE TABLE `auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `roles` varchar(255) DEFAULT NULL,
  `meta_data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
