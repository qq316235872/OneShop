-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Aug 02, 2015 at 10:02 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `oneshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `os_action`
--
DROP TABLE IF EXISTS `os_action`;
CREATE TABLE `os_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `os_action`
--

INSERT INTO `os_action` (`id`, `name`, `title`, `remark`, `rule`, `log`, `type`, `status`, `update_time`) VALUES
(1, 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了后台', 1, 1, 1387181220),
(2, 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', 2, 0, 1380173180),
(3, 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', 2, 1, 1383285646),
(4, 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', 2, 0, 1386139726),
(5, 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', 2, 0, 1383285551),
(6, 'update_config', '更新配置', '新增或修改或删除配置', '', '', 1, 1, 1383294988),
(7, 'update_model', '更新模型', '新增或修改模型', '', '', 1, 1, 1383295057),
(8, 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', 1, 1, 1383295963),
(9, 'update_channel', '更新导航', '新增或修改或删除导航', '', '', 1, 1, 1383296301),
(10, 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', 1, 1, 1383296392),
(11, 'update_category', '更新分类', '新增或修改或删除分类', '', '', 1, 1, 1383296765);

-- --------------------------------------------------------

--
-- Table structure for table `os_action_log`
--
DROP TABLE IF EXISTS `os_action_log`;
CREATE TABLE `os_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表' AUTO_INCREMENT=13 ;


-- --------------------------------------------------------

--
-- Table structure for table `os_addon`
--
DROP TABLE IF EXISTS `os_addon`;
CREATE TABLE `os_addon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '插件名或标识',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text NOT NULL COMMENT '插件描述',
  `config` text COMMENT '配置',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(8) NOT NULL DEFAULT '' COMMENT '版本号',
  `adminlist` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '插件类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='插件表' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `os_addon`
--

INSERT INTO `os_addon` (`id`, `name`, `title`, `description`, `config`, `author`, `version`, `adminlist`, `type`, `create_time`, `update_time`, `sort`, `status`) VALUES
(1, 'ReturnTop', '返回顶部', '返回顶部', '{"status":"1","theme":"rocket","customer":"","case":"","qq":"","weibo":""}', 'CoreThink', '1.0', 0, 0, 1407681961, 1408602081, 0, 1),
(2, 'Email', '邮件插件', '实现系统发邮件功能', '{"status":"1","MAIL_SMTP_TYPE":"1","MAIL_SMTP_SECURE":"0","MAIL_SMTP_PORT":"25","MAIL_SMTP_HOST":"smtp.qq.com","MAIL_SMTP_USER":"","MAIL_SMTP_PASS":"","default":"[MAILBODY]"}', 'OneShop', '1.0', 0, 0, 1428732454, 1428732454, 0, 1),
(3, 'SyncLogin', '第三方账号登陆', '第三方账号登陆', '{"type":["Weixin","Qq","Sina","Renren"],"meta":"","WeixinKEY":"","WeixinSecret":"","QqKEY":"","QqSecret":"","SinaKEY":"","SinaSecret":"","RenrenKEY":"","RenrenSecret":""}', 'OneShop', '1.0', 1, 0, 1428250248, 1428250248, 0, 1),
(4, 'AdFloat', '图片漂浮广告', '图片漂浮广告', '{"status":"0","url":"http:\\/\\/www.oneshop.cn","image":"","width":"100","height":"100","speed":"10","target":"1"}', 'CoreThink', '1.0', 0, 0, 1408602081, 1408602081, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_addon_hook`
--
DROP TABLE IF EXISTS `os_addon_hook`;
CREATE TABLE `os_addon_hook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '钩子ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `addons` varchar(255) NOT NULL COMMENT '钩子挂载的插件 ''，''分割',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='钩子表' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `os_addon_hook`
--

INSERT INTO `os_addon_hook` (`id`, `name`, `description`, `addons`, `type`, `create_time`, `update_time`, `status`) VALUES
(1, 'PageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', 'SyncLogin', 1, 1407681961, 1407681961, 1),
(2, 'PageFooter', '页面footer钩子，一般用于加载插件CSS文件和代码', 'ReturnTop,AdFloat', 1, 1407681961, 1407681961, 1),
(3, 'SyncLogin', '第三方登陆', 'SyncLogin', 1, 1407681961, 1407681961, 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_member`
--
DROP TABLE IF EXISTS `os_member`;
CREATE TABLE `os_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- Table structure for table `os_store_module`
--
DROP TABLE IF EXISTS `os_store_module`;
CREATE TABLE `os_store_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
  `description` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `developer` varchar(32) NOT NULL DEFAULT '' COMMENT '开发者',
  `version` varchar(8) NOT NULL DEFAULT '' COMMENT '版本',
  `admin_menu` text NOT NULL COMMENT '菜单节点',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_system_config`
--
DROP TABLE IF EXISTS `os_system_config`;
CREATE TABLE `os_system_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(32) NOT NULL COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置值',
  `group` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类型',
  `options` varchar(255) NOT NULL DEFAULT '' COMMENT '配置额外值',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统配置表' AUTO_INCREMENT=32 ;

--
-- Dumping data for table `os_system_config`
--

INSERT INTO `os_system_config` (`id`, `title`, `name`, `value`, `group`, `type`, `options`, `tip`, `create_time`, `update_time`, `sort`, `status`) VALUES
(1, '站点开关', 'TOGGLE_WEB_SITE', '1', 1, 'select', '0:关闭,1:开启', '站点关闭后将不能访问', 1378898976, 1438523454, 1, 1),
(2, '网站标题', 'WEB_SITE_TITLE', 'OneShop', 1, 'text', '', '网站标题前台显示标题', 1378898976, 1379235274, 2, 1),
(7, '版权信息', 'WEB_SITE_COPYRIGHT', '版权所有 © 2014-2015 OneShop', 1, 'text', '', '设置在网站底部显示的版权信息', 1406991855, 1438519054, 6, 1),
(8, '网站备案号', 'WEB_SITE_ICP', 'XXICP备XXXXXX号', 1, 'text', '', '设置在网站底部显示的备案号', 1378900335, 1438519008, 7, 1),
(10, '前台主题', 'DEFAULT_THEME', 'default', 1, 'select', 'default:默认', '前台模版主题，不影响后台', 1425215616, 1425299454, 9, 1),
(16, '敏感字词', 'SENSITIVE_WORDS', '傻逼,垃圾', 2, 'textarea', '', '用户注册及内容显示敏感字词', 1420385145, 1420387079, 6, 1),
(17, '后台主题', 'ADMIN_THEME', 'default', 3, 'select', 'default:默认主题\r\nblueidea:蓝色理想\r\ngreen:绿色生活', '后台界面主题', 1436678171, 1436690570, 0, 1),
(18, '是否显示页面Trace', 'SHOW_PAGE_TRACE', '0', 3, 'select', '0:关闭\r\n1:开启', '是否显示页面Trace信息', 1387165685, 1387165685, 1, 1),
(20, '配置分组', 'CONFIG_GROUP_LIST', '1:基本\r\n2:用户\r\n3:系统\r\n', 3, 'array', '', '配置分组', 1379228036, 1426930700, 3, 1),
(21, '分页数量', 'LIST_ROWS', '10', 3, 'num', '', '分页时每页的记录数', 1434019462, 1434019481, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_system_menu`
--
DROP TABLE IF EXISTS `os_system_menu`;
CREATE TABLE `os_system_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '链接地址',
  `icon` varchar(64) NOT NULL COMMENT '图标',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台菜单表' AUTO_INCREMENT=82 ;

--
-- Dumping data for table `os_system_menu`
--

INSERT INTO `os_system_menu` (`id`, `pid`, `title`, `url`, `icon`, `create_time`, `update_time`, `sort`, `status`) VALUES
(1, 0, '首页', 'Admin/Index/index', 'glyphicon glyphicon-home', 1426580628, 1426580628, 1, 1),
(2, 0, '系统', 'Admin/SystemConfig/group', 'glyphicon glyphicon-cog', 1426580628, 1426580628, 2, 1),
(6, 1, '系统操作', '', '', 1426580628, 1426580628, 1, 1),
(7, 2, '系统功能', '', '', 1426580628, 1426580628, 1, 1),
(8, 2, '数据备份', '', '', 1426580628, 1426580628, 2, 1),
(12, 6, '清空缓存', 'Admin/Index/rmdirr', '', 1427475588, 1427475588, 1, 1),
(13, 7, '系统设置', 'Admin/SystemConfig/group', '', 1426580628, 1430291269, 1, 1),
(14, 13, '修改', 'Admin/SystemConfig/groupSave', '', 1426580628, 1426580628, 1, 1),
(23, 7, '菜单管理', 'Admin/SystemMenu/index', '', 1426580628, 1430291065, 3, 1),
(24, 23, '添加', 'Admin/SystemMenu/add', '', 1426580628, 1426580628, 1, 1),
(25, 23, '编辑', 'Admin/SystemMenu/edit', '', 1426580628, 1426580628, 2, 1),
(26, 23, '设置状态', 'Admin/SystemMenu/setStatus', '', 1426580628, 1426580628, 3, 1),
(27, 7, '配置管理', 'Admin/SystemConfig/index', '', 1426580628, 1430291167, 4, 1),
(28, 27, '添加', 'Admin/SystemConfig/add', '', 1426580628, 1426580628, 1, 1),
(29, 27, '编辑', 'Admin/SystemConfig/edit', '', 1426580628, 1426580628, 2, 1),
(30, 27, '设置状态', 'Admin/SystemConfig/setStatus', '', 1426580628, 1426580628, 3, 1),
(31, 7, '数据字典', 'Admin/Datebase/index', '', 1429851071, 1430291185, 5, -1),
(32, 7, '插件列表', 'Admin/Addon/index', '', 1427475588, 1427475588, 6, 1),
(33, 32, '安装', 'Admin/Addon/install', '', 1427475588, 1427475588, 1, 1),
(34, 32, '卸载', 'Admin/Addon/uninstall', '', 1427475588, 1427475588, 2, 1),
(35, 32, '执行', 'Admin/Addon/execute', '', 1427475588, 1427475588, 3, 1),
(36, 32, '插件设置', 'Admin/Addon/config', '', 1427475588, 1427475588, 4, 1),
(37, 32, '数据列表', 'Admin/Addon/adminList', '', 1427475588, 1427475588, 5, 1),
(38, 8, '数据备份', 'Admin/Datebase/export', '', 1426580628, 1426580628, 3, 1),
(39, 38, '备份', 'Admin/Datebase/do_export', '', 1426580628, 1426580628, 1, 1),
(40, 38, '优化表', 'Admin/Datebase/optimize', '', 1426580628, 1426580628, 2, 1),
(41, 38, '修复表', 'Admin/Datebase/repair', '', 1426580628, 1426580628, 3, 1),
(42, 8, '数据还原', 'Admin/Datebase/import', '', 1426580628, 1426580628, 2, 1),
(43, 42, '还原备份', 'Admin/Datebase/do_import', '', 1426580628, 1426580628, 1, 1),
(44, 42, '删除备份', 'Admin/Datebase/del', '', 1426580628, 1426580628, 2, 1),
(45, 9, '栏目分类', 'Admin/Category/index', '', 1426580628, 1430290312, 1, 1),
(46, 45, '添加', 'Admin/Category/add', '', 1426580628, 1426580628, 1, 1),
(47, 45, '编辑', 'Admin/Category/edit', '', 1426580628, 1426580628, 2, 1),
(48, 45, '设置状态', 'Admin/Category/setStatus', '', 1426580628, 1426580628, 3, 1),
(50, 45, '文档列表', 'Admin/Document/index', '', 1427475588, 1427475588, 4, 1),
(51, 50, '添加', 'Admin/Document/add', '', 1426580628, 1426580628, 1, 1),
(52, 50, '编辑', 'Admin/Document/edit', '', 1426580628, 1426580628, 2, 1),
(53, 50, '设置状态', 'Admin/Document/setStatus', '', 1426580628, 1426580628, 3, 1),
(54, 9, '标签列表', 'Admin/Tag/index', '', 1426580628, 1430290718, 3, 1),
(55, 54, '添加', 'Admin/Tag/add', '', 1426580628, 1426580628, 1, 1),
(56, 54, '编辑', 'Admin/Tag/edit', '', 1426580628, 1426580628, 2, 1),
(57, 54, '设置状态', 'Admin/Tag/setStatus', '', 1426580628, 1426580628, 3, 1),
(58, 9, '万能评论', 'Admin/UserComment/index', '', 1426580628, 1426580628, 4, 1),
(59, 58, '添加', 'Admin/UserComment/add', '', 1426580628, 1426580628, 1, 1),
(60, 58, '编辑', 'Admin/UserComment/edit', '', 1426580628, 1426580628, 2, 1),
(61, 58, '设置状态', 'Admin/UserComment/setStatus', '', 1426580628, 1426580628, 3, 1),
(62, 9, '回收站', 'Admin/Document/recycle', '', 1427475588, 1430290597, 5, -1),
(63, 10, '上传管理', 'Admin/Upload/index', '', 1427475588, 1427475588, 1, 1),
(64, 63, '上传文件', 'Admin/Upload/upload', '', 1427475588, 1427475588, 1, 1),
(65, 63, '下载图片', 'Admin/Upload/downremoteimg', '', 1427475588, 1427475588, 2, 1),
(66, 63, '文件浏览', 'Admin/Upload/fileManager', '', 1427475588, 1427475588, 3, 1),
(67, 11, '用户列表', 'Admin/User/index', '', 1426580628, 1426580628, 1, 1),
(68, 67, '添加', 'Admin/User/add', '', 1426580628, 1426580628, 1, 1),
(69, 67, '编辑', 'Admin/User/edit', '', 1426580628, 1426580628, 2, 1),
(70, 67, '设置状态', 'Admin/User/setStatus', '', 1426580628, 1426580628, 3, 1),
(71, 11, '部门管理', 'Admin/UserGroup/index', '', 1426580628, 1426580628, 2, -1),
(72, 71, '添加', 'Admin/UserGroup/add', '', 1426580628, 1426580628, 1, 1),
(73, 71, '编辑', 'Admin/UserGroup/edit', '', 1426580628, 1426580628, 2, 1),
(74, 71, '设置状态', 'Admin/UserGroup/setStatus', '', 1426580628, 1426580628, 3, 1),
(75, 32, '新增数据', 'Admin/Addon/adminAdd', '', 1426580628, 1426580628, 6, 1),
(76, 32, '编辑数据', 'Admin/Addon/adminEdit', '', 1426580628, 1426580628, 7, 1),
(77, 32, '设置状态', 'Admin/Addon/setStatus', '', 1426580628, 1426580628, 8, 1),
(78, 2, '应用商店', '', '', 1437185077, 1437185164, 2, -1),
(79, 78, '功能模块', 'Admin/StoreModule/index', '', 1437185242, 1437185242, 1, -1),
(80, 78, '前台主题', 'Admin/StoreTheme/index', '', 1437185290, 1437185290, 2, -1),
(81, 78, '全局插件', 'Admin/StoreAddon/index', '', 1437185290, 1437185290, 3, -1);

-- --------------------------------------------------------

--
-- Table structure for table `os_ucenter_member`
--
DROP TABLE IF EXISTS `os_ucenter_member`;
CREATE TABLE `os_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- Table structure for table `os_upload`
--
DROP TABLE IF EXISTS `os_upload`;
CREATE TABLE `os_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件链接',
  `ext` char(4) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件sha1编码',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文件上传表' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_upload`
--

INSERT INTO `os_upload` (`id`, `name`, `path`, `url`, `ext`, `size`, `md5`, `sha1`, `create_time`, `update_time`, `sort`, `status`) VALUES
(1, 'logo.png', '/Uploads/2015-08-02/55bdbb502366b.png', '', 'png', 4017, '12989706964617d22d131f9387dd6bc2', '3756256648a97bef5da63e1647f06b26fdbebaa6', 1438497616, 1438497616, 0, 1);
