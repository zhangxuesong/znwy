<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'express';
$config = $this->module['config'];
$conifg['qq_lbskey'] = !empty($config['qq_lbskey']) ? $config['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zycj_express';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '快递驿站';
if ($operation == 'list') {
    $current = '快件列表';
    $myret = 0;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition1 .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition1 .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition1 .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' or expresssn LIKE \'%' . $_GPC['keyword'] . '%\' or orderno LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY `CTIME` DESC, `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zycj_express_store') . ' where id = :id and weid = :weid';
            $data[$k]['storename'] = pdo_fetchcolumn($sql, array(':id' => $data[$k]['sid'], ':weid' => $mywe['weid']));
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '已取件';
            } elseif ($data[$k]['status'] == 2) {
                $data[$k]['statustxt'] = '未取件';
            } else {
                $data[$k]['statustxt'] = '待取件';
            }
            $sql = 'select title from ' . tablename('rhinfo_zycj_express_company') . ' where id = :id and weid = :weid';
            $data[$k]['company'] = pdo_fetchcolumn($sql, array(':id' => $data[$k]['compid'], ':weid' => $mywe['weid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增快件';
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $expresssn = $_GPC['expresssn'];
        $orderno = substr($expresssn, 0 - 2) . build_order_no();
        $data = array('weid' => $mywe['weid'], 'sid' => $_GPC['sid'], 'compid' => $_GPC['compid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'orderno' => $orderno, 'expresssn' => $expresssn, 'image' => $_GPC['image'], 'contact' => $_GPC['contact'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'remark' => $_GPC['remark'], 'label' => $_GPC['label'], 'io' => $_GPC['io'], 'status' => $_GPC['status'], 'cuid' => 0, 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid and status=1';
    $stores = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_label') . ' where weid = :weid and category=2';
    $labels = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $mycomp = array();
    $mycab = array();
    $mylocal = array();
    $k = 0;
    while (!($k >= count($stores))) {
        $sql = 'select b.id,b.title from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid = :sid and b.status=1';
        $comps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $stores[$k]['id']));
        $mycomp[$stores[$k]['id']] = $comps;
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
        $cabs = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $stores[$k]['id']));
        $mycab[$stores[$k]['id']] = $cabs;
        $m = 0;
        while (!($m >= count($cabs))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and status = 1';
            $locals = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cabid' => $cabs[$k]['id']));
            $mylocal[$cabs[$m]['id']] = $locals;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑快件';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $expresssn = $_GPC['expresssn'];
        $orderno = substr($expresssn, 0 - 2) . build_order_no();
        $data = array('sid' => $_GPC['sid'], 'compid' => $_GPC['compid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'orderno' => $orderno, 'expresssn' => $expresssn, 'image' => $_GPC['image'], 'contact' => $_GPC['contact'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'remark' => $_GPC['remark'], 'label' => $_GPC['label'], 'status' => $_GPC['status']);
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid and status=1';
    $stores = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_label') . ' where weid = :weid and category=2';
    $labels = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $mycomp = array();
    $mycab = array();
    $mylocal = array();
    $k = 0;
    while (!($k >= count($stores))) {
        $sql = 'select b.id,b.title from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid = :sid and b.status=1';
        $comps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $stores[$k]['id']));
        $mycomp[$stores[$k]['id']] = $comps;
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
        $cabs = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $stores[$k]['id']));
        $mycab[$stores[$k]['id']] = $cabs;
        $m = 0;
        while (!($m >= count($cabs))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and status = 1';
            $locals = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cabid' => $cabs[$k]['id']));
            $mylocal[$cabs[$m]['id']] = $locals;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select b.id,b.title from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid = :sid and b.status=1';
    $ecomps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $item['sid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
    $ecabs = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $item['sid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and status=1';
    $elocals = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cabid' => $item['cabid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除快件';
    $id = intval($_GPC['id']);
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'take') {
    $current = '快件核销';
    $id = intval($_GPC['id']);
    $result = pdo_update($tablename, array('status' => 1), array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '核销失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'storelist') {
    $current = '驿站列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition1 .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition1 .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition1 .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_store') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'mindex', 'sid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            $data[$k]['storeurl'] = $this->my_mobileurl($this->createMobileUrl('expressp', array('op' => 'index', 'sid' => $data[$k]['id'])));
            $data[$k]['storeqrcode'] = $this->createqrcode($data[$k]['storeurl']);
            $data[$k]['takeurl'] = $this->my_mobileurl($this->createMobileUrl('expressp', array('op' => 'takeexpress', 'sid' => $data[$k]['id'])));
            $data[$k]['takeqrcode'] = $this->createqrcode($data[$k]['takeurl']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('storelist');
} elseif ($operation == 'storeadd') {
    $current = '新增驿站';
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'storeno' => $_GPC['storeno'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'content' => htmlspecialchars_decode($_GPC['content']), 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'isbind' => $_GPC['isbind'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_store', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'storelist')) . $mywe['direct']);
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
    include $this->mywtpl('storepost');
} elseif ($operation == 'storeedit') {
    $current = '修改驿站';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'storeno' => $_GPC['storeno'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'content' => htmlspecialchars_decode($_GPC['content']), 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'isbind' => $_GPC['isbind'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_store', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'storelist')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('storepost');
} elseif ($operation == 'storedelete') {
    $current = '删除驿站';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_store', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'storestatus') {
    $current = '支付状态';
    $id = intval($_GPC['id']);
    $data = array('ispay' => $_GPC['ispay']);
    $result = pdo_update('rhinfo_zycj_express_store', $data, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . $_GPC['ispay'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'pay') {
    $myret = 0;
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('ispay' => $_GPC['ispay'], 'paytype' => $_GPC['paytype'], 'submerchid' => $_GPC['submerchid'], 'bankmerchid' => $_GPC['bankmerchid'], 'ymfurl' => $_GPC['ymfurl'], 'ymfkey' => $_GPC['ymfkey'], 'paysuccessurl' => $_GPC['paysuccessurl'], 'rsdbankmerchid' => $_GPC['rsdbankmerchid'], 'bankkey' => $_GPC['bankkey'], 'inprice' => $_GPC['inprice'], 'outprice' => $_GPC['outprice'], 'outdays' => $_GPC['outdays'], 'dayprice' => $_GPC['dayprice'], 'ylbid' => $_GPC['ylbid'], 'cabtoken' => $_GPC['cabtoken'], 'issmsmsg' => $_GPC['issmsmsg'], 'istplmsg' => $_GPC['istplmsg'], 'starkey' => $_GPC['starkey'], 'starorg' => $_GPC['starorg'], 'startrm' => $_GPC['startrm'], 'starmerchid' => $_GPC['starmerchid'], 'isalipay' => $_GPC['isalipay'], 'aliaccount' => $_GPC['aliaccount'], 'alipartner' => $_GPC['alipartner'], 'alisecret' => $_GPC['alisecret'], 'alipay_type' => $_GPC['alipay_type'], 'alipay_appid' => $_GPC['alipay_appid'], 'alipay_rsa2' => $_GPC['alipay_rsa2'], 'alipay_private' => $_GPC['alipay_private'], 'alipay_seller_id' => $_GPC['alipay_seller_id'], 'alipay_app_auth_token' => $_GPC['alipay_app_auth_token']);
        $result = pdo_update('rhinfo_zycj_express_store', $data, array('id' => $id, 'weid' => $mywe['weid']));
        if (!empty($_GPC['ylbid'])) {
            $set = array();
            $set['url'] = 'list_bind.php';
            $params = array('id' => $_GPC['ylbid']);
            $res = ylb_http_post($set, $params);
            if ($res['total_count'] == 0) {
                $set = array();
                $set['url'] = 'bind.php';
                $params = array('token' => $this->syscfg['ylb_token'], 'id' => $_GPC['ylbid'], 'm' => 1, 'uid' => 'rhinfo_' . $item['mobile']);
                $res = ylb_http_post($set, $params);
            }
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'storelist')));
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $navtitle = $item['title'];
    $current = '支付参数';
    include $this->mywtpl('pay');
} elseif ($operation == 'complist') {
    $current = '快递公司列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_company') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'eindex')));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('complist');
} elseif ($operation == 'compadd') {
    $current = '新增快递公司';
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'compcode' => $_GPC['compcode'], 'thumb' => $_GPC['thumb'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_company', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'complist')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('comppost');
} elseif ($operation == 'compedit') {
    $current = '修改快递公司';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('title' => $_GPC['title'], 'compcode' => $_GPC['compcode'], 'thumb' => $_GPC['thumb'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_company', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'complist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    include $this->mywtpl('comppost');
} elseif ($operation == 'compdelete') {
    $current = '删除快递公司';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_company', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'selectcomp') {
    $current = '选择快递公司';
    $sid = $_GPC['sid'];
    if ($_W['isajax']) {
        $comparr = explode('_', $_GPC['compstr']);
        pdo_delete('rhinfo_zycj_express_storecomp', array('weid' => $mywe['weid'], 'sid' => $sid));
        $k = 0;
        while (!($k >= count($comparr))) {
            $data = array('weid' => $mywe['weid'], 'sid' => $sid, 'compid' => $comparr[$k]);
            pdo_insert('rhinfo_zycj_express_storecomp', $data);
            ($k += 1) + -1;
        }
        exit('ok');
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid and id=:sid';
    $store = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':sid' => $sid));
    $sql = 'select compid,expid from ' . tablename('rhinfo_zycj_express_storecomp') . ' where weid = :weid and sid=:sid';
    $storecomps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $sid));
    $storecomp = array();
    $k = 0;
    while (!($k >= count($storecomps))) {
        $storecomp[] = $storecomps[$k]['compid'];
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_company') . ' where weid = :weid and city=:city and district=:district';
    $companys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':city' => $store['city'], ':district' => $store['district']));
    $count = count($companys);
    $compname = array();
    $m = 0;
    while (!($m >= $count)) {
        if (in_array($companys[$m]['id'], $storecomp)) {
            $compname[] = $companys[$m]['title'];
        }
        ($m += 1) + -1;
    }
    include $this->mywtpl('storecomp');
} elseif ($operation == 'personlist') {
    $current = '快递人员列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition .= ' and compid=:compid ';
    $params['compid'] = $_GPC['compid'];
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_person') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_person') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
    }
    include $this->mywtpl('personlist');
} elseif ($operation == 'personadd') {
    $current = '新增快递人员';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'compid' => $_GPC['compid'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_person', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'personlist', 'compid' => $_GPC['compid'])) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('personpost');
} elseif ($operation == 'personedit') {
    $current = '修改快递人员';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_person', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'personlist', 'compid' => $_GPC['compid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_person') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    include $this->mywtpl('personpost');
} elseif ($operation == 'persondelete') {
    $current = '删除快递人员';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_person', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delpersonall') {
    $current = '删除全部人员';
    $result = pdo_delete('rhinfo_zycj_express_person', array('weid' => $mywe['weid'], 'compid' => $_GPC['compid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'compid=' . $_GPC['compid']);
    exit(0);
} elseif ($operation == 'employeelist') {
    $current = '操作人员列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition .= ' and sid=:sid ';
    $params['sid'] = $_GPC['sid'];
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_employee') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_employee') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
    }
    include $this->mywtpl('employeelist');
} elseif ($operation == 'employeeadd') {
    $current = '新增操作人员';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'sid' => $_GPC['sid'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_employee', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'employeelist', 'sid' => $_GPC['sid'])) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('employeepost');
} elseif ($operation == 'employeeedit') {
    $current = '修改操作人员';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_employee', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'employeelist', 'sid' => $_GPC['sid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_employee') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    include $this->mywtpl('employeepost');
} elseif ($operation == 'employeedelete') {
    $current = '删除操作人员';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_employee', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delemployeeall') {
    $current = '删除全部操作人员';
    $result = pdo_delete('rhinfo_zycj_express_employee', array('weid' => $mywe['weid'], 'sid' => $_GPC['sid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'sid=' . $_GPC['sid']);
    exit(0);
} elseif ($operation == 'contactlist') {
    $current = '联系人列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition .= ' and sid=:sid ';
    $params['sid'] = $_GPC['sid'];
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (realname LIKE \'%' . $_GPC['keyword'] . '%\' or mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_contact') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_contact') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
    }
    include $this->mywtpl('contactlist');
} elseif ($operation == 'contactadd') {
    $current = '新增联系人';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'realname' => $_GPC['realname'], 'sid' => $_GPC['sid'], 'mobile' => $_GPC['mobile'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_contact', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'contactlist', 'sid' => $_GPC['sid'])) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('contactpost');
} elseif ($operation == 'contactedit') {
    $current = '修改联系人';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $data = array('realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_contact', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'contactlist', 'sid' => $_GPC['sid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_contact') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    include $this->mywtpl('contactpost');
} elseif ($operation == 'contactdelete') {
    $current = '删除联系人';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_contact', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delcontactall') {
    $current = '删除全部联系人';
    $result = pdo_delete('rhinfo_zycj_express_contact', array('weid' => $mywe['weid'], 'sid' => $_GPC['sid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'sid=' . $_GPC['sid']);
    exit(0);
} elseif ($operation == 'cablist') {
    $current = '快递柜列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_cabinet') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabinet') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $data[$k]['store'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':sid' => $data[$k]['sid']));
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('cablist');
} elseif ($operation == 'cabadd') {
    $current = '新增快递柜';
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'sid' => $_GPC['sid'], 'locksn' => $_GPC['locksn'], 'thumb' => $_GPC['thumb'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_cabinet', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'cablist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid ' . $rcondition;
    $stores = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('cabpost');
} elseif ($operation == 'cabedit') {
    $current = '修改快递柜';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('title' => $_GPC['title'], 'sid' => $_GPC['sid'], 'locksn' => $_GPC['locksn'], 'thumb' => $_GPC['thumb'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_cabinet', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'cablist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid ' . $rcondition;
    $stores = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabinet') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('cabpost');
} elseif ($operation == 'cabdelete') {
    $current = '删除快递柜';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_cabinet', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'stlocalist') {
    $sql = 'SELECT title FROM ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid=:weid and id=:cabid';
    $current = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cabid' => $_GPC['cabid']));
    $current .= ' > 储存格列表';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition .= ' and cabid=:cabid ';
    $params['cabid'] = $_GPC['cabid'];
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_cabstloca') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
    }
    include $this->mywtpl('stlocalist');
} elseif ($operation == 'stlocaadd') {
    $current = '新增储存格';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'cabid' => $_GPC['cabid'], 'spec' => $_GPC['spec'], 'ctrlcode' => $_GPC['ctrlcode'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express_cabstloca', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'stlocalist', 'cabid' => $_GPC['cabid'])) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('stlocapost');
} elseif ($operation == 'stlocaedit') {
    $current = '修改储存格';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'spec' => $_GPC['spec'], 'ctrlcode' => $_GPC['ctrlcode'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        pdo_update('rhinfo_zycj_express_cabstloca', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'stlocalist', 'cabid' => $_GPC['cabid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('stlocapost');
} elseif ($operation == 'stlocadel') {
    $current = '删除储存格';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_cabstloca', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delstlocaall') {
    $current = '删除全部格子';
    $result = pdo_delete('rhinfo_zycj_express_cabstloca', array('weid' => $mywe['weid'], 'cabid' => $_GPC['cabid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'cabid=' . $_GPC['cabid']);
    exit(0);
} elseif ($operation == 'stbatchadd') {
    $current = '批量新增储存格';
    if (!$_W['ispost']) {
    }
    include $this->mywtpl('stbatchadd');
} elseif ($operation == 'spersonlist') {
    $current = '快递人员列表';
    $operation = 'list';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition .= ' and sid=:sid ';
    $params['sid'] = $_GPC['sid'];
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_express_storecomp') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_storecomp') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title,mobile from ' . tablename('rhinfo_zycj_express_company') . ' where id = :id and weid = :weid and status=1';
            $company = pdo_fetch($sql, array(':id' => $data[$k]['compid'], ':weid' => $mywe['weid']));
            $data[$k]['comptitle'] = $company['title'];
            $sql = 'select title,status,mobile from ' . tablename('rhinfo_zycj_express_person') . ' where id = :id and compid=:compid and weid = :weid';
            $person = pdo_fetch($sql, array(':id' => $data[$k]['expid'], ':compid' => $data[$k]['compid'], ':weid' => $mywe['weid']));
            if (!empty($person)) {
                $data[$k]['persontitle'] = $person['title'];
                $data[$k]['mobile'] = $person['mobile'];
                $data[$k]['status'] = $person['status'];
            } else {
                $data[$k]['persontitle'] = $company['title'];
                $data[$k]['mobile'] = $company['mobile'];
                $data[$k]['status'] = 1;
            }
            ($k += 1) + -1;
        }
    }
    include $this->mywtpl('spersonlist');
} elseif ($operation == 'spersonadd') {
    $current = '新增译站快递人员';
    $sid = $_GPC['sid'];
    if ($_W['isajax']) {
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express_storecomp') . ' where weid = :weid and sid=:sid and compid = :compid and expid=:expid';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':compid' => $_GPC['compid'], ':expid' => $_GPC['expid'], ':sid' => $_GPC['sid']));
        if ($count > 0) {
            echo '快递人员已存在!';
            exit(0);
        }
        $data = array('weid' => $mywe['weid'], 'sid' => $_GPC['sid'], 'compid' => $_GPC['compid'], 'expid' => $_GPC['expid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zycj_express_storecomp', $data);
        $id = pdo_insertid();
        if ($res) {
            echo 'ok';
        } else {
            echo '更新失败!';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid and id=:sid';
    $store = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':sid' => $sid));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_company') . ' where weid = :weid and city=:city and district=:district';
    $companys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':city' => $store['city'], ':district' => $store['district']));
    $myperson = array();
    $k = 0;
    while (!($k >= count($companys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_person') . ' where weid = :weid and compid = :compid and status=1';
        $persons = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':compid' => $companys[$k]['id']));
        $myperson[$companys[$k]['id']] = $persons;
        ($k += 1) + -1;
    }
    include $this->mywtpl('spersonpost');
} elseif ($operation == 'spersonedit') {
    $current = '修改译站快递人员';
    $id = $_GPC['id'];
    if ($_W['isajax']) {
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express_storecomp') . ' where weid = :weid and sid=:sid and compid = :compid and expid=:expid and id<>:id';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':compid' => $_GPC['compid'], ':expid' => $_GPC['expid'], ':sid' => $_GPC['sid'], ':id' => $id));
        if ($count > 0) {
            echo '快递人员已存在!';
            exit(0);
        }
        $data = array('compid' => $_GPC['compid'], 'expid' => $_GPC['expid']);
        $res = pdo_update('rhinfo_zycj_express_storecomp', $data, array('id' => $id, 'weid' => $mywe['weid']));
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_storecomp') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid = :weid and id=:sid';
    $store = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':sid' => $item['sid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_company') . ' where weid = :weid and city=:city and district=:district';
    $companys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':city' => $store['city'], ':district' => $store['district']));
    $myperson = array();
    $k = 0;
    while (!($k >= count($companys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_person') . ' where weid = :weid and compid = :compid and status=1';
        $persons = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':compid' => $companys[$k]['id']));
        $myperson[$companys[$k]['id']] = $persons;
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_express_person') . ' where weid = :weid and compid = :compid and status=1';
    $epersons = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':compid' => $item['compid']));
    include $this->mywtpl('spersonpost');
} elseif ($operation == 'spersondelete') {
    $current = '删除译站快递人员';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_storecomp', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delspersonall') {
    $current = '删除译站快递人员';
    $result = pdo_delete('rhinfo_zycj_express_storecomp', array('weid' => $mywe['weid'], 'sid' => $_GPC['sid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'sid=' . $_GPC['sid']);
    exit(0);
} elseif ($operation == 'import') {
    $current = '联系人导入';
    if ($_W['isajax']) {
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file_types = explode('.', $_FILES['upfile']['name']);
            $file_type = $file_types[count($file_types) - 1];
            if (strtolower($file_type) != 'csv' && strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
                echo '类型不正确，请重新上传';
                exit(0);
            }
            $savePath = IA_ROOT . '/addons/rhinfo_zyxq/upfile/';
            $str = date('Ymdhis');
            $file_name = $str . '.' . $file_type;
            if (!copy($tmp_file, $savePath . $file_name)) {
                echo '上传失败';
                exit(0);
            }
            if (strtolower($file_type) == 'csv') {
                $res = import_csv($savePath . $file_name);
            } else {
                $res = import_excel($savePath . $file_name);
            }
            $i = 0;
            $k = 0;
            while (!($k >= count($res))) {
                if ($k != 0) {
                    $data['weid'] = $mywe['weid'];
                    $data['sid'] = $_GPC['sid'];
                    $data['realname'] = $res[$k][0];
                    $data['mobile'] = $res[$k][1];
                    $data['remark'] = $res[$k][2];
                    $data['cuid'] = $mywe['uid'];
                    $data['ctime'] = TIMESTAMP;
                    if (!empty($data['mobile'])) {
                        $result = pdo_insert('rhinfo_zycj_express_contact', $data);
                        if ($result) {
                            ($i += 1) + -1;
                        }
                    }
                }
                ($k += 1) + -1;
            }
            if ($i > 0) {
                echo 'ok';
            } else {
                echo '导入失败!';
            }
            unlink($savePath . $file_name);
            exit(0);
        } else {
            echo '文件不正确错误!';
            exit(0);
        }
    }
    include $this->mywtpl('import');
} elseif ($operation == 'label') {
    if ($_W['isajax']) {
        $id = $_GPC['id'];
        if (!empty($id)) {
            $data = array('title' => $_GPC['label']);
            $res = pdo_update('rhinfo_zycj_express_label', $data, array('weid' => $mywe['weid'], 'id' => $id));
        } else {
            $data = array('weid' => $mywe['weid'], 'category' => $_GPC['category'], 'title' => $_GPC['label'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $res = pdo_insert('rhinfo_zycj_express_label', $data);
        }
        if ($res) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $rights = $this->myrights(15, $mydo, 'list');
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zycj_express_label') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $data = array(array('id' => 1, 'title' => '寄件标签'), array('id' => 2, 'title' => '取件标签'));
    $condition .= ' and category = :category';
    $k = 0;
    while (!($k >= count($data))) {
        $params[':category'] = $data[$k]['id'];
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_label') . ' where ' . $condition . ' ORDER BY title*1 ASC ';
        $data[$k]['cate'] = pdo_fetchall($sql, $params);
        ($k += 1) + -1;
    }
    include $this->mywtpl();
} elseif ($operation == 'delalllabel') {
    $current = '删除快件标签';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_express_label', array('weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'dellabel') {
    $current = '删除快件标签';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_express_label', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}