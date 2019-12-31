<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'invoice';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'feecalc';
$tablename = 'rhinfo_zyxq_feebill';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '费用管理';
$calmethod = $this->calmethod;
return $operation == 'invoice';