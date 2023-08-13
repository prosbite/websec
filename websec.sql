/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50733
Source Host           : localhost:3306
Source Database       : websec

Target Server Type    : MYSQL
Target Server Version : 50733
File Encoding         : 65001

Date: 2023-08-13 09:33:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `other_data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', '$2a$10$KUyzYU7uK1X2ZUauKB.uK.rbHooV8BrGGyFuGZ9PiTxrqzQiHtW.a', 'admin@admin.com', 'James Bond', null);

-- ----------------------------
-- Table structure for `user_tokens`
-- ----------------------------
DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_token_expiry` (`token`,`expiry_time`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_tokens
-- ----------------------------
INSERT INTO `user_tokens` VALUES ('1', '1', 'ec66f22c82a0eb22c3ecf4a0ac7869508d398acc3338eabeda3d563d5d15cb48', '1691139567');
INSERT INTO `user_tokens` VALUES ('2', '1', '29155189bd473e8754d9abe69f42f222108162819e5d7816bc8550107c27339b', '1691857381');
INSERT INTO `user_tokens` VALUES ('3', '1', 'de3a7bd5f9e50260098e3f10a27cc8392fa7c28627742fa64bec1f4fb8913152', '1691893110');
INSERT INTO `user_tokens` VALUES ('4', '1', 'c36c1af47bb24b0e3de7f993e2f9e12525813f345aaa7df506b45bc71c630d8a', '1691893694');
INSERT INTO `user_tokens` VALUES ('5', '1', '94d8656a3a80d8747b5c8512adec29e13f29a2a017dc85453227be90ed9c43b7', '1691893711');
