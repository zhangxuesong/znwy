<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_web();
$sql = "CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_business') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) NOT NULL DEFAULT '0',  `title` varchar(60) NOT NULL ,  `contact` varchar(60) NOT NULL,  `starttime` varchar(10),  `endtime` varchar(10),  `openid` varchar(60),  `mobile` varchar(60) NOT NULL,    `telphone` varchar(60) DEFAULT NULL,  `province` varchar(60) DEFAULT NULL ,  `city` varchar(60) DEFAULT NULL ,  `district` varchar(60) DEFAULT NULL ,  `address` varchar(255) NOT NULL,  `lng` varchar(10) DEFAULT NULL,  `lat` varchar(10) DEFAULT NULL,  `url` varchar(255) DEFAULT NULL,  `thumb` varchar(255) DEFAULT NULL,  `banner` text,  `content` text,  `regions` text,  `onhand` int(10) NOT NULL,  `inqty` int(10) NOT NULL,  `outqty` int(10) NOT NULL,  `intime` int(11) NOT NULL,  `outtime` int(11) NOT NULL,  `ispay` tinyint(1) NOT NULL DEFAULT '0',  `paytype` tinyint(1) NOT NULL DEFAULT '0',  `submerchid` varchar(100) DEFAULT NULL,  `bankmerchid` varchar(100) DEFAULT NULL,  `ymfurl` varchar(100) DEFAULT NULL,  `ymfkey` varchar(50) DEFAULT NULL,  `rsdbankmerchid` varchar(50) DEFAULT NULL,  `bankkey` varchar(50) DEFAULT NULL,  `paysuccessurl` varchar(100) DEFAULT NULL,  `cost` int(6) NOT NULL DEFAULT '0',  `credit` int(6) NOT NULL DEFAULT '0',  `views` int(11) DEFAULT '0',  `follows` int(11) DEFAULT '0',  `comment` int(10) NOT NULL DEFAULT '0',  `displayorder` smallint(6) NOT NULL,  `status` tinyint(1) DEFAULT '1',    `isrecommand` tinyint(1) DEFAULT '0',  `isstore` tinyint(1) DEFAULT '0',  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_store') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) NOT NULL DEFAULT '0',  `bid` int(10) NOT NULL DEFAULT '0' ,  `storeno` varchar(20) NOT NULL ,  `title` varchar(60) NOT NULL ,  `contact` varchar(60) NOT NULL,  `starttime` varchar(10),  `endtime` varchar(10),  `openid` varchar(60),  `mobile` varchar(60) NOT NULL,    `telphone` varchar(60) DEFAULT NULL,  `province` varchar(60) DEFAULT NULL ,  `city` varchar(60) DEFAULT NULL ,  `district` varchar(60) DEFAULT NULL ,  `address` varchar(255) NOT NULL,  `lng` varchar(10) DEFAULT NULL,  `lat` varchar(10) DEFAULT NULL,  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_service') . " (  `id` int(11) NOT NULL AUTO_INCREMENT,  `weid` int(11) NOT NULL DEFAULT '0',  `parentid` int(10) NOT NULL DEFAULT '0',  `title` varchar(50) DEFAULT NULL,  `thumb` varchar(255) DEFAULT NULL,  `displayorder` tinyint(3) DEFAULT '0',  `formid` int(10) NOT NULL DEFAULT '0',  `enabled` tinyint(1) DEFAULT '1',    `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `parentid` (`parentid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_serviceitem') . " (  `id` int(11) NOT NULL AUTO_INCREMENT,  `weid` int(11) NOT NULL DEFAULT '0',  `sid` int(10) NOT NULL DEFAULT '0',  `title` varchar(50) DEFAULT NULL,  `displayorder` tinyint(3) DEFAULT '0',  `enabled` tinyint(1) DEFAULT '1',    `isrecommand` tinyint(3) DEFAULT '0',  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `sid` (`sid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_creditlog') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) NOT NULL DEFAULT '0',  `bid` int(10) NOT NULL DEFAULT '0',  `io` tinyint(1) NOT NULL,  `credit` int(10) NOT NULL DEFAULT '0',  `fromopenid` varchar(60),  `toopenid` varchar(60),  `storeno` varchar(20),  `title` varchar(255),  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;\tCREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_follow') . " (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(11) NOT NULL DEFAULT '0',  `bid` int(10) NOT NULL DEFAULT '0' ,  `openid` varchar(255),  `nickname` varchar(255) ,  `headimgurl` varchar(255),  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`),  KEY `openid` (`openid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_comment') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) unsigned NOT NULL ,  `openid` varchar(50) ,  `bid` int(10) NOT NULL DEFAULT '0' ,  `sid` int(11) NOT NULL DEFAULT '0',  `stars` int(10) NOT NULL ,  `nickname` varchar(255) ,  `headimgurl` varchar(255),  `comment` varchar(255) ,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`),  KEY `openid` (`openid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_activity') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) unsigned NOT NULL ,  `bid` int(10) NOT NULL DEFAULT '0' ,  `title` varchar(60),  `starttime` int(11) NOT NULL ,  `endtime` int(11) NOT NULL ,  `strategyid` int(10) NOT NULL DEFAULT '0',  `style` tinyint(1) NOT NULL DEFAULT '1',  `content` text,  `poster` varchar(255),  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_form') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) unsigned NOT NULL ,  `title` varchar(30),  `remark` varchar(100),  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_fields') . " (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) unsigned NOT NULL ,  `formid` int(10) NOT NULL DEFAULT '0',  `title` varchar(30) ,  `ftype` varchar(20) ,  `value` varchar(255) ,  `essential` tinyint(1),  `description` varchar(255) ,  `displayorder` int(10),  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `formid` (`formid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_person') . " (  `id` int(11) NOT NULL AUTO_INCREMENT,  `weid` int(11) NOT NULL DEFAULT '0',  `bid` int(10) NOT NULL DEFAULT '0' ,  `parentid` int(10) NOT NULL DEFAULT '0',  `title` varchar(50) DEFAULT NULL,  `thumb` varchar(255) DEFAULT NULL,  `displayorder` tinyint(3) DEFAULT '0',  `formid` int(10) NOT NULL DEFAULT '0',  `enabled` tinyint(1) DEFAULT '1',    `isrecommand` tinyint(3) DEFAULT '0',  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `bid` (`bid`),  KEY `parentid` (`parentid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;CREATE TABLE IF NOT EXISTS " . tablename('rhinfo_housekeeping_reservice') . " (  `id` int(11) NOT NULL AUTO_INCREMENT,  `weid` int(11) NOT NULL DEFAULT '0',  `sid` int(10) NOT NULL DEFAULT '0' ,  `parentid` int(10) NOT NULL DEFAULT '0',  `title` varchar(50) ,  `thumb` varchar(255),  `displayorder` tinyint(3) DEFAULT '0',  `formid` int(10) NOT NULL DEFAULT '0',  `enabled` tinyint(1) DEFAULT '1',    `isrecommand` tinyint(3) DEFAULT '0',  `cuid` int(10) NOT NULL,  `ctime` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `weid` (`weid`),  KEY `sid` (`sid`),  KEY `parentid` (`parentid`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;";
if ($_GPC['pwd'] == 'rhinfo_zyxq_ok') {
    pdo_query($sql);
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=90';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'90\', \'20\', \'基本设置\', null, \'housekeeping\', \'index\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=91';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'91\', \'20\', \'服务类别\', null, \'housekeeping\', \'catelist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=92';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'92\', \'20\', \'服务模型\', null, \'housekeeping\', \'servicelist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=93';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'93\', \'20\', \'服务公司\', null, \'housekeeping\', \'complist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'支付参数\', null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=94';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'94\', \'20\', \'服务人员\', null, \'housekeeping\', \'personlist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=95';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'95\', \'20\', \'订单管理\', null, \'housekeeping\', \'orderlist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=96';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'96\', \'20\', \'评论管理\', null, \'housekeeping\', \'commentlist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=97';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'97\', \'20\', \'佣金管理\', null, \'housekeeping\', \'orderlist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_secprg') . ' where id=98';
    $total = pdo_fetchcolumn($sql);
    if ($total == 0) {
        $sql = 'INSERT INTO ' . tablename('rhinfo_zyxq_secprg') . ' VALUES (\'98\', \'20\', \'提现管理\', null, \'housekeeping\', \'orderlist\', null, \'0\', \'1\', \'1\', \'1\', \'1\', \'1\', \'0\', \'1\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', null, null, null, null, null);';
        pdo_query($sql);
    }
    echo 'ok';
} else {
    echo '密码不正确！';
}
exit(0);