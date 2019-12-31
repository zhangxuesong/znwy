<?php
//升级数据表
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) DEFAULT NULL,
  `category` tinyint(1) NOT NULL DEFAULT '0',
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` text,
  `replydesc` varchar(200) DEFAULT NULL,
  `style` tinyint(1) NOT NULL DEFAULT '0',
  `checkmethod` tinyint(1) NOT NULL DEFAULT '0',
  `votetype` tinyint(1) NOT NULL DEFAULT '0',
  `votemethod` tinyint(1) NOT NULL DEFAULT '0',
  `votetext` varchar(1000) DEFAULT NULL,
  `singleimage` varchar(255) DEFAULT NULL,
  `singledesc` varchar(200) DEFAULT NULL,
  `multimages` text,
  `isvotesign` tinyint(1) NOT NULL DEFAULT '0',
  `signstart` int(11) NOT NULL,
  `signend` int(11) NOT NULL,
  `signmoney` int(11) NOT NULL,
  `signnum` int(6) NOT NULL,
  `signmeas` tinyint(1) NOT NULL DEFAULT '0',
  `signextend` varchar(1000) DEFAULT NULL,
  `question` text,
  `remark` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_activity','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `title` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `category` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `image` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','replydesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `replydesc` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','style')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `style` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','checkmethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `checkmethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','votetype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `votetype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','votemethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `votemethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','votetext')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `votetext` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','singleimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `singleimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','singledesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `singledesc` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','multimages')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `multimages` text");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','isvotesign')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `isvotesign` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signstart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signstart` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signend` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signmoney')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signmoney` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signnum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signnum` int(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signmeas')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signmeas` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','signextend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `signextend` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','question')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `question` text");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `remark` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_activity_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `actid` int(10) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `realname` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `extend_1` varchar(100) DEFAULT NULL,
  `extend_2` varchar(100) DEFAULT NULL,
  `extend_3` varchar(100) DEFAULT NULL,
  `extend_4` varchar(100) DEFAULT NULL,
  `extend_5` varchar(100) DEFAULT NULL,
  `extend_6` varchar(100) DEFAULT NULL,
  `extend_7` varchar(100) DEFAULT NULL,
  `extend_8` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `actid` (`actid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','actid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `actid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `uid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `realname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_1` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_2` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_3` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_4` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_5` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_6')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_6` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_7')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_7` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','extend_8')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `extend_8` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_sign','actid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_sign')." ADD   KEY `actid` (`actid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_activity_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `actid` int(10) NOT NULL,
  `voteid` int(4) NOT NULL,
  `votenum` int(10) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `actid` (`actid`),
  KEY `voteid` (`voteid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','actid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `actid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','voteid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `voteid` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','votenum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `votenum` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `uid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','actid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   KEY `actid` (`actid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_activity_vote','voteid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_activity_vote')." ADD   KEY `voteid` (`voteid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `images` text,
  `content` text,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `isrecommand` tinyint(3) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_article','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_article','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `cid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_article','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_article','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `displayorder` tinyint(3) unsigned DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `isrecommand` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_article_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_article_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `displayorder` tinyint(3) unsigned DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `enabled` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   `isrecommand` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_category')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_article_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `aid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_article_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','aid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   `aid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_article_log','aid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_article_log')." ADD   KEY `aid` (`aid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(60) NOT NULL,
  `btype` tinyint(3) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` int(5) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `bgimage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `btype` (`btype`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_banner','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','btype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `btype` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','link')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `link` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `displayorder` int(5) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `enabled` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','bgimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   `bgimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_banner','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_banner_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bannerid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `clicktime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bannerid` (`bannerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','bannerid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `bannerid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','clicktime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   `clicktime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_banner_statistics','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_banner_statistics')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_building` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `flid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `units` int(4) NOT NULL,
  `floors` int(4) NOT NULL,
  `rooms` int(4) NOT NULL,
  `isbarn` tinyint(1) NOT NULL,
  `isunit` tinyint(1) NOT NULL,
  `unitname` varchar(60) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `aurine_bid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_building','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_building','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `flid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','units')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `units` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','floors')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `floors` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','rooms')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `rooms` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','isbarn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `isbarn` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','isunit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `isunit` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','unitname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `unitname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','aurine_bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   `aurine_bid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_building','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_building','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_building','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_building')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_car` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `remark` varchar(100) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `startdate` int(11) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `mobile` (`mobile`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_car','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_car','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `uid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `deleted` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `startdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   `enddate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car')." ADD   KEY `mobile` (`mobile`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_car_feeorder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `parklotid` int(10) NOT NULL,
  `iologid` int(10) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `fee` decimal(8,2) NOT NULL,
  `paytime` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `parklotid` (`parklotid`),
  KEY `iologid` (`iologid`),
  KEY `carno` (`carno`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `parklotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','iologid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `iologid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `fee` decimal(8,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','paytime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `paytime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   KEY `parklotid` (`parklotid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','iologid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   KEY `iologid` (`iologid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_feeorder','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_feeorder')." ADD   KEY `carno` (`carno`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_car_iolog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `parklotid` int(10) NOT NULL,
  `ioid` int(10) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `io` tinyint(1) DEFAULT '0',
  `intime` int(11) NOT NULL,
  `outtime` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `payfee` int(11) NOT NULL DEFAULT '0',
  `paystatus` tinyint(1) DEFAULT '0',
  `payuid` int(10) NOT NULL DEFAULT '0',
  `payopenid` varchar(60) DEFAULT NULL,
  `ctime` int(10) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `parklotid` (`parklotid`),
  KEY `ioid` (`ioid`),
  KEY `io` (`io`),
  KEY `carno` (`carno`),
  KEY `status` (`status`),
  KEY `paystatus` (`paystatus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `parklotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','ioid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `ioid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `io` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','intime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `intime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','outtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `outtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','payfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `payfee` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','paystatus')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `paystatus` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','payuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `payuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','payopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `payopenid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `ctime` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   `image` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `parklotid` (`parklotid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','ioid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `ioid` (`ioid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `io` (`io`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `carno` (`carno`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_iolog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_iolog')." ADD   KEY `status` (`status`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_car_whitelist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `parklotid` int(10) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `realname` varchar(20) DEFAULT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `parklotid` (`parklotid`),
  KEY `carno` (`carno`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `parklotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `realname` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','idcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `idcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `starttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `endtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   KEY `parklotid` (`parklotid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_car_whitelist','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_car_whitelist')." ADD   KEY `carno` (`carno`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_carbill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(4) NOT NULL,
  `cid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `startdate` int(11) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `daterange` varchar(100) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `payfee` decimal(10,2) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `paytype` tinyint(1) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cfrom` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `payuid` int(10) NOT NULL,
  `printpznum` varchar(30) NOT NULL,
  `printtimes` tinyint(2) NOT NULL DEFAULT '0',
  `payno` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_carbill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `lid` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `startdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `enddate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','daterange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `daterange` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','payfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `payfee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `paytype` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','cfrom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `cfrom` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','payuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `payuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','printpznum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `printpznum` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','printtimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `printtimes` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   `payno` varchar(128) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carbill','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carbill')." ADD   KEY `lid` (`lid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_carmove` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `fromuid` int(10) NOT NULL DEFAULT '0',
  `fromopenid` varchar(50) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `touid` int(10) NOT NULL DEFAULT '0',
  `toopenid` varchar(50) DEFAULT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `images` text,
  `remark` varchar(100) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_carmove','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','fromuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `fromuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','fromopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `fromopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','touid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `touid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','toopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `toopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_carmove','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_carmove')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `title` varchar(60) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `tplid` varchar(60) NOT NULL,
  `topcolor` varchar(20) NOT NULL,
  `textcolor` varchar(20) DEFAULT NULL,
  `openid` varchar(60) NOT NULL,
  `uid` int(10) NOT NULL,
  `right1` tinyint(1) NOT NULL DEFAULT '0',
  `right2` tinyint(1) NOT NULL DEFAULT '0',
  `right3` tinyint(1) NOT NULL DEFAULT '0',
  `right4` tinyint(1) NOT NULL DEFAULT '0',
  `right5` tinyint(1) NOT NULL DEFAULT '0',
  `right6` tinyint(1) NOT NULL DEFAULT '0',
  `right7` tinyint(1) NOT NULL DEFAULT '0',
  `right8` tinyint(1) NOT NULL DEFAULT '0',
  `right9` tinyint(1) NOT NULL DEFAULT '0',
  `right10` tinyint(1) NOT NULL DEFAULT '0',
  `right11` tinyint(1) NOT NULL DEFAULT '0',
  `right12` tinyint(1) NOT NULL DEFAULT '0',
  `right13` tinyint(1) NOT NULL DEFAULT '0',
  `right14` tinyint(1) NOT NULL DEFAULT '0',
  `right15` tinyint(1) NOT NULL DEFAULT '0',
  `right16` tinyint(1) NOT NULL DEFAULT '0',
  `right17` tinyint(1) NOT NULL DEFAULT '0',
  `right18` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `openids` text,
  `reporttime` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `type` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','tplid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `tplid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','topcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `topcolor` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','textcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `textcolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right1` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right2` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right3` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right4` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right5` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right6')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right6` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right7')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right7` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right8')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right8` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right9')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right9` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right10')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right10` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right11')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right11` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right12')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right12` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right13')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right13` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right14')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right14` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right15')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right15` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right16')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right16` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right17')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right17` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','right18')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `right18` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_category','openids')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `openids` text");}
