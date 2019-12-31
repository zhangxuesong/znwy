<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'weather';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$curr = 'home';
$mydo = 'weather';
$myurl = $this->createMobileurl($mydo);
$config = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
if ($operation == 'index') {
    if ($this->syscfg['displayweather'] == 2) {
        include $this->mymtpl('index');
    } else {
        $this->mymsg('error', '温馨提示', '极速数据接口没有申请或没有开启.', 'close');
    }
} elseif ($operation == 'ajax') {
    if ($_W['isajax']) {
        $url = 'https://api.jisuapi.com/weather/query?appkey=' . $config['js_appkey'] . '&city=' . $_GPC['city'];
        $result = my_curlOpen($url);
        $jsonarr = json_decode($result, true);
        if ($jsonarr['status'] == 0) {
            $ret = $jsonarr['result'];
            if ($_GPC['isdetail'] == 1) {
                include $this->mymtpl('detail');
            } else {
                include $this->mymtpl('ajax');
            }
        }
    }
    exit(0);
}