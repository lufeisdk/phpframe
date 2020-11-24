/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : phpframe

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 24/11/2020 13:27:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pf_admin
-- ----------------------------
DROP TABLE IF EXISTS `pf_admin`;
CREATE TABLE `pf_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '用户名',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '密码',
  `salt` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '盐值',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色id',
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态，1可用0禁止',
  `created_at` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pf_admin
-- ----------------------------
INSERT INTO `pf_admin` VALUES (1, 'admin', 'c4b460d422eeaa34b3ae1227a2e18da1', 'Z2Dvut', 1, 1, '2020-11-18 14:02:47');

-- ----------------------------
-- Table structure for pf_user
-- ----------------------------
DROP TABLE IF EXISTS `pf_user`;
CREATE TABLE `pf_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '用户名',
  `nickname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '昵称',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '密码',
  `salt` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '随机加密因子',
  `regtime` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '注册时间',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '头像',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '手机号',
  `sex` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '性别，1男2女0未知',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '邮箱',
  `ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT 'ip地址',
  `last_logintime` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '最后一次登录时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pf_user
-- ----------------------------
INSERT INTO `pf_user` VALUES (1, 'tom', 'TomCat', '19c51a63daf8367c4f1cfe168f073342', 'I1pHGw', 1605680027, 'avatar.png', '13888888889', 0, 'qiu@126.com', '127.0.0.1', '2020-11-18 14:17:18');

SET FOREIGN_KEY_CHECKS = 1;
