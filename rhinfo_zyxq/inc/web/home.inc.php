<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_web();
$mywe = $this->mywe;
if ($_W['isfounder']) {
    pdo_update('modules_bindings', array('direct' => 1), array('direct' => 0, 'module' => 'rhinfo_zyxq', 'entry' => 'menu', 'do' => 'property'));
}
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$company = $this->syscfg;
$company['logo'] = empty($company['logo']) ? $this->syspub['logo'] : $company['logo'];
$company['title'] = empty($company['title']) ? $this->syspub['title'] : $company['title'];
$logo = $company['logo'];
if (!empty($logo)) {
    $logo = tomedia($logo);
}
$menus = $this->mymenus();
$isinit = false;
$banners_count = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid', array(':weid' => $mywe['weid']));
$navs_count = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_nav') . ' where weid=:weid', array(':weid' => $mywe['weid']));
if (empty($banners_count) && empty($navs_count)) {
    $isinit = true;
}
include $this->mywtpl('index');