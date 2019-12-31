<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'uploader';
$this->my_check_mobile();
if ($operation == 'nowxapp') {
    isetcookie('__regionid', '', 0 - 10000);
    $_W['minirid'] = 0;
    show_json(1);
}
if ($operation == 'scan') {
    if ($_GPC['devtype'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where status=1 and weid = :weid and locksn=:locksn ';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':locksn' => $_GPC['result']));
        if (!empty($item)) {
            if ($item['tid'] > 0) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            } elseif ($item['bid'] > 0) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            } else {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            }
            $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
            if ($total > 0) {
                if ($item['devtype'] == 1 || $item['devtype'] == 2) {
                    if ($item['doortype'] == 5) {
                        $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_net_open_key', 'device_sncode' => $item['locksn']);
                        $result = mailin_http_post($set, $post_data);
                        if ($result['state'] == 1) {
                            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'net_open', 'device_sncode' => $item['locksn'], 'net_open_key' => $result['return_data']['net_open_key']);
                            $ret = mailin_http_post($set, $post_data);
                            if ($return['state'] == 1) {
                                $res['code'] = '0';
                                $res['msg'] = '开门成功';
                            } else {
                                $res['code'] = '1';
                                $res['msg'] = $return['state'] . '-' . $return['$return'];
                            }
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $result['state'] . '-' . $result['return_data'];
                        }
                    } else {
                        $set = array();
                        $set['url'] = '/doormaster/server/remote_control';
                        $set['token'] = $region['thinmoo_token'];
                        $set['op'] = 'OPEN_DOOR';
                        $data = "{\r\n\t\t\t\t\t\t\t\"dev_sn\":\"" . $item['locksn'] . "\"\r\n\t\t\t\t\t\t  }";
                        $result = thinmoo_http_post($set, $data);
                        if ($result['ret'] == '0') {
                            $res['code'] = '0';
                            $res['msg'] = '开门成功';
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $result['ret'] . '-' . $result['msg'];
                        }
                    }
                } elseif ($item['devtype'] == 3) {
                    if ($item['doortype'] == 2) {
                        $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
                    } elseif ($item['doortype'] == 5) {
                        $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_net_open_key', 'device_sncode' => $item['locksn']);
                        $result = mailin_http_post($set, $post_data);
                        if ($result['state'] == 1) {
                            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'net_open', 'device_sncode' => $item['locksn'], 'net_open_key' => $result['return_data']['net_open_key']);
                            $ret = mailin_http_post($set, $post_data);
                            if ($return['state'] == 1) {
                                $res['code'] = '0';
                                $res['msg'] = '开门成功';
                            } else {
                                $res['code'] = '1';
                                $res['msg'] = $return['state'] . '-' . $return['$return'];
                            }
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $result['state'] . '-' . $result['return_data'];
                        }
                    }
                } else {
                    $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
                }
                $this->opendoorlog($id, $res);
                if ($res['code'] == '0') {
                    show_json(1, '开门成功');
                } else {
                    show_json(0, '开门失败');
                }
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and doorid=:doorid and status=1';
            $visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':doorid' => $item['id']));
            if (!empty($visit)) {
                if (!($visit['effetime'] >= TIMESTAMP)) {
                    show_json(0, array('message' => '访客有效期已过', 'url' => $this->createMobileUrl('opendoor', array('op' => 'askvisit', 'id' => $visit['doorid']))));
                }
                if (!($visit['opentimes'] >= 1)) {
                    show_json(0, array('message' => '访客开门次数已用完', 'url' => $this->createMobileUrl('opendoor', array('op' => 'askvisit', 'id' => $visit['doorid']))));
                }
                if ($item['devtype'] == 1 || $item['devtype'] == 2) {
                    if ($item['doortype'] == 5) {
                        $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_net_open_key', 'device_sncode' => $item['locksn']);
                        $result = mailin_http_post($set, $post_data);
                        if ($result['state'] == 1) {
                            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'net_open', 'device_sncode' => $item['locksn'], 'net_open_key' => $result['return_data']['net_open_key']);
                            $ret = mailin_http_post($set, $post_data);
                            if ($ret['state'] == 1) {
                                $res['code'] = '0';
                                $res['msg'] = '开门成功';
                            } else {
                                $res['code'] = '1';
                                $res['msg'] = $ret['state'] . '-' . $ret['$return'];
                            }
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $result['state'] . '-' . $result['return_data'];
                        }
                    } else {
                        $set = array();
                        $set['url'] = '/doormaster/server/remote_control';
                        $set['token'] = $thinmoo_token;
                        $set['op'] = 'OPEN_DOOR';
                        $data = "{\r\n\t\t\t\t\t\t\t\"dev_sn\":\"" . $item['locksn'] . "\"\r\n\t\t\t\t\t\t  }";
                        $result = thinmoo_http_post($set, $data);
                        if ($result['ret'] == '0') {
                            $res['code'] = '0';
                            $res['msg'] = '开门成功';
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $res['ret'] . '-' . $res['msg'];
                        }
                    }
                } elseif ($item['devtype'] == 3) {
                    if ($item['doortype'] == 2) {
                        $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
                    } elseif ($item['doortype'] == 5) {
                        $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_net_open_key', 'device_sncode' => $item['locksn']);
                        $result = mailin_http_post($set, $post_data);
                        if ($result['state'] == 1) {
                            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'net_open', 'device_sncode' => $item['locksn'], 'net_open_key' => $result['return_data']['net_open_key']);
                            $ret = mailin_http_post($set, $post_data);
                            if ($ret['state'] == 1) {
                                $res['code'] = '0';
                                $res['msg'] = '开门成功';
                            } else {
                                $res['code'] = '1';
                                $res['msg'] = $ret['state'] . '-' . $ret['$return'];
                            }
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $result['state'] . '-' . $result['return_data'];
                        }
                    }
                } else {
                    $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
                }
                $this->opendoorlog($id, $res);
                if ($res['code'] == '0') {
                    $sql = 'update ' . tablename('rhinfo_zyxq_door_visit') . ' set opentimes=opentimes - 1 where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and doorid=:dooorid and status=1 ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':doorid' => $item['id']));
                    show_json(1, '开门成功');
                } else {
                    show_json(0, '开门失败');
                }
            } else {
                show_json(0, array('message' => '您还没有访客权限', 'url' => $this->createMobileUrl('opendoor', array('op' => 'askvisit', 'id' => $item['id']))));
            }
        } else {
            show_json(0, '该门禁不存在或已停用');
        }
    } elseif ($_GPC['devtype'] == 3) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and devicesn=:devicesn';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':devicesn' => $_GPC['result']));
        if (!empty($item)) {
            $selfid = $item['id'];
            $fee = $item['price'];
            if (!($fee > 0) || empty($fee)) {
                show_json(0, '价格参数未设置');
            }
            load()->model('mc');
            $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
            $behavior = $setting['creditbehaviors'];
            $creditnames = $setting['creditnames'];
            $credits = mc_credit_fetch($_W['member']['uid'], '*');
            if ($this->syscfg['devpaytype'] == 1) {
                if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
                    show_json(0, '余额不足，请充值');
                }
            }
            if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
                $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
                $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
                $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $charging['rid']));
                $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
                $params = array('money' => $fee, 'title' => '自助设备', 'feetype' => 4, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'selfid' => $selfid, 'pid' => $item['pid'], 'rid' => $item['rid']);
                $res = $this->my_single_pay($params);
                if ($res['errno'] == 1) {
                    show_json(0, $res['message']);
                }
                show_json(1, $res['result']);
            } else {
                $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                $crediturl = $this->my_mobileurl($crediturl);
                mc_credit_update($_W['member']['uid'], 'credit2', 0 - $fee, array(0, '自助设备', 'rhinfo_zyxq'));
                $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and id=:id ';
                $selfdevice = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $selfid));
                $res = $this->mxdevopen($selfdevice['devicesn']);
                $seldevice_log = array('weid' => $_W['uniacid'], 'selfid' => $selfid, 'title' => $selfdevice['title'], 'openid' => $_W['openid'], 'out_trade_no' => $uniontid, 'fee' => $fee, 'status' => 0, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                if ($res['code'] == '0') {
                    $seldevice_log['status'] = 1;
                    pdo_insert('rhinfo_zycj_selfdevice_log', $seldevice_log);
                } else {
                    pdo_insert('rhinfo_zycj_selfdevice_log', $seldevice_log);
                    show_json(2, '设备故障，请联系处理');
                }
                mc_notice_credit2($_W['openid'], $_W['member']['uid'], $fee, 0, '自助设备', $crediturl, '点击查看详情');
                show_json(1, array('wechat' => ''));
            }
        }
        show_json(0, '设备编号不存在');
    } elseif ($_GPC['devtype'] == 2) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and devicesn=:devicesn';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':devicesn' => $_GPC['result']));
        if (!empty($item)) {
            show_json(1, array('wechat' => 1, 'url' => $this->createMobileUrl('charging', array('op' => 'detail', 'id' => $item['id']))));
        }
        show_json(0, '设备编号不存在');
    } else {
        show_json(0, '找不到相关智能设备');
    }
}
load()->func('file');
if ($operation == 'uploader') {
    $field = $_GPC['file'];
    if (!empty($_FILES[$field]['name'])) {
        if (is_array($_FILES[$field]['name'])) {
            $files = array();
            $k = 0;
            while (!($k >= count($_FILES[$field]['name']))) {
                $file = array('name' => $_FILES[$field]['name'][$k], 'type' => $_FILES[$field]['type'][$k], 'tmp_name' => $_FILES[$field]['tmp_name'][$k], 'error' => $_FILES[$field]['error'][$k], 'size' => $_FILES[$field]['size'][$k]);
                $files[] = $this->myupload($file);
                ($k += 1) + -1;
            }
            $ret = array('status' => 'success', 'files' => $files);
            exit(json_encode($ret));
        }
        $result = $this->myupload($_FILES[$field]);
        exit(json_encode($result));
    }
    $result['message'] = '请选择要上传的图片';
    exit(json_encode($result));
} elseif ($operation == 'wxuploader') {
    $access_token = WeAccount::token();
    $media_id = $_GPC['media_id'];
    if (empty($media_id)) {
        $resarr['error'] = 1;
        $resarr['message'] = '获取微信媒体参数失败';
        exit(json_encode($resarr));
    }
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
    $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
    $ext = 'jpg';
    mkdirs(ATTACHMENT_ROOT . '/' . $path);
    $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);
    $target_temp = '../attachment/' . $path . 'temp_' . $filename;
    $tmp_file = IA_ROOT . '/attachment/' . $path . 'temp_' . $filename;
    $target_file = IA_ROOT . '/attachment/' . $path . $filename;
    $ch = curl_init($url);
    $fp = fopen($target_temp, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    if (file_exists($target_temp)) {
        $resarr['error'] = 0;
        $imagesize = $this->syscfg['imagesize'] > 1024 ? 640 : $this->syscfg['imagesize'];
        file_image_thumb($tmp_file, $target_file, $imagesize);
        file_delete($tmp_file);
        $pathname = $path . $filename;
        $fileurl = trim($_W['attachurl'] . $pathname);
        if (!empty($_W['setting']['remote']['type'])) {
            $remotestatus = file_remote_upload($pathname);
            if (is_error($remotestatus)) {
                $result['message'] = $remote['message'];
                return $result;
            }
            $remoteurl = tomedia($pathname);
            $fileurl = $remoteurl;
        }
        $resarr['realimgurl'] = $pathname;
        $resarr['imgurl'] = $fileurl;
        $resarr['message'] = '上传成功';
        if (empty($_GPC['nodisplay'])) {
            pdo_insert('core_attachment', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'filename' => 'rhinfo_zyxq_' . date('Ymd') . random(5, true) . '.jpg', 'attachment' => $pathname, 'type' => 1, 'createtime' => TIMESTAMP));
        }
    } else {
        $resarr['error'] = 1;
        $resarr['message'] = '上传失败';
    }
    echo json_encode($resarr, true);
    exit(0);
} elseif ($operation == 'remove') {
    $file = $_GPC['filename'];
    file_delete($file);
    exit(json_encode(array('status' => 'success')));
} elseif ($operation == 'imageocr') {
    $config = $this->module['config'];
    $access_token = WeAccount::token();
    $media_id = $_GPC['media_id'];
    if (empty($media_id)) {
        $resarr['error'] = 1;
        $resarr['message'] = '获取微信媒体参数失败';
        exit(json_encode($resarr));
    }
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
    $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
    $ext = 'jpg';
    mkdirs(ATTACHMENT_ROOT . '/' . $path);
    $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);
    $target_temp = '../attachment/' . $path . 'temp_' . $filename;
    $tmp_file = IA_ROOT . '/attachment/' . $path . 'temp_' . $filename;
    $target_file = IA_ROOT . '/attachment/' . $path . $filename;
    $ch = curl_init($url);
    $fp = fopen($target_temp, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    if (file_exists($target_temp)) {
        $imagesize = $this->syscfg['imagesize'] > 1024 ? 640 : $this->syscfg['imagesize'];
        file_image_thumb($tmp_file, $target_file, $imagesize);
        unlink($tmp_file);
        $url = 'https://api.jisuapi.com/bankcardcognition/recognize?appkey=' . $config['js_appkey'];
        $post = array('pic' => curl_file_create(realpath($target_file)));
        $result = my_curlOpen($url, array('post' => $post, 'isupfile' => true));
        $jsonarr = json_decode($result, true);
        if ($jsonarr['status'] != 0) {
            $resarr['error'] = 1;
            $resarr['message'] = $jsonarr['msg'];
        } else {
            unlink($target_file);
            $ret = $jsonarr['result'];
            $resarr['error'] = 0;
            $resarr['bankcard'] = $ret['number'];
            $resarr['message'] = '识别成功';
        }
    } else {
        $resarr['error'] = 1;
        $resarr['message'] = '操作失败';
    }
    echo json_encode($resarr, true);
    exit(0);
} elseif ($operation == 'yt_imageocr') {
    $access_token = WeAccount::token();
    $media_id = $_GPC['media_id'];
    if (empty($media_id)) {
        $resarr['error'] = 1;
        $resarr['message'] = '获取微信媒体参数失败';
        exit(json_encode($resarr));
    }
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
    $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
    $ext = 'jpg';
    mkdirs(ATTACHMENT_ROOT . '/' . $path);
    $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);
    $target_temp = '../attachment/' . $path . 'temp_' . $filename;
    $tmp_file = IA_ROOT . '/attachment/' . $path . 'temp_' . $filename;
    $target_file = IA_ROOT . '/attachment/' . $path . $filename;
    $ch = curl_init($url);
    $fp = fopen($target_temp, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    if (file_exists($target_temp)) {
        $resarr['error'] = 0;
        $imagesize = $this->syscfg['imagesize'] > 1024 ? 640 : $this->syscfg['imagesize'];
        file_image_thumb($tmp_file, $target_file, $imagesize);
        file_delete($tmp_file);
        $pathname = $path . $filename;
        $fileurl = trim($_W['attachurl'] . $pathname);
        $yt_image = file_get_contents($target_file);
        if (!empty($_W['setting']['remote']['type'])) {
            $remotestatus = file_remote_upload($pathname);
            if (is_error($remotestatus)) {
                $result['message'] = $remote['message'];
                return $result;
            }
            $remoteurl = tomedia($pathname);
            $fileurl = $remoteurl;
        }
        $config = $this->module['config'];
        $set = array('qq' => $config['yt_qq'], 'appid' => $config['yt_appid'], 'secretid' => $config['yt_secretid'], 'secretkey' => $config['yt_secretkey']);
        $resarr['realimgurl'] = $pathname;
        $resarr['imgurl'] = $fileurl;
        $resarr['message'] = '上传成功';
        $resarr['expresssn'] = '';
        $resarr['mobile'] = '';
        $resarr['remark'] = '';
        $res = Youtu_ocr_post($set, base64_encode($yt_image), 'waybill');
        if ($res['errorcode'] == 0) {
            $items = $res['items'];
            $k = 0;
            while (!($k >= count($items))) {
                if ($items[$k]['item'] == '收件人名称') {
                    $resarr['remark'] = $items[$k]['itemstring'];
                }
                if ($items[$k]['item'] == '收件人手机号') {
                    $resarr['mobile'] = $items[$k]['itemstring'];
                }
                if ($items[$k]['item'] == '运单号') {
                    $resarr['expresssn'] = $items[$k]['itemstring'];
                }
                ($k += 1) + -1;
            }
        } else {
            $resarr['message'] = !empty($res['errormsg']) ? $res['errormsg'] : '请检查参数设置';
        }
    } else {
        $resarr['error'] = 1;
        $resarr['message'] = '上传失败';
    }
    echo json_encode($resarr, true);
    exit(0);
}