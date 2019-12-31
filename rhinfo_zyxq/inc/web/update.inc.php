<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '系统管理';
$mydo = 'update';
load()->func('communication');
load()->func('db');
if ($operation == 'index') {
    $current = '系统维护';
    if (checksubmit()) {
        $siteurl = $_GPC['siteurl'];
        $siteip = gethostbyname($_SERVER['HTTP_HOST']);
        $secretkey = $_GPC['secretkey'];
        $version = $_GPC['version'];
        $version = '0.1';
        if ($siteurl !== $_W['siteroot']) {
            $this->mywebmsg('错误', '站点域名不符!', '', 'warning');
        }
        if ($siteip !== $_GPC['siteip']) {
            $this->mywebmsg('错误', '站点IP不符!', '', 'warning');
        }
        if ($result['status'] == 1) {
            $version = pdo_update('rhinfo_zyxq_version', array('secretkey' => $secretkey, 'status' => '1'));
        } else {
            $this->mywebmsg('错误', '授权不成功!', $this->createWebUrl($mydo, array('op' => 'index')), 'warning');
        }
    }
    include $this->mywtpl('index');
} elseif ($operation == 'navmenu') {
    $current = '菜单修复';
    $sql = "\r\n\t\t\tDELETE FROM " . tablename('rhinfo_zyxq_secprg') . ";\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('1', '1', '角色管理', null, 'role', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('2', '1', '用户管理', null, 'user', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('3', '1', '系统配置', null, 'sysset', 'index', null, '0', '1', '0', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('4', '1', '系统日志', null, 'syslog', 'list', null, '0', '1', '0', '0', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('5', '2', '物业公司', null, 'property', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '0', '0', '0', '支付参数', '管理权限', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('6', '2', '小区管理', null, 'region', 'list', null, '1', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '楼宇', '商铺', '车位', '支付导航广告', '基础设置','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('7', '2', '商圈管理', null, 'region', 'blist', null, '2', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '楼宇', '商铺', '车位', '支付导航广告', '基础设置','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('8', '2', '楼宇管理', null, 'building', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '批量添加', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('9', '2', '单元管理', null, 'unit', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '添加房屋', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('10', '2', '房屋管理', null, 'room', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('11', '2', '车位管理', null, 'parking', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '批量添加', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('12', '2', '商铺管理', null, 'shop', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '批量添加', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('13', '5', '物业通知', null, 'notify', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '1', '1', '0', '0', '0', '0', '发送', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('14', '5', '业主报修', null, 'repair', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '0', '0', '0', '0', '评价', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('15', '5', '投诉建议', null, 'suggest', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '0', '0', '0', '0', '评价', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('16', '5', '智能门禁', null, 'door', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('17', '5', '服务团队', null, 'team', 'list', null, '9', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '评价', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('18', '3', '收费项目', null, 'fee', 'item', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '1', '0', '1', '0', '0', '0', '0', '个性单价', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('19', '7', '车位信息', null, 'car', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '0', '0', '租赁', '租赁记录','月卡', null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('20', '3', '已缴账单', null, 'feecal', 'report', null, '9', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('21', '7', '车辆管理', null, 'car', 'carlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '月卡', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('22', '8', '默认广告', null, 'banner', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('23', '8', '默认导航', null, 'sysnav', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('24', '10', '保洁记录', null, 'environ', 'clist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('25', '10', '保洁安排', null, 'environ', 'cleaning', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('26', '10', '绿化信息', null, 'environ', 'green', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('27', '10', '绿化记录', null, 'environ', 'glist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('28', '9', '巡更分布', null, 'security', 'patrol', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('29', '9', '巡更路线', null, 'security', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('30', '6', '房屋住户', null, 'member', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '登记', '变更', '变更记录', '异常登记', '异常记录','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('31', '6', '房产绑定', null, 'member', 'weixin', null, '9', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('32', '11', '版块分类', null, 'forum', 'catelist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '广告', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('33', '11', '版块管理', null, 'forum', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('34', '3', '物业账单', null, 'fee', 'bill', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '1', '1', '0', '0', '0', '导入', '缴费通知', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('35', '3', '抄表账单', null, 'fee', 'three', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', '1', '0', '0','录入', '收费', '导入', null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('36', '3', '收银台', null, 'fee', 'list', null, '8', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '收费', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('37', '4', '物业缴费统计', null, 'report', 'paylist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('38', '4', '物业未缴统计', null, 'report', 'billlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('39', '4', '微信绑定统计', null, 'report', 'bindlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('40', '2', '广告管理', null, 'rbanner', 'list', null, '0', '0', '1', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('41', '2', '基础设置', null, 'category', 'list', null, '0', '0', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('42', '1', '系统维护', null, 'update', 'index', null, '98', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('43', '11', '话题管理', null, 'forum', 'postlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('44', '11', '回复管理', null, 'forum', 'replylist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('45', '11', '投诉管理', null, 'forum', 'complain', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('46', '11', '版主管理', null, 'forum', 'manager', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('47', '11', '社区会员', null, 'forum', 'member', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('48', '12', '资讯分类', null, 'article', 'category', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('49', '12', '资讯列表', null, 'article', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('50', '6', '商铺住户', null, 'member', 'slist', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '登记', '变更', '变更记录', '异常登记','异常记录' ,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('51', '2', '导航管理', null, 'regionav', 'list', null, '0', '0', '1', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('52', '3', '账单事务', null, 'feecal', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '1', '0', '1', '1', '0', '0', '0', '生成账单','上传支付宝', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('53', '6', '成员管理', null, 'member', 'userlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('54', '8', '指定广告', null, 'rbanner', 'rlist', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('55', '8', '指定导航', null, 'regionav', 'rlist', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('56', '8', '业主中心', null, 'pagediy', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('57', '7', '续租账单', null, 'car', 'carbill', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', '收款', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('58', '7', '停车场管理', null, 'parkinglot', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '0', '入口', '出口', '计费规则', '进出记录', null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('59', '7', '车位共享', null, 'car', 'carshare', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('60', '3', '收支登记', null, 'feecal', 'costlist', null, '10', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '1', '1', '1', '1', '0', '0', '收支项目', '装修保证金', '预收款项', null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('61', '5', '智能梯控', null, 'elevator', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\t\t\t\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('62', '4', '业主报修分析', null, 'report', 'repairlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('63', '4', '投诉建议分析', null, 'report', 'suggestlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('64', '7', '挪车记录', null, 'car', 'movecar', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('65', '9', '巡检类别', null, 'devpatrol', 'category', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '巡检标准', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('66', '9', '巡检项目', null, 'devpatrol', 'device', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '0', '0', '0', '维保厂商', '巡检记录', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('67', '9', '巡检计划', null, 'devpatrol', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('68', '14', '业委会申请', null, 'proprietor', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0',null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('69', '14', '业委会成员', null, 'proprietor', 'mlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '成员', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('70', '15', '积分策略', null, 'platform', 'list', null, '0', '1', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '1', '1', '0', '0', '0', '发行积分', '销售积分', null, null, null,'1');\t\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('71', '15', '红包策略', null, 'redpacket', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('72', '15', '任务策略', null, 'task', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('73', '15', '智能充电站', null, 'charging', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '设定价格', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('74', '15', '自助智能设备', null, 'selfwashcar', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '设备分类', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('75', '15', '快递驿站', null, 'express', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '快件管理', '快递公司', '快递柜', '支付参数', '操作人员','1');\t\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('76', '2', '园区管理', null, 'region', 'glist', null, '3', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '楼宇', '商铺', '车位', '支付导航广告', '基础设置','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('77', '2', '市场管理', null, 'region', 'mlist', null, '4', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '楼宇', '商铺', '车位', '支付导航广告', '基础设置','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('78', '2', '公寓管理', null, 'region', 'alist', null, '5', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '1', '1', '1', '1', '楼宇', '商铺', '车位', '支付导航广告', '基础设置','1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('79', '1', '系统帮助', null, 'help', 'list', null, '99', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\t\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('80', '13', '商家分类', null, 'business', 'catelist', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('81', '13', '商家管理', null, 'business', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '支付参数', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('82', '13', '商家活动', null, 'business', 'actlist',null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\t\t\t\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('83', '16', '承租人', null, 'lease', 'plist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('84', '16', '租赁区域', null, 'lease', 'location', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('85', '16', '租赁合同', null, 'lease', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', 0, '0', '0', '0', '收费项目', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('86', '16', '租赁账单', null, 'lease', 'bill', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '1', '0', '0', '1', '1', '0', '0', '0', '收款', '催费', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('87', '8', '常见问题', null, 'question', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('88', '8', '便民电话', null, 'telphone', 'list', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('89', '5', '物品登记', null, 'progoods', 'list', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '物品分类', null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('101', '7', '月卡记录', null, 'car', 'monthcarlog', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('102', '7', '停车缴费', null, 'car', 'parkpaylog', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('103', '7', '续租记录', null, 'car', 'carbillpay', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('104', '16', '缴费记录', null, 'leasea', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('105', '4', '智能充电统计', null, 'report', 'charging', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('106', '4', '自助设备统计', null, 'report', 'selfdevice', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('107', '4', '停车缴费统计', null, 'report', 'carpay', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('108', '5', '内部工单', null, 'repairp', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('109', '4', '内部工单分析', null, 'report', 'repairplist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('110', '4', '收支统计分析', null, 'report', 'costlist', null, '0', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('111', '5', '临时访客', null, 'visit', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('112', '3', '对账单', null, 'feecalb', 'paybill', null, '9', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '1', '1', '0', '0', '0', '发票登记', '打小票', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('113', '12', '活动管理', null, 'activity', 'list', null, '0', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '1', '1', '0', '0', '0', '参数设置', '查看数据', null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('114', '6', '会员管理', null, 'member', 'fans', null, '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('115', '1', '支付日志', null, 'paylog', 'list', null, '0', '1', '0', '0', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t\tINSERT INTO " . tablename('rhinfo_zyxq_secprg') . " VALUES ('116', '3', '账单分析', null, 'feecalc', 'feebill', null, '9', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', null, null, null, null, null,'1');\r\n\t\t";
    pdo_query($sql);
    echo 'ok';
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
    exit(0);
} elseif ($operation == 'repair') {
    $current = '数据库修复';
    if (file_exists(IA_ROOT . '/addons/rhinfo_zyxq/upgrade.php')) {
        require_once IA_ROOT . '/addons/rhinfo_zyxq/upgrade.php';
    }
    $sql = 'select version from ' . tablename('modules') . 'where name=\'rhinfo_zyxq\' limit 1';
    $version = pdo_fetchcolumn($sql);
    pdo_update('rhinfo_zyxq_version', array('version' => $version));
    echo 'ok';
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
    exit(0);
} elseif ($operation == 'default') {
    $current = '系统恢复默认';
    if (pdo_tableexists('rhinfo_zyxq_parkinglog')) {
        pdo_delete('rhinfo_zyxq_parkinglog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_member_sub')) {
        pdo_delete('rhinfo_zyxq_member_sub', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_member')) {
        pdo_delete('rhinfo_zyxq_member', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_category')) {
        pdo_delete('rhinfo_zyxq_category', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_notice')) {
        pdo_delete('rhinfo_zyxq_notice', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_notice_log')) {
        pdo_delete('rhinfo_zyxq_notice_log', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_repair')) {
        pdo_delete('rhinfo_zyxq_repair', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_repair_record')) {
        pdo_delete('rhinfo_zyxq_repair_record', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_suggest')) {
        pdo_delete('rhinfo_zyxq_suggest', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_suggest_record')) {
        pdo_delete('rhinfo_zyxq_suggest_record', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_team')) {
        pdo_delete('rhinfo_zyxq_team', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_team_comment')) {
        pdo_delete('rhinfo_zyxq_team_comment', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_door')) {
        pdo_delete('rhinfo_zyxq_door', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_doorlog')) {
        pdo_delete('rhinfo_zyxq_doorlog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_door_visit')) {
        pdo_delete('rhinfo_zyxq_door_visit', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_room_chglog')) {
        pdo_delete('rhinfo_zyxq_room_chglog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_car')) {
        pdo_delete('rhinfo_zyxq_car', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_feebill')) {
        pdo_delete('rhinfo_zyxq_feebill', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_feebillitem')) {
        pdo_delete('rhinfo_zyxq_feebillitem', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_feeitem')) {
        pdo_delete('rhinfo_zyxq_feeitem', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_region_comment')) {
        pdo_delete('rhinfo_zyxq_region_comment', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_region_follow')) {
        pdo_delete('rhinfo_zyxq_region_follow', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_syslog')) {
        pdo_delete('rhinfo_zyxq_syslog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_secgroup')) {
        pdo_delete('rhinfo_zyxq_secgroup', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_secgsys')) {
        pdo_delete('rhinfo_zyxq_secgsys', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_secgprg')) {
        pdo_delete('rhinfo_zyxq_secgprg', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_secusys')) {
        pdo_delete('rhinfo_zyxq_secusys', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_secuprg')) {
        pdo_delete('rhinfo_zyxq_secuprg', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_room')) {
        pdo_delete('rhinfo_zyxq_room', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_unit')) {
        pdo_delete('rhinfo_zyxq_unit', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_building')) {
        pdo_delete('rhinfo_zyxq_building', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_garage')) {
        pdo_delete('rhinfo_zyxq_garage', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parking')) {
        pdo_delete('rhinfo_zyxq_parking', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_shop')) {
        pdo_delete('rhinfo_zyxq_shop', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_location')) {
        pdo_delete('rhinfo_zyxq_location', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_pagediy')) {
        pdo_delete('rhinfo_zyxq_pagediy', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_pagediy_cate')) {
        pdo_delete('rhinfo_zyxq_pagediy_cate', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingrule')) {
        pdo_delete('rhinfo_zyxq_parkingrule', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingiolog')) {
        pdo_delete('rhinfo_zyxq_parkingiolog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingio')) {
        pdo_delete('rhinfo_zyxq_parkingio', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkinglot')) {
        pdo_delete('rhinfo_zyxq_parkinglot', array('weid' => $mywe['weid']));
    }
    echo 'ok';
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
    exit(0);
} elseif ($operation == 'init') {
    $current = '系统初始化';
    if (pdo_tableexists('rhinfo_zyxq_parkinglog')) {
        pdo_delete('rhinfo_zyxq_parkinglog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_notice')) {
        pdo_delete('rhinfo_zyxq_notice', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_notice_log')) {
        pdo_delete('rhinfo_zyxq_notice_log', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_repair')) {
        pdo_delete('rhinfo_zyxq_repair', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_repair_record')) {
        pdo_delete('rhinfo_zyxq_repair_record', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_suggest')) {
        pdo_delete('rhinfo_zyxq_suggest', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_suggest_record')) {
        pdo_delete('rhinfo_zyxq_suggest_record', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_team_comment')) {
        pdo_delete('rhinfo_zyxq_team_comment', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_doorlog')) {
        pdo_delete('rhinfo_zyxq_doorlog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_door_visit')) {
        pdo_delete('rhinfo_zyxq_door_visit', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_room_chglog')) {
        pdo_delete('rhinfo_zyxq_room_chglog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_feebill')) {
        pdo_delete('rhinfo_zyxq_feebill', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_feebillitem')) {
        pdo_delete('rhinfo_zyxq_feebillitem', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_region_comment')) {
        pdo_delete('rhinfo_zyxq_region_comment', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_region_follow')) {
        pdo_delete('rhinfo_zyxq_region_follow', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_syslog')) {
        pdo_delete('rhinfo_zyxq_syslog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_pagediy')) {
        pdo_delete('rhinfo_zyxq_pagediy', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_pagediy_cate')) {
        pdo_delete('rhinfo_zyxq_pagediy_cate', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingrule')) {
        pdo_delete('rhinfo_zyxq_parkingrule', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingiolog')) {
        pdo_delete('rhinfo_zyxq_parkingiolog', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkingio')) {
        pdo_delete('rhinfo_zyxq_parkingio', array('weid' => $mywe['weid']));
    }
    if (pdo_tableexists('rhinfo_zyxq_parkinglot')) {
        pdo_delete('rhinfo_zyxq_parkinglot', array('weid' => $mywe['weid']));
    }
    echo 'ok';
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
    exit(0);
} elseif ($operation == 'clear') {
    $current = '数据清理';
    if ($_W['isajax']) {
        $delcates = explode(',', $_GPC['delcates']);
        $i = 0;
        $condition = ' weid=:weid and pid=:pid and rid=:rid';
        $params = array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']);
        $condition1 = ' weid=:weid and pid=:pid and rid=:rid';
        $params1 = array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']);
        if ($_GPC['bid']) {
            $condition .= ' and bid=:bid';
            $params[':bid'] = $_GPC['bid'];
        }
        $k = 0;
        while (!($k >= count($delcates))) {
            if ($delcates[$k] == 'notice') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where ' . $condition;
                $notices = pdo_fetchall($sql, $params);
                $m = 0;
                while (!($m >= count($notices))) {
                    pdo_delete('rhinfo_zyxq_notice_log', array('weid' => $mywe['weid'], 'nid' => $notices[$m]['id']));
                    pdo_delete('rhinfo_zyxq_notice', array('weid' => $mywe['weid'], 'id' => $notices[$m]['id']));
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'repair') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition1;
                $repairs = pdo_fetchall($sql, $params1);
                $m = 0;
                while (!($m >= count($repairs))) {
                    pdo_delete('rhinfo_zyxq_repair_record', array('weid' => $mywe['weid'], 'rid' => $repairs[$m]['id']));
                    pdo_delete('rhinfo_zyxq_repair', array('weid' => $mywe['weid'], 'id' => $repairs[$m]['id']));
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'suggest') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition1;
                $suggests = pdo_fetchall($sql, $params1);
                $m = 0;
                while (!($m >= count($suggests))) {
                    pdo_delete('rhinfo_zyxq_suggest_record', array('weid' => $mywe['weid'], 'sid' => $suggests[$m]['id']));
                    pdo_delete('rhinfo_zyxq_suggest', array('weid' => $mywe['weid'], 'id' => $suggests[$m]['id']));
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'team') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where ' . $condition1;
                $teams = pdo_fetchall($sql, $params1);
                $m = 0;
                while (!($m >= count($teams))) {
                    pdo_delete('rhinfo_zyxq_team_comment', array('weid' => $mywe['weid'], 'teamid' => $teams[$m]['id']));
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'door') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where ' . $condition;
                $doors = pdo_fetchall($sql, $params);
                $m = 0;
                while (!($m >= count($doors))) {
                    pdo_delete('rhinfo_zyxq_door_visit', array('weid' => $mywe['weid'], 'doorid' => $doors[$m]['id']));
                    pdo_delete('rhinfo_zyxq_doorlog', array('weid' => $mywe['weid'], 'did' => $doors[$m]['id']));
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'fee') {
                if ($_GPC['bid']) {
                    pdo_delete('rhinfo_zyxq_feebillitem', array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid']));
                    pdo_delete('rhinfo_zyxq_feebill', array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid']));
                } else {
                    pdo_delete('rhinfo_zyxq_feebillitem', array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid']));
                    pdo_delete('rhinfo_zyxq_feebill', array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid']));
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'lifepay_room') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
                $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
                $set = array();
                $set['app_id'] = $this->syscfg['alipay_appid'];
                $set['prikey'] = $this->syscfg['alipay_rsa2'];
                $set['app_auth_token'] = $region['lifepay_token'];
                if ($_GPC['bid']) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid=:pid and  rid = :rid and bid=:bid ';
                    $rooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
                } else {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid=:pid and  rid = :rid';
                    $rooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
                }
                $m = 0;
                while (!($m >= count($rooms))) {
                    if (!empty($rooms[$m]['lifepay_hid'])) {
                        $set['method'] = 'roominfo.delete';
                        $params = "{\r\n\t\t\t\t\t\t\t\t\"batch_id\":\"" . date('YmdHis') . random(8, 1) . "\",\r\n\t\t\t\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t\t\t\t\"out_room_id_set\":[\"" . $rooms[$m]['lifepay_hid'] . "\"]\r\n\t\t\t\t\t\t\t\t  }";
                        $res = my_alipay_life($set, $params);
                        if (is_error($res)) {
                            echo $res['message'];
                            exit(0);
                        } else {
                            $res = json_decode($res, 1);
                            $res = $res['alipay_eco_cplife_roominfo_delete_response'];
                            if ($res['code'] !== '10000') {
                                if (!empty($res['sub_code'])) {
                                    echo $res['sub_msg'] . $res['sub_code'];
                                } else {
                                    echo $res['msg'] . $res['code'];
                                }
                                exit(0);
                            }
                            pdo_update('rhinfo_zyxq_room', array('lifepay_hid' => ''), array('weid' => $mywe['weid'], 'id' => $rooms[$m]['id']));
                        }
                    }
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'lifepay_bill') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
                $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
                $set = array();
                $set['app_id'] = $this->syscfg['alipay_appid'];
                $set['prikey'] = $this->syscfg['alipay_rsa2'];
                $set['app_auth_token'] = $region['lifepay_token'];
                if ($_GPC['bid']) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid=:pid and  rid = :rid and bid=:bid and category=1 and isalipay=1 ';
                    $bills = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
                } else {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid=:pid and  rid = :rid and category=1 and isalipay=1';
                    $bills = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
                }
                $m = 0;
                while (!($m >= count($bills))) {
                    $set['method'] = 'bill.delete';
                    $params = "{\r\n\t\t\t\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t\t\t\t\"bill_entry_id_list\":[\"" . $bills[$m]['id'] . "\"]\r\n\t\t\t\t\t\t\t\t  }";
                    $res = my_alipay_life($set, $params);
                    if (is_error($res)) {
                        echo $res['message'];
                        exit(0);
                    } else {
                        $res = json_decode($res, 1);
                        $res = $res['alipay_eco_cplife_bill_delete_response'];
                        if ($res['code'] !== '10000') {
                            if (!empty($res['sub_code'])) {
                                echo $res['sub_msg'] . $res['sub_code'];
                            } else {
                                echo $res['msg'] . $res['code'];
                            }
                            exit(0);
                        }
                        pdo_update('rhinfo_zyxq_feebill', array('isalipay' => 1), array('weid' => $mywe['weid'], 'id' => $bills[$m]['id']));
                    }
                    ($m += 1) + -1;
                }
                ($i += 1) + -1;
            } elseif ($delcates[$k] == 'forum') {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_board') . ' where ' . $condition;
                $boards = pdo_fetchall($sql, $params);
                $m = 0;
                while (!($m >= count($boards))) {
                    pdo_delete('rhinfo_zyxq_sns_board_follow', array('weid' => $mywe['weid'], 'bid' => $boards[$m]['id']));
                    pdo_delete('rhinfo_zyxq_sns_post', array('weid' => $mywe['weid'], 'boardid' => $boards[$m]['id']));
                    pdo_delete('rhinfo_zyxq_sns_board', array('weid' => $mywe['weid'], 'id' => $boards[$m]['id']));
                    ($m += 1) + -1;
                }
                pdo_delete('rhinfo_zyxq_sns_complain', array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid']));
                ($i += 1) + -1;
            }
            ($k += 1) + -1;
        }
        if ($i > 0) {
            echo 'ok';
        } else {
            echo '删除失败';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('clear');
} elseif ($operation == 'modify') {
    $current = '主体变更';
    if ($_W['isajax']) {
        if (empty($_GPC['pid']) || empty($_GPC['npid'])) {
            exit('物业不能为空');
        }
        if (empty($_GPC['rid'])) {
            exit('主体不能为空');
        }
        pdo_update('rhinfo_zyxq_category', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_building', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_location', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_unit', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_room', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_shop', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_parking', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_garage', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_parkinglog', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_member', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_member_sub', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_notice', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_repair', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_repairp', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_suggest', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_team', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_team_comment', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_door', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_door_visit', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_printer', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_car', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_carmove', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_carbill', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_feebill_create', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_feebillitem', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_feeitem', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_feeitem_building', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_regionmenu', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_regionav', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_rbanner', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_banner_statistics', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_shop_abnlog', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_shop_chglog', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_roomprice', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_room_abnlog', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_room_chglog', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_room_tag', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_parkinglot', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_parkinglock', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zycj_business', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zycj_business_cate', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_patrolpos', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_patrolline', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_costitem', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_costdetail', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zycj_task', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_elevator', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zycj_charging', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zycj_selfwashcar', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_devpatrol_task', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_devpatrol_category', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_devpatrol_device', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_lessee', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_lease', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_leaselocation', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_leasebill', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_leasebillitem', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_progoods', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_progoods_cate', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_activity', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        pdo_update('rhinfo_zyxq_secuser', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
        $res = pdo_update('rhinfo_zyxq_region', array('pid' => $_GPC['npid']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
        if ($res) {
            echo 'ok';
        } else {
            echo '变更异常';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('modify');
}
function table_schema($db, $tablename = '')
{
    $result = $db->fetch('SHOW TABLE STATUS LIKE \'' . trim($db->tablename($tablename), '`') . '\'');
    if (empty($result)) {
        return array();
    }
    $ret['tablename'] = $result['Name'];
    $ret['charset'] = $result['Collation'];
    $ret['engine'] = $result['Engine'];
    $ret['increment'] = $result['Auto_increment'];
    $result = $db->fetchall('SHOW FULL COLUMNS FROM ' . $db->tablename($tablename));
    $k = 0;
    while (true) {
        if ($k >= count($result)) {
            $result = $db->fetchall('SHOW INDEX FROM ' . $db->tablename($tablename));
            $k = 0;
            while (true) {
                if ($k >= count($result)) {
                    return $ret;
                }
                $ret['indexes'][$result[$k]['Key_name']]['name'] = $result[$k]['Key_name'];
                $ret['indexes'][$result[$k]['Key_name']]['type'] = $result[$k]['Key_name'] == 'PRIMARY' ? 'primary' : ($result[$k]['Non_unique'] == 0 ? 'unique' : 'index');
                $ret['indexes'][$result[$k]['Key_name']]['fields'][] = $result[$k]['Column_name'];
                ($k += 1) + -1;
            }
        }
        $temp = array();
        $type = explode(' ', $result[$k]['Type'], 2);
        $temp['name'] = $result[$k]['Field'];
        $pieces = explode('(', $type[0], 2);
        $temp['type'] = $pieces[0];
        $temp['length'] = rtrim($pieces[1], ')');
        $temp['null'] = $result[$k]['Null'] != 'NO';
        $temp['signed'] = empty($type[1]);
        $temp['increment'] = $result[$k]['Extra'] == 'auto_increment';
        $temp['default'] = $result[$k]['Default'];
        $ret['fields'][$result[$k]['Field']] = $temp;
        ($k += 1) + -1;
    }
}