if(!pdo_fieldexists('rhinfo_zyxq_category','reporttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   `reporttime` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_category','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_category')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_costdetail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `io` tinyint(1) NOT NULL,
  `cate` tinyint(1) NOT NULL,
  `title` varchar(60) NOT NULL,
  `itemid` int(10) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `handling` varchar(30) DEFAULT NULL,
  `handledate` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `publicity` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `printpznum` varchar(30) NOT NULL,
  `printtimes` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `io` (`io`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_costdetail','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `io` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','cate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `cate` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `itemid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','money')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `money` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','handling')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `handling` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','handledate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `handledate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','publicity')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `publicity` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','printpznum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `printpznum` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','printtimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   `printtimes` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costdetail','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costdetail')." ADD   KEY `io` (`io`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_costitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `io` tinyint(1) NOT NULL,
  `title` varchar(60) NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `io` (`io`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_costitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `io` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `status` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_costitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_costitem')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devpatrol_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   `enabled` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_category')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devpatrol_cateitem` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `cateid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `desc` varchar(100) DEFAULT NULL,
  `itemtype` varchar(10) DEFAULT NULL,
  `value` varchar(300) DEFAULT NULL,
  `displayorder` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `cateid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','desc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `desc` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','itemtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `itemtype` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','value')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `value` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   `displayorder` int(6) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_cateitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_cateitem')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devpatrol_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `taskid` int(10) NOT NULL,
  `deviceid` int(10) NOT NULL,
  `itemtype` varchar(10) DEFAULT NULL,
  `itemtitle` varchar(50) DEFAULT NULL,
  `itemdesc` varchar(100) DEFAULT NULL,
  `itemcontent` varchar(255) DEFAULT NULL,
  `itemimage` varchar(255) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `taskid` (`taskid`),
  KEY `deviceid` (`deviceid`),
  KEY `ctime` (`ctime`),
  KEY `cuid` (`cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','taskid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `taskid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','deviceid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `deviceid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','itemtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `itemtype` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','itemtitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `itemtitle` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','itemdesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `itemdesc` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','itemcontent')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `itemcontent` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','itemimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `itemimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','taskid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   KEY `taskid` (`taskid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','deviceid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   KEY `deviceid` (`deviceid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_content','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_content')." ADD   KEY `ctime` (`ctime`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devpatrol_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cateid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `spec` varchar(60) DEFAULT NULL,
  `brand` varchar(60) DEFAULT NULL,
  `suppid` int(10) NOT NULL DEFAULT '0',
  `images` text,
  `itemstr` text,
  `address` varchar(100) DEFAULT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cateid` (`cateid`),
  KEY `suppid` (`suppid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `cateid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','spec')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `spec` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','brand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `brand` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','suppid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `suppid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','itemstr')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `itemstr` text");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `address` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_device','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_device')." ADD   KEY `cateid` (`cateid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devpatrol_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `itemstr` text,
  `startdate` int(11) NOT NULL,
  `cycle` tinyint(1) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `week` tinyint(1) NOT NULL DEFAULT '0',
  `month` tinyint(2) NOT NULL DEFAULT '0',
  `year` tinyint(1) NOT NULL DEFAULT '0',
  `starttime` varchar(10) DEFAULT NULL,
  `endtime` varchar(10) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cycle` (`cycle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','itemstr')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `itemstr` text");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','cycle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `cycle` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','day')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `day` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','week')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `week` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','month')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `month` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','year')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `year` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `starttime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `endtime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_devpatrol_task','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devpatrol_task')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_devsupplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(60) NOT NULL,
  `telphone` varchar(60) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `title` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','province')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `province` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','city')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `city` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','district')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `district` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `address` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `contact` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `telphone` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `openid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_devsupplier','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_devsupplier')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_door` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `doorcate` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `locksn` varchar(60) NOT NULL,
  `lockid` varchar(20) NOT NULL,
  `sim` varchar(60) NOT NULL,
  `simdate` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `relationfee` tinyint(1) NOT NULL,
  `doorrange` int(10) NOT NULL DEFAULT '0',
  `offline` tinyint(1) NOT NULL DEFAULT '0',
  `devtype` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `doortype` tinyint(1) NOT NULL DEFAULT '4',
  `device_json` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_door','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_door','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','doorcate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `doorcate` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','locksn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `locksn` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','lockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `lockid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','sim')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `sim` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `simdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','relationfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `relationfee` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','doorrange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `doorrange` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','offline')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `offline` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','devtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `devtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','doortype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `doortype` tinyint(1) NOT NULL DEFAULT '4'");}
if(!pdo_fieldexists('rhinfo_zyxq_door','device_json')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   `device_json` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   KEY `lid` (`lid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_door_faces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `did` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `faceimg` varchar(255) DEFAULT NULL,
  `status` int(4) NOT NULL DEFAULT '0',
  `ctime` int(10) NOT NULL,
  `startdate` int(10) DEFAULT NULL,
  `enddate` int(10) DEFAULT NULL,
  `starttime` varchar(20) DEFAULT NULL,
  `endtime` varchar(20) DEFAULT NULL,
  `nickname` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `userid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `did` (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_door_faces','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','did')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `did` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','faceimg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `faceimg` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `status` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `ctime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `startdate` int(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `enddate` int(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `starttime` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `endtime` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `nickname` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','userid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   `userid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_faces','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_faces')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_door_visit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `doorid` int(10) NOT NULL,
  `fromopenid` varchar(50) DEFAULT NULL,
  `fromuid` int(10) NOT NULL,
  `toopenid` varchar(50) DEFAULT NULL,
  `touid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `effedate` varchar(60) DEFAULT NULL,
  `effetime` int(11) NOT NULL,
  `opentimes` tinyint(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  `reason` varchar(60) DEFAULT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `hid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `doorid` (`doorid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_door_visit','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','doorid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `doorid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','fromopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `fromopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','fromuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `fromuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','toopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `toopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','touid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `touid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','effedate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `effedate` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','effetime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `effetime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','opentimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `opentimes` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','reason')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `reason` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_door_visit','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_door_visit')." ADD   KEY `tid` (`tid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_doorlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `did` int(10) NOT NULL,
  `status` int(4) NOT NULL DEFAULT '0',
  `result` varchar(60) DEFAULT NULL,
  `opentime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `did` (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_doorlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','did')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `did` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `status` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','result')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `result` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','opentime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   `opentime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_doorlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_doorlog')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_elevator` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `locksn` varchar(60) NOT NULL,
  `sim` varchar(60) NOT NULL,
  `simdate` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `relationfee` tinyint(1) NOT NULL,
  `range` int(10) NOT NULL DEFAULT '0',
  `offline` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `locktype` tinyint(1) NOT NULL DEFAULT '0',
  `devtype` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_elevator','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','locksn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `locksn` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','sim')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `sim` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `simdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','relationfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `relationfee` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','range')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `range` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','offline')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `offline` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','locktype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `locktype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','devtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   `devtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_elevator','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevator')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_elevatorlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `eid` int(10) NOT NULL,
  `status` int(4) NOT NULL DEFAULT '0',
  `result` varchar(60) DEFAULT NULL,
  `opentime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `eid` (`eid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','eid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `eid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `status` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','result')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `result` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','opentime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   `opentime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_elevatorlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_elevatorlog')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_environ_arrange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `rid` int(10) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `lid` int(10) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bid` int(10) NOT NULL,
  `bname` varchar(255) NOT NULL,
  `tid` int(10) NOT NULL,
  `tname` varchar(255) NOT NULL,
  `hid` int(10) NOT NULL,
  `hname` varchar(255) NOT NULL,
  `areacont` varchar(255) NOT NULL,
  `ask` varchar(255) NOT NULL,
  `cycleid` int(11) NOT NULL,
  `cycle` varchar(255) NOT NULL,
  `day` varchar(255) NOT NULL,
  `week` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `ctime` varchar(255) NOT NULL,
  `starttime` varchar(255) NOT NULL,
  `endtime` varchar(255) NOT NULL,
  `startdate` varchar(255) NOT NULL,
  `simdate` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','pname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `pname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','rname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `rname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','lname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `lname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','bname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `bname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','tname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `tname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','hname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `hname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','areacont')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `areacont` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','ask')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `ask` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','cycleid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `cycleid` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','cycle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `cycle` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','day')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `day` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','week')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `week` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','month')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `month` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `ctime` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `starttime` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `endtime` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `startdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   `simdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_arrange','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_arrange')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_environ_grecord` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `rid` int(10) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `lid` int(10) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bid` int(10) NOT NULL,
  `bname` varchar(255) NOT NULL,
  `tid` int(10) NOT NULL,
  `tname` varchar(255) NOT NULL,
  `hid` int(10) NOT NULL,
  `hname` varchar(255) NOT NULL,
  `vegeid` varchar(255) NOT NULL,
  `vegename` varchar(255) NOT NULL,
  `kind` varchar(255) NOT NULL,
  `num` int(10) NOT NULL,
  `planter` varchar(255) NOT NULL,
  `simdate` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','pname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `pname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','rname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `rname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','lname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `lname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','bname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `bname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','tname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `tname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','hname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `hname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','vegeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `vegeid` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','vegename')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `vegename` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','kind')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `kind` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','num')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `num` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','planter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `planter` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `simdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','img')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','note')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `note` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `uid` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','phone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   `phone` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_grecord','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_grecord')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_environ_greeninfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `rid` int(10) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `lid` int(10) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bid` int(10) NOT NULL,
  `bname` varchar(255) NOT NULL,
  `tid` int(10) NOT NULL,
  `tname` varchar(255) NOT NULL,
  `hid` int(10) NOT NULL,
  `hname` varchar(255) NOT NULL,
  `vegeid` varchar(255) NOT NULL,
  `vegename` varchar(255) NOT NULL,
  `kind` varchar(255) NOT NULL,
  `num` int(10) NOT NULL,
  `treeage` varchar(255) NOT NULL,
  `startdate` varchar(255) NOT NULL,
  `simdate` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','pname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `pname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','rname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `rname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','lname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `lname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','bname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `bname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','tname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `tname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','hname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `hname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','vegeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `vegeid` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','vegename')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `vegename` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','kind')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `kind` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','num')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `num` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','treeage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `treeage` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `startdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   `simdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_greeninfo','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_greeninfo')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_environ_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `rid` int(10) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `lid` int(10) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bid` int(10) NOT NULL,
  `bname` varchar(255) NOT NULL,
  `tid` int(10) NOT NULL,
  `tname` varchar(255) NOT NULL,
  `hid` int(10) NOT NULL,
  `hname` varchar(255) NOT NULL,
  `cname` varchar(255) NOT NULL,
  `startdate` varchar(255) NOT NULL,
  `simdate` varchar(255) NOT NULL,
  `cleantion` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `areacont` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_environ_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','pname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `pname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','rname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `rname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','lname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `lname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','bname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `bname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','tname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `tname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','hname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `hname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','cname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `cname` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `startdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','simdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `simdate` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','cleantion')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `cleantion` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','phone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `phone` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','areacont')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `areacont` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','img')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','note')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `note` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   `uid` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_environ_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_environ_record')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feebill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `itemid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `feetype` tinyint(3) DEFAULT NULL,
  `threeqty` decimal(10,2) NOT NULL,
  `startqty` decimal(10,2) NOT NULL,
  `endqty` decimal(10,2) NOT NULL,
  `measure` varchar(30) DEFAULT NULL,
  `category` tinyint(1) NOT NULL,
  `startdate` int(11) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `daterange` varchar(100) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `payfee` decimal(10,2) NOT NULL,
  `latefee` decimal(10,2) NOT NULL,
  `laterate` decimal(6,2) NOT NULL,
  `creditfee` decimal(10,2) NOT NULL,
  `latedays` int(11) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `paytype` tinyint(1) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `isalipay` tinyint(1) NOT NULL DEFAULT '0',
  `cfrom` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `payuid` int(10) NOT NULL,
  `printpznum` varchar(30) NOT NULL,
  `printtimes` tinyint(2) NOT NULL DEFAULT '0',
  `payno` varchar(128) DEFAULT NULL,
  `payfrom` tinyint(1) NOT NULL DEFAULT '0',
  `invoicenum` varchar(20) DEFAULT NULL,
  `invstatus` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `category` (`category`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feebill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `address` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','feetype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `feetype` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','threeqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `threeqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','startqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `startqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','endqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `endqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','measure')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `measure` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `startdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `enddate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','daterange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `daterange` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','payfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `payfee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','latefee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `latefee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','laterate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `laterate` decimal(6,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','creditfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `creditfee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','latedays')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `latedays` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `paytype` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','isalipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `isalipay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','cfrom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `cfrom` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','payuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `payuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','printpznum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `printpznum` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','printtimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `printtimes` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `payno` varchar(128) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','payfrom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `payfrom` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','invoicenum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `invoicenum` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','invstatus')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   `invstatus` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill')." ADD   KEY `category` (`category`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feebill_create` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `itemid` varchar(255) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `lastdate` int(11) DEFAULT NULL,
  `paymonths` tinyint(4) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `category` (`category`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `itemid` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','lastdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `lastdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','paymonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `paymonths` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebill_create','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebill_create')." ADD   KEY `category` (`category`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feebillitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `itemid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `billdate` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `hid` (`hid`),
  KEY `itemid` (`itemid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `billdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feebillitem','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feebillitem')." ADD   KEY `itemid` (`itemid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feeitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `title` varchar(60) NOT NULL,
  `calmethod` varchar(60) NOT NULL,
  `measure` varchar(30) DEFAULT NULL,
  `paymonths` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `laterate` decimal(6,2) DEFAULT NULL,
  `latedays` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `isimport` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(30) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `discount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `thumb` varchar(255) DEFAULT NULL,
  `relacost` tinyint(1) NOT NULL DEFAULT '0',
  `feeround` tinyint(1) NOT NULL DEFAULT '1',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `feemonths` tinyint(1) NOT NULL DEFAULT '1',
  `finmonths` int(4) NOT NULL DEFAULT '0',
  `givemonths` int(4) NOT NULL DEFAULT '0',
  `latedate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feeitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','calmethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `calmethod` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','measure')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `measure` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','paymonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `paymonths` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','laterate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `laterate` decimal(6,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','latedays')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `latedays` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','isimport')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `isimport` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','icon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `icon` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','color')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `color` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','discount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `discount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','relacost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `relacost` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','feeround')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `feeround` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','feemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `feemonths` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','finmonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `finmonths` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','givemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `givemonths` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','latedate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   `latedate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feeitem_building` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `flid` int(10) NOT NULL,
  `itemid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `flid` (`flid`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `flid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feeitem_building','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feeitem_building')." ADD   KEY `flid` (`flid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_feelocation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `pricerule` tinyint(1) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_feelocation','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `category` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','pricerule')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `pricerule` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_feelocation','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_feelocation')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_garage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `buildarea` decimal(10,2) NOT NULL,
  `usearea` decimal(10,2) NOT NULL,
  `addarea` decimal(10,2) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobile1` varchar(20) DEFAULT NULL,
  `isfree` tinyint(1) NOT NULL DEFAULT '0',
  `freestart` int(11) NOT NULL,
  `freeend` int(11) NOT NULL,
  `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `paydate` int(11) DEFAULT NULL,
  `billdate` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lifepay_hid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `electmeter` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_garage','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','buildarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `buildarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','usearea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `usearea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','addarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `addarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','mobile1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `mobile1` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','isfree')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `isfree` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','freestart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `freestart` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','freeend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `freeend` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','isdiscount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `billdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','lifepay_hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `lifepay_hid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','electmeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   `electmeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_garage','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_garage')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_house_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `tag` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_house_tag','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','tag')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `tag` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_house_tag','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_house_tag')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_lease` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `lesseeid` int(10) NOT NULL,
  `paymethod` tinyint(1) DEFAULT '0',
  `contact` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `leaseterm` int(6) NOT NULL DEFAULT '0',
  `area` decimal(16,2) NOT NULL DEFAULT '0.00',
  `location` text,
  `feeitem` text,
  `deposit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `depositdesc` varchar(255) DEFAULT NULL,
  `totalfee` decimal(16,2) NOT NULL DEFAULT '0.00',
  `signdate` int(11) NOT NULL,
  `attachment` text,
  `remark` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_lease','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','lesseeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `lesseeid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','paymethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `paymethod` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `contact` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `mobile` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','leaseterm')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `leaseterm` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','area')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `area` decimal(16,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','location')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `location` text");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','feeitem')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `feeitem` text");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','deposit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `deposit` decimal(16,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','depositdesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `depositdesc` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','totalfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `totalfee` decimal(16,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','signdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `signdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','attachment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `attachment` text");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `remark` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lease','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lease')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_leasebill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `leaseid` int(10) NOT NULL,
  `lesseeid` int(10) NOT NULL,
  `itemid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `feetype` tinyint(3) NOT NULL DEFAULT '1',
  `threeqty` decimal(10,2) NOT NULL,
  `startqty` decimal(10,2) NOT NULL,
  `endqty` decimal(10,2) NOT NULL,
  `measure` varchar(30) DEFAULT NULL,
  `category` tinyint(1) NOT NULL,
  `startdate` int(11) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `daterange` varchar(100) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `payfee` decimal(10,2) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `paytype` tinyint(1) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `payuid` int(10) NOT NULL,
  `printpznum` varchar(30) NOT NULL,
  `printtimes` tinyint(2) NOT NULL DEFAULT '0',
  `payno` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `leaseid` (`leaseid`),
  KEY `category` (`category`),
  KEY `itemid` (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_leasebill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','leaseid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `leaseid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','lesseeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `lesseeid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','feetype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `feetype` tinyint(3) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','threeqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `threeqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','startqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `startqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','endqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `endqty` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','measure')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `measure` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `startdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `enddate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','daterange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `daterange` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','payfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `payfee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `paytype` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','payuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `payuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','printpznum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `printpznum` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','printtimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `printtimes` tinyint(2) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   `payno` varchar(128) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','leaseid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   KEY `leaseid` (`leaseid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebill','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebill')." ADD   KEY `category` (`category`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_leasebillitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `leaseid` int(10) NOT NULL,
  `itemid` int(10) NOT NULL,
  `months` tinyint(3) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `billdate` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `leaseid` (`leaseid`),
  KEY `itemid` (`itemid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','leaseid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `leaseid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','months')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `months` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `billdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','leaseid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   KEY `leaseid` (`leaseid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leasebillitem','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leasebillitem')." ADD   KEY `itemid` (`itemid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_leaselocation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(1) DEFAULT '0',
  `location` text,
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `category` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','location')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `location` text");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_leaselocation','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_leaselocation')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_lessee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `lesstype` tinyint(1) DEFAULT '0',
  `contact` varchar(60) NOT NULL,
  `telphone` varchar(60) NOT NULL,
  `mobile` varchar(60) NOT NULL,
  `address` varchar(100) NOT NULL,
  `certtype` tinyint(1) DEFAULT '0',
  `certno` varchar(50) DEFAULT NULL,
  `certphoto` varchar(255) DEFAULT NULL,
  `attachment` text,
  `operateitem` varchar(60) DEFAULT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_lessee','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','lesstype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `lesstype` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `contact` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `telphone` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `mobile` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','certtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `certtype` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','certno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `certno` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','certphoto')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `certphoto` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','attachment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `attachment` text");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','operateitem')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `operateitem` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `openid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `remark` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_lessee','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_lessee')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `flid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `bg` varchar(30) DEFAULT NULL,
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `feemonths` tinyint(1) NOT NULL DEFAULT '1',
  `finmonths` int(4) NOT NULL DEFAULT '0',
  `givemonths` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_location','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_location','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `category` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `flid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `price` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','bg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `bg` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_location','feemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `feemonths` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_location','finmonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `finmonths` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_location','givemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   `givemonths` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_location','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_location','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_location','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_location','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_location')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_mc_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `openid_wxapp` varchar(50) NOT NULL,
  `openid_alipay` varchar(50) NOT NULL,
  `openid_aliapp` varchar(50) NOT NULL,
  `openid_haina` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`),
  KEY `openid_wxapp` (`openid_wxapp`),
  KEY `openid_alipay` (`openid_alipay`),
  KEY `openid_aliapp` (`openid_aliapp`),
  KEY `openid_haina` (`openid_haina`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_mc_members','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `weid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `uid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `openid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_wxapp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `openid_wxapp` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_alipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `openid_alipay` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_aliapp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `openid_aliapp` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_haina')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `openid_haina` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `status` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `uid` (`uid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `openid` (`openid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_wxapp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `openid_wxapp` (`openid_wxapp`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_alipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `openid_alipay` (`openid_alipay`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mc_members','openid_aliapp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mc_members')." ADD   KEY `openid_aliapp` (`openid_aliapp`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `isdefault` tinyint(1) NOT NULL DEFAULT '0',
  `isowner` tinyint(1) NOT NULL DEFAULT '0',
  `otype` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `category` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `auditopenid` varchar(60) DEFAULT NULL,
  `audituid` int(10) NOT NULL DEFAULT '0',
  `bankcard` varchar(30) DEFAULT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `doortime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  `proprietor_json` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `category` (`category`),
  KEY `deleted` (`deleted`),
  KEY `status` (`status`),
  KEY `isdefault` (`isdefault`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_member','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_member','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `realname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','isdefault')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `isdefault` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','isowner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `isowner` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','otype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `otype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `deleted` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `category` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','auditopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `auditopenid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','audituid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `audituid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member','bankcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `bankcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','doortime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `doortime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','reason')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `reason` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','idcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `idcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','proprietor_json')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   `proprietor_json` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `category` (`category`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `deleted` (`deleted`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member')." ADD   KEY `status` (`status`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_member_sub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `parentid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `otype` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  `effedate` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_member_sub','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','parentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `parentid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','otype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `otype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','effedate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   `effedate` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_member_sub','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_member_sub')." ADD   KEY `tid` (`tid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_mycar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `checkdate` varchar(20) DEFAULT NULL,
  `safeno` varchar(30) DEFAULT NULL,
  `remark` varchar(30) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `openid` (`openid`),
  KEY `carno` (`carno`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_mycar','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `uid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','checkdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `checkdate` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','safeno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `safeno` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `remark` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_mycar','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_mycar')." ADD   KEY `openid` (`openid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` int(5) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `bgimage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_nav','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `category` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','link')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `link` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `displayorder` int(5) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `enabled` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','bgimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   `bgimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_nav','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_nav')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_navcate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(60) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_navcate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_navcate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_navcate')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` varchar(1000) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `content` text,
  `range` tinyint(1) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `stime` int(11) NOT NULL,
  `etime` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `category` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `audituid` int(10) NOT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`(333)),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `cid` (`cid`),
  KEY `range` (`range`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_notice','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `bid` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','range')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `range` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','reason')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `reason` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','stime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `stime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','etime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `etime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `category` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','audituid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `audituid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `openid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `bid` (`bid`(333))");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice')." ADD   KEY `cid` (`cid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_notice_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `nid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `nid` (`nid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_notice_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','nid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   `nid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_log','nid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_log')." ADD   KEY `nid` (`nid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_notice_sendlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `nid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `nid` (`nid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','nid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   `nid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_notice_sendlog','nid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_notice_sendlog')." ADD   KEY `nid` (`nid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_operationvideo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `title` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `image` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','video')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `video` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_operationvideo','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_operationvideo')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_pagediy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `cateid` int(10) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `icon` varchar(30) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_pagediy','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `cateid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `title` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','icon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `icon` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','color')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `color` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `url` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `status` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `displayorder` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_pagediy_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `style` tinyint(1) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `icon` varchar(30) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `smalltitle` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `style` (`style`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','style')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `style` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `title` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','icon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `icon` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','color')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `color` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','smalltitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `smalltitle` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `url` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `status` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `displayorder` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pagediy_cate','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pagediy_cate')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(1) NOT NULL DEFAULT '0',
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobile1` varchar(20) DEFAULT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `buildarea` decimal(10,2) NOT NULL,
  `usearea` decimal(10,2) NOT NULL,
  `addarea` decimal(10,2) NOT NULL,
  `isfree` tinyint(1) NOT NULL DEFAULT '0',
  `freestart` int(11) NOT NULL,
  `freeend` int(11) NOT NULL,
  `paydate` int(11) DEFAULT NULL,
  `paymonths` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lifepay_hid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `lockmac` varchar(30) NOT NULL,
  `pricemethod` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parking','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `category` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','mobile1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `mobile1` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','buildarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `buildarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','usearea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `usearea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','addarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `addarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','isfree')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `isfree` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','freestart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `freestart` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','freeend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `freeend` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','paymonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `paymonths` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','lifepay_hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `lifepay_hid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','lockmac')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `lockmac` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','pricemethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   `pricemethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parking','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parking')." ADD   KEY `lid` (`lid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkingio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `lotid` int(10) NOT NULL,
  `io` tinyint(1) NOT NULL,
  `title` varchar(60) NOT NULL,
  `locksn` varchar(60) NOT NULL,
  `lockid` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `pc_ioid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `lotid` (`lotid`),
  KEY `io` (`io`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkingio','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','lotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `lotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `io` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','locksn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `locksn` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','lockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `lockid` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','pc_ioid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `pc_ioid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingio','lotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingio')." ADD   KEY `lotid` (`lotid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkingiolog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `lotid` int(10) NOT NULL,
  `ioid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `io` tinyint(1) NOT NULL,
  `result` varchar(60) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `lotid` (`lotid`),
  KEY `io` (`io`),
  KEY `stauts` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','lotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `lotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','ioid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `ioid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `io` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','result')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `result` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','lotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   KEY `lotid` (`lotid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingiolog','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingiolog')." ADD   KEY `io` (`io`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkinglock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `parkingid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `lockmac` varchar(30) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `remark` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `realname` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','parkingid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `parkingid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','lockmac')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `lockmac` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `realname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglock','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglock')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkinglog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(4) NOT NULL,
  `title` varchar(60) NOT NULL,
  `cid` int(10) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `paymonths` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `lid` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','paymonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `paymonths` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglog','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglog')." ADD   KEY `lid` (`lid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkinglot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `parkingnum` int(11) NOT NULL,
  `carnum` int(11) NOT NULL DEFAULT '0',
  `ischarge` tinyint(1) NOT NULL DEFAULT '0',
  `starttime` varchar(10) DEFAULT NULL,
  `endtime` varchar(10) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `qty` tinyint(4) NOT NULL DEFAULT '0',
  `minute` tinyint(4) NOT NULL DEFAULT '0',
  `dayfee` tinyint(4) NOT NULL DEFAULT '0',
  `unit` tinyint(1) NOT NULL DEFAULT '1',
  `getfee` tinyint(1) NOT NULL DEFAULT '1',
  `pricerule` tinyint(1) NOT NULL DEFAULT '0',
  `cloudid` varchar(50) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `pc_plotid` varchar(60) DEFAULT NULL,
  `pc_ruleid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `qrurl` varchar(255) DEFAULT NULL,
  `monthcardprice` decimal(10,2) DEFAULT NULL,
  `rechargmonths` int(3) NOT NULL DEFAULT '0',
  `givemonths` int(3) NOT NULL DEFAULT '0',
  `pc_secret` varchar(60) DEFAULT NULL,
  `monthmethod` tinyint(1) NOT NULL DEFAULT '0',
  `paymethod` tinyint(1) NOT NULL DEFAULT '0',
  `parktype` tinyint(1) NOT NULL DEFAULT '1',
  `authentication` tinyint(1) NOT NULL DEFAULT '0',
  `cloudruleid` varchar(50) NOT NULL,
  `devtype` tinyint(1) NOT NULL DEFAULT '0',
  `doortype` tinyint(1) NOT NULL DEFAULT '0',
  `pc_type` tinyint(1) NOT NULL DEFAULT '0',
  `year_coupon` int(5) NOT NULL DEFAULT '0',
  `month_coupon` tinyint(4) NOT NULL DEFAULT '0',
  `maxmoney` tinyint(4) NOT NULL DEFAULT '0',
  `maxminutes` int(4) NOT NULL DEFAULT '0',
  `isreserve` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','parkingnum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `parkingnum` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','carnum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `carnum` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','ischarge')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `ischarge` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `starttime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `endtime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `price` decimal(8,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','qty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `qty` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','minute')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `minute` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','dayfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `dayfee` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','unit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `unit` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','getfee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `getfee` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pricerule')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pricerule` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','cloudid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `cloudid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pc_plotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pc_plotid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pc_ruleid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pc_ruleid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `address` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','qrurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `qrurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','monthcardprice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `monthcardprice` decimal(10,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','rechargmonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `rechargmonths` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','givemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `givemonths` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pc_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pc_secret` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','monthmethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `monthmethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','paymethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `paymethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','parktype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `parktype` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','authentication')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `authentication` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','cloudruleid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `cloudruleid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','devtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `devtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','doortype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `doortype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pc_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `pc_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','year_coupon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `year_coupon` int(5) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','month_coupon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `month_coupon` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','maxmoney')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `maxmoney` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','maxminutes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `maxminutes` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','isreserve')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   `isreserve` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkinglot_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `parkid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `ctype` tinyint(1) NOT NULL DEFAULT '0',
  `qty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `carno` varchar(20) DEFAULT NULL,
  `couponserial` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `parkid` (`parkid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','parkid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `parkid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','ctype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `ctype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','qty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `qty` decimal(10,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `starttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `endtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','couponserial')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `couponserial` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkinglot_coupon','parkid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkinglot_coupon')." ADD   KEY `parkid` (`parkid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkingrule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `lotid` int(10) NOT NULL,
  `starttime` smallint(2) NOT NULL,
  `endtime` smallint(2) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `lotid` (`lotid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','lotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   `lotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   `starttime` smallint(2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   `endtime` smallint(2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   `price` decimal(8,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkingrule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkingrule')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parklock_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `parklockid` int(10) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `fromopenid` varchar(50) DEFAULT NULL,
  `fromuid` int(10) NOT NULL,
  `toopenid` varchar(50) DEFAULT NULL,
  `touid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `category` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(8,2) DEFAULT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `parklockid` (`parklockid`),
  KEY `touid` (`touid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','parklockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `parklockid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','fromopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `fromopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','fromuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `fromuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','toopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `toopenid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','touid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `touid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `starttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `endtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `category` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklock_share','parklockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklock_share')." ADD   KEY `parklockid` (`parklockid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parklocklog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `lockid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `updown` tinyint(1) NOT NULL,
  `result` varchar(60) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `lockid` (`lockid`),
  KEY `updown` (`updown`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','lockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `lockid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','updown')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `updown` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','result')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `result` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','lockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   KEY `lockid` (`lockid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parklocklog','updown')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parklocklog')." ADD   KEY `updown` (`updown`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkpay_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `parklotid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(1) NOT NULL DEFAULT '0',
  `carno` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `realname` varchar(20) DEFAULT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  `payno` varchar(60) DEFAULT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `parkingid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `parklotid` (`parklotid`),
  KEY `cuid` (`cuid`),
  KEY `carno` (`carno`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `parklotid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `category` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `carno` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `realname` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `starttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `endtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `fee` decimal(10,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `payno` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','idcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `idcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `status` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','parkingid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   `parkingid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','parklotid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `parklotid` (`parklotid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `cuid` (`cuid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkpay_log','carno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkpay_log')." ADD   KEY `carno` (`carno`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_parkshare_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `shareid` int(10) NOT NULL DEFAULT '0',
  `parklockid` int(10) NOT NULL,
  `title` varchar(60) DEFAULT NULL,
  `out_trade_no` varchar(100) DEFAULT NULL,
  `fee` decimal(6,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '0',
  `uid` int(10) NOT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `shareid` (`shareid`),
  KEY `parklockid` (`parklockid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','shareid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `shareid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','parklockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `parklockid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `title` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','out_trade_no')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `out_trade_no` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `fee` decimal(6,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `openid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `starttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `endtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','shareid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   KEY `shareid` (`shareid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_parkshare_paylog','parklockid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_parkshare_paylog')." ADD   KEY `parklockid` (`parklockid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_patrolline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `positions` varchar(1000) DEFAULT NULL,
  `starttime` varchar(10) DEFAULT NULL,
  `endtime` varchar(10) DEFAULT NULL,
  `isimage` tinyint(1) NOT NULL DEFAULT '0',
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_patrolline','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','positions')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `positions` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `starttime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `endtime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','isimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `isimage` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolline','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolline')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_patrollog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `lineid` int(10) NOT NULL,
  `patid` int(10) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `position` varchar(60) DEFAULT NULL,
  `distance` int(10) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `lineid` (`lineid`),
  KEY `patid` (`patid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_patrollog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','lineid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `lineid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','patid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `patid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `image` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','position')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `position` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','distance')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `distance` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   `ctime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrollog','lineid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrollog')." ADD   KEY `lineid` (`lineid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_patrolpos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `devicesn` varchar(60) NOT NULL,
  `distance` int(10) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','devicesn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `devicesn` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','distance')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `distance` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   KEY `lid` (`lid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_patrolpos','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_patrolpos')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `payno` varchar(60) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL,
  `billid` varchar(1000) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `tid` varchar(128) NOT NULL,
  `paytype` varchar(20) NOT NULL DEFAULT '0',
  `bid` int(10) NOT NULL DEFAULT '0',
  `sid` int(10) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '0',
  `paytime` int(10) NOT NULL,
  `ctime` int(10) NOT NULL,
  `remark` varchar(60) DEFAULT NULL,
  `feetype` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `payno` (`payno`),
  KEY `status` (`status`),
  KEY `billid` (`billid`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_paylog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `payno` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','billid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `billid` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `tid` varchar(128) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `paytype` varchar(20) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `bid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `sid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `status` int(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','paytime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `paytime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `ctime` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `remark` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','feetype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   `feetype` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','payno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   KEY `payno` (`payno`)");}
if(!pdo_fieldexists('rhinfo_zyxq_paylog','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_paylog')." ADD   KEY `status` (`status`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_pmember` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `realname` varchar(60) NOT NULL,
  `nickname` varchar(60) NOT NULL,
  `mobile` varchar(60) NOT NULL,
  `avatar` varchar(100) NOT NULL,
  `level` tinyint(1) DEFAULT '0',
  `remark` varchar(100) NOT NULL,
  `content` text,
  `openid` varchar(100) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_pmember','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `realname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `nickname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `mobile` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','avatar')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `avatar` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','level')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `level` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `openid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pmember','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pmember')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_pricerule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `flid` int(10) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `floorstart` int(4) NOT NULL,
  `floorend` int(4) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `flid` (`flid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_pricerule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `flid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','floorstart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `floorstart` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','floorend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `floorend` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_pricerule','flid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_pricerule')." ADD   KEY `flid` (`flid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `printbrand` tinyint(1) NOT NULL,
  `printtype` tinyint(1) NOT NULL,
  `apikey` varchar(100) DEFAULT NULL,
  `printkey` varchar(100) DEFAULT NULL,
  `printno` varchar(30) DEFAULT NULL,
  `userid` varchar(30) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `remark` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_printer','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','printbrand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `printbrand` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','printtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `printtype` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','apikey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `apikey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','printkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `printkey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','printno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `printno` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','userid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `userid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `remark` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_printer','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_printer')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_progoods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `cateid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(60) DEFAULT NULL,
  `spec` varchar(60) DEFAULT NULL,
  `goodssn` varchar(60) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `price` decimal(6,2) NOT NULL DEFAULT '0.00',
  `brand` varchar(60) DEFAULT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `memo` varchar(100) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `ouid` int(10) NOT NULL,
  `otime` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `ouid` (`ouid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_progoods','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `cateid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `title` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','spec')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `spec` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','goodssn')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `goodssn` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','position')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `position` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `price` decimal(6,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','brand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `brand` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','memo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `memo` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','ouid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `ouid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','otime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `otime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `status` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_progoods_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_progoods_cate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_progoods_cate')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_property` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `content` text,
  `telphone` varchar(60) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  `userno` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `ispay` tinyint(1) NOT NULL DEFAULT '0',
  `paytype` tinyint(1) NOT NULL DEFAULT '2',
  `aliaccount` varchar(100) DEFAULT NULL,
  `alipartner` varchar(100) DEFAULT NULL,
  `alisecret` varchar(100) DEFAULT NULL,
  `submerchid` varchar(100) DEFAULT NULL,
  `bankmerchid` varchar(50) DEFAULT NULL,
  `ymfurl` varchar(100) DEFAULT NULL,
  `ymfkey` varchar(50) DEFAULT NULL,
  `rsdbankmerchid` varchar(50) DEFAULT NULL,
  `bankkey` varchar(50) DEFAULT NULL,
  `paysuccessurl` varchar(100) DEFAULT NULL,
  `cost` int(6) NOT NULL DEFAULT '0',
  `credit` int(6) NOT NULL DEFAULT '0',
  `paycost` int(6) NOT NULL DEFAULT '0',
  `paycredit` int(6) NOT NULL DEFAULT '0',
  `payrate` int(3) NOT NULL DEFAULT '0',
  `wecoupon` tinyint(1) NOT NULL DEFAULT '0',
  `onhand` int(10) NOT NULL,
  `inqty` int(10) NOT NULL,
  `outqty` int(10) NOT NULL,
  `intime` int(11) NOT NULL,
  `outtime` int(11) NOT NULL,
  `pc_pid` varchar(60) DEFAULT NULL,
  `lifepay_pid` varchar(60) DEFAULT NULL,
  `door_pid` varchar(60) DEFAULT NULL,
  `limitqty` int(10) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `isalipay` tinyint(1) NOT NULL DEFAULT '0',
  `muid` int(10) NOT NULL DEFAULT '0',
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `yearprice` decimal(8,2) NOT NULL DEFAULT '0.00',
  `starmerchid` varchar(20) DEFAULT NULL,
  `starkey` varchar(50) DEFAULT NULL,
  `starorg` varchar(20) DEFAULT NULL,
  `startrm` varchar(20) DEFAULT NULL,
  `parentid` int(10) NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  `board_password` varchar(60) DEFAULT NULL,
  `haina_property_id` varchar(60) DEFAULT NULL,
  `alipay_type` tinyint(1) NOT NULL DEFAULT '0',
  `alipay_appid` varchar(60) DEFAULT NULL,
  `alipay_rsa2` text,
  `alipay_private` text,
  `alipay_seller_id` varchar(60) DEFAULT NULL,
  `alipay_app_auth_token` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_property','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_property','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `title` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `image` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','banner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `banner` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_property','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `telphone` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','province')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `province` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','city')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `city` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','district')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `district` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `address` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','service')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `service` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','website')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `website` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','userno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `userno` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','username')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `username` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','ispay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `ispay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `paytype` tinyint(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','aliaccount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `aliaccount` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipartner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipartner` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alisecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alisecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','submerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `submerchid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','bankmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `bankmerchid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','ymfurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `ymfurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','ymfkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `ymfkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','rsdbankmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `rsdbankmerchid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','bankkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `bankkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','paysuccessurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `paysuccessurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','cost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `cost` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `credit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','paycost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `paycost` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','paycredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `paycredit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','payrate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `payrate` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','wecoupon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `wecoupon` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','onhand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `onhand` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','inqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `inqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','outqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `outqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','intime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `intime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','outtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `outtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','pc_pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `pc_pid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','lifepay_pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `lifepay_pid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','door_pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `door_pid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','limitqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `limitqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `status` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','isalipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `isalipay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','muid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `muid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','yearprice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `yearprice` decimal(8,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','starmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `starmerchid` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','starkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `starkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','starorg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `starorg` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','startrm')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `startrm` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','parentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `parentid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','logo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `logo` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','board_password')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `board_password` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','haina_property_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `haina_property_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_rsa2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_rsa2` text");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_private')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_private` text");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_seller_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_seller_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','alipay_app_auth_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   `alipay_app_auth_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_property','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_property')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_qacate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` int(6) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `isrecommand` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `isrecommand` (`isrecommand`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_qacate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `displayorder` int(6) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `enabled` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   `isrecommand` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_qacate','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_qacate')." ADD   KEY `isrecommand` (`isrecommand`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `cateid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `isrecommand` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` int(6) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `cateid` (`cateid`),
  KEY `isrecommand` (`isrecommand`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_question','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_question','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_question','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `cateid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_question','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `title` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_question','keywords')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `keywords` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_question','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_question','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_question','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `isrecommand` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_question','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `displayorder` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_question','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_question','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_question','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_question','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_question','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   KEY `cateid` (`cateid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_question','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_question')." ADD   KEY `isrecommand` (`isrecommand`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_rbanner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `btype` tinyint(3) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` int(5) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `boardcate` int(11) NOT NULL DEFAULT '0',
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `bgimage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `btype` (`btype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_rbanner','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','btype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `btype` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','link')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `link` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `displayorder` int(5) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `enabled` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','boardcate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `boardcate` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','bgimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   `bgimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_rbanner','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_rbanner')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `contact` varchar(60) NOT NULL,
  `telphone` varchar(60) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `province` varchar(60) NOT NULL,
  `city` varchar(60) NOT NULL,
  `district` varchar(60) NOT NULL,
  `address` varchar(100) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `servicethumb` varchar(255) DEFAULT NULL,
  `serviceicon` varchar(30) DEFAULT NULL,
  `serviceright` smallint(6) NOT NULL,
  `servicebottom` smallint(6) NOT NULL,
  `servicepagehome` tinyint(1) NOT NULL DEFAULT '0',
  `servicepageser` tinyint(1) NOT NULL DEFAULT '0',
  `servicepageste` tinyint(1) NOT NULL DEFAULT '0',
  `bindverify` tinyint(1) NOT NULL DEFAULT '0',
  `shareverify` tinyint(1) NOT NULL DEFAULT '0',
  `repairverify` tinyint(1) NOT NULL DEFAULT '0',
  `suggestverify` tinyint(1) NOT NULL DEFAULT '0',
  `payfeeverify` tinyint(1) NOT NULL DEFAULT '0',
  `smsqty` int(10) NOT NULL DEFAULT '0',
  `doorimage` varchar(255) DEFAULT NULL,
  `locationimage` varchar(255) DEFAULT NULL,
  `buildingimage` varchar(255) DEFAULT NULL,
  `unitimage` varchar(255) DEFAULT NULL,
  `visitimage` varchar(255) DEFAULT NULL,
  `register` tinyint(1) NOT NULL DEFAULT '0',
  `regbanner` varchar(255) DEFAULT NULL,
  `replyimage` varchar(255) DEFAULT NULL,
  `replydesc` varchar(255) DEFAULT NULL,
  `discount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `freebill` tinyint(1) NOT NULL DEFAULT '0',
  `emptybill` tinyint(1) NOT NULL DEFAULT '0',
  `feebillmonth` tinyint(1) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `qrurl` varchar(100) DEFAULT NULL,
  `ispay` tinyint(1) NOT NULL DEFAULT '0',
  `paytype` tinyint(1) NOT NULL DEFAULT '2',
  `aliaccount` varchar(100) DEFAULT NULL,
  `alipartner` varchar(100) DEFAULT NULL,
  `alisecret` varchar(100) DEFAULT NULL,
  `submerchid` varchar(100) DEFAULT NULL,
  `bankmerchid` varchar(50) DEFAULT NULL,
  `ymfurl` varchar(100) DEFAULT NULL,
  `ymfkey` varchar(50) DEFAULT NULL,
  `rsdbankmerchid` varchar(50) DEFAULT NULL,
  `bankkey` varchar(50) DEFAULT NULL,
  `cost` int(6) NOT NULL DEFAULT '0',
  `credit` int(6) NOT NULL DEFAULT '0',
  `paycost` int(6) NOT NULL DEFAULT '0',
  `paycredit` int(6) NOT NULL DEFAULT '0',
  `payrate` int(3) NOT NULL DEFAULT '0',
  `bindcredit` int(6) NOT NULL DEFAULT '0',
  `bindstrategyid` int(10) NOT NULL DEFAULT '0',
  `feestrategyid` int(10) NOT NULL DEFAULT '0',
  `invitestrategyid` int(10) NOT NULL DEFAULT '0',
  `paysuccessurl` varchar(100) DEFAULT NULL,
  `wecoupon` tinyint(1) NOT NULL DEFAULT '0',
  `repairnotice` tinyint(1) NOT NULL DEFAULT '0',
  `suggestnotice` tinyint(1) NOT NULL DEFAULT '0',
  `tplnoticefirst` varchar(60) DEFAULT NULL,
  `finishdays` int(3) NOT NULL DEFAULT '0',
  `abnbill` tinyint(1) NOT NULL DEFAULT '0',
  `regbannerurl` varchar(255) DEFAULT NULL,
  `isprintfeedetail` tinyint(1) NOT NULL DEFAULT '0',
  `feecontrol` tinyint(1) NOT NULL DEFAULT '0',
  `bindsuccessurl` varchar(255) DEFAULT NULL,
  `arrearagelimit1` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit2` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit3` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit4` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit5` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit6` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit7` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit8` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagelimit9` tinyint(1) NOT NULL DEFAULT '0',
  `arrearagemonths` smallint(3) NOT NULL DEFAULT '0',
  `onhand` int(10) NOT NULL,
  `inqty` int(10) NOT NULL,
  `outqty` int(10) NOT NULL,
  `intime` int(11) NOT NULL,
  `outtime` int(11) NOT NULL,
  `thirdauth` tinyint(1) NOT NULL DEFAULT '0',
  `thirdurl` varchar(100) DEFAULT NULL,
  `isrepairdisp` tinyint(1) NOT NULL DEFAULT '1',
  `issuggestdisp` tinyint(1) NOT NULL DEFAULT '1',
  `nobindlimit` tinyint(1) NOT NULL DEFAULT '0',
  `forcebind` tinyint(1) NOT NULL DEFAULT '0',
  `pc_type` tinyint(1) NOT NULL DEFAULT '0',
  `pc_rid` varchar(60) DEFAULT NULL,
  `pc_appid` varchar(60) DEFAULT NULL,
  `pc_secret` varchar(60) DEFAULT NULL,
  `lifepay_type` tinyint(1) NOT NULL DEFAULT '0',
  `lifepay_token` varchar(60) DEFAULT NULL,
  `lifepay_rid` varchar(60) DEFAULT NULL,
  `lifepay_init` tinyint(1) NOT NULL DEFAULT '0',
  `doordays` int(3) NOT NULL DEFAULT '0',
  `thinmoo_token` varchar(60) DEFAULT NULL,
  `wxminiappid` varchar(30) DEFAULT NULL,
  `wxminiappsecret` varchar(100) DEFAULT NULL,
  `wxminiappurl` varchar(100) DEFAULT NULL,
  `roomfix` varchar(10) DEFAULT NULL,
  `asktype` tinyint(1) NOT NULL DEFAULT '0',
  `roomqty` int(10) NOT NULL DEFAULT '0',
  `coveredarea` decimal(16,2) NOT NULL DEFAULT '0.00',
  `buildarea` decimal(16,2) NOT NULL DEFAULT '0.00',
  `askopenid` varchar(100) DEFAULT NULL,
  `askuid` int(10) NOT NULL,
  `asktime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `propctime` int(11) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `isalipay` tinyint(1) NOT NULL DEFAULT '0',
  `pc_channelid` varchar(60) DEFAULT NULL,
  `doorwxappid` varchar(30) DEFAULT NULL,
  `doorwxapppage` varchar(255) DEFAULT NULL,
  `starmerchid` varchar(20) DEFAULT NULL,
  `starkey` varchar(50) DEFAULT NULL,
  `starorg` varchar(20) DEFAULT NULL,
  `startrm` varchar(20) DEFAULT NULL,
  `isprintremark` tinyint(1) NOT NULL DEFAULT '0',
  `pznopre` varchar(10) DEFAULT NULL,
  `repairpnotice` tinyint(1) NOT NULL DEFAULT '0',
  `doorlock_type` tinyint(1) NOT NULL DEFAULT '1',
  `parklock_type` tinyint(1) NOT NULL DEFAULT '0',
  `parklock_token` varchar(60) DEFAULT NULL,
  `isonlinepay` tinyint(1) NOT NULL DEFAULT '1',
  `isprintdate` tinyint(1) NOT NULL DEFAULT '0',
  `muid` int(10) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `sealimage` varchar(255) DEFAULT NULL,
  `feeshowtype` tinyint(1) NOT NULL DEFAULT '0',
  `mailin_token` varchar(60) DEFAULT NULL,
  `mailin_appid` varchar(60) DEFAULT NULL,
  `mailin_secret` varchar(60) DEFAULT NULL,
  `board_password` varchar(60) DEFAULT NULL,
  `latemethod` tinyint(1) NOT NULL DEFAULT '0',
  `fsealimage` varchar(255) DEFAULT NULL,
  `repairsort` tinyint(1) NOT NULL DEFAULT '0',
  `ylbid` varchar(20) DEFAULT NULL,
  `undodays` tinyint(3) NOT NULL DEFAULT '0',
  `feebillgrant` tinyint(1) NOT NULL DEFAULT '0',
  `aurine_token` varchar(60) DEFAULT NULL,
  `aurine_appid` varchar(60) DEFAULT NULL,
  `aurine_secret` varchar(60) DEFAULT NULL,
  `aurine_rid` varchar(60) DEFAULT NULL,
  `aurine_yun_rid` varchar(60) DEFAULT NULL,
  `pc_shopid` varchar(60) DEFAULT NULL,
  `haina_community_id` varchar(60) DEFAULT NULL,
  `alipay_type` tinyint(1) NOT NULL DEFAULT '0',
  `alipay_appid` varchar(60) DEFAULT NULL,
  `alipay_seller_id` varchar(60) DEFAULT NULL,
  `alipay_app_auth_token` varchar(60) DEFAULT NULL,
  `alipay_rsa2` text,
  `alipay_private` text,
  `thinmoo_uuid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_region','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_region','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `category` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `contact` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `telphone` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','province')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `province` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','city')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `city` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','district')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `district` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `url` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','service')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `service` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','servicethumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `servicethumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','serviceicon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `serviceicon` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','serviceright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `serviceright` smallint(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','servicebottom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `servicebottom` smallint(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','servicepagehome')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `servicepagehome` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','servicepageser')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `servicepageser` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','servicepageste')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `servicepageste` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bindverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bindverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','shareverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `shareverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','repairverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `repairverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','suggestverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `suggestverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','payfeeverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `payfeeverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','smsqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `smsqty` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','doorimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `doorimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','locationimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `locationimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','buildingimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `buildingimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','unitimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `unitimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','visitimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `visitimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','register')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `register` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','regbanner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `regbanner` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','replyimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `replyimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','replydesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `replydesc` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','discount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `discount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','freebill')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `freebill` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','emptybill')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `emptybill` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','feebillmonth')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `feebillmonth` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','groupid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `groupid` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `openid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','qrurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `qrurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','ispay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `ispay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','paytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `paytype` tinyint(1) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aliaccount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aliaccount` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipartner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipartner` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alisecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alisecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','submerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `submerchid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bankmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bankmerchid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','ymfurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `ymfurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','ymfkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `ymfkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','rsdbankmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `rsdbankmerchid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bankkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bankkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','cost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `cost` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `credit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','paycost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `paycost` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','paycredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `paycredit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','payrate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `payrate` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bindcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bindcredit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bindstrategyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bindstrategyid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','feestrategyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `feestrategyid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','invitestrategyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `invitestrategyid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','paysuccessurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `paysuccessurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','wecoupon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `wecoupon` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','repairnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `repairnotice` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','suggestnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `suggestnotice` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','tplnoticefirst')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `tplnoticefirst` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','finishdays')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `finishdays` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','abnbill')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `abnbill` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','regbannerurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `regbannerurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isprintfeedetail')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isprintfeedetail` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','feecontrol')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `feecontrol` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','bindsuccessurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `bindsuccessurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit1` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit2` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit3` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit4` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit5` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit6')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit6` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit7')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit7` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit8')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit8` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagelimit9')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagelimit9` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','arrearagemonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `arrearagemonths` smallint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','onhand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `onhand` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','inqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `inqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','outqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `outqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','intime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `intime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','outtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `outtime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','thirdauth')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `thirdauth` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','thirdurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `thirdurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isrepairdisp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isrepairdisp` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','issuggestdisp')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `issuggestdisp` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','nobindlimit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `nobindlimit` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','forcebind')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `forcebind` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_rid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_secret` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lifepay_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lifepay_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lifepay_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lifepay_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lifepay_rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lifepay_rid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','lifepay_init')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `lifepay_init` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','doordays')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `doordays` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','thinmoo_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `thinmoo_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','wxminiappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `wxminiappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','wxminiappsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `wxminiappsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','wxminiappurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `wxminiappurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','roomfix')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `roomfix` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','asktype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `asktype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','roomqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `roomqty` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','coveredarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `coveredarea` decimal(16,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','buildarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `buildarea` decimal(16,2) NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','askopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `askopenid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','askuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `askuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','asktime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `asktime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','propctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `propctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isalipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isalipay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_channelid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_channelid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','doorwxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `doorwxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','doorwxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `doorwxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','starmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `starmerchid` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','starkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `starkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','starorg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `starorg` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','startrm')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `startrm` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isprintremark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isprintremark` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pznopre')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pznopre` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','repairpnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `repairpnotice` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','doorlock_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `doorlock_type` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','parklock_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `parklock_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','parklock_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `parklock_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isonlinepay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isonlinepay` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','isprintdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `isprintdate` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','muid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `muid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','sealimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `sealimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','feeshowtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `feeshowtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','mailin_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `mailin_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','mailin_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `mailin_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','mailin_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `mailin_secret` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','board_password')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `board_password` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','latemethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `latemethod` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','fsealimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `fsealimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','repairsort')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `repairsort` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','ylbid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `ylbid` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','undodays')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `undodays` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','feebillgrant')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `feebillgrant` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aurine_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aurine_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aurine_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aurine_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aurine_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aurine_secret` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aurine_rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aurine_rid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','aurine_yun_rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `aurine_yun_rid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pc_shopid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `pc_shopid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','haina_community_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `haina_community_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_seller_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_seller_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_app_auth_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_app_auth_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_rsa2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_rsa2` text");}
if(!pdo_fieldexists('rhinfo_zyxq_region','alipay_private')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `alipay_private` text");}
if(!pdo_fieldexists('rhinfo_zyxq_region','thinmoo_uuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   `thinmoo_uuid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_region_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `company` varchar(60) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `telphone` varchar(60) DEFAULT NULL,
  `licimage` varchar(255) DEFAULT NULL,
  `icardaimage` varchar(255) DEFAULT NULL,
  `icardbimage` varchar(255) DEFAULT NULL,
  `accimage` varchar(255) DEFAULT NULL,
  `doorimage` varchar(255) DEFAULT NULL,
  `payimage` varchar(255) DEFAULT NULL,
  `remark` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_region_account','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','company')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `company` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `contact` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `telphone` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','licimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `licimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','icardaimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `icardaimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','icardbimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `icardbimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','accimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `accimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','doorimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `doorimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','payimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `payimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `remark` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_account','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_account')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_region_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `stars` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_region_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','stars')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `stars` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `comment` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_comment','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_comment')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_region_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_region_follow','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `deleted` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_follow','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_follow')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_region_smslog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `io` tinyint(4) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `title` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','io')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `io` tinyint(4) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_region_smslog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_region_smslog')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_regionav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `wxappid` varchar(30) DEFAULT NULL,
  `wxapppage` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` int(5) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `bgimage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_regionav','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `category` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','link')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `link` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','wxapppage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `wxapppage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `displayorder` int(5) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `enabled` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','bgimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   `bgimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_regionav','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionav')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_regionmenu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `menucolor` varchar(10) DEFAULT NULL,
  `menu1` varchar(30) DEFAULT NULL,
  `menu2` varchar(30) DEFAULT NULL,
  `menu3` varchar(30) DEFAULT NULL,
  `menu4` varchar(30) DEFAULT NULL,
  `menu5` varchar(30) DEFAULT NULL,
  `icon1` varchar(30) NOT NULL DEFAULT 'icon-home',
  `icon2` varchar(30) NOT NULL DEFAULT 'icon-mark1',
  `icon3` varchar(30) NOT NULL DEFAULT 'icon-like1',
  `icon4` varchar(30) NOT NULL DEFAULT 'icon-lights',
  `icon5` varchar(30) NOT NULL DEFAULT 'icon-person2',
  `menu1url` varchar(100) DEFAULT NULL,
  `menu2url` varchar(100) DEFAULT NULL,
  `menu3url` varchar(100) DEFAULT NULL,
  `menu4url` varchar(100) DEFAULT NULL,
  `menu5url` varchar(100) DEFAULT NULL,
  `servicedisplay` tinyint(1) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menucolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menucolor` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu1` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu2` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu3` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu4` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu5` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','icon1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `icon1` varchar(30) NOT NULL DEFAULT 'icon-home'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','icon2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `icon2` varchar(30) NOT NULL DEFAULT 'icon-mark1'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','icon3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `icon3` varchar(30) NOT NULL DEFAULT 'icon-like1'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','icon4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `icon4` varchar(30) NOT NULL DEFAULT 'icon-lights'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','icon5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `icon5` varchar(30) NOT NULL DEFAULT 'icon-person2'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu1url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu1url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu2url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu2url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu3url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu3url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu4url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu4url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','menu5url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `menu5url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','servicedisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `servicedisplay` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `enabled` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_regionmenu','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_regionmenu')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_repair` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `images` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `star` tinyint(3) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `getopenid` varchar(60) DEFAULT NULL,
  `getuid` int(10) NOT NULL DEFAULT '0',
  `lasttime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `reporttime` int(11) NOT NULL DEFAULT '0',
  `reporttimes` smallint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_repair','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','star')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `star` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `comment` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','getopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `getopenid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','getuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `getuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','lasttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `lasttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','reporttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `reporttime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','reporttimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   `reporttimes` smallint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair')." ADD   KEY `hid` (`hid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_repair_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `repairid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `updown` tinyint(1) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `repairid` (`repairid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','repairid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `repairid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','updown')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `updown` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `comment` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','repairid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   KEY `repairid` (`repairid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_comment')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_repair_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `image` text,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_repair_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `image` text");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repair_record','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repair_record')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_repairp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `images` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `star` tinyint(3) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `getopenid` varchar(60) DEFAULT NULL,
  `getuid` int(10) NOT NULL DEFAULT '0',
  `lasttime` int(11) NOT NULL,
  `reporttime` int(11) NOT NULL,
  `reporttimes` smallint(3) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  `getmutiuid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_repairp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','star')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `star` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `comment` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','getopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `getopenid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','getuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `getuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','lasttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `lasttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','reporttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `reporttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','reporttimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `reporttimes` smallint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','getmutiuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   `getmutiuid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `hid` (`hid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_repairp_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `image` text,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `image` text");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_repairp_record','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_repairp_record')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_replyrule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `replyid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `qr` tinyint(1) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `parkid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `replyid` (`replyid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_replyrule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','replyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `replyid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','qr')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `qr` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','parkid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   `parkid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_replyrule','replyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_replyrule')." ADD   KEY `replyid` (`replyid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `floor` int(10) NOT NULL,
  `buildarea` decimal(10,2) NOT NULL,
  `usearea` decimal(10,2) NOT NULL,
  `addarea` decimal(10,2) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobile1` varchar(20) DEFAULT NULL,
  `isfree` tinyint(1) NOT NULL DEFAULT '0',
  `freestart` int(11) NOT NULL,
  `freeend` int(11) NOT NULL,
  `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `paydate` int(11) DEFAULT NULL,
  `billdate` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `isnotice` tinyint(1) NOT NULL DEFAULT '1',
  `deposit` decimal(10,2) NOT NULL,
  `preelectric` decimal(10,2) NOT NULL,
  `prewater` decimal(10,2) NOT NULL,
  `prepayment` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lifepay_hid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `birthyear` smallint(6) DEFAULT NULL,
  `birthmonth` tinyint(3) DEFAULT NULL,
  `birthday` tinyint(3) DEFAULT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  `watermeter` varchar(30) NOT NULL,
  `electmeter` varchar(30) NOT NULL,
  `gasmeter` varchar(30) NOT NULL,
  `aurine_hid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `mobile` (`mobile`),
  KEY `mobile1` (`mobile1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','floor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `floor` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','buildarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `buildarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','usearea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `usearea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','addarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `addarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','mobile1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `mobile1` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','isfree')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `isfree` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room','freestart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `freestart` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','freeend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `freeend` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','isdiscount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `paydate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `billdate` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','isnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `isnotice` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_room','deposit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `deposit` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','preelectric')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `preelectric` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','prewater')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `prewater` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','prepayment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `prepayment` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room','lifepay_hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `lifepay_hid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','birthyear')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `birthyear` smallint(6) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','birthmonth')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `birthmonth` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','birthday')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `birthday` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','idcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `idcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','watermeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `watermeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','electmeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `electmeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','gasmeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `gasmeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','aurine_hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   `aurine_hid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room')." ADD   KEY `mobile` (`mobile`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_abnlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `content` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `remark` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_abnlog','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_abnlog')." ADD   KEY `tid` (`tid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_chglog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `newownername` varchar(30) DEFAULT NULL,
  `newmobile` varchar(20) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','newownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `newownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','newmobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `newmobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_chglog','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_chglog')." ADD   KEY `tid` (`tid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_feeitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `calmethod` varchar(30) NOT NULL,
  `qty` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `measure` varchar(30) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `paymonths` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `billdate` int(11) NOT NULL,
  `remark` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `hid` (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `weid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','calmethod')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `calmethod` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','qty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `qty` decimal(10,2) unsigned NOT NULL DEFAULT '0.00'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','measure')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `measure` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','paymonths')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `paymonths` tinyint(3) unsigned NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `billdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `remark` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_feeitem','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_feeitem')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_mp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `otype` tinyint(1) NOT NULL DEFAULT '1',
  `isnotice` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `birthyear` smallint(6) DEFAULT NULL,
  `birthmonth` tinyint(3) DEFAULT NULL,
  `birthday` tinyint(3) DEFAULT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_mp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','otype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `otype` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','isnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `isnotice` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `deleted` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','birthyear')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `birthyear` smallint(6) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','birthmonth')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `birthmonth` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','birthday')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `birthday` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','idcard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   `idcard` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_mp','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_mp')." ADD   KEY `hid` (`hid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_parking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `parkingid` int(10) NOT NULL,
  `remark` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `hid` (`hid`),
  KEY `parkingid` (`parkingid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_parking','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `weid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','parkingid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `parkingid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `remark` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_parking','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_parking')." ADD   KEY `hid` (`hid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_room_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `tagid` int(10) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_room_tag','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','tagid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `tagid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_room_tag','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_room_tag')." ADD   KEY `tid` (`tid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_roomprice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `itemid` int(10) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_roomprice','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','itemid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `itemid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','category')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `category` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','price')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `price` decimal(8,2) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','isdiscount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','startdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `startdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','enddate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `enddate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `remark` varchar(200) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_roomprice','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_roomprice')." ADD   KEY `hid` (`hid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secacc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `limitqty` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `issysset` tinyint(1) NOT NULL DEFAULT '1',
  `ispayset` tinyint(1) NOT NULL DEFAULT '1',
  `chargingqty` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secacc','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','limitqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `limitqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','issysset')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `issysset` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','ispayset')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `ispayset` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','chargingqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   `chargingqty` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secacc','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secacc')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secaprg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `aid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `sid` (`sid`),
  KEY `pid` (`pid`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secaprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','aid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   `aid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   KEY `sid` (`sid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secaprg','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secaprg')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secasys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `aid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `sid` (`sid`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secasys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','aid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   `aid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secasys','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secasys')." ADD   KEY `sid` (`sid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secgprg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `add` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  `export` tinyint(1) NOT NULL DEFAULT '0',
  `query` tinyint(1) NOT NULL DEFAULT '0',
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `audit` tinyint(1) NOT NULL DEFAULT '0',
  `other1` tinyint(1) NOT NULL DEFAULT '0',
  `other2` tinyint(1) NOT NULL DEFAULT '0',
  `other3` tinyint(1) NOT NULL DEFAULT '0',
  `other4` tinyint(1) NOT NULL DEFAULT '0',
  `other5` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `gid` (`gid`),
  KEY `sid` (`sid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secgprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `gid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','add')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `add` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','edit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `edit` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','delete')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `delete` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','export')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `export` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','query')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `query` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','view')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `view` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','print')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `print` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `enabled` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','audit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `audit` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','other1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `other1` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','other2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `other2` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','other3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `other3` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','other4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `other4` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','other5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   `other5` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   KEY `gid` (`gid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgprg')." ADD   KEY `sid` (`sid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(100) NOT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secgroup','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `status` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgroup','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgroup')." ADD   KEY `weid` (`weid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secgsys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `gid` (`gid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secgsys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   `gid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secgsys','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secgsys')." ADD   KEY `gid` (`gid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sechelp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `do` varchar(30) DEFAULT NULL,
  `op` varchar(30) DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `content` text,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `pid` (`pid`),
  KEY `do` (`do`),
  KEY `op` (`op`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sechelp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','do')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `do` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','op')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `op` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `title` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   KEY `sid` (`sid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sechelp','do')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sechelp')." ADD   KEY `do` (`do`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secprg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `do` varchar(60) DEFAULT NULL,
  `op` varchar(60) DEFAULT NULL,
  `iconfont` varchar(60) DEFAULT NULL,
  `displayorder` int(5) NOT NULL DEFAULT '0',
  `isdisplay` tinyint(1) NOT NULL DEFAULT '1',
  `issys` tinyint(1) NOT NULL DEFAULT '0',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `export` tinyint(1) NOT NULL DEFAULT '1',
  `query` tinyint(1) NOT NULL DEFAULT '1',
  `view` tinyint(1) NOT NULL DEFAULT '1',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `audit` tinyint(1) NOT NULL DEFAULT '1',
  `other1` tinyint(1) NOT NULL DEFAULT '0',
  `other2` tinyint(1) NOT NULL DEFAULT '0',
  `other3` tinyint(1) NOT NULL DEFAULT '0',
  `other4` tinyint(1) NOT NULL DEFAULT '0',
  `other5` tinyint(1) NOT NULL DEFAULT '0',
  `name1` varchar(60) DEFAULT NULL,
  `name2` varchar(60) DEFAULT NULL,
  `name3` varchar(60) DEFAULT NULL,
  `name4` varchar(60) DEFAULT NULL,
  `name5` varchar(60) DEFAULT NULL,
  `isdismenu` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `issys` (`issys`),
  KEY `isdisplay` (`isdisplay`),
  KEY `isdismenu` (`isdismenu`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `url` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','do')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `do` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','op')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `op` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','iconfont')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `iconfont` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `displayorder` int(5) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','isdisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `isdisplay` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','issys')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `issys` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','add')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `add` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','edit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `edit` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','delete')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `delete` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','export')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `export` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','query')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `query` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','view')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `view` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','print')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `print` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `enabled` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','audit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `audit` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','other1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `other1` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','other2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `other2` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','other3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `other3` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','other4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `other4` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','other5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `other5` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','name1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `name1` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','name2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `name2` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','name3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `name3` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','name4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `name4` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','name5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `name5` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','isdismenu')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   `isdismenu` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   KEY `sid` (`sid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','issys')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   KEY `issys` (`issys`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secprg','isdisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secprg')." ADD   KEY `isdisplay` (`isdisplay`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secsys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `displayorder` tinyint(4) NOT NULL DEFAULT '0',
  `issys` tinyint(4) NOT NULL DEFAULT '0',
  `isdisplay` tinyint(1) NOT NULL DEFAULT '1',
  `iconfont` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `issys` (`issys`),
  KEY `isdisplay` (`isdisplay`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secsys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   `displayorder` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','issys')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   `issys` tinyint(4) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','isdisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   `isdisplay` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','iconfont')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   `iconfont` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secsys','issys')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secsys')." ADD   KEY `issys` (`issys`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_secuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `userno` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `uid` int(10) NOT NULL,
  `usergroup` tinyint(4) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `mobilephone` varchar(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(100) DEFAULT NULL,
  `lastvisit` int(10) DEFAULT NULL,
  `lastip` varchar(15) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `haina_uid` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `gid` (`gid`),
  KEY `userno` (`userno`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_secuser','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `gid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','userno')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `userno` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','username')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `username` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','password')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `password` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','usergroup')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `usergroup` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','name')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `name` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','mobilephone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `mobilephone` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `status` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `remark` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','lastvisit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `lastvisit` int(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','lastip')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `lastip` varchar(15) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','haina_uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   `haina_uid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_secuser','gid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_secuser')." ADD   KEY `gid` (`gid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `lid` int(4) NOT NULL,
  `floor` int(10) NOT NULL,
  `buildarea` decimal(10,2) NOT NULL,
  `usearea` decimal(10,2) NOT NULL,
  `addarea` decimal(10,2) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobile1` varchar(20) DEFAULT NULL,
  `isfree` tinyint(1) NOT NULL DEFAULT '0',
  `freestart` int(11) NOT NULL,
  `freeend` int(11) NOT NULL,
  `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0',
  `paydate` int(11) NOT NULL,
  `billdate` int(11) NOT NULL,
  `shopname` varchar(60) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lifepay_hid` varchar(60) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  `isnotice` tinyint(1) NOT NULL DEFAULT '1',
  `watermeter` varchar(30) NOT NULL,
  `electmeter` varchar(30) NOT NULL,
  `gasmeter` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `mobile` (`mobile`),
  KEY `mobile1` (`mobile1`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_shop','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `lid` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','floor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `floor` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','buildarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `buildarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','usearea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `usearea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','addarea')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `addarea` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','mobile1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `mobile1` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','isfree')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `isfree` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','freestart')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `freestart` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','freeend')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `freeend` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','isdiscount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `isdiscount` decimal(2,1) NOT NULL DEFAULT '0.0'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','paydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `paydate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','billdate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `billdate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','shopname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `shopname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','lifepay_hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `lifepay_hid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','isnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `isnotice` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','watermeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `watermeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','electmeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `electmeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','gasmeter')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   `gasmeter` varchar(30) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   KEY `lid` (`lid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop')." ADD   KEY `mobile` (`mobile`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_shop_abnlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `content` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `remark` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_abnlog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_abnlog')." ADD   `ctime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_shop_chglog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `newownername` varchar(30) DEFAULT NULL,
  `newmobile` varchar(20) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `lid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','newownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `newownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','newmobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `newmobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_chglog','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_chglog')." ADD   KEY `lid` (`lid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_shop_mp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `rid` int(10) NOT NULL,
  `lid` int(4) NOT NULL,
  `sid` int(10) NOT NULL,
  `ownername` varchar(30) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `otype` tinyint(1) NOT NULL DEFAULT '1',
  `isnotice` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `rid` (`rid`),
  KEY `lid` (`lid`),
  KEY `sid` (`sid`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `lid` int(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','ownername')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `ownername` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `mobile` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','otype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `otype` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','isnotice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `isnotice` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   `deleted` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','lid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   KEY `lid` (`lid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_shop_mp','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_shop_mp')." ADD   KEY `sid` (`sid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_board` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `logo` varchar(255) DEFAULT '',
  `desc` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `showgroups` text,
  `showlevels` text,
  `postgroups` text,
  `postlevels` text,
  `showagentlevels` text,
  `postagentlevels` text,
  `postcredit` int(11) DEFAULT '0',
  `replycredit` int(11) DEFAULT '0',
  `bestcredit` int(11) DEFAULT '0',
  `bestboardcredit` int(11) DEFAULT '0',
  `notagent` tinyint(3) DEFAULT '0',
  `notagentpost` tinyint(3) DEFAULT '0',
  `topcredit` int(11) DEFAULT '0',
  `topboardcredit` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `noimage` tinyint(3) DEFAULT '0',
  `novoice` tinyint(3) DEFAULT '0',
  `needfollow` tinyint(3) DEFAULT '0',
  `needpostfollow` tinyint(3) DEFAULT '0',
  `share_title` varchar(255) DEFAULT '',
  `share_icon` varchar(255) DEFAULT '',
  `share_desc` varchar(255) DEFAULT '',
  `keyword` varchar(255) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  `banner` varchar(255) DEFAULT '',
  `needcheck` tinyint(3) DEFAULT '0',
  `needcheckmanager` tinyint(3) DEFAULT '0',
  `needcheckreply` int(11) DEFAULT '0',
  `needcheckreplymanager` int(11) DEFAULT '0',
  `showsnslevels` text,
  `postsnslevels` text,
  `showpartnerlevels` text,
  `postpartnerlevels` text,
  `notpartner` tinyint(3) DEFAULT '0',
  `notpartnerpost` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_board','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `cid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `title` varchar(50) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','logo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `logo` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','desc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `desc` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `displayorder` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `enabled` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','showgroups')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `showgroups` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','showlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `showlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postgroups')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postgroups` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','showagentlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `showagentlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postagentlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postagentlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postcredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','replycredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `replycredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','bestcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `bestcredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','bestboardcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `bestboardcredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','notagent')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `notagent` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','notagentpost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `notagentpost` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','topcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `topcredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','topboardcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `topboardcredit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `status` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','noimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `noimage` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','novoice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `novoice` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needfollow')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needfollow` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needpostfollow')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needpostfollow` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','share_title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `share_title` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','share_icon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `share_icon` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','share_desc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `share_desc` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','keyword')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `keyword` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `isrecommand` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','banner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `banner` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needcheck')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needcheck` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needcheckmanager')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needcheckmanager` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needcheckreply')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needcheckreply` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','needcheckreplymanager')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `needcheckreplymanager` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','showsnslevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `showsnslevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postsnslevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postsnslevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','showpartnerlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `showpartnerlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','postpartnerlevels')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `postpartnerlevels` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','notpartner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `notpartner` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','notpartnerpost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   `notpartnerpost` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_board_follow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `bid` (`bid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `bid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `openid` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   `ctime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_board_follow','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_board_follow')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `displayorder` tinyint(3) unsigned DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `enabled` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','isrecommand')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   `isrecommand` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_category','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_category')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_complain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `type` tinyint(3) NOT NULL,
  `postsid` int(11) NOT NULL DEFAULT '0',
  `defendant` varchar(255) NOT NULL DEFAULT '0',
  `complainant` varchar(255) NOT NULL DEFAULT '0',
  `complaint_type` int(10) NOT NULL DEFAULT '0',
  `complaint_text` text NOT NULL,
  `images` text NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `checkedtime` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(3) NOT NULL DEFAULT '0',
  `checked_note` varchar(255) NOT NULL,
  `deleted` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `type` (`type`),
  KEY `postsid` (`postsid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `type` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','postsid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `postsid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','defendant')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `defendant` varchar(255) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','complainant')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `complainant` varchar(255) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','complaint_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `complaint_type` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','complaint_text')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `complaint_text` text NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `images` text NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','createtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `createtime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','checkedtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `checkedtime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','checked')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `checked` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','checked_note')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `checked_note` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   `deleted` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complain','type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complain')." ADD   KEY `type` (`type`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_complaincate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `title` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `status` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   `displayorder` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_complaincate','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_complaincate')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `levelname` varchar(255) DEFAULT '',
  `credit` int(11) DEFAULT '0',
  `enabled` tinyint(3) DEFAULT '0',
  `post` int(11) DEFAULT '0',
  `color` varchar(255) DEFAULT '',
  `bg` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_level','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','levelname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `levelname` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `credit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `enabled` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','post')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `post` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','color')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `color` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','bg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   `bg` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_level','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_level')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_like` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `openid` varchar(255) DEFAULT '',
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_like','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `openid` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   `ctime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_like','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_like')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_manager` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `enabled` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `bid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `openid` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   `enabled` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_manager','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_manager')." ADD   KEY `bid` (`bid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `level` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  `sign` varchar(255) DEFAULT '',
  `isblack` tinyint(3) DEFAULT '0',
  `notupgrade` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_member','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `openid` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','level')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `level` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','createtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `createtime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `credit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','sign')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `sign` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','isblack')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `isblack` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','notupgrade')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   `notupgrade` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_member','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_member')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sns_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `boardid` int(11) DEFAULT '0',
  `postid` int(11) DEFAULT '0',
  `replypostid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `uid` int(10) NOT NULL,
  `avatar` varchar(255) DEFAULT '',
  `nickname` varchar(255) DEFAULT '',
  `title` varchar(50) DEFAULT '',
  `content` text,
  `images` text,
  `voice` varchar(255) DEFAULT NULL,
  `createtime` int(11) DEFAULT '0',
  `replytime` int(11) DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `islock` tinyint(1) DEFAULT '0',
  `istop` tinyint(1) DEFAULT '0',
  `isboardtop` tinyint(1) DEFAULT '0',
  `isbest` tinyint(1) DEFAULT '0',
  `isboardbest` tinyint(3) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `deletedtime` int(11) DEFAULT '0',
  `checked` tinyint(3) DEFAULT NULL,
  `checktime` int(11) DEFAULT '0',
  `isadmin` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `boardid` (`boardid`),
  KEY `postid` (`postid`),
  KEY `replypostid` (`replypostid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sns_post','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `weid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','boardid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `boardid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','postid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `postid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','replypostid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `replypostid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `openid` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','avatar')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `avatar` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `nickname` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `title` varchar(50) DEFAULT ''");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `content` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','voice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `voice` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','createtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `createtime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','replytime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `replytime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `credit` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','views')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `views` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','islock')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `islock` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','istop')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `istop` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','isboardtop')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `isboardtop` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','isbest')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `isbest` tinyint(1) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','isboardbest')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `isboardbest` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','deleted')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `deleted` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','deletedtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `deletedtime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','checked')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `checked` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','checktime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `checktime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','isadmin')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   `isadmin` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','boardid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `boardid` (`boardid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','postid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `postid` (`postid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_sns_post','replypostid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sns_post')." ADD   KEY `replypostid` (`replypostid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_suggest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `hid` int(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `images` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `star` tinyint(3) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `getopenid` varchar(60) DEFAULT NULL,
  `getuid` int(10) NOT NULL DEFAULT '0',
  `lasttime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `reporttime` int(11) NOT NULL DEFAULT '0',
  `reporttimes` smallint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cid` (`cid`),
  KEY `bid` (`bid`),
  KEY `tid` (`tid`),
  KEY `hid` (`hid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_suggest','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `tid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `hid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `address` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','images')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `images` text");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','star')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `star` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `comment` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','getopenid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `getopenid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','getuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `getuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','lasttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `lasttime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','reporttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `reporttime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','reporttimes')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   `reporttimes` smallint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `cid` (`cid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `bid` (`bid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `tid` (`tid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest','hid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest')." ADD   KEY `hid` (`hid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_suggest_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `suggestid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `updown` tinyint(1) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `suggestid` (`suggestid`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','suggestid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `suggestid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','updown')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `updown` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `comment` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','suggestid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   KEY `suggestid` (`suggestid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_comment')." ADD   KEY `uid` (`uid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_suggest_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `sid` int(10) NOT NULL,
  `openid` varchar(60) NOT NULL,
  `uid` int(10) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `image` text,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `sid` (`sid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `sid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `openid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `content` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','image')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `image` text");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_suggest_record','sid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_suggest_record')." ADD   KEY `sid` (`sid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sysagreement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `content1` text,
  `content2` text,
  `content3` text,
  `cuid` int(10) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','content1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `content1` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','content2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `content2` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','content3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `content3` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `cuid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysagreement','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysagreement')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_syslog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `do` varchar(60) DEFAULT NULL,
  `op` varchar(60) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `content` varchar(200) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `do` (`do`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_syslog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','do')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `do` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','op')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `op` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `content` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','ip')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `ip` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_syslog','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syslog')." ADD   KEY `pid` (`pid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_syspub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `copyright` text,
  `ptitle` varchar(100) DEFAULT NULL,
  `plogo` varchar(100) DEFAULT NULL,
  `pbackground` varchar(255) DEFAULT NULL,
  `pbgcolor` varchar(20) DEFAULT NULL,
  `isclose` tinyint(1) NOT NULL DEFAULT '0',
  `closereason` varchar(60) DEFAULT NULL,
  `closeurl` varchar(255) DEFAULT NULL,
  `wxtitle` varchar(100) DEFAULT NULL,
  `statistics` varchar(1000) DEFAULT NULL,
  `wxcopyright` text,
  `haina_appid` varchar(30) DEFAULT NULL,
  `haina_secret` varchar(100) DEFAULT NULL,
  `haina_agentid` varchar(30) DEFAULT NULL,
  `haina_token` varchar(60) DEFAULT NULL,
  `haina_encodingaeskey` varchar(100) DEFAULT NULL,
  `ishaina` tinyint(1) NOT NULL DEFAULT '0',
  `wecopyright` tinyint(1) NOT NULL DEFAULT '0',
  `isalilife` tinyint(1) NOT NULL DEFAULT '0',
  `alilife_appid` varchar(60) DEFAULT NULL,
  `alilife_rsa2` text,
  `board_password` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_syspub','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `title` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','logo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `logo` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','copyright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `copyright` text");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','ptitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `ptitle` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','plogo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `plogo` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','pbackground')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `pbackground` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','pbgcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `pbgcolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','isclose')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `isclose` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','closereason')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `closereason` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','closeurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `closeurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','wxtitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `wxtitle` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','statistics')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `statistics` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','wxcopyright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `wxcopyright` text");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','haina_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `haina_appid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','haina_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `haina_secret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','haina_agentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `haina_agentid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','haina_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `haina_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','haina_encodingaeskey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `haina_encodingaeskey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','ishaina')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `ishaina` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','wecopyright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `wecopyright` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','isalilife')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `isalilife` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','alilife_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `alilife_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','alilife_rsa2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `alilife_rsa2` text");}
if(!pdo_fieldexists('rhinfo_zyxq_syspub','board_password')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_syspub')." ADD   `board_password` varchar(60) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_sysset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `copyright` text,
  `telphone` varchar(20) DEFAULT NULL,
  `wxtitle` varchar(100) DEFAULT NULL,
  `wxcopyright` text,
  `ptitle` varchar(100) DEFAULT NULL,
  `plogo` varchar(100) DEFAULT NULL,
  `style` varchar(60) DEFAULT NULL,
  `isclose` tinyint(1) NOT NULL DEFAULT '0',
  `closereason` varchar(60) DEFAULT NULL,
  `closeurl` varchar(255) DEFAULT NULL,
  `adminstyle` varchar(60) DEFAULT 'web',
  `menucolor` varchar(10) DEFAULT NULL,
  `btncolor` varchar(10) DEFAULT NULL,
  `bgcolor` varchar(10) DEFAULT NULL,
  `servicedisplay` tinyint(1) NOT NULL,
  `statistics` varchar(1000) DEFAULT NULL,
  `topcolor` varchar(20) DEFAULT NULL,
  `textcolor` varchar(20) DEFAULT NULL,
  `feecolor` varchar(20) DEFAULT NULL,
  `tplid1` varchar(80) DEFAULT NULL,
  `tplid2` varchar(80) DEFAULT NULL,
  `tplid3` varchar(80) DEFAULT NULL,
  `tplid4` varchar(80) DEFAULT NULL,
  `tplid5` varchar(80) DEFAULT NULL,
  `tplid6` varchar(80) DEFAULT NULL,
  `tplid7` varchar(80) DEFAULT NULL,
  `tplid8` varchar(80) DEFAULT NULL,
  `tplid9` varchar(80) DEFAULT NULL,
  `tplid10` varchar(80) DEFAULT NULL,
  `tplid11` varchar(80) DEFAULT NULL,
  `tplid12` varchar(80) DEFAULT NULL,
  `bindverify` tinyint(1) NOT NULL DEFAULT '0',
  `shareverify` tinyint(1) NOT NULL DEFAULT '0',
  `smstype` tinyint(1) NOT NULL DEFAULT '1',
  `appkey` varchar(100) DEFAULT NULL,
  `app_key` varchar(100) DEFAULT NULL,
  `app_serve` varchar(100) DEFAULT NULL,
  `signname` varchar(30) DEFAULT NULL,
  `verifyid` varchar(60) DEFAULT NULL,
  `repairid` varchar(60) DEFAULT NULL,
  `suggestid` varchar(60) DEFAULT NULL,
  `feeid` varchar(60) DEFAULT NULL,
  `noticeid` varchar(60) DEFAULT NULL,
  `movecarid` varchar(60) DEFAULT NULL,
  `cost` int(6) NOT NULL DEFAULT '0',
  `minmoney` int(6) NOT NULL DEFAULT '0',
  `credit` int(6) NOT NULL DEFAULT '0',
  `credit2` int(3) NOT NULL DEFAULT '0',
  `doortype` tinyint(1) NOT NULL DEFAULT '1',
  `lockurl` varchar(60) NOT NULL,
  `apikey` varchar(100) NOT NULL,
  `apiid` varchar(100) NOT NULL,
  `servicemerchid` varchar(60) NOT NULL,
  `merchsecret` varchar(60) NOT NULL,
  `locationdoor` tinyint(1) NOT NULL DEFAULT '1',
  `regionrange` int(10) NOT NULL DEFAULT '0',
  `doorrange` int(10) NOT NULL DEFAULT '0',
  `isweaddress` tinyint(1) NOT NULL DEFAULT '0',
  `memberfield` tinyint(1) NOT NULL DEFAULT '0',
  `isrraddress` tinyint(1) NOT NULL DEFAULT '0',
  `menu1` varchar(30) DEFAULT NULL,
  `menu2` varchar(30) DEFAULT NULL,
  `menu3` varchar(30) DEFAULT NULL,
  `menu4` varchar(30) DEFAULT NULL,
  `menu5` varchar(30) DEFAULT NULL,
  `icon1` varchar(30) NOT NULL DEFAULT 'icon-home',
  `icon2` varchar(30) NOT NULL DEFAULT 'icon-mark1',
  `icon3` varchar(30) NOT NULL DEFAULT 'icon-like1',
  `icon4` varchar(30) NOT NULL DEFAULT 'icon-lights',
  `icon5` varchar(30) NOT NULL DEFAULT 'icon-person2',
  `menu1url` varchar(100) DEFAULT NULL,
  `menu2url` varchar(100) DEFAULT NULL,
  `menu3url` varchar(100) DEFAULT NULL,
  `menu4url` varchar(100) DEFAULT NULL,
  `menu5url` varchar(100) DEFAULT NULL,
  `stetext1` varchar(30) DEFAULT NULL,
  `stetext2` varchar(30) DEFAULT NULL,
  `stetext3` varchar(30) DEFAULT NULL,
  `stetext4` varchar(30) DEFAULT NULL,
  `followurl` varchar(100) DEFAULT NULL,
  `qrcode` varchar(100) DEFAULT NULL,
  `sharetitle` varchar(60) DEFAULT NULL,
  `shareicon` varchar(100) DEFAULT NULL,
  `sharedesc` varchar(200) DEFAULT NULL,
  `outtime` tinyint(4) NOT NULL DEFAULT '5',
  `ishome` tinyint(1) NOT NULL DEFAULT '0',
  `stewarddisplay` tinyint(1) NOT NULL DEFAULT '0',
  `isoneregion` tinyint(1) NOT NULL DEFAULT '0',
  `service` tinyint(1) NOT NULL DEFAULT '0',
  `imagesize` int(6) NOT NULL DEFAULT '0',
  `listtype` tinyint(1) NOT NULL DEFAULT '0',
  `app_code` varchar(100) DEFAULT NULL,
  `alisignname` varchar(30) DEFAULT NULL,
  `pagesize` smallint(6) NOT NULL DEFAULT '0',
  `membertext` varchar(30) DEFAULT NULL,
  `isshowfeebill` tinyint(1) NOT NULL DEFAULT '1',
  `isshowhouse` tinyint(1) NOT NULL DEFAULT '1',
  `isshowrepair` tinyint(1) NOT NULL DEFAULT '1',
  `isshowforum` tinyint(1) NOT NULL DEFAULT '1',
  `isshowtask` tinyint(1) NOT NULL DEFAULT '1',
  `isshowvisit` tinyint(1) NOT NULL DEFAULT '1',
  `isshowlike` tinyint(1) NOT NULL DEFAULT '1',
  `isshowdevice` tinyint(1) NOT NULL DEFAULT '1',
  `iswegroup` tinyint(1) NOT NULL DEFAULT '0',
  `binddomain` varchar(255) DEFAULT NULL,
  `pbackground` varchar(255) DEFAULT NULL,
  `pbgcolor` varchar(20) DEFAULT NULL,
  `bindlocation` tinyint(1) NOT NULL DEFAULT '1',
  `isplatformcredit` tinyint(1) NOT NULL DEFAULT '0',
  `paynopre` varchar(10) NOT NULL DEFAULT 'Property',
  `businesstplid` varchar(80) DEFAULT NULL,
  `redpackettplid` varchar(80) DEFAULT NULL,
  `apiclient_cert` varchar(100) DEFAULT NULL,
  `apiclient_key` varchar(100) DEFAULT NULL,
  `rootca` varchar(100) DEFAULT NULL,
  `siteurl` varchar(100) DEFAULT NULL,
  `alipay_appid` varchar(60) DEFAULT NULL,
  `alipay_rsa2` text,
  `limitqty` int(10) NOT NULL,
  `iswap` tinyint(1) NOT NULL DEFAULT '0',
  `wxappid` varchar(30) DEFAULT NULL,
  `wxappsecret` varchar(100) DEFAULT NULL,
  `wxmerchid` varchar(30) DEFAULT NULL,
  `wxmerchsecret` varchar(100) DEFAULT NULL,
  `access_key_id` varchar(60) DEFAULT NULL,
  `access_key_secret` varchar(60) DEFAULT NULL,
  `aliaccsignname` varchar(30) DEFAULT NULL,
  `isreg` tinyint(1) NOT NULL DEFAULT '0',
  `wxminiappid` varchar(30) DEFAULT NULL,
  `wxminiappsecret` varchar(100) DEFAULT NULL,
  `wxminimerchid` varchar(30) DEFAULT NULL,
  `wxminimerchsecret` varchar(100) DEFAULT NULL,
  `wxminiappurl` varchar(100) DEFAULT NULL,
  `wxminititle` varchar(30) DEFAULT NULL,
  `devpaytype` tinyint(1) NOT NULL DEFAULT '0',
  `mx_appid` varchar(60) DEFAULT NULL,
  `mx_appkey` varchar(60) DEFAULT NULL,
  `yk_appid` varchar(60) DEFAULT NULL,
  `yk_appkey` varchar(60) DEFAULT NULL,
  `smsprice` decimal(6,4) DEFAULT NULL,
  `isworkersound` tinyint(1) NOT NULL DEFAULT '0',
  `workermanurl` varchar(100) DEFAULT NULL,
  `isdisptel` tinyint(1) NOT NULL DEFAULT '0',
  `displayweather` tinyint(1) NOT NULL DEFAULT '0',
  `memberheaderstyle` tinyint(1) NOT NULL DEFAULT '0',
  `memberliststyle` tinyint(1) NOT NULL DEFAULT '0',
  `isdevreg` tinyint(1) NOT NULL DEFAULT '0',
  `isdidplayscan` tinyint(1) NOT NULL DEFAULT '0',
  `followtip` varchar(100) DEFAULT NULL,
  `wethercolor` varchar(10) DEFAULT NULL,
  `forumthumb` varchar(255) DEFAULT NULL,
  `forumicon` varchar(30) DEFAULT NULL,
  `forumright` smallint(6) NOT NULL,
  `forumbottom` smallint(6) NOT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `qashowtype` tinyint(1) NOT NULL DEFAULT '0',
  `isshowqa` tinyint(1) NOT NULL DEFAULT '0',
  `isalipay` tinyint(1) NOT NULL DEFAULT '0',
  `aliaccount` varchar(100) DEFAULT NULL,
  `alisecret` varchar(100) DEFAULT NULL,
  `alipartner` varchar(100) DEFAULT NULL,
  `iswxpay` tinyint(1) NOT NULL DEFAULT '0',
  `appid` varchar(30) DEFAULT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `merchid` varchar(30) DEFAULT NULL,
  `serviceappid` varchar(30) DEFAULT NULL,
  `serviceappsecret` varchar(100) DEFAULT NULL,
  `servicemerchsecret` varchar(100) DEFAULT NULL,
  `submerchid` varchar(30) DEFAULT NULL,
  `credit3` int(3) NOT NULL,
  `expressid` varchar(30) DEFAULT NULL,
  `nobindhome` tinyint(1) NOT NULL DEFAULT '0',
  `expresssendid` varchar(30) DEFAULT NULL,
  `isopenapi` tinyint(1) NOT NULL DEFAULT '0',
  `api_token` varchar(60) DEFAULT NULL,
  `isshowexpress` tinyint(1) NOT NULL DEFAULT '0',
  `expresstplid` varchar(80) DEFAULT NULL,
  `isdiscredit` tinyint(1) NOT NULL DEFAULT '1',
  `rsdmerchid` varchar(30) DEFAULT NULL,
  `rsdmerchsecret` varchar(100) DEFAULT NULL,
  `ymfmerchid` varchar(30) DEFAULT NULL,
  `ymfmerchsecret` varchar(100) DEFAULT NULL,
  `ymfurl` varchar(255) DEFAULT NULL,
  `starmerchid` varchar(20) DEFAULT NULL,
  `starkey` varchar(50) DEFAULT NULL,
  `starorg` varchar(20) DEFAULT NULL,
  `startrm` varchar(20) DEFAULT NULL,
  `managemenustyle` tinyint(1) NOT NULL DEFAULT '0',
  `isaddregion` tinyint(1) NOT NULL DEFAULT '0',
  `ds_appid` varchar(60) DEFAULT NULL,
  `ds_appkey` varchar(60) DEFAULT NULL,
  `bl_apikey` varchar(100) DEFAULT NULL,
  `bl_apiid` varchar(100) DEFAULT NULL,
  `mj_apikey` varchar(100) DEFAULT NULL,
  `mj_apiid` varchar(100) DEFAULT NULL,
  `board_password` varchar(60) DEFAULT NULL,
  `iswepay` tinyint(1) NOT NULL DEFAULT '0',
  `haina_appid` varchar(30) DEFAULT NULL,
  `haina_secret` varchar(100) DEFAULT NULL,
  `haina_agentid` varchar(30) DEFAULT NULL,
  `forcefollow` tinyint(1) NOT NULL DEFAULT '0',
  `ylb_token` varchar(20) DEFAULT NULL,
  `wxminibgimage` varchar(255) DEFAULT NULL,
  `isproperty` tinyint(1) NOT NULL DEFAULT '0',
  `isvisitor` tinyint(1) NOT NULL DEFAULT '0',
  `alipay_private` text,
  `alipay_life_appid` varchar(60) DEFAULT NULL,
  `alipay_life_key` text,
  `alipay_app_appid` varchar(60) DEFAULT NULL,
  `alipay_app_key` text,
  `alipay_app_title` varchar(60) DEFAULT NULL,
  `alipay_app_url` varchar(60) DEFAULT NULL,
  `alipay_type` tinyint(1) NOT NULL DEFAULT '0',
  `alipay_seller_id` varchar(60) DEFAULT NULL,
  `alipay_app_auth_token` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_sysset','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `weid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `title` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','logo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `logo` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','copyright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `copyright` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `telphone` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxtitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxtitle` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxcopyright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxcopyright` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ptitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ptitle` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','plogo')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `plogo` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','style')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `style` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isclose')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isclose` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','closereason')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `closereason` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','closeurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `closeurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','adminstyle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `adminstyle` varchar(60) DEFAULT 'web'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menucolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menucolor` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','btncolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `btncolor` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','bgcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `bgcolor` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','servicedisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `servicedisplay` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','statistics')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `statistics` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','topcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `topcolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','textcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `textcolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','feecolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `feecolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid1` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid2` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid3` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid4` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid5` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid6')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid6` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid7')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid7` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid8')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid8` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid9')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid9` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid10')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid10` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid11')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid11` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','tplid12')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `tplid12` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','bindverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `bindverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','shareverify')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `shareverify` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','smstype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `smstype` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','appkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `appkey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','app_key')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `app_key` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','app_serve')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `app_serve` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','signname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `signname` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','verifyid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `verifyid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','repairid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `repairid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','suggestid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `suggestid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','feeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `feeid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','noticeid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `noticeid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','movecarid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `movecarid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','cost')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `cost` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','minmoney')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `minmoney` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','credit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `credit` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','credit2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `credit2` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','doortype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `doortype` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','lockurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `lockurl` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','apikey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `apikey` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','apiid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `apiid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','servicemerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `servicemerchid` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','merchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `merchsecret` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','locationdoor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `locationdoor` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','regionrange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `regionrange` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','doorrange')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `doorrange` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isweaddress')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isweaddress` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','memberfield')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `memberfield` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isrraddress')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isrraddress` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu1` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu2` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu3` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu4` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu5` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','icon1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `icon1` varchar(30) NOT NULL DEFAULT 'icon-home'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','icon2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `icon2` varchar(30) NOT NULL DEFAULT 'icon-mark1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','icon3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `icon3` varchar(30) NOT NULL DEFAULT 'icon-like1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','icon4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `icon4` varchar(30) NOT NULL DEFAULT 'icon-lights'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','icon5')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `icon5` varchar(30) NOT NULL DEFAULT 'icon-person2'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu1url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu1url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu2url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu2url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu3url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu3url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu4url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu4url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','menu5url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `menu5url` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','stetext1')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `stetext1` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','stetext2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `stetext2` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','stetext3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `stetext3` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','stetext4')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `stetext4` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','followurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `followurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','qrcode')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `qrcode` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','sharetitle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `sharetitle` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','shareicon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `shareicon` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','sharedesc')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `sharedesc` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','outtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `outtime` tinyint(4) NOT NULL DEFAULT '5'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ishome')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ishome` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','stewarddisplay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `stewarddisplay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isoneregion')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isoneregion` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','service')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `service` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','imagesize')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `imagesize` int(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','listtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `listtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','app_code')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `app_code` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alisignname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alisignname` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','pagesize')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `pagesize` smallint(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','membertext')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `membertext` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowfeebill')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowfeebill` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowhouse')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowhouse` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowrepair')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowrepair` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowforum')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowforum` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowtask')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowtask` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowvisit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowvisit` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowlike')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowlike` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowdevice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowdevice` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','iswegroup')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `iswegroup` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','binddomain')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `binddomain` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','pbackground')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `pbackground` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','pbgcolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `pbgcolor` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','bindlocation')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `bindlocation` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isplatformcredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isplatformcredit` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','paynopre')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `paynopre` varchar(10) NOT NULL DEFAULT 'Property'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','businesstplid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `businesstplid` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','redpackettplid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `redpackettplid` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','apiclient_cert')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `apiclient_cert` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','apiclient_key')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `apiclient_key` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','rootca')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `rootca` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','siteurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `siteurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_rsa2')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_rsa2` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','limitqty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `limitqty` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','iswap')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `iswap` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxappsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxappsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxmerchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxmerchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxmerchsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','access_key_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `access_key_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','access_key_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `access_key_secret` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','aliaccsignname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `aliaccsignname` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isreg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isreg` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminiappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminiappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminiappsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminiappsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminimerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminimerchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminimerchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminimerchsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminiappurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminiappurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminititle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminititle` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','devpaytype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `devpaytype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','mx_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `mx_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','mx_appkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `mx_appkey` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','yk_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `yk_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','yk_appkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `yk_appkey` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','smsprice')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `smsprice` decimal(6,4) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isworkersound')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isworkersound` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','workermanurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `workermanurl` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isdisptel')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isdisptel` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','displayweather')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `displayweather` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','memberheaderstyle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `memberheaderstyle` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','memberliststyle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `memberliststyle` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isdevreg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isdevreg` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isdidplayscan')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isdidplayscan` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','followtip')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `followtip` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wethercolor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wethercolor` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','forumthumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `forumthumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','forumicon')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `forumicon` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','forumright')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `forumright` smallint(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','forumbottom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `forumbottom` smallint(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `openid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','qashowtype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `qashowtype` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowqa')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowqa` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isalipay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isalipay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','aliaccount')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `aliaccount` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alisecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alisecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipartner')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipartner` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','iswxpay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `iswxpay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `appid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `secret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','merchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `merchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','serviceappid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `serviceappid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','serviceappsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `serviceappsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','servicemerchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `servicemerchsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','submerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `submerchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','credit3')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `credit3` int(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','expressid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `expressid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','nobindhome')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `nobindhome` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','expresssendid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `expresssendid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isopenapi')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isopenapi` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','api_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `api_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isshowexpress')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isshowexpress` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','expresstplid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `expresstplid` varchar(80) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isdiscredit')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isdiscredit` tinyint(1) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','rsdmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `rsdmerchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','rsdmerchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `rsdmerchsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ymfmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ymfmerchid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ymfmerchsecret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ymfmerchsecret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ymfurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ymfurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','starmerchid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `starmerchid` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','starkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `starkey` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','starorg')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `starorg` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','startrm')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `startrm` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','managemenustyle')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `managemenustyle` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isaddregion')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isaddregion` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ds_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ds_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ds_appkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ds_appkey` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','bl_apikey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `bl_apikey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','bl_apiid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `bl_apiid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','mj_apikey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `mj_apikey` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','mj_apiid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `mj_apiid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','board_password')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `board_password` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','iswepay')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `iswepay` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','haina_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `haina_appid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','haina_secret')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `haina_secret` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','haina_agentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `haina_agentid` varchar(30) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','forcefollow')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `forcefollow` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','ylb_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `ylb_token` varchar(20) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','wxminibgimage')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `wxminibgimage` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isproperty')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isproperty` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','isvisitor')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `isvisitor` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_private')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_private` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_life_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_life_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_life_key')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_life_key` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_app_appid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_app_appid` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_app_key')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_app_key` text");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_app_title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_app_title` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_app_url')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_app_url` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_seller_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_seller_id` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','alipay_app_auth_token')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   `alipay_app_auth_token` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_sysset','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_sysset')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `realname` varchar(60) NOT NULL,
  `nickname` varchar(60) NOT NULL,
  `mobile` varchar(60) NOT NULL,
  `avatar` varchar(100) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `entrydate` int(11) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `workyears` tinyint(3) NOT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `ridstr` varchar(1000) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `isdefault` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` smallint(6) NOT NULL DEFAULT '0',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `cid` (`cid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_team','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_team','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `cid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','realname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `realname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `nickname` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','mobile')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `mobile` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','avatar')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `avatar` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','remark')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','entrydate')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `entrydate` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','content')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `content` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','workyears')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `workyears` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `openid` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','ridstr')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `ridstr` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `status` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','isdefault')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `isdefault` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_team','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `displayorder` smallint(6) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_team','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team','cid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team')." ADD   KEY `cid` (`cid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_team_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `teamid` int(10) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `updown` tinyint(1) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `teamid` (`teamid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_team_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','teamid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `teamid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `openid` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `uid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','updown')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `updown` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','nickname')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `nickname` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','headimgurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `headimgurl` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','comment')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `comment` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   KEY `rid` (`rid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_team_comment','teamid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_team_comment')." ADD   KEY `teamid` (`teamid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_telcate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `parentid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `parentid` (`parentid`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_telcate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `weid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','parentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `parentid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `title` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `displayorder` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','enabled')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   `enabled` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telcate','parentid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telcate')." ADD   KEY `parentid` (`parentid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_telphone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `cateid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `contact` varchar(60) NOT NULL,
  `starttime` varchar(10) DEFAULT NULL,
  `endtime` varchar(10) DEFAULT NULL,
  `telphone` varchar(60) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `lng` varchar(10) DEFAULT NULL,
  `lat` varchar(10) DEFAULT NULL,
  `displayorder` smallint(6) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `cuid` int(10) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `cateid` (`cateid`),
  KEY `status` (`status`),
  KEY `city` (`city`),
  KEY `district` (`district`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_telphone','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `weid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `cateid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','thumb')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','contact')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `contact` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','starttime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `starttime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','endtime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `endtime` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','telphone')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `telphone` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','province')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `province` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','city')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `city` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','district')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `district` varchar(60) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','address')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `address` varchar(255) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','lng')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `lng` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','lat')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `lat` varchar(10) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','displayorder')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `displayorder` smallint(6) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `status` tinyint(1) DEFAULT '1'");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `cuid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','cateid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   KEY `cateid` (`cateid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   KEY `status` (`status`)");}
