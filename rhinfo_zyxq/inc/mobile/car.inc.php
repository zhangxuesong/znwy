<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'car';
$mydo = 'car';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
if ($operation == 'index') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_car') . ' where id=:id';
    $caritem = pdo_fetch($sql, array(':id' => $id));
    if ($_W['isajax']) {
        @session_start();
        $verifycode = trim($_GPC['verifycode']);
        $key = '__rhinfo_zyxq_car_verifycodesession_' . $_W['uniacid'] . '_' . $_GPC['mobile'];
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        if (empty($_GPC['carno'])) {
            show_json(0, '抱歉,车牌不能为空');
        }
        if (empty($caritem)) {
            show_json(0, '抱歉,业主未登记车牌');
        }
        if (empty($caritem['openid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile';
            $member = pdo_fetch($sql, array(':weid' => $caritem['weid'], ':mobile' => $caritem['mobile']));
            if (empty($member['openid'])) {
                show_json(0, '抱歉,业主未绑定微信');
            }
            $caritem['openid'] = $member['openid'];
            $caritem['uid'] = $member['uid'];
        }
        $data = array('weid' => $caritem['weid'], 'pid' => $caritem['pid'], 'rid' => $caritem['rid'], 'title' => $_GPC['carno'], 'images' => $images, 'fromuid' => $_W['member']['uid'], 'fromopenid' => $_W['openid'], 'touid' => $caritem['uid'], 'toopenid' => $caritem['openid'], 'ownername' => $caritem['ownername'], 'mobile' => $_GPC['mobile'], 'remark' => $_GPC['remark'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_carmove', $data);
        $id = pdo_insertid();
        if ($res) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $caritem['weid'], ':rid' => $caritem['rid']));
            if (!empty($caritem['openid'])) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '亲，您的爱车要挪一下', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '来自' . $_W['fans']['nickname'] . ',车牌' . $_GPC['carno'], 'color' => $textcolor), 'remark' => array('value' => '请尽快确认并处理,谢谢.'));
                $url = $this->createMobileurl('car', array('op' => 'movecfm', 'id' => $id));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($caritem['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg('亲，您的爱车要挪一下', '请尽快确认并处理,谢谢.', $url, '', $caritem['openid']);
                }
            } elseif ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                $ret = $this->send_sms($this->syscfg['smstype'], $caritem['mobile'], $this->syscfg['movecarid'], array('name' => $caritem['ownername'], 'content' => $caritem['carno'], 'phone' => $_GPC['mobile']));
            } else {
                show_json(0, '短信参数配置错误');
            }
            show_json(1, '提交成功,请等待');
        } else {
            show_json(0, '提交失败');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $caritem['carno'] = empty($caritem['carno']) ? $caritem['title'] : $caritem['carno'];
    include $this->mymtpl('index');
} elseif ($operation == 'moveadd') {
    if ($_W['isajax']) {
        @session_start();
        $verifycode = trim($_GPC['verifycode']);
        $key = '__rhinfo_zyxq_car_verifycodesession_' . $_W['uniacid'] . '_' . $_GPC['mobile'];
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        if (empty($_GPC['carno'])) {
            show_json(0, '抱歉,车牌不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_car') . ' where carno=:carno';
        $caritem = pdo_fetch($sql, array(':carno' => $_GPC['carno']));
        if (empty($caritem)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where carno=:carno';
            $caritem = pdo_fetch($sql, array(':carno' => $_GPC['carno']));
            if (empty($caritem)) {
                show_json(0, '抱歉,业主未登记车牌');
            }
        }
        if (empty($caritem['openid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile';
            $member = pdo_fetch($sql, array(':weid' => $caritem['weid'], ':mobile' => $caritem['mobile']));
            if (empty($member['openid'])) {
                show_json(0, '抱歉,业主未绑定微信');
            }
            $caritem['openid'] = $member['openid'];
            $caritem['uid'] = $member['uid'];
        }
        $data = array('weid' => $caritem['weid'], 'pid' => $caritem['pid'], 'rid' => $caritem['rid'], 'title' => $_GPC['carno'], 'images' => $images, 'fromuid' => $_W['member']['uid'], 'fromopenid' => $_W['openid'], 'touid' => $caritem['uid'], 'toopenid' => $caritem['openid'], 'ownername' => $caritem['ownername'], 'mobile' => $_GPC['mobile'], 'remark' => $_GPC['remark'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_carmove', $data);
        $id = pdo_insertid();
        if ($res) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $caritem['weid'], ':rid' => $caritem['rid']));
            if (!empty($caritem['openid'])) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '亲，您的爱车要挪一下', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '来自' . $_W['fans']['nickname'] . ',车牌' . $_GPC['carno'], 'color' => $textcolor), 'remark' => array('value' => '请尽快确认并处理,谢谢.'));
                $url = $this->createMobileurl('car', array('op' => 'movecfm', 'id' => $id));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($caritem['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg('亲，您的爱车要挪一下', '请尽快确认并处理,谢谢.', $url, '', $caritem['openid']);
                }
            } elseif ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                $ret = $this->send_sms($this->syscfg['smstype'], $caritem['mobile'], $this->syscfg['movecarid'], array('name' => $caritem['ownername'], 'content' => $caritem['carno'], 'phone' => $_GPC['mobile']));
            } else {
                show_json(0, '短信参数配置错误');
            }
            show_json(1, '提交成功,请等待');
        } else {
            show_json(0, '提交失败');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    include $this->mymtpl('moveadd');
} elseif ($operation == 'verifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    $key = '__rhinfo_zyxq_car_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    show_json(0, $ret['message']);
} elseif ($operation == 'movecfm') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carmove') . ' where id=:id';
    $item = pdo_fetch($sql, array(':id' => $id));
    if ($_W['isajax']) {
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => '车主回复'), 'keyword1' => array('value' => '亲，非常抱歉给您带来不便', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '来自' . $_W['fans']['nickname'] . ',' . $_GPC['reply'], 'color' => $textcolor), 'remark' => array('value' => '请耐心等待车主处理,谢谢.'));
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($item['fromopenid'], $this->syscfg['tplid1'], $postdata, '', $topcolor);
        }
        show_json(1);
    }
    $images = iunserializer($item['images']);
    load()->model('mc');
    $fans = array();
    $fans = mc_fansinfo($item['fromopenid'], $_W['acid'], $_W['uniacid']);
    include $this->mymtpl('movecfm');
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
} elseif ($operation == 'pmap') {
    $id = intval($_GPC['id']);
    $rid = intval($_GPC['rid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and rid=:rid and status=1';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('pmap');
} elseif ($operation == 'parklist') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $range = empty($_GPC['range']) ? 500 : $_GPC['range'];
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where status =1 and ' . $condition;
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
            if ($_GPC['cfrom'] == 2) {
                $data[$k]['payurl'] = $this->createMobileurl($mydo, array('op' => 'monthcard', 'parkid' => $data[$k]['id'], 'carno' => $_GPC['carno']));
            }
            if ($_GPC['cfrom'] == 3) {
                $data[$k]['payurl'] = $this->createMobileurl($mydo, array('op' => 'parkio', 'lotid' => $data[$k]['id']));
            } else {
                $data[$k]['payurl'] = $this->createMobileurl($mydo, array('op' => 'pay', 'parkid' => $data[$k]['id'], 'carno' => $_GPC['carno']));
            }
            if (!empty($data[$k]['rid'])) {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'pmap', 'id' => $data[$k]['id'], 'rid' => $data[$k]['rid']));
            } else {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $data[$k]['id']));
            }
            $sql = 'select thumb,telphone from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid ';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $data[$k]['rid']));
            $data[$k]['thumb'] = tomedia($region['thumb']);
            $data[$k]['telphone'] = $region['telphone'];
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
    include $this->mymtpl('parklist');
} elseif ($operation == 'carno') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
    $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
    $region = array();
    $user['rtitle'] = '';
    include $this->mymtpl('carno');
} elseif ($operation == 'monthcard') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
    $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        if (empty($park['status'])) {
            show_json(0, '抱歉，暂停在线缴费');
        }
        if ($park['authentication'] > 0) {
            @session_start();
            $verifycode = trim($_GPC['verifycode']);
            $key = '__rhinfo_zyxq_car_verifycodesession_' . $_W['uniacid'] . '_' . $_GPC['mobile'];
            if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
                show_json(0, '验证码错误或已过期');
            }
            if ($park['authentication'] == 2) {
                $url = 'https://api.jisuapi.com/idcardverify/verify?appkey=' . $sysconfig['js_appkey'] . '&idcard=' . $_GPC['idcard'] . '&realname=' . $_GPC['realname'];
                $result = my_curlOpen($url);
                $jsonarr = json_decode($result, true);
                if ($jsonarr['status'] != 0) {
                    show_json(0, $jsonarr['msg']);
                } else {
                    $ret = $jsonarr['result'];
                    if ($ret['verifystatus'] != 0) {
                        show_json(0, $ret['verifymsg']);
                    }
                }
            }
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $park['rid']));
        $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
        $params = array('money' => $_GPC['money'], 'title' => '办理月卡', 'feetype' => 16, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $park['pid'], 'rid' => $park['rid'], 'parkid' => $_GPC['parkid'], 'carno' => $_GPC['carno'], 'startdate' => $_GPC['startdate'], 'enddate' => $_GPC['enddate'], 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'idcard' => $_GPC['idcard'], 'qty' => $_GPC['qty']);
        if (empty($user['mobile'])) {
            pdo_update('mc_members', array('mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'idcard' => $_GPC['idcard']), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        }
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
    if (empty($park)) {
        header('Location:' . $this->createMobileUrl($mydo, array('op' => 'parklist', 'cfrom' => 2)));
        exit(0);
    }
    if (empty($park['status'])) {
        $this->mymsg('error', '温馨提示', '抱歉，暂停在线缴费', 'close');
    }
    $cararr = array();
    $carno = $_GPC['carno'];
    if (!empty($carno)) {
        $carlen = mb_strlen($carno, 'utf-8');
        $k = 0;
        while (!($k >= $carlen)) {
            $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
            ($k += 1) + -1;
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $park['pid'], ':rid' => $park['rid']));
    $startdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime('+1 month'));
    if ($park['monthmethod'] == 1 || $park['monthmethod'] == 2 || $park['monthmethod'] == 3) {
        $enddate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', strtotime('+1 month')))));
    }
    $park['pc_type'] = !empty($park['pc_type']) ? $park['pc_type'] : $region['pc_type'];
    if (!($park['pc_type'] == 1)) {
        if ($park['pc_type'] == 2) {
            if (!empty($park['pc_plotid']) && !empty($carno)) {
                $set = array();
                $set['pc_appid'] = $region['pc_appid'];
                $set['pc_secret'] = $region['pc_secret'];
                $set['url'] = 'api/queryCreatMonthCardData.action';
                $params = array('parkId' => $park['pc_plotid'], 'type' => 1, 'carNum' => $carno);
                $res = ipms_http_post($set, $params);
                if ($res['result'] == 'success') {
                    $res = json_decode(urldecode($res['data']), true);
                    if (!empty($res)) {
                        $res = $res['datas'][0];
                        $startdate = date('Y-m-d', strtotime($res['tpEndTime']));
                        if (!(strtotime($res['tpEndTime']) >= TIMESTAMP)) {
                            $enddate = date('Y-m-d', strtotime('+1 month'));
                        } else {
                            $enddate = date('Y-m-d', strtotime('+1 month', strtotime($res['tpEndTime'])));
                        }
                    }
                }
            }
        } elseif ($park['pc_type'] == 3) {
            $set = array('url' => 'app/getMonInfo.aspx', 'parkno' => $park['pc_plotid'], 'secret' => $park['pc_secret']);
            $params = array('CarNo' => $carno);
            $res = etpcar_http_post($set, $params);
            $moninfo = $res;
            $res = $res['ReMsg'];
            if ($res['ErrNo'] == '0000') {
                $startdate = date('Y-m-d', strtotime($moninfo['StartTime']));
                if (!(strtotime($moninfo['EndTime']) >= TIMESTAMP)) {
                    $enddate = date('Y-m-d', strtotime('+1 month'));
                } else {
                    $enddate = date('Y-m-d', strtotime('+1 month', strtotime($moninfo['EndTime'])));
                }
            }
        } elseif (!($park['pc_type'] == 4)) {
            if ($park['pc_type'] == 5) {
                $set = array('url' => 'parking/findCarList/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $park['pc_secret']);
                $params = array('plantNum' => $carno, 'cardTypeId' => 2, 'page' => 1, 'size' => 1);
                $res = deliyun_http_post($set, $params);
                if ($res['ecode'] == 0) {
                    $res = $res['data'];
                    if (!empty($res)) {
                        $startdate = date('Y-m-d', strtotime($res['beginDate']));
                        if (!(strtotime($res['endDate']) >= TIMESTAMP)) {
                            $enddate = date('Y-m-d', strtotime('+1 month'));
                        } else {
                            $enddate = date('Y-m-d', strtotime('+1 month', strtotime($res['endDate'])));
                        }
                    }
                }
            } elseif ($park['pc_type'] == 8) {
                $res = pdo_get('rhinfo_zyxq_car_whitelist', array('weid' => $_W['uniacid'], 'parklotid' => $park['id'], 'carno' => $carno));
                if (!empty($res)) {
                    $startdate = date('Y-m-d', $res['starttime']);
                    if (!($res['endtime'] >= TIMESTAMP)) {
                        $enddate = date('Y-m-d', strtotime('+1 month'));
                    } else {
                        $enddate = date('Y-m-d', strtotime('+1 month', $res['endtime']));
                    }
                }
            }
        }
    }
    include $this->mymtpl('monthcard');
} elseif ($operation == 'monthcard2') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
    $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        if (empty($park['status'])) {
            show_json(0, '抱歉，暂停在线缴费');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $park['rid']));
        $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
        $params = array('money' => $_GPC['money'], 'title' => '办理月卡', 'feetype' => 19, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $park['pid'], 'rid' => $park['rid'], 'parkid' => $_GPC['parkid'], 'carno' => $_W['member']['uid'], 'startdate' => $_GPC['startdate'], 'enddate' => $_GPC['enddate'], 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'qty' => $_GPC['qty']);
        if (empty($user['mobile'])) {
            pdo_update('mc_members', array('mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname']), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        }
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
    if (empty($park)) {
        header('Location:' . $this->createMobileUrl($mydo, array('op' => 'parklist', 'cfrom' => 2)));
        exit(0);
    }
    if (empty($park['status'])) {
        $this->mymsg('error', '温馨提示', '抱歉，暂停在线缴费', 'close');
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $park['pid'], ':rid' => $park['rid']));
    $startdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime('+1 month'));
    if ($park['monthmethod'] == 1 || $park['monthmethod'] == 2 || $park['monthmethod'] == 3) {
        $enddate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', strtotime('+1 month')))));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and pid=:pid and  rid = :rid and parklotid=:lotid and carno=:uid and category=2 order by id desc';
    $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $park['pid'], ':rid' => $park['rid'], 'lotid' => $park['id'], ':uid' => $_W['member']['uid']));
    if (!empty($monthcar)) {
        $startdate = date('Y-m-d', strtotime($monthcar['starttime']));
        if (!(strtotime($monthcar['endtime']) >= TIMESTAMP)) {
            $enddate = date('Y-m-d');
        } else {
            $enddate = date('Y-m-d', strtotime('+1 month', strtotime($monthcar['endtime'])));
        }
    }
    include $this->mymtpl('monthcard');
} elseif ($operation == 'pay') {
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
        $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
        if (empty($park['status'])) {
            show_json(0, '抱歉，暂停在线缴费');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
        $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
        $params = array('money' => $_GPC['money'], 'title' => '停车缴费', 'feetype' => 15, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'parkid' => $_GPC['parkid'], 'recordid' => $_GPC['recordid'], 'intime' => strtotime($_GPC['intime']), 'carno' => $_GPC['carno']);
        if ($park['pc_type'] == 8) {
            $feeorder = array('weid' => $_W['uniacid'], 'parklotid' => $_GPC['parkid'], 'iologid' => $_GPC['recordid'], 'carno' => $_GPC['carno'], 'fee' => $_GPC['money'], 'status' => 0, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_car_feeorder', $feeorder);
            $feeorderid = pdo_insertid();
            $params['feeorderid'] = $feeorderid;
        }
        if (empty($user['mobile'])) {
            pdo_update('mc_members', array('mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname']), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        }
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
    if (!empty($_GPC['parkid'])) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
        $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and rid=:rid order by lat desc';
        $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    }
    if (empty($park)) {
        header('Location:' . $this->createMobileUrl($mydo, array('op' => 'parklist', 'cfrom' => 1)));
        exit(0);
    }
    if (empty($park['status'])) {
        $this->mymsg('error', '温馨提示', '抱歉，暂停在线缴费', 'close');
    }
    if ($park['parktype'] == 2) {
        header('Location:' . $this->createMobileUrl($mydo, array('op' => 'parkio', 'lotid' => $park['id'])));
        exit(0);
    }
    if (!empty($_GPC['carno'])) {
        $carno = $_GPC['carno'];
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and uid=:uid';
        $mycar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
        if (!empty($mycar['carno'])) {
            $carno = $mycar['carno'];
        } else {
            header('Location:' . $this->createMobileUrl($mydo, array('op' => 'carno', 'parkid' => $park['id'])));
            exit(0);
        }
    }
    $carlen = mb_strlen($carno, 'utf-8');
    $cararr = array();
    $parinfo = array();
    $k = 0;
    while (!($k >= $carlen)) {
        $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $park['pid'], ':rid' => $park['rid']));
    $park['pc_type'] = !empty($park['pc_type']) ? $park['pc_type'] : $region['pc_type'];
    if (!($park['pc_type'] == 1)) {
        if ($park['pc_type'] == 2) {
            $set = array();
            $set['pc_appid'] = $region['pc_appid'];
            $set['pc_secret'] = $region['pc_secret'];
            $set['url'] = 'api/getPaymentRecord.action';
            $params = array('parkId' => $park['pc_plotid'], 'carNum' => $carno);
            $res = ipms_httpsign_post($set, $params);
            if ($res['result'] == 'success') {
                $res = json_decode(urldecode($res['data']), true);
                if (!empty($res['parkingRecordId'])) {
                    $timediff = TIMESTAMP - strtotime($res['carInTime']);
                    $days = intval($timediff / 86400);
                    $hours = intval($timediff % 86400 / 3600);
                    $minutes = intval($timediff % 86400 % 3600 / 60);
                    $money = $res['onlineCost'] / 100;
                    $parkinfo = array('intime' => $res['carInTime'], 'money' => $money, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'recordid' => $res['parkingRecordId']);
                }
            }
        } elseif ($park['pc_type'] == 3) {
            $set = array('url' => 'app/getTmpFee.aspx', 'parkno' => $park['pc_plotid'], 'secret' => $park['pc_secret']);
            $params = array('CarNo' => $carno, 'ParkSize' => 'Big');
            $res = etpcar_http_post($set, $params);
            $order = $res;
            $res = $res['ReMsg'];
            if ($res['ErrNo'] == '0000') {
                $timediff = TIMESTAMP - strtotime($order['InTime']);
                $days = intval($timediff / 86400);
                $hours = intval($timediff % 86400 / 3600);
                $minutes = intval($timediff % 86400 % 3600 / 60);
                $money = $order['MustFee'] / 100;
                $parkinfo = array('intime' => $order['InTime'], 'money' => $money, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'feetype' => $order['FeeType'], 'recordid' => $order['OrderNo']);
            }
        } elseif ($park['pc_type'] == 4) {
            $set = array();
            $set['pc_secret'] = $region['pc_secret'];
            $set['url'] = 'BCStandard/open/pubApi/getCharge';
            $params = array('plateId' => $carno);
            $res = bluecar_http_post($set, $params);
            if ($res['status'] == 'success') {
                $res = $res['datas'];
                if (!empty($res['recordId'])) {
                    $timediff = TIMESTAMP - strtotime($res['inTime']);
                    $days = intval($timediff / 86400);
                    $hours = intval($timediff % 86400 / 3600);
                    $minutes = intval($timediff % 86400 % 3600 / 60);
                    $money = $res['payCharge'] / 100;
                    $parkinfo = array('intime' => $res['inTime'], 'money' => $money, 'days' => $days, 'hours' => $hours, 'recordid' => $res['recordId']);
                }
            }
        } elseif ($park['pc_type'] == 5) {
            $set = array('url' => 'parking/findParkInfo/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $park['pc_secret']);
            $params = array('plantNum' => $carno);
            $res = deliyun_http_post($set, $params);
            if ($res['ecode'] == 0) {
                $res = $res['data'];
                $timediff = TIMESTAMP - strtotime($res['inTime']);
                $days = intval($timediff / 86400);
                $hours = intval($timediff % 86400 / 3600);
                $minutes = intval($timediff % 86400 % 3600 / 60);
                $parkinfo = array('intime' => $res['inTime'], 'money' => $res['chargeMoney'], 'days' => $days, 'hours' => $hours, 'recordid' => $res['iden']);
            }
        } elseif ($park['pc_type'] == 8) {
            $res = pdo_get('rhinfo_zyxq_car_iolog', array('weid' => $_W['uniacid'], 'parklotid' => $park['id'], 'carno' => $carno, 'io' => 1, 'status' => 0));
            if (!empty($res)) {
                $timediff = TIMESTAMP - strtotime($res['intime']);
                $days = intval($timediff / 86400);
                $hours = intval($timediff % 86400 / 3600);
                $minutes = intval($timediff % 86400 % 3600 / 60);
                $sql = 'select * from ' . tablename('rhinfo_zyxq_car_whitelist') . ' where weid = :weid and parklotid=:parklotid and carno=:carno';
                $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parklotid' => $park['id'], ':carno' => $carno));
                if (!empty($monthcar)) {
                    if (!(TIMESTAMP > $monthcar['endtime'])) {
                        $money = 0;
                    }
                } else {
                    $park['minute'] = empty($park['minute']) ? 1 : $park['minute'];
                    if ($park['minute'] > 0 && !(intval($timediff / 60) > $park['minute'])) {
                        $money = 0;
                    } else {
                        $mystoptime = round($timediff / 3600, 2);
                        $price = $park['price'];
                        $stopstart = date('H', $res['intime']);
                        $stopend = date('H', TIMESTAMP);
                        $istoday = 0;
                        if (date('Y-m-d', $res['intime']) == date('Y-m-d')) {
                            $istoday = 1;
                        }
                        if ($park['pricerule'] == 1) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $park['id']));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($stopstart >= $pricerules[$k]['starttime'] && !($stopstart > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($park['pricerule'] == 2) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($mystoptime >= $pricerules[$k]['starttime'] && !($mystoptime > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($park['pricerule'] == 3) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $stopprice = 0;
                            if ($istoday == 1) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if ($stopstart >= $pricerules[$k]['starttime']) {
                                        if (!($stopstart > $pricerules[$k]['endtime'])) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                            if ($istoday == 0) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if (!($stopend > $pricerules[$k]['endtime'])) {
                                        if ($stopend >= $pricerules[$k]['starttime']) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                        }
                        if ($park['unit'] == 1) {
                            $stoptime = round($timediff / 3600, 2);
                            $stoptime -= $days * 24;
                        } else {
                            $stoptime = intval($timediff / 60);
                            $stoptime -= $days * 24 * 60;
                        }
                        if ($park['pricerule'] == 3) {
                            $stopfee = $stopprice;
                        } else {
                            $stopfee = $stoptime * $price * $park['qty'];
                        }
                        if ($days > 0) {
                            $stopfee = $park['dayfee'] > 0 && $stopfee > $park['dayfee'] ? $park['dayfee'] : $stopfee;
                            $stopfee += $days * $park['dayfee'];
                        } else {
                            $stopfee = $park['dayfee'] > 0 && $stopfee > $park['dayfee'] ? $park['dayfee'] : $stopfee;
                        }
                        if ($park['getfee'] == 1) {
                            $stopfee = round($stopfee, 0);
                        } elseif ($park['getfee'] == 2) {
                            $stopfee = !(intval($stopfee) >= $stopfee) ? intval($stopfee) + 1 : intval($stopfee);
                        } elseif ($park['getfee'] == 3) {
                            $stopfee = intval($stopfee);
                        }
                        if ($res['payfee'] > 0) {
                            $money = $stopfee - $res['payfee'];
                        } else {
                            $money = $stopfee;
                        }
                    }
                }
                $parkinfo = array('intime' => $res['intime'], 'money' => $money, 'days' => $days, 'hours' => $hours, 'recordid' => $res['id']);
            }
        }
    }
    include $this->mymtpl('pay');
} elseif ($operation == 'parkio') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:lotid ';
    $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':lotid' => $_GPC['lotid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $park['pid'], ':rid' => $park['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingio') . ' where weid=:weid and lotid=:lotid and status=1 order by io,title';
    $parkios = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $_GPC['lotid']));
    if ($park['parktype'] == 2) {
        $k = 0;
        while (!($k >= count($parkios))) {
            $res = $this->devstatus($this->syscfg['doortype'], $parkios[$k]['locksn']);
            if ($res['code'] == '0') {
                $parkios[$k]['isonline'] = '1';
            } else {
                $parkios[$k]['isonline'] = '2';
            }
            ($k += 1) + -1;
        }
    } else {
        $k = 0;
        while (!($k >= count($parkios))) {
            $parkios[$k]['isonline'] = '1';
            ($k += 1) + -1;
        }
    }
    include $this->mymtpl('parkio');
} elseif ($operation == 'mycar') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_mycar') . ' where weid = :weid and (openid = :openid or uid=:uid)';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl('mycar');
} elseif ($operation == 'parking') {
    if ($_W['isajax']) {
        if ($_GPC['cfrom'] == 'rid') {
            $lists = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_location') . ' where weid=:weid and rid=:rid and category=2', array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        } elseif ($_GPC['cfrom'] == 'lid') {
            $enddate = strtotime('-1 months');
            $lists = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and rid=:rid and lid=:lid and category=0 and (status=0 or (enddate >0 and enddate < :enddate)) ', array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':lid' => $_GPC['lid'], ':enddate' => $enddate));
        }
        show_json(1, array('list' => $lists));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and (uid=:uid or openid=:openid) and category=1 and deleted=0 and status=0 order by isdefault desc';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
    $houses = pdo_fetchall('select rid as value,address as text from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and (uid=:uid or openid=:openid) and category=1 and deleted=0 and status=0', array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
    $locations = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_location') . ' where weid=:weid and rid=:rid and category=2 ', array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    include $this->mymtpl('parking');
} elseif ($operation == 'parklock') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and id=:id';
        $parking = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
        $sql = 'select parklock_type,parklock_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $parking['rid']));
        if ($region['parklock_type'] == 1) {
            $set = array('url' => 'setLock', 'token' => $region['parklock_token']);
            $data = '/' . $parking['lockmac'] . '/' . $_GPC['updown'];
            $res = pshare_http_post2($set, $data);
            if ($res['code'] == 200) {
                $this->parklocklog($_GPC['id'], $res['updownState'], array('code' => 1, 'msg' => '操作成功'));
                show_json(1, '操作成功');
            } else {
                $this->parklocklog($_GPC['id'], 0, array('code' => 1, 'msg' => $res['msg']));
                show_json(0, $res['msg']);
            }
        } else {
            show_json(0, '参数未设置');
        }
    } else {
        show_json(0, '非法操作');
    }
} elseif ($operation == 'parklease') {
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and id=:parkingid ';
        $parking = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkingid' => $_GPC['parkingid']));
        $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'index')));
        $params = array('money' => $_GPC['money'], 'title' => '租赁车位', 'feetype' => 20, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $parking['pid'], 'rid' => $parking['rid'], 'parkingid' => $_GPC['parkingid'], 'carno' => $_GPC['carno'], 'startdate' => $_GPC['startdate'], 'enddate' => $_GPC['enddate'], 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'qty' => $_GPC['qty']);
        if (empty($user['mobile'])) {
            pdo_update('mc_members', array('mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname']), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        }
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
    $startdate = date('Y-m-d');
    $months = !empty($location['feemonths']) ? $location['feemonths'] : 1;
    $enddate = date('Y-m-d', strtotime('+' . $months . ' month'));
    $cararr = array();
    $sql = 'select carno from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and deleted=0 and category=1 and status=0 and (uid=:uid or openid=:openid) order by isdefault desc';
    $carno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
    $cararr = array();
    if (!empty($carno)) {
        $carlen = mb_strlen($carno, 'utf-8');
        $k = 0;
        while (!($k >= $carlen)) {
            $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
            ($k += 1) + -1;
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    $location = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_location') . ' where weid=:weid and rid=:rid and id=:lid ', array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['locationid']));
    $parking = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and rid=:rid and id=:parkid ', array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':parkid' => $_GPC['parkingid']));
    include $this->mymtpl('parkinfo');
} elseif ($operation == 'addlock') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    if ($_W['isajax']) {
        if (empty($_GPC['title'])) {
            show_json(0, '车位名称不能为空');
        }
        if (empty($_GPC['lockmac'])) {
            show_json(0, '车位锁MAC不能为空');
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkinglock') . ' where weid=:weid and lockmac=:lockmac';
        $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':lockmac' => $_GPC['lockmac']));
        if ($count > 0) {
            show_json(0, '车位锁MAC已存在');
        }
        if ($region['parklock_type'] == 1) {
            $set = array('url' => 'getLock', 'token' => $region['parklock_token']);
            $data = '/' . $_GPC['lockmac'];
            $res = pshare_http_post2($set, $data);
            if (!($res['code'] == 200)) {
                show_json(0, '车位锁不在线');
            }
        }
        $data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $myrid, 'parkingid' => $_GPC['parkingid'], 'title' => $_GPC['title'], 'lockmac' => $_GPC['lockmac'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $_W['member']['realname'], 'mobile' => $_W['member']['mobile'], 'lng' => '', 'lat' => '', 'remark' => $_GPC['remark'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_parkinglock', $data);
        if (!empty($res)) {
            if (!empty($_GPC['parkingid'])) {
                pdo_update('rhinfo_zyxq_parking', array('lockmac' => $_GPC['lockmac']), array('weid' => $_W['uniacid'], 'id' => $_GPC['parkingid']));
            }
            show_json(1, '添加成功');
        } else {
            show_json(0, '添加失败');
        }
    }
    if (empty($region)) {
        header('Location:' . $this->createMobileUrl('home', array('op' => 'mlist')));
        exit(0);
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where category=3 and weid = :weid and rid=:rid and (openid = :openid or uid=:uid) and deleted=0 and status=0 ORDER BY isdefault desc, id DESC ';
    $parklist = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl('addlock');
} elseif ($operation == 'lockdelete') {
    $id = intval($_GPC['id']);
    $res = pdo_delete('rhinfo_zyxq_parkinglock', array('id' => $id, 'weid' => $_W['uniacid']));
    if ($res) {
        show_json(1, '删除成功');
    }
    show_json(0, '删除失败');
} elseif ($operation == 'mylock') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglock') . ' where weid = :weid and uid=:uid ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($list))) {
        $region = pdo_fetch('select title,parklock_type, parklock_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid', array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
        if (!empty($region)) {
            $list[$k]['region'] = $region['title'];
            if ($region['parklock_type'] == 1) {
                $set = array('url' => 'getLock', 'token' => $region['parklock_token']);
                $data = '/' . $list[$k]['lockmac'];
                $res = pshare_http_post2($set, $data);
                if ($res['code'] == 200) {
                    $list[$k]['parklock'] = $res['data'];
                }
            }
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parklock_share') . ' where weid = :weid and touid=:uid and starttime <=' . TIMESTAMP . ' and endtime>=' . TIMESTAMP;
    $sharelist = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($sharelist))) {
        $region = pdo_fetch('select title,parklock_type, parklock_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid', array(':weid' => $_W['uniacid'], ':rid' => $sharelist[$k]['rid']));
        $parklock = pdo_get('rhinfo_zyxq_parkinglock', array('weid' => $_W['uniacid'], 'id' => $sharelist[$k]['parklockid']), array('lockmac'));
        if (!empty($region)) {
            $sharelist[$k]['region'] = $region['title'];
            if ($region['parklock_type'] == 1) {
                $set = array('url' => 'getLock', 'token' => $region['parklock_token']);
                $data = '/' . $parklock['lockmac'];
                $res = pshare_http_post2($set, $data);
                if ($res['code'] == 200) {
                    $sharelist[$k]['parklock'] = $res['data'];
                }
            }
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('mylock');
} elseif ($operation == 'ctrllock') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglock') . ' where weid=:weid and id=:id';
        $parking = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
        $sql = 'select parklock_type,parklock_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $parking['rid']));
        if ($region['parklock_type'] == 1) {
            $set = array('url' => 'setLock', 'token' => $region['parklock_token']);
            $data = '/' . $parking['lockmac'] . '/' . $_GPC['updown'];
            $res = pshare_http_post2($set, $data);
            if ($res['code'] == 200) {
                $this->parklocklog($_GPC['id'], $res['updownState'], array('code' => 1, 'msg' => '操作成功'));
                show_json(1, '操作成功');
            } else {
                $this->parklocklog($_GPC['id'], 0, array('code' => 1, 'msg' => $res['msg']));
                show_json(0, $res['msg']);
            }
        } else {
            show_json(0, '参数未设置');
        }
    } else {
        show_json(0, '非法操作');
    }
} elseif ($operation == 'sharepark') {
    $parklockid = intval($_GPC['parklockid']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkinglock') . ' where weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $parklockid));
    $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $item['rid']), array('title'));
    if ($_W['ispost']) {
        $data = array('weid' => $_W['uniacid'], 'parklockid' => $parklockid, 'pid' => $item['pid'], 'rid' => $item['rid'], 'title' => $item['title'], 'fromopenid' => $_W['openid'], 'fromuid' => $_W['member']['uid'], 'starttime' => strtotime($_GPC['starttime']), 'endtime' => strtotime($_GPC['endtime']), 'category' => 0, 'status' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_parklock_share', $data);
        $subid = pdo_insertid();
        $qrcode_url = $this->createMobileurl($mydo, array('op' => 'getgrant', 'id' => $subid));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
        $_share['title'] = $_W['fans']['nickname'] . '授权您车位锁权限';
        $_share['imgUrl'] = MODULE_URL . 'static/mobile/images/key.jpg';
        $_share['desc'] = '快速获取车位锁授权.';
        $_share['link'] = $qrcode_url;
        include $this->mymtpl('sharegrant');
    } else {
        include $this->mymtpl('sharepark');
    }
} elseif ($operation == 'getgrant') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parklock_share') . ' where status=0 and category=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (!empty($item)) {
        pdo_update('rhinfo_zyxq_parklock_share', array('status' => 1, 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'id' => $id));
        if (!($item['endtime'] >= TIMESTAMP)) {
            $this->mymsg('error', '非常抱歉', '有效期已过', 'close');
        } else {
            header('Location:' . $this->createMobileUrl($mydo, array('op' => 'mylock')));
        }
    } else {
        header('Location:' . $this->createMobileUrl($mydo, array('op' => 'mylock')));
    }
} elseif ($operation == 'shareprice') {
    $parklockid = intval($_GPC['parklockid']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parklock_share') . ' where status=0 and  weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $parklockid));
    if (!empty($item)) {
        $res = pdo_update('rhinfo_zyxq_parklock_share', array('category' => 1, 'price' => $_GPC['price'], 'remark' => $_GPC['remark']), array('weid' => $_W['uniacid'], 'id' => $parklockid));
        if (!empty($res)) {
            show_json(1, '操作成功');
        } else {
            show_json(0, '操作失败');
        }
    } else {
        show_json(0, '未找到共享车位');
    }
} elseif ($operation == 'sharelist') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $range = empty($_GPC['range']) ? 500 : $_GPC['range'];
        $temp_data = array();
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parklock_share') . ' where status=0 and category=1 and endtime>' . TIMESTAMP . ' and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $isout = false;
            $sql = 'SELECT title,lat,lng,address FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            if (empty($list[$k]['lat']) || empty($list[$k]['lng'])) {
                $list[$k]['lat'] = $region['lat'];
                $list[$k]['lng'] = $region['lng'];
            }
            if ($lat != 0 && $lng != 0 && !empty($list[$k]['lat']) && !empty($list[$k]['lng'])) {
                $distance = GetDistance($lat, $lng, $list[$k]['lat'], $list[$k]['lng'], 2);
                if (!(0 >= $range) && !($range >= $distance)) {
                    $isout = true;
                }
                $list[$k]['distance'] = $distance;
            } else {
                $list[$k]['distance'] = 100000;
            }
            if (!empty($list[$k]['rid'])) {
                $list[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'lockpmap', 'id' => $list[$k]['id'], 'rid' => $list[$k]['rid']));
            } else {
                $list[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'lockmap', 'id' => $list[$k]['id']));
            }
            $list[$k]['region'] = $region['title'];
            $list[$k]['address'] = $region['address'];
            $list[$k]['starttime'] = date('Y-m-d H:i', $list[$k]['starttime']);
            $list[$k]['endtime'] = date('Y-m-d H:i', $list[$k]['endtime']);
            $member = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $list[$k]['fromuid']), array('avatar', 'nickname', 'mobile'));
            $list[$k]['avatar'] = $member['avatar'];
            $list[$k]['nickname'] = $member['nickname'];
            $list[$k]['mobile'] = $member['mobile'];
            if ($isout == false) {
                $temp_data[] = $list[$k];
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
    include $this->mymtpl('sharelist');
} elseif ($operation == 'lockmap') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parklock_share') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    $region = pdo_get($sql, array('weid' => $_W['uniacid'], 'id' => $item['rid']), array('address', 'lat', 'lng'));
    $item['address'] = $region['address'];
    if (empty($item['lat']) || empty($item['lng'])) {
        $item['lat'] = $region['lat'];
        $item['lng'] = $region['lng'];
    }
    include $this->mymtpl('lockmap');
} elseif ($operation == 'lockpmap') {
    $id = intval($_GPC['id']);
    $rid = intval($_GPC['rid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parklock_share') . ' where weid=:weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($item['lat']) || empty($item['lng'])) {
        $item['lat'] = $region['lat'];
        $item['lng'] = $region['lng'];
    }
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zyxq_parklock_share') . ' where weid=:weid and rid=:rid and status=1';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('lockpmap');
} elseif ($operation == 'sharepay') {
    if ($_W['isajax']) {
        $shareid = $_GPC['shareid'];
        $fee = $_GPC['fee'];
        if (empty($fee) || !($fee > 0)) {
            show_json(0, '价格未设定');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parklock_share') . ' where weid=:weid and id=:id ';
        $parkshare = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $shareid));
        if ($parkshare['status'] == 1) {
            show_json(0, '已经有人预定');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $params = array('money' => $fee, 'title' => '车位共享', 'feetype' => 21, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'shareid' => $shareid, 'pid' => $parkshare['pid'], 'rid' => $parkshare['rid']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    show_json(0, '操作异常');
} elseif ($operation == 'remonthcard') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and id=:id';
    $parkpay = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid=:rid and id=:parklotid';
    $parklot = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $parkpay['rid'], ':parklotid' => $parkpay['parklotid']));
    if ($_W['isajax']) {
        if (!empty($parklot['pc_plotid']) && $parkpay['endtime'] > TIMESTAMP) {
            $res = $this->pc_remonthcard($parkpay['rid'], $parklot, $parkpay);
            show_json($res['code'], $res['msg']);
        } else {
            show_json(0, '操作异常');
        }
    }
    if (empty($parkpay)) {
        $this->mymsg('error', '温馨提示', '未找到相关记录.', 'close');
    }
    if ($parkpay['stauts'] == 1) {
        $this->mymsg('error', '温馨提示', '已经操作成功.', 'close');
    }
    $carlen = mb_strlen($parkpay['carno'], 'utf-8');
    $cararr = array();
    $parinfo = array();
    $k = 0;
    while (!($k >= $carlen)) {
        $cararr[] = mb_substr($parkpay['carno'], $k, 1, 'utf-8');
        ($k += 1) + -1;
    }
    include $this->mymtpl('remonthcard');
} elseif ($operation == 'coupon') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
    $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $_GPC['parkid']));
    $carno = $_GPC['carno'];
    if ($_W['isajax']) {
        if (empty($park['year_coupon']) || empty($park['month_coupon'])) {
            show_json(0, '抱歉，优惠券未开放');
        }
        if ($_GPC['ctype'] == 1) {
            if ($_GPC['qty'] > $park['maxmoney']) {
                show_json(0, '金额不能超过' . $park['maxmoney'] . '元');
            }
        } elseif ($_GPC['qty'] > $park['maxminutes']) {
            show_json(0, '时间不能超过' . $park['maxminutes'] . '分钟');
        }
        $member = pdo_get('rhinfo_zyxq_member', array('rid' => $park['rid'], 'uid' => $_W['member']['uid'], 'deleted' => 0), array('id'));
        if (empty($member)) {
            show_json(0, '非业主不可享受此服务');
        }
        $res = $this->getarrearage($_W['member']['uid']);
        if ($res) {
            show_json(0, '物业欠费不可享受此服务');
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkinglot_coupon') . ' where rid=:rid and parkid=:parkid and uid=:uid ';
        $month_count = pdo_fetchcolumn($sql . 'and ctime>=' . strtotime('now -30days') . ' and ctime<=' . TIMESTAMP, array(':rid' => $park['rid'], ':parkid' => $park['id'], ':uid' => $_W['member']['uid']));
        $month_count = empty($month_count) ? 0 : $month_count;
        if (!($park['month_coupon'] > $month_count)) {
            show_json(0, '30天可领取' . $park['month_coupon'] . ', 您已超出');
        }
        $year_count = pdo_fetchcolumn($sql . 'and ctime>=' . strtotime(date('Y') . '-01-01') . ' and ctime<=' . strtotime(date('Y') . '-12-31'), array(':rid' => $park['rid'], ':parkid' => $park['id'], ':uid' => $_W['member']['uid']));
        $year_count = empty($year_count) ? 0 : $year_count;
        if (!($park['year_coupon'] > $year_count)) {
            show_json(0, '一年内可领取' . $park['year_coupon'] . ', 您已超出');
        }
        $data = array('weid' => $_W['uniacid'], 'pid' => $park['pid'], 'rid' => $park['rid'], 'parkid' => $park['id'], 'uid' => $_W['member']['uid'], 'ctype' => $_GPC['ctype'], 'qty' => $_GPC['qty'], 'starttime' => TIMESTAMP, 'endtime' => strtotime('+1 days'), 'carno' => $carno, 'ctime' => TIMESTAMP);
        $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $park['rid']));
        $park['pc_type'] = !empty($park['pc_type']) ? $park['pc_type'] : $region['pc_type'];
        if ($park['pc_type'] == 2) {
            $set = array();
            $set['pc_appid'] = $region['pc_appid'];
            $set['pc_secret'] = $region['pc_secret'];
            $set['url'] = 'third_api/user_getUser.action';
            $params = array('channelId' => $region['pc_channelid'], 'userPhone' => $user['mobile']);
            $res = ipms_httpsign_post($set, $params);
            if ($res['ret'] != '0') {
                $set['url'] = 'third_api/user_create.action';
                $params = array('channelId' => $region['pc_channelid'], 'userPhone' => $user['mobile'], 'carNum' => $carno);
                $res = ipms_httpsign_post($set, $params);
                if ($res['ret'] != '0') {
                    show_json(0, '领券失败' . $res['retInfo']);
                }
            }
            $set['url'] = 'third_api/shopCouponAction_addAndBildCoupon.action';
            $params = array('shopId' => $region['pc_shopid'], 'parkId' => $park['pc_plotid'], 'carNum' => $carno, 'buildingId' => $region['pc_rid'], 'channelId' => $region['pc_channelid'], 'validBegin' => date('Y-m-d H:i:s'), 'validEnd' => date('Y-m-d H:i:s', strtotime('+1 days')), 'userPhone' => $user['mobile']);
            if ($_GPC['ctype'] == 1) {
                $params['couponStrategy'] = 0;
                $params['couponValue'] = $_GPC['qty'] * 100;
            } else {
                $params['couponStrategy'] = 1;
                $params['couponTime'] = $_GPC['qty'];
            }
            $res = ipms_httpsign_post($set, $params);
            if ($res['ret'] == '0') {
                $data['couponserial'] = $res['serialNumber'];
            } else {
                $this->mysyslog(0, 'error', 'visit', '领取优惠券' . $carno, $res['retInfo']);
                show_json(0, '领券失败' . $res['retInfo']);
            }
        } elseif ($park['pc_type'] == 3) {
            $set = array('url' => 'app/UpTicketNo.aspx', 'parkno' => $park['pc_plotid'], 'secret' => $park['pc_secret']);
            $shopid = $region['pc_shopid'];
            if (empty($shopid)) {
                $set['url'] = 'app/UpCompany.aspx';
                $params = array('OperateType' => 'Add', 'LinkName' => $region['title'], 'CompanyName' => $region['title']);
                $res = etpcar_http_post($set, $params);
                $resmsg = $res['ReMsg'];
                if ($resmsg['ErrNo'] == '0000') {
                    $set['url'] = 'app/getSellerList.aspx';
                    $params = array('CompanyName' => $region['title']);
                    $ret = etpcar_http_post($set, $params);
                    $retmsg = $ret['ReMsg'];
                    if ($retmsg['ErrNo'] == '0000') {
                        $customer = $ret['SellerList']['SellerInfo'];
                        $shopid = $customer['CustomerID'];
                        pdo_update('rhinfo_zyxq_region', array('pc_shopid' => $shopid), array('weid' => $_W['uniacid'], 'id' => $region['id']));
                    } else {
                        $this->mysyslog(0, 'error', 'visit', '创建商家失败', $resmsg['ErrMsg']);
                        show_json(0, '领券失败' . $resmsg['ErrMsg']);
                    }
                } else {
                    $this->mysyslog(0, 'error', 'visit', '创建商家失败', $resmsg['ErrMsg']);
                    show_json(0, '领券失败' . $resmsg['ErrMsg']);
                }
            }
            $params = array('OperateType' => 'Add', 'CarNo' => $carno, 'CarSetNo' => random(8), 'StartTime' => date('YmdHis'), 'EndTime' => date('YmdHis', strtotime('+1 days')), 'CustomerID' => $shopid);
            if ($_GPC['ctype'] == 1) {
                $params['DistType'] = 2;
                $params['DistFee'] = $_GPC['qty'] * 100;
            } else {
                $params['DistType'] = 3;
                $params['DistFee'] = $_GPC['qty'];
            }
            $res = etpcar_http_post($set, $params);
            $resmsg = $res['ReMsg'];
            if (!($resmsg['ErrNo'] == '0000')) {
                $this->mysyslog(0, 'error', 'visit', '领取优惠券' . $carno, $resmsg['ErrMsg']);
                show_json(0, '领券失败' . $resmsg['ErrMsg']);
            }
        } elseif ($park['pc_type'] == 5) {
            $set = array('url' => '/parkCarBook/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $park['pc_secret']);
            $params = array('plateNum' => $carno, 'parkNo' => random(8), 'payState' => 1, 'endDate' => strtotime('+1 days'));
            $res = deliyun_http_post($set, $params);
            if (!($res['ecode'] == 0)) {
                $this->mysyslog(0, 'error', 'visit', '领取优惠券' . $carno, $res['msg']);
                show_json(0, '领券失败' . $res['msg']);
            }
        } else {
            show_json(0, '领券失败，参数错误');
        }
        pdo_insert('rhinfo_zyxq_parkinglot_coupon', $data);
        show_json(1, '领券成功');
    }
    $carlen = mb_strlen($carno, 'utf-8');
    $cararr = array();
    $k = 0;
    while (!($k >= $carlen)) {
        $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
        ($k += 1) + -1;
    }
    $parks = pdo_getall('rhinfo_zyxq_parkinglot', array('weid' => $_W['uniacid'], 'rid' => $park['rid']), array('id', 'title', 'address'));
    include $this->mymtpl();
}