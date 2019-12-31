<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'express';
$mydo = 'express';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
$sid = intval($_GPC['sid']);
$compid = intval($_GPC['compid']);
$ismanager = false;
if (empty($user['mobile'])) {
    header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
    exit(0);
}
if ($operation == 'list') {
    header('Location:' . $this->createMobileurl('expressp', array('op' => 'list')));
    exit(0);
} elseif ($operation == 'index') {
    header('Location:' . $this->createMobileurl('expressp', array('op' => 'index', 'sid' => $sid)));
    exit(0);
} elseif ($operation == 'add' || $operation == 'adde') {
    if ($_W['isajax']) {
        if (empty($_GPC['expresssn'])) {
            show_json(0, '快递编号为空');
        }
        $cabstloca = $_GPC['cabstloca'];
        $localarr = array();
        $localarr = explode('-', $cabstloca);
        if (!empty($cabstloca) && empty($localarr[2])) {
            show_json(0, '存放位置不正确');
        }
        $expresssn = $_GPC['expresssn'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and expresssn=:expresssn';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expresssn' => $expresssn));
        if ($total > 0) {
            show_json(0, '快递单号已存在');
        }
        $orderno = $this->express_build_orderno(build_order_no() . substr($expresssn, 0 - 2), $expresssn);
        $data = array('weid' => $_W['uniacid'], 'sid' => $_GPC['sid'], 'title' => '无', 'mobile' => $_GPC['mobile'], 'orderno' => $orderno, 'expresssn' => $expresssn, 'cabid' => $localarr[0], 'stid' => $localarr[2], 'image' => $_GPC['image'], 'remark' => $_GPC['remark'], 'label' => $_GPC['label'], 'io' => 2, 'compid' => $_GPC['compid'], 'status' => 0, 'smsstatus' => 0, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :compid';
        $mycomp = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        if ($_GPC['cfrom'] == 'company') {
            $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.compid=:compid and a.sid=:sid and b.uid=:uid';
            $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
        }
        if ($_GPC['cfrom'] == 'express') {
            $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.expid=b.id where a.weid = :weid and a.compid=:compid and a.sid=:sid and  b.uid=:uid ';
            $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
        }
        if ($this->syscfg['smsprice'] > 0 && $store['issmsmsg'] == 1) {
            if ($_GPC['cfrom'] == 'express') {
                if (empty($person) || !($person['smsqty'] >= 1)) {
                    show_json(0, '快递员短信数量不足');
                }
            } elseif ($_GPC['cfrom'] == 'company') {
                if (empty($company) || !($company['smsqty'] >= 1)) {
                    show_json(0, '快递公司短信数量不足');
                }
            } elseif (!($store['smsqty'] >= 1)) {
                show_json(0, '译站短信数量不足');
            }
        }
        if (empty($store['issmsmsg']) && empty($store['istplmsg'])) {
            show_json(0, '未开启通知');
        }
        pdo_insert('rhinfo_zycj_express', $data);
        $id = pdo_insertid();
        if ($id > 0) {
            if ($store['issmsmsg'] == 1) {
                if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                    $ret = $this->send_sms($this->syscfg['smstype'], $_GPC['mobile'], $this->syscfg['expressid'], array('store' => $store['title'] . '(' . $store['address'] . ')', 'ordersn' => $orderno . ',' . $mycomp['title']));
                    if ($ret['status'] == 1) {
                        pdo_update('rhinfo_zycj_express', array('smsstatus' => 1), array('weid' => $_W['uniacid'], 'id' => $id));
                        $smslog_data = array('weid' => $_W['uniacid'], 'title' => '短信已发送,取件码' . $orderno, 'io' => 2, 'mobile' => $_GPC['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        if ($this->syscfg['smsprice'] > 0) {
                            if ($_GPC['cfrom'] == 'express') {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_person') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $person['id']));
                                $smslog_data['sid'] = $store['id'];
                                $smslog_data['compid'] = $person['compid'];
                                $smslog_data['expid'] = $person['id'];
                            } elseif ($_GPC['cfrom'] == 'company') {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_company') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $company['id']));
                                $smslog_data['sid'] = $store['id'];
                                $smslog_data['compid'] = $company['compid'];
                                $smslog_data['expid'] = 0;
                            } else {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_store') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $store['id']));
                                $smslog_data['sid'] = $store['id'];
                                $smslog_data['compid'] = 0;
                                $smslog_data['expid'] = 0;
                            }
                        } elseif ($_GPC['cfrom'] == 'express') {
                            $smslog_data['sid'] = $store['id'];
                            $smslog_data['compid'] = $person['compid'];
                            $smslog_data['expid'] = $person['id'];
                        } elseif ($_GPC['cfrom'] == 'company') {
                            $smslog_data['sid'] = $store['id'];
                            $smslog_data['compid'] = $company['compid'];
                            $smslog_data['expid'] = 0;
                        } else {
                            $smslog_data['sid'] = $store['id'];
                            $smslog_data['compid'] = 0;
                            $smslog_data['expid'] = 0;
                        }
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                    } else {
                        $smslog_data = array('weid' => $_W['uniacid'], 'sid' => $store['id'], 'title' => '短信发送失败，取件码' . $orderno, 'io' => 0, 'mobile' => $_GPC['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                        $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $_GPC['mobile'], '操作失败id=' . $id . $ret['message']);
                    }
                }
            }
            if ($store['istplmsg'] == 1) {
                $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
                $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
                load()->model('mc');
                $openid = mc_uid2openid($member['uid']);
                if (!empty($openid)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => $store['title'] . '(' . $store['address'] . ')'), 'keyword1' => array('value' => $orderno, 'color' => $topcolor), 'keyword2' => array('value' => $expresssn, 'color' => $textcolor), 'keyword3' => array('value' => empty($mycomp['title']) ? $store['title'] : $mycomp['title'], 'color' => $textcolor), 'keyword4' => array('value' => $store['mobile'], 'color' => $textcolor), 'remark' => array('value' => '请凭取件码及时取件，感谢您的支持.'));
                    $url = $this->createMobileurl('expressp', array('op' => 'myexpress', 'cate' => 2));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['expresstplid'])) {
                        $this->send_mysendtplnotice($openid, $this->syscfg['expresstplid'], $postdata, $url, $topcolor);
                    }
                }
            }
            show_json(1);
        }
        show_json(0, '快件入库失败');
    }
    if ($operation == 'add') {
        $sql = 'select b.id as value,b.title as text from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid';
        $companys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'adde') {
        $companys = array();
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'store' || $res['from'] == 'company' || $res['from'] == 'express' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $mycab = array();
        $myspec = array();
        $mylocal = array();
        $sql = 'select title,id from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
        $cabs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $mycab = $cabs;
        $m = 0;
        while (!($m >= count($cabs))) {
            $specs = array('0' => array('id' => 1, 'title' => '小格'), '1' => array('id' => 2, 'title' => '中格'), '2' => array('id' => 3, 'title' => '大格'));
            $myspec[$cabs[$m]['id']] = $specs;
            $n = 0;
            while (!($n >= count($specs))) {
                $sql = 'select title , id from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and spec = :spec and status=1 order by title*1,id';
                $locals = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':cabid' => $cabs[$m]['id'], ':spec' => $specs[$n]['id']));
                $mylocal[$specs[$n]['id']] = $locals;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
    $labels = pdo_getall('rhinfo_zycj_express_label', array('weid' => $_W['uniacid'], 'category' => 2));
    include $this->mymtpl('add');
} elseif ($operation == 'edit' || $operation == 'edite') {
    $expressid = $_GPC['expressid'];
    if ($_W['isajax']) {
        if (empty($_GPC['expresssn'])) {
            show_json(0, '快递编号为空');
        }
        $expresssn = $_GPC['expresssn'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and expresssn=:expresssn and id<>:expressid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expresssn' => $expresssn, ':expressid' => $expressid));
        if ($total > 0) {
            show_json(0, '快递单号已存在');
        }
        $data = array('mobile' => $_GPC['mobile'], 'expresssn' => $expresssn, 'remark' => $_GPC['remark'], 'label' => $_GPC['label'], 'compid' => $_GPC['compid']);
        $res = pdo_update('rhinfo_zycj_express', $data, array('weid' => $_W['uniacid'], 'id' => $expressid));
        if ($res) {
            show_json(2);
        }
        show_json(0, '修改失败');
    }
    if ($operation == 'edit') {
        $sql = 'select b.id as value,b.title as text from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid';
        $companys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'edite') {
        $companys = array();
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'store' || $res['from'] == 'company' || $res['from'] == 'express' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $mycab = array();
        $myspec = array();
        $mylocal = array();
        $sql = 'select title,id from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
        $cabs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $mycab = $cabs;
        $m = 0;
        while (!($m >= count($cabs))) {
            $specs = array('0' => array('id' => 1, 'title' => '小格'), '1' => array('id' => 2, 'title' => '中格'), '2' => array('id' => 3, 'title' => '大格'));
            $myspec[$cabs[$m]['id']] = $specs;
            $n = 0;
            while (!($n >= count($specs))) {
                $sql = 'select title , id from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and spec = :spec and status=1 order by title*1,id';
                $locals = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':cabid' => $cabs[$m]['id'], ':spec' => $specs[$n]['id']));
                $mylocal[$specs[$n]['id']] = $locals;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and id=:expressid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expressid' => $_GPC['expressid']));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid = :weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $item['compid']));
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
    $labels = pdo_getall('rhinfo_zycj_express_label', array('weid' => $_W['uniacid'], 'category' => 2));
    include $this->mymtpl('edit');
} elseif ($operation == 'batchadd' || $operation == 'batchadde') {
    if ($_W['isajax']) {
        if (empty($_GPC['expresssn'])) {
            show_json(0, '快递编号为空');
        }
        $expresssn = $_GPC['expresssn'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and expresssn=:expresssn';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expresssn' => $expresssn));
        if ($total > 0) {
            show_json(0, '快递单号已存在');
        }
        $orderno = $this->express_build_orderno(build_order_no() . substr($expresssn, 0 - 2), $expresssn);
        $data = array('weid' => $_W['uniacid'], 'sid' => $_GPC['sid'], 'title' => '无', 'orderno' => $orderno, 'expresssn' => $expresssn, 'compid' => $_GPC['compid'], 'io' => 2, 'status' => 0, 'smsstatus' => 0, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express', $data);
        $id = pdo_insertid();
        if ($id > 0) {
            show_json(1);
        } else {
            show_json(0, '快件入库失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $mycab = array();
    $myspec = array();
    $mylocal = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid = :weid and sid = :sid and status=1';
    $cabs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $mycab = $cabs;
    $m = 0;
    while (!($m >= count($cabs))) {
        $specs = array('0' => array('id' => 1, 'title' => '小格'), '1' => array('id' => 2, 'title' => '中格'), '2' => array('id' => 3, 'title' => '大格'));
        $myspec[$cabs[$m]['id']] = $specs;
        $n = 0;
        while (!($n >= count($specs))) {
            $sql = 'select title , id from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid = :weid and cabid = :cabid and spec = :spec and status=1 order by title*1,id';
            $locals = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':cabid' => $cabs[$m]['id'], ':spec' => $specs[$n]['id']));
            $mylocal[$specs[$n]['id']] = $locals;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    if ($operation == 'batchadd') {
        $sql = 'select b.id as value,b.title as text from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid';
        $companys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'batchadde') {
        $companys = array();
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'store') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and io=2 and status=0 and smsstatus=0 order by id desc';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    } elseif ($res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and cuid=:cuid and io=2 and status=0 and smsstatus=0 order by id desc';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':cuid' => $_W['member']['uid']));
    } elseif ($res['from'] == 'company' || $res['from'] == 'express') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and compid=:compid and cuid=:cuid and io=2 and status=0 and smsstatus=0 order by id desc';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid, ':cuid' => $_W['member']['uid']));
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
    $labels = pdo_getall('rhinfo_zycj_express_label', array('weid' => $_W['uniacid'], 'category' => 2));
    include $this->mymtpl('batchadd');
} elseif ($operation == 'savemobile') {
    if ($_W['isajax'] && !empty($_GPC['mobile'])) {
        $data = array('mobile' => $_GPC['mobile']);
        $res = pdo_update('rhinfo_zycj_express', $data, array('weid' => $_W['uniacid'], 'sid' => $sid, 'expresssn' => $_GPC['expresssn']));
        show_json(1, '保存成功');
    }
    show_json(0, '操作异常');
} elseif ($operation == 'del') {
    if ($_W['isajax']) {
        $result = pdo_delete('rhinfo_zycj_express', array('weid' => $_W['uniacid'], 'sid' => $_GPC['sid'], 'expresssn' => $_GPC['expresssn']));
        if (!empty($result)) {
            show_json(1);
        } else {
            show_json(0, '删除失败');
        }
    }
} elseif ($operation == 'send') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if ($_W['isajax'] && !empty($_GPC['expresssn']) && !empty($store)) {
        $cabstloca = $_GPC['cabstloca'];
        $localarr = array();
        $localarr = explode('-', $cabstloca);
        if (!empty($cabstloca) && empty($localarr[2])) {
            show_json(0, '存放位置不正确');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and expresssn=:expresssn';
        $express = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expresssn' => $_GPC['expresssn']));
        $data = array('mobile' => $_GPC['mobile'], 'cabid' => $localarr[0], 'stid' => $localarr[2], 'image' => $_GPC['image'], 'remark' => $_GPC['remark'], 'label' => $_GPC['label'], 'status' => 0);
        $res = pdo_update('rhinfo_zycj_express', $data, array('weid' => $_W['uniacid'], 'id' => $express['id']));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :compid';
        $mycomp = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        if ($_GPC['cfrom'] == 'company') {
            $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.compid=:compid and a.sid=:sid and b.uid=:uid';
            $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
        }
        if ($_GPC['cfrom'] == 'express') {
            $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.expid=b.id where a.weid = :weid and a.compid=:compid and a.sid=:sid and b.uid=:uid ';
            $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
        }
        if ($this->syscfg['smsprice'] > 0) {
            if ($_GPC['cfrom'] == 'express') {
                if (empty($person) || !($person['smsqty'] >= 1)) {
                    show_json(0, '快递员短信数量不足');
                }
            } elseif ($_GPC['cfrom'] == 'company') {
                if (empty($company) || !($company['smsqty'] >= 1)) {
                    show_json(0, '快递公司短信数量不足');
                }
            } elseif (!($store['smsqty'] >= 1)) {
                show_json(0, '译站短信数量不足');
            }
        }
        if (empty($store['issmsmsg']) && empty($store['istplmsg'])) {
            show_json(0, '未开启通知');
        }
        if ($store['issmsmsg'] == 1) {
            if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                $ret = $this->send_sms($this->syscfg['smstype'], $_GPC['mobile'], $this->syscfg['expressid'], array('store' => $store['title'] . '(' . $store['address'] . ')', 'ordersn' => $express['orderno'] . ',' . $mycomp['title']));
                if ($ret['status'] == 1) {
                    pdo_update('rhinfo_zycj_express', array('smsstatus' => 1), array('weid' => $_W['uniacid'], 'id' => $express['id']));
                    $smslog_data = array('weid' => $_W['uniacid'], 'title' => '短信已发送，取件码' . $express['orderno'], 'io' => 2, 'mobile' => $_GPC['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                    if ($this->syscfg['smsprice'] > 0) {
                        if ($_GPC['cfrom'] == 'express') {
                            $sql = 'update ' . tablename('rhinfo_zycj_express_person') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                            pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $person['id']));
                            $smslog_data['sid'] = $sid;
                            $smslog_data['compid'] = $person['compid'];
                            $smslog_data['expid'] = $person['id'];
                        } elseif ($_GPC['cfrom'] == 'company') {
                            $sql = 'update ' . tablename('rhinfo_zycj_express_company') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                            pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $company['id']));
                            $smslog_data['sid'] = $sid;
                            $smslog_data['compid'] = $company['compid'];
                            $smslog_data['expid'] = 0;
                        } else {
                            $sql = 'update ' . tablename('rhinfo_zycj_express_store') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                            pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $store['id']));
                            $smslog_data['sid'] = $store['id'];
                            $smslog_data['compid'] = 0;
                            $smslog_data['expid'] = 0;
                        }
                    } elseif ($_GPC['cfrom'] == 'express') {
                        $smslog_data['sid'] = $sid;
                        $smslog_data['compid'] = $person['compid'];
                        $smslog_data['expid'] = $person['id'];
                    } elseif ($_GPC['cfrom'] == 'company') {
                        $smslog_data['sid'] = $sid;
                        $smslog_data['compid'] = $company['compid'];
                        $smslog_data['expid'] = 0;
                    } else {
                        $smslog_data['sid'] = $store['id'];
                        $smslog_data['compid'] = 0;
                        $smslog_data['expid'] = 0;
                    }
                    pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                } else {
                    $smslog_data = array('weid' => $_W['uniacid'], 'sid' => $sid, 'title' => '短信发送失败，取件码' . $express['orderno'], 'io' => 0, 'mobile' => $_GPC['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $_GPC['mobile'], '操作失败id=' . $id . $ret['message']);
                }
            }
        }
        if ($store['istplmsg'] == 1) {
            $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
            $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
            load()->model('mc');
            $openid = mc_uid2openid($member['uid']);
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title'] . '(' . $store['address'] . ')'), 'keyword1' => array('value' => $express['orderno'], 'color' => $topcolor), 'keyword2' => array('value' => $express['expresssn'], 'color' => $textcolor), 'keyword3' => array('value' => empty($mycomp['title']) ? $store['title'] : $mycomp['title'], 'color' => $textcolor), 'keyword4' => array('value' => $store['mobile'], 'color' => $textcolor), 'remark' => array('value' => '请凭取件码及时取件，感谢您的支持.'));
                $url = $this->createMobileurl($mydo, array('op' => 'myexpress', 'cate' => 2));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['expresstplid'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['expresstplid'], $postdata, $url, $topcolor);
                }
            }
            show_json(1, '操作成功');
        }
    }
    show_json(0, '操作错误');
} elseif ($operation == 'takeexpress') {
    if ($_W['isajax']) {
        if (empty($_GPC['expresssn'])) {
            show_json(0, '取件码不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and (orderno=:orderno or expresssn=:expresssn)';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':orderno' => $_GPC['expresssn'], ':expresssn' => $_GPC['expresssn']));
        if ($item['status'] > 0) {
            show_json(0, '此快件已取出');
        }
        $res = pdo_update('rhinfo_zycj_express', array('status' => 1, 'taketime' => TIMESTAMP, 'takeuid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'sid' => $sid, 'id' => $item['id']));
        if (!empty($res)) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            if ($_GPC['cfrom'] == 1) {
                $openid = $_W['openid'];
            } else {
                $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
                $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $item['mobile']));
                load()->model('mc');
                $openid = mc_uid2openid($member['uid']);
            }
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => '亲，您的快件已成功取件', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号' . $item['expresssn'], 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持.'));
                $url = $this->createMobileurl($mydo, array('op' => 'myexpress', 'cate' => 2));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                }
            }
            show_json(1, '取件成功');
        } else {
            show_json(0, '取件失败');
        }
    }
    show_json(0, '操作错误');
} elseif ($operation == 'postsend') {
    if ($_W['isajax']) {
        if (empty($_GPC['id'])) {
            show_json(0, '未找到快件');
        }
        if ($_GPC['cate'] == 2 || $_GPC['cate'] == 3) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            if (empty($store['issmsmsg']) && empty($store['istplmsg'])) {
                show_json(0, '未开启通知');
            }
            $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and id = :id';
            $express = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
            if (empty($express['mobile'])) {
                show_json(0, '手机号为空');
            }
            $compid = $express['compid'];
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :compid';
            $mycomp = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
            if ($_GPC['cfrom'] == 'company') {
                $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.compid=:compid and a.sid=:sid and b.uid=:uid';
                $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
            }
            if ($_GPC['cfrom'] == 'person') {
                $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.expid=b.id where a.weid = :weid and b.compid=:compid and a.sid=:sid and b.uid=:uid ';
                $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid, ':sid' => $sid, ':uid' => $_W['member']['uid']));
            }
            if ($this->syscfg['smsprice'] > 0 && $store['issmsmsg'] == 1) {
                if ($_GPC['cfrom'] == 'express') {
                    if (!($person['smsqty'] >= 1)) {
                        show_json(0, '快递员短信数量不足');
                    }
                } elseif ($_GPC['cfrom'] == 'company') {
                    if (!($company['smsqty'] >= 1)) {
                        show_json(0, '快递公司短信数量不足');
                    }
                } elseif (!($store['smsqty'] >= 1)) {
                    show_json(0, '译站短信数量不足');
                }
            }
            if ($store['issmsmsg'] == 1) {
                if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                    $ret = $this->send_sms($this->syscfg['smstype'], $express['mobile'], $this->syscfg['expressid'], array('store' => $store['title'] . '(' . $store['address'] . ')', 'ordersn' => $express['orderno'] . ',' . $mycomp['title']));
                    if ($ret['status'] == 1) {
                        pdo_update('rhinfo_zycj_express', array('smsstatus' => 1), array('weid' => $_W['uniacid'], 'id' => $_GPC['id']));
                        $smslog_data = array('weid' => $_W['uniacid'], 'title' => '短信已发送，取件码' . $express['orderno'], 'io' => 2, 'mobile' => $express['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        if ($this->syscfg['smsprice'] > 0) {
                            if ($_GPC['cfrom'] == 'express') {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_person') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $person['id']));
                                $smslog_data['sid'] = $sid;
                                $smslog_data['compid'] = $person['compid'];
                                $smslog_data['expid'] = $person['id'];
                            } elseif ($_GPC['cfrom'] == 'company') {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_company') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $company['id']));
                                $smslog_data['sid'] = $sid;
                                $smslog_data['compid'] = $company['id'];
                                $smslog_data['expid'] = 0;
                            } else {
                                $sql = 'update ' . tablename('rhinfo_zycj_express_store') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                                pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $store['id']));
                                $smslog_data['sid'] = $store['id'];
                                $smslog_data['compid'] = 0;
                                $smslog_data['expid'] = 0;
                            }
                        } elseif ($_GPC['cfrom'] == 'express') {
                            $smslog_data['sid'] = $sid;
                            $smslog_data['compid'] = $person['compid'];
                            $smslog_data['expid'] = $person['id'];
                        } elseif ($_GPC['cfrom'] == 'company') {
                            $smslog_data['sid'] = $sid;
                            $smslog_data['compid'] = $company['id'];
                            $smslog_data['expid'] = 0;
                        } else {
                            $smslog_data['sid'] = $store['id'];
                            $smslog_data['compid'] = 0;
                            $smslog_data['expid'] = 0;
                        }
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                    } else {
                        $smslog_data = array('weid' => $_W['uniacid'], 'sid' => $sid, 'title' => '短信发送失败', 'io' => 0, 'mobile' => $express['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                        $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $express['mobile'], '操作失败id=' . $_GPC['id'] . $ret['message']);
                        show_json(0, '发送失败');
                    }
                } else {
                    show_json(0, '参数未配置');
                }
            }
            if ($store['istplmsg'] == 1) {
                $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
                $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $express['mobile']));
                load()->model('mc');
                $openid = mc_uid2openid($member['uid']);
                if (!empty($openid)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => $store['title'] . '(' . $store['address'] . ')'), 'keyword1' => array('value' => $express['orderno'], 'color' => $topcolor), 'keyword2' => array('value' => $express['expresssn'], 'color' => $textcolor), 'keyword3' => array('value' => empty($mycomp['title']) ? $store['title'] : $mycomp['title'], 'color' => $textcolor), 'keyword4' => array('value' => $store['mobile'], 'color' => $textcolor), 'remark' => array('value' => '请凭取件码及时取件，感谢您的支持.'));
                    $url = $this->createMobileurl('expressp', array('op' => 'myexpress', 'cate' => 2));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['expresstplid'])) {
                        $this->send_mysendtplnotice($openid, $this->syscfg['expresstplid'], $postdata, $url, $topcolor);
                    }
                    show_json(1, '发送成功');
                }
            }
        }
        if ($_GPC['cate'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            if (empty($store['issmsmsg']) && empty($store['istplmsg'])) {
                show_json(0, '未开启通知');
            }
            $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and id = :id';
            $express = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
            $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.compid=b.compid where a.weid = :weid and a.sid=:sid and b.status=1';
            $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :id';
            $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $express['compid']));
            if (empty($person['mobile'])) {
                $send_mobile = $company['mobile'];
                $send_uid = $company['uid'];
                $send_compid = $company['id'];
            } else {
                $send_mobile = $person['mobile'];
                $send_uid = $person['uid'];
                $send_compid = $person['compid'];
            }
            if ($store['issmsmsg'] == 1) {
                if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                    if (!($store['smsqty'] >= 1)) {
                        show_json(0, '译站短信数量不足');
                    }
                    $ret = $this->send_sms($this->syscfg['smstype'], $send_mobile, $this->syscfg['expresssendid'], array('store' => $store['title'] . '(' . $store['address'] . ')', 'content' => $express['fromcity'] . '~' . $express['city']));
                    if ($ret['status'] == 1) {
                        pdo_update('rhinfo_zycj_express', array('smsstatus' => 1), array('weid' => $_W['uniacid'], 'id' => $_GPC['id']));
                        $smslog_data = array('weid' => $_W['uniacid'], 'title' => '短信已发送', 'io' => 2, 'mobile' => $send_mobile, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        $sql = 'update ' . tablename('rhinfo_zycj_express_store') . ' set smsqty = smsqty -1 where weid=:weid and id=:id';
                        pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $store['id']));
                        $smslog_data['sid'] = $store['id'];
                        $smslog_data['compid'] = 0;
                        $smslog_data['expid'] = 0;
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                    } else {
                        $smslog_data = array('weid' => $_W['uniacid'], 'sid' => $store['id'], 'title' => '短信发送失败', 'io' => 0, 'mobile' => $send_mobile, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zycj_express_smslog', $smslog_data);
                        $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $send_mobile, '操作失败id=' . $_GPC['id'] . $ret['message']);
                        show_json(0, '发送失败');
                    }
                } else {
                    show_json(0, '参数未配置');
                }
            }
            if ($store['istplmsg'] == 1) {
                load()->model('mc');
                $openid = mc_uid2openid($send_uid);
                if (!empty($openid)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => $store['title'] . '(' . $store['address'] . ')'), 'keyword1' => array('value' => '有新的快件到达驿站', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $express['title'], 'color' => $textcolor), 'remark' => array('value' => '请及时派件，感谢您的支持.'));
                    $url = $this->createMobileurl('expressp', array('op' => 'eindex', 'sid' => $sid, 'compid' => $send_compid));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
                show_json(1, '发送成功');
            }
        }
    }
    show_json(0, '操作错误');
} elseif ($operation == 'postprice') {
    if ($_W['isajax']) {
        if (empty($_GPC['id'])) {
            show_json(0, '未找到快件');
        }
        $res = pdo_update('rhinfo_zycj_express', array('status' => 1, 'price' => $_GPC['price'], 'expresssn' => $_GPC['expresssn']), array('weid' => $_W['uniacid'], 'sid' => $sid, 'id' => $_GPC['id']));
        if ($res) {
            load()->model('mc');
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and id=:id ';
            $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':id' => $_GPC['id']));
            $openid = mc_uid2openid($item['cuid']);
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => $item['price'] > 0 ? '亲，您的快件已接单，请支付' : '亲，您的快件已接单，请到店支付', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $item['price'] > 0 ? '快件单号' . $item['expresssn'] . ' 费用:' . $item['price'] : '快件单号' . $item['expresssn'], 'color' => $textcolor), 'remark' => array('value' => '请尽快确认并处理,谢谢.'));
                $url = $this->createMobileurl('expressp', array('op' => 'myexpress', 'cate' => 1));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg('亲，您的快件已接单，', '请尽快确认并处理,谢谢.', $url, '', $openid);
                }
            }
            show_json(1, '操作成功');
        }
        show_json(0, '操作失败');
    }
    show_json(0, '操作错误');
} elseif ($operation == 'posttake') {
    if ($_W['isajax']) {
        if (empty($_GPC['id'])) {
            show_json(0, '取件码不能为空');
        }
        $res = pdo_update('rhinfo_zycj_express', array('status' => 1, 'taketime' => TIMESTAMP, 'takeuid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'id' => $_GPC['id']));
        if (!empty($res)) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and id=:id ';
            $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':id' => $_GPC['id']));
            $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
            $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $item['mobile']));
            load()->model('mc');
            $openid = mc_uid2openid($member['uid']);
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => '亲，您的快件已成功取件', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号:' . $item['expresssn'], 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持.'));
                $url = $this->createMobileurl($mydo, array('op' => 'myexpress', 'cate' => 2));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                }
            }
            show_json(1, '核销成功');
        } else {
            show_json(0, '核销失败');
        }
    }
    show_json(0, '操作错误');
} elseif ($operation == 'charge') {
    $paytype = $this->paytype;
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if ($_W['isajax']) {
        $payfees = floatval($_GPC['payfee']);
        if (!($payfees > 0)) {
            echo '支付金额错误';
            exit(0);
        }
        $data['weid'] = $_W['uniacid'];
        $data['uid'] = $_W['member']['uid'];
        $data['openid'] = $_W['openid'];
        $data['title'] = $express['title'];
        if ($_GPC['isscan'] == 1 && !empty($_GPC['auth_code'])) {
            if (substr($_GPC['auth_code'], 0, 2) == '28') {
                $data['paytype'] = $paytype[2];
                $mypaytype = 2;
            } else {
                $data['paytype'] = $paytype[1];
                $mypaytype = 1;
            }
        } else {
            $data['paytype'] = $paytype[$_GPC['paytype']];
            $mypaytype = $_GPC['paytype'];
        }
        $data['billid'] = $_GPC['bid'];
        $data['ctime'] = TIMESTAMP;
        $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
        $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
        $payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
        $payno = createnum(substr($payno, strlen($paynopre), 14));
        $payno = $paynopre . $payno;
        $data['tid'] = $payno . random(8, 1);
        $data['payno'] = $payno;
        $data['fee'] = $payfees;
        $data['status'] = 0;
        $data['pid'] = 0;
        $data['rid'] = 0;
        $data['bid'] = 0;
        $data['sid'] = $sid;
        $data['feetype'] = 110;
        pdo_insert('rhinfo_zyxq_paylog', $data);
        $logid = pdo_insertid();
        if ($_GPC['isscan'] == 1) {
            $params = array('title' => $data['title'], 'tid' => $data['tid'], 'fee' => $data['fee']);
            $params['uniontid'] = $params['tid'];
            $params['out_trade_no'] = $params['tid'];
            $params['total_amount'] = $params['fee'];
            $params['subject'] = $params['title'];
            $params['body'] = $_W['uniacid'] . ':2';
            $params['logid'] = $logid;
            $params['auth_code'] = $_GPC['auth_code'];
            $params['clientip'] = $_W['clientip'];
            $res = $this->my_scancode_pay($params, array(), $store);
            if ($res['errno'] == 1) {
                echo $res['message'];
                exit(0);
            }
        }
        pdo_update('rhinfo_zyxq_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $logid));
        show_json(1, '收款成功');
    }
    $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
    $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
    $payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
    $payno = createnum(substr($payno, strlen($paynopre), 14));
    $payno = $paynopre . $payno;
    include $this->mymtpl('charge');
} elseif ($operation == 'mindex') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    $sid = $res['sid'];
    if ($res['from'] == 'store') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        load()->model('mc');
        $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
        $ismanager = true;
        include $this->mymtpl('mindex');
    } elseif ($res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        load()->model('mc');
        $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
        include $this->mymtpl('mindex');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'get_today') {
    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2 and (ctime between :ctime1 and :ctime2)';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=1 and (ctime between :ctime1 and :ctime2)';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2 and status=1 and (taketime between :ctime1 and :ctime2)';
    $params = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $today_repair = pdo_fetchcolumn($sql1, $params);
    $today_suggest = pdo_fetchcolumn($sql2, $params);
    $today_member = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('today_repair' => $today_repair . '件', 'today_suggest' => $today_suggest . '件', 'today_member' => $today_member . '件'));
} elseif ($operation == 'get_express') {
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2  and status=0';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=1 and status=0';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2 and status=0 and smsstatus=0';
    $params = array(':weid' => $_W['uniacid'], ':sid' => $sid);
    $express_take = pdo_fetchcolumn($sql1, $params);
    $express_send = pdo_fetchcolumn($sql2, $params);
    $express_notice = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('express_take' => $express_take, 'express_send' => $express_send, 'express_notice' => $express_notice));
} elseif ($operation == 'get_fee') {
    $starttime30 = strtotime('now -30days');
    $starttime7 = strtotime('now -7days');
    $endtime = TIMESTAMP;
    $sql1 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and (feetype=10 or feetype=110) and sid=:sid and status=1 and paytime>0 and paytime between :starttime and :endtime ';
    $sql2 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and (feetype=10 or feetype=110) and sid=:sid and status=1 and paytime>0 and paytime between :starttime and :endtime ';
    $sql3 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and (feetype=10 or feetype=110) and sid=:sid and status=1 and paytime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paytime),\'%y-%m-%d\')) =0';
    $payfee_week = pdo_fetchcolumn($sql1, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $starttime7, ':endtime' => $endtime));
    $payfee_week = empty($payfee_week) ? 0 : $payfee_week;
    $payfee_month = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $starttime30, ':endtime' => $endtime));
    $payfee_month = empty($payfee_month) ? 0 : $payfee_month;
    $payfee_today = pdo_fetchcolumn($sql3, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $payfee_today = empty($payfee_today) ? 0 : $payfee_today;
    show_json(1, array('payfee_week' => $payfee_week, 'payfee_month' => $payfee_month, 'payfee_today' => $payfee_today));
} elseif ($operation == 'get_comp_fee') {
    $starttime30 = strtotime('now -30days');
    $endtime = TIMESTAMP;
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=0 and io=2';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=0 and io=1';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=1 and balancetime>0 and io=2 and balancetime between :starttime and :endtime ';
    $sql4 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=1 and balancetime>0 and io=1 and balancetime between :starttime and :endtime ';
    $sql5 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=1 and balancetime>0 and io=2 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(balancetime),\'%y-%m-%d\')) =0';
    $sql6 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and balancestatus=1 and balancetime>0 and io=1 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(balancetime),\'%y-%m-%d\')) =0';
    $payfee_no2 = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid));
    $payfee_no2 = empty($payfee_no2) ? 0 : $payfee_no2;
    $payfee_no1 = pdo_fetchcolumn($sql1, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid));
    $payfee_no1 = empty($payfee_no1) ? 0 : $payfee_no1;
    $payfee_month2 = pdo_fetchcolumn($sql3, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid, ':starttime' => $starttime30, ':endtime' => $endtime));
    $payfee_month2 = empty($payfee_month2) ? 0 : $payfee_month2;
    $payfee_month1 = pdo_fetchcolumn($sql4, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid, ':starttime' => $starttime30, ':endtime' => $endtime));
    $payfee_month1 = empty($payfee_month1) ? 0 : $payfee_month1;
    $payfee_today2 = pdo_fetchcolumn($sql5, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid));
    $payfee_today2 = empty($payfee_today2) ? 0 : $payfee_today2;
    $payfee_today1 = pdo_fetchcolumn($sql6, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid));
    $payfee_today1 = empty($payfee_today1) ? 0 : $payfee_today1;
    show_json(1, array('payfee_month2' => $payfee_month2, 'payfee_month1' => $payfee_month1, 'payfee_today2' => $payfee_today2, 'payfee_today1' => $payfee_today1, 'payfee_no2' => $payfee_no2, 'payfee_no1' => $payfee_no1));
} elseif ($operation == 'myset') {
    if ($_W['isajax']) {
        if (empty($_GPC['title'])) {
            show_json(0, '请输入译站名称');
        }
        if (empty($_GPC['telphone'])) {
            show_json(0, '请输入电话');
        }
        if (empty($_GPC['mobile'])) {
            show_json(0, '请输入手机号码');
        }
        $data = array('title' => $_GPC['title'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb'], 'istplmsg' => $_GPC['istplmsg'], 'issmsmsg' => $_GPC['issmsmsg']);
        $res = pdo_update('rhinfo_zycj_express_store', $data, array('weid' => $_W['uniacid'], 'id' => $sid));
        show_json(1);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if ($store['uid'] == $_W['member']['uid']) {
        include $this->mymtpl('myset');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'qrcode') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    if ($res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $qrcode_url = $this->createMobileurl($mydo, array('op' => 'pay', 'sid' => $sid));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
        include $this->mymtpl('qrcode');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有译站权限', 'close');
    }
} elseif ($operation == 'payfee') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and (feetype=10 or feetype=110) and sid=:sid ';
        $params[':sid'] = $sid;
        if (!empty($_GPC['paydate'])) {
            $condition .= ' and paytime >=' . strtotime($_GPC['paydate']);
        }
        $condition .= ' and status =1 ';
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_paylog') . ' where ' . $condition, $params);
        $condition .= ' order by paytime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_paylog') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['paydate'] = empty($list[$k]['paytime']) ? '' : date('Y-m-d', $list[$k]['paytime']);
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if ($store['uid'] == $_W['member']['uid']) {
        include $this->mymtpl('payfee');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'express' || $operation == 'expresse') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and sid=:sid ';
        $params[':sid'] = $sid;
        if ($_GPC['cate'] == 1) {
            if ($_GPC['today'] == 1) {
                $condition .= ' and io=1 ';
            } else {
                $condition .= ' and io=1 and status =0';
            }
        } elseif ($_GPC['cate'] == 2) {
            if ($_GPC['today'] == 1) {
                $condition .= ' and io=2 ';
            } else {
                $condition .= ' and io=2 and status =0';
            }
        } elseif ($_GPC['cate'] == 3) {
            $condition .= ' and io=2 and smsstatus =0 and status =0';
        } elseif ($_GPC['cate'] == 4) {
            $condition .= ' and io=2 and status =1';
            if (!empty($_GPC['ctime'])) {
                if ($_GPC['today'] == 1) {
                    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
                    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
                    $condition .= ' and (taketime between :ctime1 and :ctime2)';
                    $params[':ctime1'] = $ctime1;
                    $params[':ctime2'] = $ctime2;
                } else {
                    $condition .= ' and taketime >=' . strtotime($_GPC['taketime']);
                }
            }
        }
        if ($_GPC['cate'] == 1 || $_GPC['cate'] == 2 || $_GPC['cate'] == 3) {
            if (!empty($_GPC['ctime'])) {
                if ($_GPC['today'] == 1) {
                    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
                    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
                    $condition .= ' and (ctime between :ctime1 and :ctime2)';
                    $params[':ctime1'] = $ctime1;
                    $params[':ctime2'] = $ctime2;
                } else {
                    $condition .= ' and ctime >=' . strtotime($_GPC['ctime']);
                }
            }
            if (!empty($_GPC['keyword'])) {
                $condition .= ' and (expresssn like "%' . $_GPC['keyword'] . '%" or mobile like "%' . $_GPC['keyword'] . '%" or orderno like "%' . $_GPC['keyword'] . '%")';
            }
        }
        $editurl = $this->createMobileUrl($mydo, array('op' => 'edit', 'sid' => $sid));
        if ($_GPC['cfrom'] == 'express') {
            $condition .= ' and cuid=' . $_W['member']['uid'];
            $editurl = $this->createMobileUrl($mydo, array('op' => 'edite', 'sid' => $sid));
        }
        if ($_GPC['cfrom'] == 'company') {
            $editurl = $this->createMobileUrl($mydo, array('op' => 'edite', 'sid' => $sid));
        }
        if (!empty($_GPC['compid'])) {
            $condition .= ' and compid=' . $_GPC['compid'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
            $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
            $list[$k]['thumb'] = tomedia($company['thumb']);
            $list[$k]['comptitle'] = $company['title'];
            if ($_GPC['cate'] == 1) {
                $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'senddetail', 'expressid' => $list[$k]['id'], 'sid' => $sid));
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and cabid=:cabid and id=:stid';
                $list[$k]['local'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':cabid' => $list[$k]['cabid'], ':stid' => $list[$k]['stid']));
                $list[$k]['url'] = '';
                $list[$k]['taketime'] = !empty($list[$k]['taketime']) ? date('Y-m-d H:i', $list[$k]['taketime']) : '';
            }
            $sql = 'select address,realname from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and mobile=:mobile and deleted=0 and status=0';
            $mymember = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $store['rid'], ':mobile' => $list[$k]['mobile']));
            $list[$k]['address'] = $mymember['address'];
            $list[$k]['realname'] = $mymember['realname'];
            $list[$k]['cfrom'] = $_GPC['cfrom'];
            $list[$k]['eurl'] = $editurl . '&expressid=' . $list[$k]['id'];
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if ($operation == 'express') {
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'expresse') {
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'company' || $res['from'] == 'express' || $res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        if ($_GPC['cate'] == 1) {
            include $this->mymtpl('express_send');
        } elseif ($_GPC['cate'] == 2 || $_GPC['cate'] == 4) {
            include $this->mymtpl('express_take');
        } elseif ($_GPC['cate'] == 3) {
            include $this->mymtpl('express_notice');
        } else {
            $this->mymsg('error', '温馨提示', '操作类别为空', 'close');
        }
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'tabexpress' || $operation == 'tabexpresse') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and sid=:sid ';
        $params[':sid'] = $sid;
        if ($_GPC['cate'] == 1) {
            $condition .= ' and io=1 ';
        } elseif ($_GPC['cate'] == 2) {
            $condition .= ' and io=2 ';
        }
        if (!empty($_GPC['ctime'])) {
            $condition .= ' and ctime >=' . strtotime($_GPC['ctime']);
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and (expresssn like "%' . $_GPC['keyword'] . '%" or mobile like "%' . $_GPC['keyword'] . '%" or orderno like "%' . $_GPC['keyword'] . '%")';
        }
        if (!empty($_GPC['cuid'])) {
            $condition .= ' and cuid=' . $_GPC['cuid'];
        }
        if (!empty($_GPC['compid'])) {
            $condition .= ' and compid=' . $_GPC['compid'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        if ($_GPC['cate'] == 1) {
            $k = 0;
            while (!($k >= count($list))) {
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
                $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
                $list[$k]['thumb'] = tomedia($company['thumb']);
                $list[$k]['comptitle'] = $company['title'];
                $list[$k]['taketime'] = empty($list[$k]['taketime']) ? '' : date('Y-m-d H:i', $list[$k]['taketime']);
                $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'senddetail', 'expressid' => $list[$k]['id'], 'sid' => $sid));
                ($k += 1) + -1;
            }
        } elseif ($_GPC['cate'] == 2) {
            $k = 0;
            while (!($k >= count($list))) {
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
                $sql = 'select title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and cabid=:cabid and id=:stid';
                $list[$k]['local'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':cabid' => $list[$k]['cabid'], ':stid' => $list[$k]['stid']));
                $list[$k]['taketime'] = empty($list[$k]['taketime']) ? '' : date('Y-m-d H:i', $list[$k]['taketime']);
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
                $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
                $list[$k]['thumb'] = tomedia($company['thumb']);
                $list[$k]['comptitle'] = $company['title'];
                ($k += 1) + -1;
            }
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if ($operation == 'tabexpress') {
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'tabexpresse') {
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'company' || $res['from'] == 'express' || $res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $_GPC['cate'] = empty($_GPC['cate']) ? 2 : $_GPC['cate'];
        include $this->mymtpl('express');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'eindex') {
    $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    $sid = $res['sid'];
    $compid = $res['compid'];
    if ($res['from'] == 'company') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        $person = array();
        $sql = 'select distinct sid from ' . tablename('rhinfo_zycj_express_storecomp') . ' where weid = :weid and compid=:compid';
        $storecomps = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        if (!empty($storecomps)) {
            $k = 0;
            while (!($k >= count($storecomps))) {
                $sql = 'select id, thumb,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
                $mystore = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $storecomps[$k]['sid']));
                $storecomps[$k]['title'] = $mystore['title'];
                $storecomps[$k]['thumb'] = $mystore['thumb'];
                ($k += 1) + -1;
            }
        }
        $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid and b.id=:compid and  b.uid=:uid';
        $excomps = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid, ':uid' => $uid));
        include $this->mymtpl('eindex');
    } elseif ($res['from'] == 'express') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.expid=b.id  where a.weid=:weid and a.sid=:sid and a.compid=:compid and b.uid = :uid';
        $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $compid, ':uid' => $_W['member']['uid']));
        $sql = 'select distinct sid from ' . tablename('rhinfo_zycj_express_storecomp') . ' where weid = :weid and compid=:compid';
        $storecomps = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':compid' => $compid));
        if (!empty($storecomps)) {
            $k = 0;
            while (!($k >= count($storecomps))) {
                $sql = 'select id, thumb,title from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
                $mystore = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $storecomps[$k]['sid']));
                $storecomps[$k]['title'] = $mystore['title'];
                $storecomps[$k]['thumb'] = $mystore['thumb'];
                ($k += 1) + -1;
            }
        }
        $sql = 'select c.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_person') . ' as b on a.expid=b.id  left join ' . tablename('rhinfo_zycj_express_company') . ' as c on b.compid=c.id where a.weid=:weid and b.uid = :uid and a.sid=:sid';
        $excomps = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':sid' => $sid));
        include $this->mymtpl('eindex');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'get_uid_today') {
    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2 and compid=:compid and cuid=:uid and ctime between :ctime1 and :ctime2';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=1 and compid=:compid and cuid=:uid and ctime between :ctime1 and :ctime2';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and io=2 and status=1 and compid=:compid and cuid=:uid and taketime between :ctime1 and :ctime2';
    $params = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $_GPC['compid'], ':uid' => $_W['member']['uid'], ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $params2 = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $_GPC['compid'], ':uid' => $_W['member']['uid'], ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $today_repair = pdo_fetchcolumn($sql1, $params);
    $today_suggest = pdo_fetchcolumn($sql2, $params2);
    $today_member = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('today_repair' => $today_repair . '件', 'today_suggest' => $today_suggest . '件', 'today_member' => $today_member . '件'));
} elseif ($operation == 'employee') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    $sid = $res['sid'];
    if ($res['from'] == 'store') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_employee') . ' where weid=:weid and sid = :sid';
        $employee = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $k = 0;
        while (!($k >= count($employee))) {
            $fans = mc_fansinfo($employee[$k]['uid'], $_W['acid'], $_W['uniacid']);
            $employee[$k]['avatar'] = empty($fans['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $fans['avatar'];
            ($k += 1) + -1;
        }
        include $this->mymtpl('employee');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'delemp') {
    if ($_W['isajax']) {
        if (empty($_GPC['id'])) {
            show_json(0, '未找到此员工');
        }
        $res = pdo_delete('rhinfo_zycj_express_employee', array('weid' => $_W['uniacid'], 'id' => $_GPC['id']));
        if ($res) {
            show_json(1, '删除成功');
        } else {
            show_json(0, '删除失败');
        }
    }
    show_json(0, '操作错误');
} elseif ($operation == 'mobilenext') {
    if ($_W['ispost']) {
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'addemp', 'mobile' => $_GPC['mobile'], 'sid' => $_GPC['sid'])));
        exit(0);
    }
    include $this->mymtpl('mobilenext');
} elseif ($operation == 'checkmobile') {
    $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and mobile = :mobile';
    $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    if (empty($member)) {
        show_json(0, '未找到用户，请确认手机号是否正确');
    }
    show_json(1);
} elseif ($operation == 'addemp') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and uid = :uid';
        $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_GPC['uid']));
        if (empty($member)) {
            show_json(0, '未找到用户');
        }
        $data = array('weid' => $_W['uniacid'], 'title' => $_GPC['title'], 'mobile' => $_GPC['mobile'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'sid' => $_GPC['sid'], 'status' => 1, 'remark' => $_GPC['remark'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zycj_express_employee', $data);
        $id = pdo_insertid();
        if ($res) {
            show_json(1, '添加成功');
        } else {
            show_json(0, '添加失败');
        }
    }
    $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and mobile = :mobile';
    $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    include $this->mymtpl('addemp');
} elseif ($operation == 'get_compid_express') {
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=2  and status=0';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=1 and status=0';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=2 and status=0 and smsstatus=0';
    $params = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $_GPC['compid']);
    $express_take = pdo_fetchcolumn($sql1, $params);
    $express_send = pdo_fetchcolumn($sql2, $params);
    $express_notice = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('express_take' => $express_take, 'express_send' => $express_send, 'express_notice' => $express_notice));
} elseif ($operation == 'senddetail') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and id = :expressid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':expressid' => $_GPC['expressid']));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :compid';
    $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $item['compid']));
    $item['paytype'] = $item['paytype'] == 1 ? '寄付' : '到付';
    include $this->mymtpl('senddetail');
} elseif ($operation == 'money') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    if ($res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $k = 0;
        while (!($k >= count($list))) {
            $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=2 and balancestatus=0';
            $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=1 and balancestatus=0';
            $params = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $list[$k]['id']);
            $list[$k]['takecount'] = pdo_fetchcolumn($sql1, $params);
            $list[$k]['sendcount'] = pdo_fetchcolumn($sql2, $params);
            $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'express_fee', 'sid' => $sid, 'compid' => $list[$k]['id'], 'bstatus' => 0));
            ($k += 1) + -1;
        }
        $this->syscfg['wxtitle'] = $store['title'];
        include $this->mymtpl('money');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'express_fee' || $operation == 'express_feee') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and sid=:sid ';
        $params[':sid'] = $sid;
        if ($_GPC['bstatus'] == 1) {
            $condition .= ' and balancestatus =1 ';
            if ($_GPC['today'] == 1) {
                $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
                $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
                $condition .= ' and (balancetime between :ctime1 and :ctime2)';
                $params[':ctime1'] = $ctime1;
                $params[':ctime2'] = $ctime2;
            } else {
                $balancetime = strtotime(date('Y-m-d', strtotime('-1 month')));
                $condition .= ' and balancetime > :balancetime';
                $params[':balancetime'] = $balancetime;
            }
        } else {
            $condition .= ' and balancestatus =0 ';
        }
        if ($_GPC['cfrom'] == 'express') {
            $condition .= ' and cuid=' . $_W['member']['uid'];
        }
        $condition .= ' and compid=' . $_GPC['compid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $_GPC['compid']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['thumb'] = tomedia($company['thumb']);
            $list[$k]['comptitle'] = $company['title'];
            $sql = 'select address,realname from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and mobile=:mobile and deleted=0 and status=0';
            $mymember = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $store['rid'], ':mobile' => $list[$k]['mobile']));
            $list[$k]['address'] = $mymember['address'];
            $list[$k]['realname'] = $mymember['realname'];
            $list[$k]['expresssn'] = empty($list[$k]['expresssn']) ? '未接单' : $list[$k]['expresssn'];
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if ($operation == 'express_fee') {
        $res = $this->express_store_rights($sid, $_W['member']['uid']);
    }
    if ($operation == 'express_feee') {
        $res = $this->express_express_rights($sid, $compid, $_W['member']['uid']);
    }
    if ($res['from'] == 'company' || $res['from'] == 'express' || $res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $condition .= ' and sid=:sid ';
        $params[':sid'] = $sid;
        $condition .= ' and balancestatus =0 ';
        if ($res['from'] == 'express') {
            $condition .= ' and cuid=' . $_W['member']['uid'];
        }
        $condition .= ' and compid=' . $_GPC['compid'];
        $data = pdo_fetchall('select id,title from ' . tablename('rhinfo_zycj_express') . ' where ' . $condition, $params);
        $count_take = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where io=2 and ' . $condition, $params);
        $count_send = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where io=1 and ' . $condition, $params);
        $money = $count_take * $store['inprice'] + $count_send * $store['outprice'];
        $this->syscfg['wxtitle'] = $store['title'];
        include $this->mymtpl('express_fee');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'paymoney') {
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $sid = $_GPC['sid'];
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $returl = !empty($item['paysuccessurl']) ? $item['paysuccessurl'] : $returl;
        $params = array('money' => $_GPC['money'], 'title' => '快件结算', 'feetype' => 17, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'sid' => $sid, 'compid' => $_GPC['compid'], 'billids' => $_GPC['billids']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_single_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_single_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    show_json(0, '支付错误');
} elseif ($operation == 'rechargemoney') {
    if ($_W['isajax']) {
        $res = pdo_update('rhinfo_zycj_express', array('balancestatus' => 1, 'balancetime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'sid' => $sid, 'compid' => $_GPC['compid'], 'balancestatus' => 0));
        if ($res) {
            show_json(1);
        } else {
            show_json(0, '核销失败');
        }
    }
    show_json(0, '核销错误');
} elseif ($operation == 'statistics') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    if ($res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        $sql = 'select b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid = :weid and a.sid=:sid';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $startdate = !empty($_GPC['startdate']) ? $_GPC['startdate'] : date('Y-m-d');
        $enddate = !empty($_GPC['enddate']) ? $_GPC['enddate'] : date('Y-m-d');
        $condition = ' and ctime>=' . strtotime($startdate) . ' and ctime<=' . strtotime('+1 days', strtotime($enddate));
        $k = 0;
        while (!($k >= count($list))) {
            $sql1 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=2' . $condition;
            $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_express') . ' where weid = :weid and sid=:sid and compid=:compid and io=1' . $condition;
            $params = array(':weid' => $_W['uniacid'], ':sid' => $sid, ':compid' => $list[$k]['id']);
            $list[$k]['takecount'] = pdo_fetchcolumn($sql1, $params);
            $list[$k]['sendcount'] = pdo_fetchcolumn($sql2, $params);
            ($k += 1) + -1;
        }
        $this->syscfg['wxtitle'] = $store['title'];
        include $this->mymtpl('statistics');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'smslog') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and sid=:sid ';
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and mobile=' . $_GPC['keyword'];
        }
        $params[':sid'] = $sid;
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express_smslog') . ' where ' . $condition, $params);
        $condition .= ' order by id desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express_smslog') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:id';
            $list[$k]['company'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['compid']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    if ($res['from'] == 'store' || $res['from'] == 'employee') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $this->syscfg['wxtitle'] = $store['title'];
        include $this->mymtpl('smslog');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'cabinet') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    $sid = $res['sid'];
    if ($res['from'] == 'store') {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        if ($_W['isajax']) {
            $set = array('url' => 'device/screen/openCabinet', 'token' => $store['cabtoken']);
            $data = array('deviceId' => $_GPC['locksn'], 'jsonData' => array('lockTotalNum' => 1, 'lockAddress' => $_GPC['ctrlcode'], 'commandType' => 'openOne', 'sourceType' => 'O'));
            $res = wondware_http_post($set, $data);
            if ($res['code'] == 0) {
                show_json(1);
            } else {
                show_json(0, $res['msg']);
            }
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid=:weid and sid = :sid';
        $cabinet = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $k = 0;
        while (!($k >= count($cabinet))) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and cabid = :cabid';
            $cabinet[$k]['cabstloca'] = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':cabid' => $cabinet[$k]['id']));
            ($k += 1) + -1;
        }
        include $this->mymtpl('cabinet');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有驿站权限', 'close');
    }
} elseif ($operation == 'get_mobile') {
    $condition .= ' and sid=:sid and status=0 and mobile like \'%' . $_GPC['mobile'] . '%\'';
    $params[':sid'] = $sid;
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express_contact') . ' where ' . $condition;
    $list = pdo_fetchall($sql, $params);
    show_json(1, array('list' => $list, 'total' => count($list)));
}