if(!pdo_fieldexists('rhinfo_zyxq_telphone','city')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_telphone')." ADD   KEY `city` (`city`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_unit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `pid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `bid` int(10) NOT NULL,
  `title` varchar(60) NOT NULL,
  `floors` int(10) NOT NULL,
  `rooms` int(10) NOT NULL,
  `cuid` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`),
  KEY `bid` (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_unit','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `weid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `pid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `rid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','bid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `bid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','title')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','floors')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `floors` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','rooms')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `rooms` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','cuid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `cuid` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','ctime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   `ctime` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','weid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   KEY `weid` (`weid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   KEY `pid` (`pid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_unit','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_unit')." ADD   KEY `rid` (`rid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_version` (
  `version` varchar(60) NOT NULL,
  `secretkey` varchar(255) DEFAULT NULL,
  `siteurl` varchar(255) DEFAULT NULL,
  `siteip` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('rhinfo_zyxq_version','version')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_version')." ADD 
  `version` varchar(60) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_version','secretkey')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_version')." ADD   `secretkey` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_version','siteurl')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_version')." ADD   `siteurl` varchar(255) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_rhinfo_zyxq_wepay_log` (
  `plid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `acid` int(10) NOT NULL,
  `openid` varchar(40) NOT NULL,
  `uniontid` varchar(64) NOT NULL,
  `tid` varchar(128) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `module` varchar(50) NOT NULL,
  `tag` varchar(2000) NOT NULL,
  `is_usecard` tinyint(3) unsigned NOT NULL,
  `card_type` tinyint(3) unsigned NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `card_fee` decimal(10,2) unsigned NOT NULL,
  `encrypt_code` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `rid` int(10) NOT NULL DEFAULT '0',
  `feetype` int(3) NOT NULL DEFAULT '0',
  `paytime` int(11) NOT NULL DEFAULT '0',
  `cfrom` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plid`),
  KEY `uniacid` (`uniacid`),
  KEY `tid` (`tid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','plid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD 
  `plid` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `type` varchar(20) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','uniacid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','acid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `acid` int(10) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','openid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `openid` varchar(40) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','uniontid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `uniontid` varchar(64) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `tid` varchar(128) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','status')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `status` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','module')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `module` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','tag')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `tag` varchar(2000) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','is_usecard')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `is_usecard` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','card_type')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `card_type` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','card_id')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `card_id` varchar(50) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','card_fee')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `card_fee` decimal(10,2) unsigned NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','encrypt_code')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `encrypt_code` varchar(100) NOT NULL");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','uid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `uid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','pid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `pid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','rid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `rid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','feetype')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `feetype` int(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','paytime')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `paytime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','cfrom')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   `cfrom` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','plid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   PRIMARY KEY (`plid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','uniacid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('rhinfo_zyxq_wepay_log','tid')) {pdo_query("ALTER TABLE ".tablename('rhinfo_zyxq_wepay_log')." ADD   KEY `tid` (`tid`)");}
