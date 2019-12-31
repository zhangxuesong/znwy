<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '基础设置';
$mydo = 'category';
$helpcate = 'list';
$tablename = 'rhinfo_zyxq_region';
$condition = ' weid = :weid and id = :rid ';
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$params = array(':weid' => $mywe['weid'], ':rid' => $rid);
$rights = $this->myrights(2, 'category', 'list');
$category = $_GPC['category'];
$sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
$item = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
$rlist = 'list';
if ($item['category'] == 1) {
    $rlist = 'list';
} elseif ($item['category'] == 2) {
    $rlist = 'blist';
} elseif ($item['category'] == 3) {
    $rlist = 'glist';
} elseif ($itemn['category'] == 4) {
    $rlist = 'mlist';
} elseif ($item['category'] == 5) {
    $rlist = 'alist';
}
$navtitle = $item['title'] . ' > ' . $navtitle;
if ($operation == 'index') {
    $current = '短信设置';
    $myret = 1;
    if ($_W['ispost']) {
        $data = array('bindverify' => $_GPC['bindverify'], 'shareverify' => $_GPC['shareverify'], 'repairverify' => $_GPC['repairverify'], 'suggestverify' => $_GPC['suggestverify'], 'payfeeverify' => $_GPC['payfeeverify']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $smsrecharge = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'smsrecharge', 'rid' => $rid)));
    $smsrecharge_qrcode = $this->createqrcode($smsrecharge);
    include $this->mywtpl('index');
} elseif ($operation == 'base') {
    $current = '基本设置';
    $myret = 1;
    if ($_W['ispost']) {
        $data = array('regbanner' => $_GPC['regbanner'], 'regbannerurl' => $_GPC['regbannerurl'], 'replyimage' => $_GPC['replyimage'], 'replydesc' => $_GPC['replydesc'], 'register' => $_GPC['register'], 'openid' => $_GPC['openid'], 'repairnotice' => $_GPC['repairnotice'], 'suggestnotice' => $_GPC['suggestnotice'], 'repairpnotice' => $_GPC['repairpnotice'], 'tplnoticefirst' => $_GPC['tplnoticefirst'], 'finishdays' => $_GPC['finishdays'], 'thirdauth' => $_GPC['thirdauth'], 'thirdurl' => $_GPC['thirdurl'], 'isrepairdisp' => $_GPC['isrepairdisp'], 'issuggestdisp' => $_GPC['issuggestdisp'], 'nobindlimit' => $_GPC['nobindlimit'], 'forcebind' => $_GPC['forcebind'], 'roomfix' => $_GPC['roomfix'], 'uid' => $_GPC['uid'], 'board_password' => $_GPC['board_password'], 'repairsort' => $_GPC['repairsort']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select nickname from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:weid';
    $item['nickname'] = pdo_fetchcolumn($sql, array(':openid' => $item['openid'], ':weid' => $mywe['weid']));
    include $this->mywtpl('base');
} elseif ($operation == 'fee') {
    $current = '账单设置';
    $myret = 1;
    if ($_W['ispost']) {
        $sealimage = $_GPC['sealimage'];
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file = $_FILES['upfile']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $sealimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $sealimage = '';
            }
        }
        $fsealimage = $_GPC['fsealimage'];
        if (!empty($_FILES['upfile1']['name'])) {
            $tmp_file = $_FILES['upfile1']['tmp_name'];
            $file = $_FILES['upfile1']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $fsealimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $fsealimage = '';
            }
        }
        $data = array('discount' => floatval($_GPC['discount']), 'freebill' => $_GPC['freebill'], 'emptybill' => $_GPC['emptybill'], 'abnbill' => $_GPC['abnbill'], 'feebillmonth' => $_GPC['feebillmonth'], 'isprintfeedetail' => $_GPC['isprintfeedetail'], 'isprintremark' => $_GPC['isprintremark'], 'isprintdate' => $_GPC['isprintdate'], 'feecontrol' => $_GPC['feecontrol'], 'sealimage' => $sealimage, 'fsealimage' => $fsealimage, 'feeshowtype' => $_GPC['feeshowtype'], 'pznopre' => $_GPC['pznopre'], 'latemethod' => $_GPC['latemethod'], 'undodays' => $_GPC['undodays'], 'feebillgrant' => $_GPC['feebillgrant']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('fee');
} elseif ($operation == 'door') {
    $current = '其他设置';
    $myret = 1;
    if ($_W['ispost']) {
        $data = array('doorimage' => $_GPC['doorimage'], 'locationimage' => $_GPC['locationimage'], 'buildingimage' => $_GPC['buildingimage'], 'unitimage' => $_GPC['unitimage'], 'visitimage' => $_GPC['visitimage'], 'service' => $_GPC['service'], 'serviceicon' => $_GPC['serviceicon'], 'servicethumb' => $_GPC['servicethumb'], 'serviceright' => $_GPC['serviceright'], 'servicebottom' => $_GPC['servicebottom'], 'servicepagehome' => $_GPC['servicepagehome'], 'servicepageser' => $_GPC['servicepageser'], 'servicepageste' => $_GPC['servicepageste'], 'arrearagemonths' => $_GPC['arrearagemonths'], 'arrearagelimit1' => $_GPC['arrearagelimit1'], 'arrearagelimit2' => $_GPC['arrearagelimit2'], 'arrearagelimit3' => $_GPC['arrearagelimit3'], 'arrearagelimit4' => $_GPC['arrearagelimit4'], 'arrearagelimit5' => $_GPC['arrearagelimit5'], 'arrearagelimit6' => $_GPC['arrearagelimit6'], 'arrearagelimit7' => $_GPC['arrearagelimit7'], 'arrearagelimit8' => $_GPC['arrearagelimit8'], 'arrearagelimit9' => $_GPC['arrearagelimit9'], 'doordays' => $_GPC['doordays']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('door');
} elseif ($operation == 'market') {
    $current = '策略设置';
    $myret = 1;
    if ($_W['ispost']) {
        $data = array('bindstrategyid' => $_GPC['bindstrategyid'], 'feestrategyid' => $_GPC['feestrategyid'], 'invitestrategyid' => $_GPC['invitestrategyid'], 'bindcredit' => $_GPC['bindcredit'], 'cost' => $_GPC['cost'], 'credit' => $_GPC['credit'], 'paycost' => $_GPC['paycost'], 'paycredit' => $_GPC['paycredit'], 'payrate' => $_GPC['payrate'], 'bindsuccessurl' => $_GPC['bindsuccessurl'], 'paysuccessurl' => $_GPC['paysuccessurl']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_redpacket') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 ';
    $redpackets = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
    include $this->mywtpl('marketing');
} elseif ($operation == 'intelligence') {
    $current = '智慧设置';
    $myret = 1;
    if ($_W['ispost']) {
        $data = array('pc_type' => $_GPC['pc_type'], 'pc_rid' => $_GPC['pc_rid'], 'pc_channelid' => $_GPC['pc_channelid'], 'pc_appid' => $_GPC['pc_appid'], 'pc_secret' => $_GPC['pc_secret'], 'pc_rid' => $_GPC['pc_rid'], 'pc_shopid' => $_GPC['pc_shopid'], 'lifepay_type' => $_GPC['lifepay_type'], 'lifepay_token' => $_GPC['lifepay_token'], 'parklock_type' => $_GPC['parklock_type'], 'doorlock_type' => $_GPC['doorlock_type'], 'thinmoo_token' => $_GPC['thinmoo_token'], 'parklock_token' => $_GPC['parklock_token'], 'doorwxappid' => $_GPC['doorwxappid'], 'doorwxapppage' => $_GPC['doorwxapppage'], 'mailin_appid' => $_GPC['mailin_appid'], 'mailin_secret' => $_GPC['mailin_secret'], 'mailin_token' => $_GPC['mailin_token'], 'aurine_appid' => $_GPC['aurine_appid'], 'aurine_secret' => $_GPC['aurine_secret'], 'aurine_token' => $_GPC['aurine_token'], 'aurine_rid' => $_GPC['aurine_rid'], 'aurine_yun_rid' => $_GPC['aurine_yun_rid']);
        pdo_update($tablename, $data, array('id' => $rid, 'weid' => $mywe['weid']));
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('sms', array('op' => $operation, 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select door_pid from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id = :pid';
    $item['door_pid'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid));
    include $this->mywtpl('intelligence');
} elseif ($operation == 'doorinit') {
    if (empty($_GPC['thinmoo_token'])) {
        echo 'token不能为空';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid));
    myload()->classs('thinmoo');
    array('thinmoo_token' => $_GPC['thinmoo_token']);
    $thinmoo = 'ThinMoo';
    if ($_GPC['isinit'] == '1') {
        $area_data = "{\r\n\t\t\t\t\"id\": \"" . $property['door_pid'] . "\",\r\n\t\t\t\t\"area_code\": \"" . $pid . "\", \r\n\t\t\t\t\"area_name\": \"" . $property['title'] . "\", \r\n\t\t\t\t\"parent_id\": 0\r\n\t\t\t  }";
        $region_data = "{\r\n\t\t\t\t\"name\": \"" . $item['title'] . "\",\r\n\t\t\t\t\"uuid\": \"" . $rid . "\"\r\n\t\t\t  }";
        $res = $thinmoo->region_update($area_data, $region_data, $pid, $rid);
        if (is_error($res)) {
            exit($res['message']);
        }
    } else {
        $area_data = "{\r\n\t\t\t\t\"area_name\": \"" . $property['title'] . "\",\r\n\t\t\t\t\"area_code\": \"" . $pid . "\", \r\n\t\t\t\t\"parent_id\": 0\r\n\t\t\t  }";
        $region_data = "{\r\n\t\t\t\t\"name\": \"" . $item['title'] . "\",\r\n\t\t\t\t\"uuid\": \"" . $rid . "\"\r\n\t\t\t  }";
        $res = $thinmoo->region_init($area_data, $region_data, $pid, $rid);
        if (is_error($res)) {
            exit($res['message']);
        }
    }
    exit('ok');
} elseif ($operation == 'lifeinit') {
    $set = array();
    $set['app_id'] = $this->syspub['alipay_appid'];
    $set['prikey'] = $this->syspub['alipay_rsa2'];
    $set['app_auth_token'] = $_GPC['lifepay_token'];
    if (empty($item['lng']) || empty($item['lat'])) {
        echo '未设定经纬度';
        exit(0);
    }
    $sysconfig = $this->module['config'];
    $sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
    $pcd_code = my_getcityadcode($item['lat'], $item['lng'], $sysconifg['qq_lbskey']);
    if (is_error($pcd_code)) {
        echo $res['message'];
        exit(0);
    }
    if (empty($set['app_id']) || empty($set['prikey']) || empty($set['app_auth_token'])) {
        echo '参数配置错误';
        exit(0);
    }
    if ($_GPC['isinit'] == '1') {
        $set['method'] = 'community.modify';
        $params = "{\r\n\t\t\t\t\"community_id\":\"" . $item['lifepay_rid'] . "\",\r\n\t\t\t\t\"community_name\":\"" . $item['title'] . "\",\r\n\t\t\t\t\"community_address\":\"" . $item['address'] . "\",\r\n\t\t\t\t\"district_code\":\"" . $pcd_code['district_code'] . "\",\r\n\t\t\t\t\"city_code\":\"" . $pcd_code['city_code'] . "\",\r\n\t\t\t\t\"province_code\":\"" . $pcd_code['province_code'] . "\",\r\n\t\t\t\t\"community_locations\":[\"" . $item['lng'] . '|' . $item['lat'] . "\"],\r\n\t\t\t\t\"hotline\":\"" . $item['telphone'] . '"}';
        $res = my_alipay_life($set, $params);
        if (is_error($res)) {
            echo $res['message'];
            exit(0);
        } else {
            $res = json_decode($res, 1);
            $res = $res['alipay_eco_cplife_community_modify_response'];
            if ($res['code'] !== '10000') {
                if (!empty($res['sub_code'])) {
                    echo $res['sub_msg'] . $res['sub_code'];
                } else {
                    echo $res['msg'] . $res['code'];
                }
                exit(0);
            }
        }
        $set['method'] = 'basicservice.modify';
        $params = "{\r\n\t\t\t\t\"community_id\":\"" . $item['lifepay_rid'] . "\",\r\n\t\t\t\t\"service_type\":\"PROPERTY_PAY_BILL_MODE\",\r\n\t\t\t\t\"status\":\"ONLINE\",\r\n\t\t\t\t\"external_invoke_address\":\"" . $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/openalipay/notify.php' . "\"\r\n\t\t\t  }";
        $res = my_alipay_life($set, $params);
        if (is_error($res)) {
            echo $res['message'];
            exit(0);
        } else {
            $res = json_decode($res, 1);
            $res = $res['alipay_eco_cplife_basicservice_modify_response'];
            if ($res['code'] !== '10000') {
                if (!empty($res['sub_code'])) {
                    echo $res['sub_msg'] . $res['sub_code'];
                } else {
                    echo $res['msg'] . $res['code'];
                }
                exit(0);
            }
        }
    } elseif (empty($item['lifepay_rid'])) {
        $set['method'] = 'community.create';
        $params = "{\r\n\t\t\t\t\t\"community_name\":\"" . $item['title'] . "\",\r\n\t\t\t\t\t\"community_address\":\"" . $item['address'] . "\",\r\n\t\t\t\t\t\"district_code\":\"" . $pcd_code['district_code'] . "\",\r\n\t\t\t\t\t\"city_code\":\"" . $pcd_code['city_code'] . "\",\r\n\t\t\t\t\t\"province_code\":\"" . $pcd_code['province_code'] . "\",\r\n\t\t\t\t\t\"community_locations\":[\"" . $item['lng'] . '|' . $item['lat'] . "\"],\r\n\t\t\t\t\t\"hotline\":\"" . $item['telphone'] . '"}';
        $res = my_alipay_life($set, $params);
        if (is_error($res)) {
            echo $res['message'];
            exit(0);
        } else {
            $res = json_decode($res, 1);
            $res = $res['alipay_eco_cplife_community_create_response'];
            if ($res['code'] !== '10000') {
                if (!empty($res['sub_code'])) {
                    echo $res['sub_msg'] . $res['sub_code'];
                } else {
                    echo $res['msg'] . $res['code'];
                }
                exit(0);
            }
            $item['lifepay_rid'] = $res['community_id'];
            $set['method'] = 'basicservice.initialize';
            $params = "{\r\n\t\t\t\t\t\t\"community_id\":\"" . $item['lifepay_rid'] . "\",\r\n\t\t\t\t\t\t\"service_type\":\"PROPERTY_PAY_BILL_MODE\",\r\n\t\t\t\t\t\t\"external_invoke_address\":\"" . $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/openalipay/notify.php' . "\"\r\n\t\t\t\t\t  }";
            $res = my_alipay_life($set, $params);
            if (is_error($res)) {
                echo $res['message'];
                exit(0);
            } else {
                pdo_update('rhinfo_zyxq_region', array('lifepay_rid' => $item['lifepay_rid']), array('weid' => $mywe['weid'], 'id' => $item['id']));
                $res = json_decode($res, 1);
                $res = $res['alipay_eco_cplife_basicservice_initialize_response'];
                if ($res['code'] !== '10000') {
                    if (!empty($res['sub_code'])) {
                        echo $res['sub_msg'] . $res['sub_code'];
                    } else {
                        echo $res['msg'] . $res['code'];
                    }
                    exit(0);
                }
            }
            pdo_update('rhinfo_zyxq_region', array('lifepay_init' => '1'), array('weid' => $mywe['weid'], 'id' => $item['id']));
        }
    } else {
        $set['method'] = 'basicservice.initialize';
        $params = "{\r\n\t\t\t\t\t\"community_id\":\"" . $item['lifepay_rid'] . "\",\r\n\t\t\t\t\t\"service_type\":\"PROPERTY_PAY_BILL_MODE\",\r\n\t\t\t\t\t\"external_invoke_address\":\"" . $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/openalipay/notify.php' . "\"\r\n\t\t\t\t  }";
        $res = my_alipay_life($set, $params);
        if (is_error($res)) {
            echo $res['message'];
            exit(0);
        } else {
            $res = json_decode($res, 1);
            $res = $res['alipay_eco_cplife_basicservice_initialize_response'];
            if ($res['code'] !== '10000') {
                if (!empty($res['sub_code'])) {
                    echo $res['sub_msg'] . $res['sub_code'];
                } else {
                    echo $res['msg'] . $res['code'];
                }
                exit(0);
            }
        }
        pdo_update('rhinfo_zyxq_region', array('lifepay_init' => '1'), array('weid' => $mywe['weid'], 'id' => $item['id']));
    }
    echo 'ok';
    exit(0);
} elseif ($operation == 'aliqrcode') {
    $set = array();
    $set['app_id'] = $this->syspub['alipay_appid'];
    $set['prikey'] = $this->syspub['alipay_rsa2'];
    $set['app_auth_token'] = $item['lifepay_token'];
    if (empty($set['app_id']) || empty($set['prikey']) || empty($set['app_auth_token'])) {
        echo '参数配置错误';
        exit(0);
    }
    $set['method'] = 'community.details.query';
    $params = "{\r\n\t\t\t\"community_id\":\"" . $item['lifepay_rid'] . "\"\r\n\t\t  }";
    $res = my_alipay_life($set, $params);
    if (is_error($res)) {
        echo $res['message'];
        exit(0);
    } else {
        $res = json_decode($res, 1);
        $res = $res['alipay_eco_cplife_community_details_query_response'];
        if ($res['code'] !== '10000') {
            if (!empty($res['sub_code'])) {
                echo $res['sub_msg'] . $res['sub_code'];
            } else {
                echo $res['msg'] . $res['code'];
            }
            exit(0);
        }
        $url = $res['qr_code_image'];
        header('Location:' . $url);
        exit(0);
    }
} elseif ($operation == 'recharge') {
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region_smslog') . ' where weid = :weid and rid = :rid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region_smslog') . " where weid = :weid and rid = :rid ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
    $pager = pagination($total, $pindex, $psize);
    include $this->mywtpl('smslog');
} elseif ($operation == 'get_mailin_token') {
    if (empty($_GPC['mailin_appid'])) {
        show_json(0, 'APPID不能为空');
    }
    if (empty($_GPC['mailin_secret'])) {
        show_json(0, '密钥不能为空');
    }
    $set = array('mailin_appid' => $_GPC['mailin_appid'], 'mailin_secret' => $_GPC['mailin_secret'], 'mailin_token' => '');
    $post_data = array('m' => 'do', 'f' => 'config', 'a' => 'get_access_token', 'effective_days' => 0);
    $res = mailin_http_post($set, $post_data);
    if ($res['state'] == 1) {
        pdo_update('rhinfo_zyxq_reigon', array('mailin_appid' => $_GPC['mailin_appid'], 'mailin_secret' => $_GPC['mailin_secret'], 'mailin_token' => $res['return_data']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
        show_json(1, $res['return_data']);
    } else {
        show_json(0, $res['return_data']);
    }
} elseif ($operation == 'get_aurine_token') {
    if (empty($_GPC['aurine_appid'])) {
        show_json(0, 'APPID不能为空');
    }
    if (empty($_GPC['aurine_secret'])) {
        show_json(0, '密钥不能为空');
    }
    $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
    $set = array('url' => 'config', 'aurine_appid' => $_GPC['aurine_appid'], 'aurine_secret' => $_GPC['aurine_secret']);
    $res = aurine_http_post($set);
    if ($res['errorCode'] == 1) {
        $token = $res['body']['token'];
        if (empty($region['aurine_rid'])) {
            $set = array('url' => 'community/add', 'aurine_appid' => $_GPC['aurine_appid'], 'aurine_secret' => $_GPC['aurine_secret'], 'aurine_token' => $token);
            $data = array('name' => $region['title'], 'addr' => $region['address'], 'city' => $region['city'], 'district' => $region['district'], 'contact_name' => $region['contact'], 'tel' => $region['telphone'], 'lng' => $region['lng'], 'lat' => $region['lat']);
            $res = aurine_http_post($set, $data);
            if ($res['errorCode'] == 1) {
                $ret = pdo_update('rhinfo_zyxq_reigon', array('aurine_appid' => $_GPC['aurine_appid'], 'aurine_secret' => $_GPC['aurine_secret'], 'aurine_token' => $token, 'aurine_rid' => $res['body']['community_id']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
                if ($ret) {
                    show_json(1, array('aurine_token' => $token, 'aurine_rid' => $res['body']['community_id']));
                } else {
                    show_json(0, '初始化失败');
                }
            } else {
                show_json(0, $res['errorMsg'] . $res['errorCode']);
            }
        }
        show_json(1, array('access_token' => $token));
    } else {
        show_json(0, $res['errorMsg'] . $res['errorCode']);
    }
}