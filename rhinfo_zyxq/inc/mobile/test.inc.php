<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'test';
$mydo = 'test';
$myurl = $this->createMobileurl($mydo);
$config = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share($this->syscfg['sharetitle'], tomedia($this->syscfg['shareicon']), $this->syscfg['sharedesc'], '');
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
if ($operation == 'index') {
    $this->mymsg('error', '温馨提示', '测试专用.', 'close');
} elseif ($operation == 'test') {
    include $this->mymtpl('test');
}