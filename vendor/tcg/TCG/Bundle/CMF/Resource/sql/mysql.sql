-- 用户表
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` VARCHAR(32) DEFAULT NULL COMMENT '用户名',
  `email` VARCHAR(128) DEFAULT NULL COMMENT '邮箱地址',
  `mobile` VARCHAR(16) DEFAULT NULL COMMENT '手机号',
  `password` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
  `qq` VARCHAR(16) NOT NULL DEFAULT '' COMMENT 'QQ',
  `wechat` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '微信',
  `name` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '名字',
  `avatar` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '头像',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `login_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '今日登录时间',
  `session_id` VARBINARY(128) NOT NULL COMMENT 'SESSION ID',
  PRIMARY KEY (`id`),
  UNIQUE (`username`),
  UNIQUE (`email`),
  UNIQUE (`mobile`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;


-- session
DROP TABLE IF EXISTS `admin_sessions`;
CREATE TABLE IF NOT EXISTS `admin_sessions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `session_id` VARBINARY(128) NOT NULL COMMENT 'SESSION ID',
  `data` BLOB NOT NULL COMMENT '数据',
  `lifetime` MEDIUMINT NOT NULL COMMENT '生命周期时间',
  `timestamp` INTEGER UNSIGNED NOT NULL COMMENT '创建的时间戳',
  PRIMARY KEY (`id`),
  UNIQUE (`session_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8 COLLATE utf8_bin;


-- 角色表
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE IF NOT EXISTS `admin_roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `key` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `description` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `left_value` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预排续树左值',
  `right_value` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预排续树右值',
  PRIMARY KEY (`id`),
  UNIQUE (`key`),
  KEY `tree_left` (`left_value`),
  KEY `tree_right` (`right_value`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;


-- 用户角色表
DROP TABLE IF EXISTS `admin_user2role`;
CREATE TABLE IF NOT EXISTS `admin_user2role` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role` (`user_id`, `role_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;


-- 角色权限表
DROP TABLE IF EXISTS `admin_role2permission`;
CREATE TABLE IF NOT EXISTS `admin_role2permission` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `permission_id` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '权限ID',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `role` (`role_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;


-- 日志表
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE IF NOT EXISTS `admin_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '用户名称',
  `date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '日期',
  `route` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '路由',
  `method` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '方法，GET，POST等',
  `input` MEDIUMTEXT COMMENT '输入',
  `output` MEDIUMTEXT COMMENT '输出',
  `info` LONGTEXT COMMENT '调试信息',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `date` (`date`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;