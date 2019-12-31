<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'charging';
$mydo = 'charging';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
$res = $this->getarrearage($_W['member']['uid']);
if ($res) {
    if ($res['arrearagelimit9']) {
        header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
        exit(0);
    }
}
if ($operation == 'index') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and (rid=0 or rid=:rid)';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $range = empty($_GPC['range']) ? 500 : $_GPC['range'];
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where status =1 and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                if (!(0 >= $range) && !($range >= $distance)) {
                    $isout = true;
                }
                $data[$k]['distance'] = $distance;
            } else {
                $data[$k]['distance'] = 100000;
            }
            $data[$k]['chargingurl'] = $this->createMobileurl($mydo, array('op' => 'detail', 'id' => $data[$k]['id']));
            if (!empty($data[$k]['rid'])) {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'pmap', 'id' => $data[$k]['id'], 'rid' => $data[$k]['rid']));
            } else {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $data[$k]['id']));
            }
            $data[$k]['online'] = 0;
            $data[$k]['useports'] = 0;
            if ($data[$k]['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'checkStas', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $data[$k]['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS' && $rs[1] == '0001') {
                    $data[$k]['online'] = 1;
                }
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'ChannelAll', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $data[$k]['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                $i = 0;
                if ($rs[0] == 'SUCCESS') {
                    $useports = array();
                    $portsta = explode(',', $rs[2]);
                    $m = 0;
                    while (!($m >= count($portsta))) {
                        if ($portsta[$m] == 0) {
                            ($i += 1) + -1;
                        }
                        ($m += 1) + -1;
                    }
                    $data[$k]['useports'] = $i;
                }
            } elseif ($data[$k]['devtype'] == 2) {
                $set = array();
                $set['url'] = '/service/deviceStatus/deviceStatus.do';
                $set['yk_appid'] = $this->syscfg['yk_appid'];
                $set['yk_appkey'] = $this->syscfg['yk_appkey'];
                $post_data = array();
                $post_data['device_code'] = $data[$k]['devicesn'];
                $res = ykdev_http_post($set, $post_data);
                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                    $data[$k]['online'] = $res['status'] == 1 ? 1 : 0;
                }
                $set['url'] = '/service/deviceStatus/queryUsablePort.do';
                $res = ykdev_http_post($set, $post_data);
                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                    $data[$k]['useports'] = $res['port_size'];
                }
            } elseif ($data[$k]['devtype'] == 3) {
                $set = array();
                $set['url'] = 'net.equip.charge.slow.port.query';
                $set['ds_appid'] = $this->syscfg['ds_appid'];
                $set['ds_appkey'] = $this->syscfg['ds_appkey'];
                $post_data = array();
                $post_data['equipCd'] = $data[$k]['devicesn'];
                $res = posei_http_post($set, $post_data);
                if ($res['code'] == 1) {
                    $data[$k]['online'] = 1;
                    $i = 0;
                    $ports = $res['ports'];
                    $m = 0;
                    while (!($m >= count($ports))) {
                        if ($ports['status'] == 1) {
                            ($i += 1) + -1;
                        }
                        ($m += 1) + -1;
                    }
                    $data[$k]['useports'] = $i;
                } else {
                    $data[$k]['online'] = 0;
                }
            } elseif ($data[$k]['devtype'] == 4) {
                $url = 'mx10/Api.php';
                $post_data = array('action' => 'sendFs22', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $data[$k]['devicesn']);
                $post_data['port'] = 'PP';
                $post_data['times'] = '0003';
                $res = Mx_httpPost($url, $post_data);
                if ($res[0] == 'SUCCESS') {
                    $data[$k]['online'] = 1;
                    $i = 0;
                    $ports = $res['ports'];
                    $m = 0;
                    while (!($m >= count($ports))) {
                        if ($ports['status'] == 1) {
                            ($i += 1) + -1;
                        }
                        ($m += 1) + -1;
                    }
                    $data[$k]['useports'] = $i;
                } else {
                    $data[$k]['online'] = 0;
                }
            }
            if ($isout == false) {
                $temp_data[] = $data[$k];
            }
            ($k += 1) + -1;
        }
        $data = multi_array_sort($temp_data, 'distance');
        $start = ($pindex - 1) * $psize;
        if (!empty($data)) {
            $data = array_slice($data, $start, $psize);
        }
        show_json(1, array('list' => $data, 'total' => count($data), 'pagesize' => $psize));
    }
    include $this->mymtpl('list');
} elseif ($operation == 'detail') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['status'] == 0) {
        $this->mymsg('error', '温馨提示', '抱歉，该充电桩暂停使用.', 'close');
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging_rule') . ' where weid=:weid and chargid = :chargid';
    $rules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':chargid' => $id));
    include $this->mymtpl('detail');
} elseif ($operation == 'getports') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $ports = array();
    $useports = array();
    if ($item['devtype'] == 1) {
        $url = 'paycloud2/Api.php';
        $post_data = array('action' => 'ChannelAll', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $item['devicesn']);
        $rs = Mx_httpPost($url, $post_data);
        if ($rs[0] == 'SUCCESS') {
            $useports = array();
            $portsta = explode(',', $rs[2]);
            $m = 0;
            while (!($m >= count($portsta))) {
                if ($portsta[$m] == 0) {
                    $ports[] = array($m + 1, 0, '空闲中');
                } elseif ($portsta[$m] == 1) {
                    $ports[] = array($m + 1, 1, '充电中');
                } elseif ($portsta[$m] == 2) {
                    $ports[] = array($m + 1, 1, '已充满');
                } elseif ($portsta[$m] == 3) {
                    $ports[] = array($m + 1, 1, '超功率');
                } else {
                    $ports[] = array($m + 1, 1, '不可用');
                }
                ($m += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 2) {
        $set = array();
        $set['yk_appid'] = $this->syscfg['yk_appid'];
        $set['yk_appkey'] = $this->syscfg['yk_appkey'];
        $post_data = array();
        $post_data['device_code'] = $item['devicesn'];
        $set['url'] = '/service/deviceStatus/queryUsablePort.do';
        $res = ykdev_http_post($set, $post_data);
        if ($res['return_code'] == 1 && $res['result_code'] == 1) {
            $useports = $res['port'];
            $i = 1;
            while (!($i > $item['ports'])) {
                if (in_array($i, $useports)) {
                    $ports[] = array($i, 0, '空闲中');
                } else {
                    $ports[] = array($i, 1, '不可用');
                }
                ($i += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 3) {
        $set = array();
        $set['url'] = 'net.equip.charge.slow.port.query';
        $set['ds_appid'] = $this->syscfg['ds_appid'];
        $set['ds_appkey'] = $this->syscfg['ds_appkey'];
        $post_data = array();
        $post_data['equipCd'] = $item['devicesn'];
        $res = posei_http_post($set, $post_data);
        if ($res['code'] == 1) {
            $getports = $res['ports'];
            $m = 0;
            while (!($m >= count($getports))) {
                if ($getports[$m]['status'] == 1) {
                    $ports[] = array($m + 1, 0, '空闲中');
                } elseif ($getports[$m]['status'] == 2) {
                    $ports[] = array($m + 1, 1, '充电中');
                } elseif ($getports[$m]['status'] == 3) {
                    $ports[] = array($m + 1, 1, '已禁用');
                } elseif ($getports[$m]['status'] == 4) {
                    $ports[] = array($m + 1, 1, '故障中');
                } else {
                    $ports[] = array($m + 1, 1, '不可用');
                }
                ($m += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 4) {
        $m = 0;
        while (!($m >= intval($item['ports']))) {
            $ports[] = array($m + 1, 0, '空闲中');
            ($m += 1) + -1;
        }
    }
    show_json(1, array('list' => $ports, 'total' => count($ports)));
} elseif ($operation == 'scan') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['status'] == 0) {
        $this->mymsg('error', '温馨提示', '抱歉，该充电桩暂停使用.', 'close');
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging_rule') . ' where weid=:weid and chargid = :chargid';
    $rules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':chargid' => $id));
    $item['online'] = 0;
    $ports = array();
    $useports = array();
    if ($item['devtype'] == 1) {
        $url = 'paycloud2/Api.php';
        $post_data = array('action' => 'ChannelAll', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $item['devicesn']);
        $rs = Mx_httpPost($url, $post_data);
        if ($rs[0] == 'SUCCESS') {
            $portsta = explode(',', $rs[2]);
            $m = 0;
            while (!($m >= count($portsta))) {
                if ($portsta[$m] == 0) {
                    $ports[] = array($m + 1, 0, '空闲中');
                } elseif ($portsta[$m] == 1) {
                    $ports[] = array($m + 1, 1, '充电中');
                } elseif ($portsta[$m] == 2) {
                    $ports[] = array($m + 1, 1, '已充满');
                } elseif ($portsta[$m] == 3) {
                    $ports[] = array($m + 1, 1, '超功率');
                } else {
                    $ports[] = array($m + 1, 1, '不可用');
                }
                ($m += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 2) {
        $set = array();
        $set['yk_appid'] = $this->syscfg['yk_appid'];
        $set['yk_appkey'] = $this->syscfg['yk_appkey'];
        $post_data = array();
        $post_data['device_code'] = $item['devicesn'];
        $set['url'] = '/service/deviceStatus/queryUsablePort.do';
        $res = ykdev_http_post($set, $post_data);
        if ($res['return_code'] == 1 && $res['result_code'] == 1) {
            $useports = $res['port'];
            $i = 1;
            while (!($i > $item['ports'])) {
                if (in_array($i, $useports)) {
                    $ports[] = array($i, 0, '空闲中');
                } else {
                    $ports[] = array($i, 1, '不可用');
                }
                ($i += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 3) {
        $set = array();
        $set['url'] = 'net.equip.charge.slow.port.query';
        $set['ds_appid'] = $this->syscfg['ds_appid'];
        $set['ds_appkey'] = $this->syscfg['ds_appkey'];
        $post_data = array();
        $post_data['equipCd'] = $item['devicesn'];
        $res = posei_http_post($set, $post_data);
        if ($res['code'] == 1) {
            $i = 0;
            $portsta = $res['ports'];
            $m = 0;
            while (!($m >= count($portsta))) {
                if ($portsta[$m]['status'] == 1) {
                    $ports[] = array($m + 1, 0, '空闲中');
                } elseif ($portsta[$m]['status'] == 2) {
                    $ports[] = array($m + 1, 1, '充电中');
                } elseif ($portsta[$m]['status'] == 3) {
                    $ports[] = array($m + 1, 1, '已禁用');
                } elseif ($portsta[$m]['status'] == 4) {
                    $ports[] = array($m + 1, 1, '故障中');
                } else {
                    $ports[] = array($m + 1, 1, '不可用');
                }
                ($m += 1) + -1;
            }
        }
    } elseif ($item['devtype'] == 4) {
        $m = 0;
        while (!($m >= intval($item['ports']))) {
            $ports[] = array($m + 1, 0, '空闲中');
            ($m += 1) + -1;
        }
    }
    include $this->mymtpl('scan');
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
} elseif ($operation == 'pmap') {
    $id = intval($_GPC['id']);
    $rid = intval($_GPC['rid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and rid=:rid and status=1';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('pmap');
} elseif ($operation == 'pay') {
    if ($_W['isajax']) {
        $chargid = $_GPC['chargid'];
        $ruleid = $_GPC['rule'];
        $port = $_GPC['port'];
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
        $charging = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $chargid));
        load()->model('mc');
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
        $behavior = $setting['creditbehaviors'];
        $creditnames = $setting['creditnames'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_rule') . ' where weid=:weid and chargid = :chargid and id=:ruleid';
        $rule = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':chargid' => $chargid, ':ruleid' => $ruleid));
        if ($charging['paytype'] == 2) {
            $rule_price = pdo_get('rhinfo_zycj_charging_rule', array('weid' => $_W['uniacid'], 'chargid' => $chargid), array('price'));
            if (empty($rule_price['price'])) {
                show_json(0, '价格未设置');
            }
        } elseif (empty($rule['price'])) {
            $fee = 0;
            $hour = $rule['hour'];
            $plus = intval($hour);
            $charging_log = array('weid' => $_W['uniacid'], 'chargid' => $chargid, 'title' => $charging['title'], 'port' => $port, 'openid' => $_W['openid'], 'out_trade_no' => date('YmdHis') . random(8, 1), 'fee' => $fee, 'hour' => $hour, 'plus' => $plus, 'status' => 1, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            if ($charging['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'SendIns', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'port' => sprintf('%02d', $port), 'pluse' => sprintf('%02d', $plus), 'device_code' => $charging['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $rs[1]);
                }
            } elseif ($charging['devtype'] == 2) {
                $set = array();
                $set['url'] = '/service/charge/startCharge.do';
                $set['yk_appid'] = $this->syscfg['yk_appid'];
                $set['yk_appkey'] = $this->syscfg['yk_appkey'];
                $post_data = array();
                $post_data['device_code'] = $charging['devicesn'];
                $post_data['out_trade_no'] = date('YmdHis') . random(8, 1);
                $post_data['out_user_id'] = $_W['member']['uid'];
                $post_data['pay_fee'] = intval($fee * 100);
                $post_data['charge_time'] = $hour * 60;
                $post_data['port'] = $port;
                $res = ykdev_http_post($set, $post_data);
                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                    $trade_no = $res['trade_no'];
                    $charging_log['trade_no'] = $trade_no;
                    $charging_log['plus'] = 0;
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $res['error_code']);
                }
            } elseif ($charging['devtype'] == 3) {
                $set = array();
                $set['url'] = 'net.equip.charge.slow.run';
                $set['ds_appid'] = $this->syscfg['ds_appid'];
                $set['ds_appkey'] = $this->syscfg['ds_appkey'];
                $post_data = array();
                $post_data['equipCd'] = $charging['devicesn'];
                $post_data['time'] = $hour * 60;
                $post_data['port'] = $port;
                $res = posei_http_post($set, $post_data);
                if ($res['code'] == 1) {
                    $charging_log['trade_no'] = 'ok';
                    $charging_log['plus'] = 0;
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => '智能充电通知'), 'keyword1' => array('value' => '充电开始', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i'), 'color' => $textcolor), 'keyword3' => array('value' => '还剩充电时间' . $post_data['time'] . '分钟', 'color' => $textcolor), 'remark' => array('value' => '您的爱车已经开始充电，请耐心等待!'));
                    if (!empty($this->syscfg['tplid1'])) {
                        $url = $this->createMobileUrl('charging', array('op' => 'my'));
                        $url = str_replace('addons/rhinfo_zyxq/', '', $this->syscfg['siteurl']) . 'app' . substr($url, 1, strlen($url));
                        $this->send_mysendtplnotice($_W['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                } else {
                    show_json(1, '开启充电失败' . $res['msg']);
                }
            } elseif ($charging['devtype'] == 4) {
                $url = 'mx10/Api.php';
                $post_data = array('action' => 'sendFs24', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $charging['devicesn'], 'port' => sprintf('%02d', $port), 'ctype' => 1, 'fee' => '1', 'ordersn' => $charging_log['out_trade_no']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $rs[1]);
                }
            }
            show_json(1, array('wechat' => ''));
        }
        if ($charging['devtype'] == 1) {
            $fee = $rule['price'];
            $hour = $rule['hour'];
            $plus = intval($hour);
            if ($charging['paytype'] == 2) {
                if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $this->syscfg['credit3'])) {
                    show_json(0, '余额不足，请充值');
                }
                $plus = 10;
                $charging_log = array('weid' => $_W['uniacid'], 'chargid' => $chargid, 'title' => $charging['title'], 'port' => $port, 'openid' => $_W['openid'], 'out_trade_no' => 'afterpay', 'fee' => 0, 'hour' => 0, 'plus' => $plus, 'status' => 0, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'SendIns', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'port' => sprintf('%02d', $port), 'pluse' => sprintf('%02d', $plus), 'device_code' => $charging['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                    show_json(1, array('wechat' => ''));
                } else {
                    show_json(0, '开启充电失败' . $rs[1]);
                }
            }
        } elseif ($charging['devtype'] == 2) {
            $fee = $rule['price'];
            $hour = $rule['hour'];
            $plus = 0;
        } elseif ($charging['devtype'] == 3) {
            $fee = $rule['price'];
            $hour = $rule['hour'];
            $plus = 0;
            if ($charging['paytype'] == 2) {
                if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $this->syscfg['credit3'])) {
                    show_json(0, '余额不足，请充值');
                }
                $plus = 10;
                $charging_log = array('weid' => $_W['uniacid'], 'chargid' => $chargid, 'title' => $charging['title'], 'port' => $port, 'openid' => $_W['openid'], 'out_trade_no' => 'afterpay', 'fee' => 0, 'hour' => 0, 'plus' => $plus, 'status' => 0, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                $set = array();
                $set['url'] = 'net.equip.charge.slow.run';
                $set['ds_appid'] = $this->syscfg['ds_appid'];
                $set['ds_appkey'] = $this->syscfg['ds_appkey'];
                $post_data = array();
                $post_data['equipCd'] = $charging['devicesn'];
                $post_data['time'] = $plus * 60;
                $post_data['port'] = $port;
                $res = posei_http_post($set, $post_data);
                if ($res['code'] == 1) {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                    show_json(1, array('wechat' => ''));
                } else {
                    show_json(1, '开启充电失败' . $res['msg']);
                }
            }
        } elseif ($charging['devtype'] == 4) {
            $fee = $rule['price'];
            $hour = $rule['hour'];
            $plus = intval($hour);
        }
        if ($this->syscfg['devpaytype'] == 1) {
            if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
                show_json(0, '余额不足，请充值');
            }
        } elseif ($this->syscfg['devpaytype'] == 2) {
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $charging['rid']));
            $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
            $params = array('money' => $fee, 'title' => '智能充电', 'feetype' => 5, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'chargid' => $chargid, 'ruleid' => $ruleid, 'port' => $port, 'plus' => $plus, 'pid' => $charging['pid'], 'rid' => $charging['rid']);
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
        } elseif (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $charging['rid']));
            $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
            $params = array('money' => $fee, 'title' => '智能充电', 'feetype' => 5, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'chargid' => $chargid, 'ruleid' => $ruleid, 'port' => $port, 'plus' => $plus, 'pid' => $charging['pid'], 'rid' => $charging['rid']);
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
        } else {
            $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
            $crediturl = $this->my_mobileurl($crediturl);
            mc_credit_update($_W['member']['uid'], 'credit2', 0 - $fee, array(0, '智能充电', 'rhinfo_zyxq'));
            $charging_log = array('weid' => $_W['uniacid'], 'chargid' => $chargid, 'title' => $charging['title'], 'port' => $port, 'openid' => $_W['openid'], 'out_trade_no' => date('YmdHis') . random(8, 1), 'fee' => $fee, 'hour' => $hour, 'plus' => $plus, 'status' => 1, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            if ($charging['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'SendIns', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'port' => sprintf('%02d', $port), 'pluse' => sprintf('%02d', $plus), 'device_code' => $charging['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $rs[1]);
                }
            } elseif ($charging['devtype'] == 2) {
                $set = array();
                $set['url'] = '/service/charge/startCharge.do';
                $set['yk_appid'] = $this->syscfg['yk_appid'];
                $set['yk_appkey'] = $this->syscfg['yk_appkey'];
                $post_data = array();
                $post_data['device_code'] = $charging['devicesn'];
                $post_data['out_trade_no'] = date('YmdHis') . random(8, 1);
                $post_data['out_user_id'] = $_W['member']['uid'];
                $post_data['pay_fee'] = intval($fee * 100);
                $post_data['charge_time'] = $hour * 60;
                $post_data['port'] = $port;
                $res = ykdev_http_post($set, $post_data);
                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                    $trade_no = $res['trade_no'];
                    $charging_log['trade_no'] = $trade_no;
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $res['error_code']);
                }
            } elseif ($charging['devtype'] == 3) {
                $set = array();
                $set['url'] = 'net.equip.charge.slow.run';
                $set['ds_appid'] = $this->syscfg['ds_appid'];
                $set['ds_appkey'] = $this->syscfg['ds_appkey'];
                $post_data = array();
                $post_data['equipCd'] = $charging['devicesn'];
                $post_data['time'] = $hour * 60;
                $post_data['port'] = $port;
                $res = posei_http_post($set, $post_data);
                if ($res['code'] == 1) {
                    $charging_log['trade_no'] = 'ok';
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => '智能充电通知'), 'keyword1' => array('value' => '充电开始', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i'), 'color' => $textcolor), 'keyword3' => array('value' => '还剩充电时间' . $post_data['time'] . '分钟', 'color' => $textcolor), 'remark' => array('value' => '您的爱车已经开始充电，请耐心等待!'));
                    if (!empty($this->syscfg['tplid1'])) {
                        $url = $this->createMobileUrl('charging', array('op' => 'my'));
                        $url = str_replace('addons/rhinfo_zyxq/', '', $this->syscfg['siteurl']) . 'app' . substr($url, 1, strlen($url));
                        $this->send_mysendtplnotice($_W['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                } else {
                    show_json(1, '开启充电失败' . $res['msg']);
                }
            } elseif ($charging['devtype'] == 4) {
                $url = 'mx10/Api.php';
                $post_data = array('action' => 'sendFs24', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $charging['devicesn'], 'port' => sprintf('%02d', $port), 'ctype' => 1, 'fee' => $fee, 'ordersn' => date('mdHis') . random(1, 1));
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $charging_log['trade_no'] = $post_data['ordersn'];
                    pdo_insert('rhinfo_zycj_charging_log', $charging_log);
                } else {
                    show_json(1, '开启充电失败' . $rs[1]);
                }
            }
            mc_notice_credit2($_W['openid'], $_W['member']['uid'], $fee, 0, '智能充电', $crediturl, '点击查看详情');
            show_json(1, array('wechat' => ''));
        }
    }
    show_json(0, '操作异常');
} elseif ($operation == 'my') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
        $charging = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $chargid));
        $set = array();
        $set['url'] = '/service/order/queryOrder.do';
        $set['yk_appid'] = $this->syscfg['yk_appid'];
        $set['yk_appkey'] = $this->syscfg['yk_appkey'];
        $post_data = array();
        $mxurl = 'paycloud2/Api.php';
        $mxpost_data = array('action' => 'Channel', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid']);
        $dsset = array();
        $dsset['url'] = 'net.equip.charge.slow.port.status';
        $dsset['ds_appid'] = $this->syscfg['ds_appid'];
        $dsset['ds_appkey'] = $this->syscfg['ds_appkey'];
        $dspost_data = array();
        $k = 0;
        while (!($k >= count($list))) {
            if ($list[$k]['status'] == 2) {
                $list[$k]['statustxt'] = '充电结束';
                $list[$k]['css'] = 'color:#ccc;';
                $list[$k]['restart'] = '0';
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            } else {
                $timediff = TIMESTAMP - $list[$k]['ctime'];
                $minute = round($timediff / 3600, 2);
                $list[$k]['css'] = '';
                $list[$k]['restart'] = '0';
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
                if ($minute > $list[$k]['hour']) {
                    pdo_update('rhinfo_zycj_charging_log', array('status' => 2), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['statustxt'] = '充电结束';
                    $list[$k]['css'] = 'color:#ccc;';
                } else {
                    $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
                    $charging = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['chargid']));
                    if ($charging['devtype'] == 1) {
                        $mxpost_data['port'] = sprintf('%02d', $list[$k]['port']);
                        $mxpost_data['device_code'] = $charging['devicesn'];
                        if (!empty($list[$k]['trade_no'])) {
                            if ($list[$k]['status'] == 1) {
                                $res = Mx_httpPost($mxurl, $mxpost_data);
                                if ($res[0] == 'SUCCESS') {
                                    $list[$k]['statustxt'] = '正在充电';
                                    $list[$k]['css'] = 'color:#ff9f05;';
                                } else {
                                    $list[$k]['statustxt'] = '未知状态';
                                    $list[$k]['css'] = 'color:#ff9f05;';
                                }
                            } elseif ($list[$k]['status'] == 2) {
                                $list[$k]['statustxt'] = '充电结束';
                                $list[$k]['css'] = 'color:#ccc;';
                            } else {
                                $list[$k]['statustxt'] = '重新开启';
                                $list[$k]['restart'] = '1';
                            }
                        } else {
                            $list[$k]['statustxt'] = '重新开启';
                            $list[$k]['restart'] = '1';
                        }
                    } elseif ($charging['devtype'] == 2) {
                        $post_data['trade_no'] = $list[$k]['trade_no'];
                        if (!empty($list[$k]['trade_no'])) {
                            if ($list[$k]['status'] == 1) {
                                $res = ykdev_http_post($set, $post_data);
                                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                                    if ($res['status'] == 10) {
                                        $list[$k]['statustxt'] = '正在充电';
                                        $list[$k]['css'] = 'color:#ff9f05;';
                                    } else {
                                        $list[$k]['statustxt'] = '充电结束';
                                        $list[$k]['css'] = 'color:#ccc;';
                                        pdo_update('rhinfo_zycj_charging_log', array('status' => 2), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                                    }
                                } else {
                                    $list[$k]['statustxt'] = '未知状态';
                                    $list[$k]['css'] = 'color:#ff9f05;';
                                }
                            } else {
                                $list[$k]['statustxt'] = '充电结束';
                                $list[$k]['css'] = 'color:#ccc;';
                            }
                        } else {
                            $list[$k]['statustxt'] = '重新开启';
                            $list[$k]['restart'] = '1';
                        }
                    } elseif ($charging['devtype'] == 3) {
                        $dspost_data['port'] = $list[$k]['port'];
                        $dspost_data['equipCd'] = $charging['devicesn'];
                        if (!empty($list[$k]['trade_no'])) {
                            if ($list[$k]['status'] == 1) {
                                $res = posei_http_post($dsset, $dspost_data);
                                if ($res['code'] == 1) {
                                    if ($res['data']['value'] > 0) {
                                        $list[$k]['statustxt'] = '正在充电';
                                        $list[$k]['css'] = 'color:#ff9f05;';
                                    }
                                } else {
                                    $list[$k]['statustxt'] = '未知状态';
                                    $list[$k]['css'] = 'color:#ff9f05;';
                                }
                            } elseif ($list[$k]['status'] == 2) {
                                $list[$k]['statustxt'] = '充电结束';
                                $list[$k]['css'] = 'color:#ccc;';
                            } else {
                                $list[$k]['statustxt'] = '重新开启';
                                $list[$k]['restart'] = '1';
                            }
                        } else {
                            $list[$k]['statustxt'] = '重新开启';
                            $list[$k]['restart'] = '1';
                        }
                    }
                }
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('my');
} elseif ($operation == 'restart') {
    if ($_W['isajax']) {
        $logid = $_GPC['logid'];
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where weid=:weid and id=:logid';
        $charging_log = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':logid' => $logid));
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and id=:id';
        $charging = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $charging_log['chargid']));
        if ($charging['devtype'] == 1) {
            $url = 'paycloud2/Api.php';
            $post_data = array('action' => 'SendIns', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'port' => sprintf('%02d', $charging_log['port']), 'pluse' => sprintf('%02d', $charging_log['plus']), 'device_code' => $charging['devicesn']);
            $rs = Mx_httpPost($url, $post_data);
            if ($rs[0] == 'SUCCESS') {
                show_json(0, '开启成功');
            } else {
                show_json(1, '开启充电失败' . $rs[1]);
            }
        } elseif ($charging['devtype'] == 2) {
            $set = array();
            $set['url'] = '/service/charge/startCharge.do';
            $set['yk_appid'] = $this->syscfg['yk_appid'];
            $set['yk_appkey'] = $this->syscfg['yk_appkey'];
            $post_data = array();
            $post_data['device_code'] = $charging['devicesn'];
            $post_data['out_trade_no'] = 'temp' . date('YmdHis') . random(8, 1);
            $post_data['out_user_id'] = $_W['member']['uid'];
            $post_data['pay_fee'] = intval($charging_log['fee'] * 100);
            $post_data['charge_time'] = $charging_log['hour'] * 60;
            $post_data['port'] = $charging_log['port'];
            $res = ykdev_http_post($set, $post_data);
            if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                $trade_no = $res['trade_no'];
                pdo_update('rhinfo_zycj_charging_log', array('trade_no' => $trade_no), array('weid' => $_W['uniacid'], 'id' => $charging_log['id']));
                show_json(0, '开启成功');
            } else {
                show_json(1, '开启充电失败' . $res['error_code']);
            }
        } elseif ($charging['devtype'] == 3) {
            $set = array();
            $set['url'] = 'net.equip.charge.slow.run';
            $set['ds_appid'] = $this->syscfg['ds_appid'];
            $set['ds_appkey'] = $this->syscfg['ds_appkey'];
            $post_data = array();
            $post_data['equipCd'] = $charging['devicesn'];
            $post_data['time'] = $charging_log['hour'] * 60;
            $post_data['port'] = $charging_log['port'];
            $res = posei_http_post($set, $post_data);
            if ($res['code'] == 1) {
                show_json(0, '开启成功');
            } else {
                show_json(1, '开启充电失败' . $res['msg']);
            }
        }
        show_json(1, '开启充电异常');
    }
    show_json(1, '开启充电异常');
} elseif ($operation == 'vipcard') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_charging_vipcard') . ' where weid = :weid and uid=:uid';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl();
} elseif ($operation == 'addcard') {
    if ($_W['isajax']) {
        if (empty($_GPC['cardno'])) {
            show_json(0, '卡号不能为空');
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_vipcard') . ' where weid=:weid and cardno=:cardno';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':cardno' => $_GPC['cardno']));
        if ($total > 0) {
            show_json(0, '卡号已经绑定');
        }
        $data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'cardno' => $_GPC['cardno'], 'status' => 1, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_charging_vipcard', $data);
        $id = pdo_insertid();
        if ($id) {
            show_json(1, '添加成功!');
        } else {
            show_json(0, '添加失败!');
        }
    }
    include $this->mymtpl();
} elseif ($operation == 'delcard') {
    $id = intval($_GPC['id']);
    $res = pdo_delete('rhinfo_zycj_charging_vipcard', array('id' => $id, 'weid' => $_W['uniacid']));
    if ($res) {
        show_json(1);
    }
    show_json(0, '删除失败');
}