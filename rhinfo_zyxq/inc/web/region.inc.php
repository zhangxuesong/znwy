<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
if ($operation == 'list') {
    $category = 1;
} elseif ($operation == 'blist') {
    $category = 2;
} elseif ($operation == 'glist') {
    $category = 3;
} elseif ($operation == 'mlist') {
    $category = 4;
} elseif ($operation == 'alist') {
    $category = 5;
} else {
    $category = !empty($_GPC['category']) ? $_GPC['category'] : 1;
}
$mydo = 'region';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zyxq_region';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$mytext = array();
if (defined('RHINFO_ZYXQ_REGION')) {
    if (RHINFO_ZYXQ_LANGUAGE == true) {
        $mytext = json_decode(RHINFO_ZYXQ_REGION, true);
    }
}
$pid = $_GPC['pid'];
if ($pid) {
    $condition .= ' and pid = :pid';
    $params['pid'] = $pid;
}
$rights = $this->myrights(2, $mydo, $operation);
if ($category == 1) {
    $navtitle = '小区管理';
    $condition .= ' and category = 1';
    $rlist = 'list';
} elseif ($category == 2) {
    $navtitle = '商圈管理';
    $condition .= ' and category = 2';
    $rlist = 'blist';
} elseif ($category == 3) {
    $navtitle = '园区管理';
    $condition .= ' and category = 3';
    $rlist = 'glist';
} elseif ($category == 4) {
    $navtitle = '市场管理';
    $condition .= ' and category = 4';
    $rlist = 'mlist';
} elseif ($category == 5) {
    $navtitle = '公寓管理';
    $condition .= ' and category = 5';
    $rlist = 'alist';
}
if ($operation == 'list' || $operation == 'blist' || $operation == 'glist' || $operation == 'mlist' || $operation == 'alist') {
    if ($category == 1) {
        $current = '小区列表';
    } elseif ($category == 2) {
        $current = '商圈列表';
    } elseif ($category == 3) {
        $current = '园区列表';
    } elseif ($category == 4) {
        $current = '市场列表';
    } elseif ($category == 5) {
        $current = '公寓列表';
    }
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($_W['uid'])) {
        if (!empty($mywe['pid'])) {
            $condition .= ' and pid = ' . $mywe['pid'];
        }
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid = :weid and id = :uid';
        $user = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':uid' => $mywe['uid']));
        if ($user['gid'] == 0) {
            $condition .= ' and pid = ' . $mywe['pid'];
        } elseif ($user['rid']) {
            $condition .= ' and id = ' . $user['rid'];
        } else {
            $condition .= ' and pid = ' . $mywe['pid'];
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $qrcode = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'scanbind', 'rid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            $visitqrcode = $this->my_mobileurl($this->createMobileUrl('visit', array('op' => 'askvisit', 'rid' => $data[$k]['id'])));
            $data[$k]['visitqrcode'] = $this->createqrcode($visitqrcode);
            $data[$k]['wxqrcode'] = $this->createqrcode($data[$k]['qrurl']);
            $scripturl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'frombind', 'rid' => $data[$k]['id'])));
            $scripthtml = "<script type=\"text/javascript\" src=\"../addons/rhinfo_zyxq/static/static/lib/jqery/jquery-1.11.1.min.js\"></script>\r\n\t\t\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\t\t\$.ajax({   \r\n\t\t\t\t\t\turl:\"" . $scripturl . "\",   \r\n\t\t\t\t\t\ttype:\"post\", \r\n\t\t\t\t\t\tdata:{qrcode:\"" . $data[$k]['qrcode'] . "\"},\r\n\t\t\t\t\t\tdataType:\"html\",\r\n\t\t\t\t\t\tsuccess:function(data){ \r\n\t\t\t\t\t\t\tif(data.length==0){\r\n\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse{\r\n\t\t\t\t\t\t\t\t\$(\"body\").append(data);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t});\r\n\t\t\t\t\t</script>";
            $data[$k]['scripthtml'] = htmlspecialchars($scripthtml);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    if ($category == 1) {
        $current = '小区列表';
    } elseif ($category == 2) {
        $current = '商圈列表';
    } elseif ($category == 3) {
        $current = '园区列表';
    } elseif ($category == 4) {
        $current = '市场列表';
    } elseif ($category == 5) {
        $current = '公寓列表';
    }
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (empty($_W['uid'])) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid = :weid and id = :uid';
        $user = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':uid' => $mywe['uid']));
        if ($user['gid'] == 0) {
            $condition .= ' and pid = ' . $mywe['pid'];
        } elseif ($user['rid']) {
            $condition .= ' and id = ' . $user['rid'];
        } else {
            $condition .= ' and pid = ' . $mywe['pid'];
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $qrcode = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'scanbind', 'rid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            $scripturl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'frombind', 'rid' => $data[$k]['id'])));
            $scripthtml = "<script type=\"text/javascript\" src=\"../addons/rhinfo_zyxq/static/static/lib/jqery/jquery-1.11.1.min.js\"></script>\r\n\t\t\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\t\t\$.ajax({   \r\n\t\t\t\t\t\turl:\"" . $scripturl . "\",   \r\n\t\t\t\t\t\ttype:\"post\", \r\n\t\t\t\t\t\tdata:{qrcode:\"" . $data[$k]['qrcode'] . "\"},\r\n\t\t\t\t\t\tdataType:\"html\",\r\n\t\t\t\t\t\tsuccess:function(data){ \r\n\t\t\t\t\t\t\tif(data.length==0){\r\n\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\telse{\r\n\t\t\t\t\t\t\t\t\$(\"body\").append(data);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t});\r\n\t\t\t\t\t</script>";
            $data[$k]['scripthtml'] = htmlspecialchars($scripthtml);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    if ($category == 1) {
        $current = '新增小区';
    } elseif ($category == 2) {
        $current = '新增商圈';
    } elseif ($category == 3) {
        $current = '新增园区';
    } elseif ($category == 4) {
        $current = '新增市场';
    } elseif ($category == 5) {
        $current = '新增公寓';
    }
    $property = $this->myproperty();
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $sql = 'select count(*) from ' . tablename($tablename) . ' where pid = :pid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid'], ':weid' => $mywe['weid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid and weid = :weid';
        $property = pdo_fetch($sql, array(':pid' => $_GPC['pid'], ':weid' => $mywe['weid']));
        if ($property['limitqty'] > 0 && $count >= $property['limitqty']) {
            $url = $this->createWebUrl($mydo, array('op' => 'list', 'direct' => 1));
            $this->mywebmsg('错误', '超出数量限制，请联系平台', $url, 'danger');
        }
        $sql = 'select count(*) from ' . tablename($tablename) . ' where weid = :weid ';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
        if ($this->syscfg['limitqty'] > 0 && $count >= $this->syscfg['limitqty']) {
            $url = $this->createWebUrl($mydo, array('op' => 'list', 'direct' => 1));
            $this->mywebmsg('错误', '超出数量限制，请联系平台', $url, 'danger');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secacc') . ' where weid = :weid and status=1 ';
        $secacc = pdo_fetch($sql, array(':weid' => $mywe['weid']));
        if ($secacc['limitqty'] > 0 && $count >= $secacc['limitqty']) {
            $url = $this->createWebUrl($mydo, array('op' => 'list', 'direct' => 1));
            $this->mywebmsg('错误', '超出数量限制，请联系平台', $url, 'danger');
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'title' => $_GPC['title'], 'category' => $category, 'thumb' => $_GPC['thumb'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'url' => $_GPC['url'], 'service' => $_GPC['service'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $rlist, 'category' => $category)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    if ($category == 1) {
        $current = '编辑小区';
    } elseif ($category == 2) {
        $current = '编辑商圈';
    } elseif ($category == 3) {
        $current = '编辑园区';
    } elseif ($category == 4) {
        $current = '编辑市场';
    } elseif ($category == 5) {
        $current = '编辑公寓';
    }
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'service' => $_GPC['service'], 'url' => $_GPC['url']);
        if (!empty($_GPC['pid'])) {
            $data['pid'] = $_GPC['pid'];
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $rlist, 'category' => $category)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $property = $this->myproperty();
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    if ($category == 1) {
        $current = '删除小区';
    } elseif ($category == 2) {
        $current = '删除商圈';
    } elseif ($category == 3) {
        $current = '删除园区';
    } elseif ($category == 4) {
        $current = '删除市场';
    } elseif ($category == 5) {
        $current = '删除公寓';
    }
    $id = intval($_GPC['id']);
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid ';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $id));
    if ($total > 0) {
        echo '删除失败,已建立相关资料，不可删除!';
        exit(0);
    }
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '独立支付状态';
    $id = intval($_GPC['id']);
    $data = array('ispay' => $_GPC['ispay']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($id, $mydo, $operation, $current, $current . $_GPC['ispay'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            if ($category == 1) {
                echo '小区已存在!';
            } elseif ($category == 2) {
                echo '商圈已存在!';
            } elseif ($category == 3) {
                echo '园区已存在!';
            } elseif ($category == 4) {
                echo '市场已存在!';
            } elseif ($category == 5) {
                echo '公寓已存在!';
            }
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'pay') {
    $myret = 0;
    $current = '支付参数';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $data = array('paytype' => $_GPC['paytype'], 'isalipay' => $_GPC['isalipay'], 'aliaccount' => $_GPC['aliaccount'], 'alipartner' => $_GPC['alipartner'], 'alisecret' => $_GPC['alisecret'], 'submerchid' => $_GPC['submerchid'], 'wecoupon' => $_GPC['wecoupon'], 'bankmerchid' => $_GPC['bankmerchid'], 'ymfurl' => $_GPC['ymfurl'], 'ymfkey' => $_GPC['ymfkey'], 'paysuccessurl' => $_GPC['paysuccessurl'], 'isonlinepay' => $_GPC['isonlinepay'], 'ispay' => $_GPC['ispay'], 'rsdbankmerchid' => $_GPC['rsdbankmerchid'], 'bankkey' => $_GPC['bankkey'], 'starkey' => $_GPC['starkey'], 'starorg' => $_GPC['starorg'], 'startrm' => $_GPC['startrm'], 'starmerchid' => $_GPC['starmerchid'], 'ylbid' => $_GPC['ylbid'], 'alipay_type' => $_GPC['alipay_type'], 'alipay_appid' => $_GPC['alipay_appid'], 'alipay_rsa2' => $_GPC['alipay_rsa2'], 'alipay_private' => $_GPC['alipay_private'], 'alipay_seller_id' => $_GPC['alipay_seller_id'], 'alipay_app_auth_token' => $_GPC['alipay_app_auth_token']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (!empty($_GPC['ylbid'])) {
            $set = array();
            $set['url'] = 'list_bind.php';
            $params = array('id' => $_GPC['ylbid']);
            $res = ylb_http_post($set, $params);
            if ($res['total_count'] == 0) {
                $set = array();
                $set['url'] = 'bind.php';
                $params = array('token' => $this->syscfg['ylb_token'], 'id' => $_GPC['ylbid'], 'm' => 1, 'uid' => 'rhinfo_' . $_GPC['ylbid']);
                $res = ylb_http_post($set, $params);
            }
        }
        $this->mysyslog($item['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $rlist, 'category' => $item['category'])) . $mywe['direct']);
        exit(0);
    }
    $navtitle = $item['title'];
    include $this->mywtpl();
} elseif ($operation == 'qrcode') {
    if ($_W['isajax']) {
        $sql = 'SELECT * FROM ' . tablename($tablename) . ' WHERE weid = :weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        load()->func('communication');
        $barcode = array('expire_seconds' => '', 'action_name' => '', 'action_info' => array('scene' => array()));
        $qrctype = 2;
        $acid = intval($_W['acid']);
        $uniacccount = WeAccount::create($acid);
        $scene_str = random(32);
        $is_exist = pdo_fetchcolumn('SELECT id FROM ' . tablename('qrcode') . ' WHERE uniacid = :uniacid AND acid = :acid AND scene_str = :scene_str AND model = 2', array(':uniacid' => $_W['uniacid'], ':acid' => $_W['acid'], ':scene_str' => $scene_str));
        if (!empty($is_exist)) {
            echo '场景值已经存在,请重新生成';
            exit(0);
        }
        $barcode['action_info']['scene']['scene_str'] = $scene_str;
        $barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
        $result = $uniacccount->barCodeCreateFixed($barcode);
        if (!is_error($result)) {
            $insert = array('uniacid' => $_W['uniacid'], 'acid' => $acid, 'scene_str' => $barcode['action_info']['scene']['scene_str'], 'keyword' => trim($region['title']), 'name' => trim($region['title']), 'model' => $qrctype, 'ticket' => $result['ticket'], 'url' => $result['url'], 'expire' => $result['expire_seconds'], 'createtime' => TIMESTAMP, 'status' => '1', 'type' => 'scene');
            pdo_insert('qrcode', $insert);
            pdo_insert('rule', array('uniacid' => $_W['uniacid'], 'name' => trim($region['title']), 'module' => 'rhinfo_zyxq', 'status' => 1));
            $ruleid = pdo_insertid();
            pdo_insert('rule_keyword', array('uniacid' => $_W['uniacid'], 'rid' => $ruleid, 'content' => trim($region['title']), 'type' => 1, 'module' => 'rhinfo_zyxq', 'status' => 1));
            pdo_insert('rhinfo_zyxq_replyrule', array('weid' => $_W['uniacid'], 'replyid' => $ruleid, 'rid' => $_GPC['rid'], 'qr' => 1, 'uid' => $mywe['uid'], 'ctime' => TIMESTAMP));
            pdo_update('rhinfo_zyxq_region', array('qrurl' => $result['url']), array('weid' => $_W['uniacid'], 'id' => $_GPC['rid']));
            echo 'ok';
        } else {
            echo '接口错误:' . $result['errorcode'] . $result['message'];
        }
        exit(0);
    }
} elseif ($operation == 'wxapp') {
    $sql = 'SELECT * FROM ' . tablename($tablename) . ' WHERE weid = :weid and id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    if ($_W['isajax'] && $region) {
        pdo_update('rhinfo_zyxq_region', array('wxminiappid' => $_GPC['wxminiappid'], 'wxminiappsecret' => $_GPC['wxminiappsecret'], 'wxminiappurl' => $_GPC['wxminiappurl']), array('weid' => $mywe['weid'], 'id' => $region['id']));
        $appjson = "{\r\n\t\t\t\t\"pages\": [\r\n\t\t\t\t\t\"pages/index/index\",\r\n\t\t\t\t\t\"pages/pay/pay\",\r\n\t\t\t\t\t\"pages/mypay/pay\",\r\n\t\t\t\t\t\"pages/logs/logs\",\r\n\t\t\t\t\t\"pages/index/navto\",\r\n\t\t\t\t\t\"pages/webview/index\"\r\n\t\t\t\t],\r\n\t\t\t\t\"window\": {\r\n\t\t\t\t\t\"backgroundTextStyle\": \"light\",\r\n\t\t\t\t\t\"navigationBarBackgroundColor\": \"#f7f7f7\",\r\n\t\t\t\t\t\"navigationBarTitleText\": \"" . $region['title'] . "\",\r\n\t\t\t\t\t\"navigationBarTextStyle\": \"black\"\r\n\t\t\t\t\t},\r\n\t\t\t\t\"networkTimeout\": {\r\n\t\t\t\t\t\"request\": 10000,\r\n\t\t\t\t\t\"downloadFile\": 10000\r\n\t\t\t\t  },\r\n\t\t\t\t  \"debug\": false,\r\n\t\t\t\t  \"navigateToMiniProgramAppIdList\": []\r\n\t\t\t\t}";
        file_put_contents(IA_ROOT . '/addons/rhinfo_zyxq/static/lib/wxapp/app.json', $appjson);
        $siteinfo = "var siteinfo = {\r\n\t\t\t\t\"uniacid\": \"" . $_W['uniacid'] . "\",\r\n\t\t\t\t\"acid\": \"" . $_W['acid'] . "\",\r\n\t\t\t\t\"multiid\": \"0\",\r\n\t\t\t\t\"version\": \"v1.0\",\r\n\t\t\t\t\"siteroot\": \"" . $_GPC['wxminiappurl'] . "app/index.php\",\r\n\t\t\t\t\"method_design\" : \"3\",\r\n\t\t\t\t\"regionid\" : \"" . $region['id'] . "\"\r\n\t\t\t\t};\r\n\t\t\tmodule.exports = siteinfo;";
        $res = file_put_contents(IA_ROOT . '/addons/rhinfo_zyxq/static/lib/wxapp/siteinfo.js', $siteinfo);
        if ($res > 0) {
            require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/rhinfo/pclzip.php';
            $Path = IA_ROOT . '/addons/rhinfo_zyxq/static/lib/wxapp/';
            $ZipFile = IA_ROOT . '/addons/rhinfo_zyxq/data/wxapp.zip';
            $ZipFile;
            $zip = 'PclZip';
            $v_list = $zip->create($Path, PCLZIP_OPT_REMOVE_PATH, $Path);
            if ($v_list == 0) {
                exit('压缩错误');
            }
            $miniapp_file = IA_ROOT . '/addons/rhinfo_zyxq/miniapi.php';
            if (file_exists($miniapp_file)) {
                load()->func('file');
                file_move($miniapp_file, IA_ROOT . '/app/miniapi.php');
            }
            exit('ok');
        } else {
            exit('打包失败，请检查服务器写入权限！');
        }
    }
    include $this->mywtpl('wxapp');
} elseif ($operation == 'menu') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_regionmenu') . ' WHERE weid = :weid and pid=:pid and rid = :rid ';
    $item = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
    if ($_W['isajax']) {
        if (!empty($item)) {
            $data = array('menucolor' => $_GPC['menucolor'], 'menu1' => $_GPC['menu1'], 'menu2' => $_GPC['menu2'], 'menu3' => $_GPC['menu3'], 'menu4' => $_GPC['menu4'], 'menu5' => $_GPC['menu5'], 'menu1url' => $_GPC['menu1url'], 'menu2url' => $_GPC['menu2url'], 'menu3url' => $_GPC['menu3url'], 'menu4url' => $_GPC['menu4url'], 'menu5url' => $_GPC['menu5url'], 'icon1' => $_GPC['icon1'], 'icon2' => $_GPC['icon2'], 'icon3' => $_GPC['icon3'], 'icon4' => $_GPC['icon4'], 'icon5' => $_GPC['icon5'], 'icon5' => $_GPC['icon5'], 'enabled' => $_GPC['enabled'], 'servicedisplay' => $_GPC['servicedisplay']);
            $res = pdo_update('rhinfo_zyxq_regionmenu', $data, array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid']));
        } else {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'menucolor' => $_GPC['menucolor'], 'menu1' => $_GPC['menu1'], 'menu2' => $_GPC['menu2'], 'menu3' => $_GPC['menu3'], 'menu4' => $_GPC['menu4'], 'menu5' => $_GPC['menu5'], 'menu1url' => $_GPC['menu1url'], 'menu2url' => $_GPC['menu2url'], 'menu3url' => $_GPC['menu3url'], 'menu4url' => $_GPC['menu4url'], 'menu5url' => $_GPC['menu5url'], 'icon1' => $_GPC['icon1'], 'icon2' => $_GPC['icon2'], 'icon3' => $_GPC['icon3'], 'icon4' => $_GPC['icon4'], 'icon5' => $_GPC['icon5'], 'icon5' => $_GPC['icon5'], 'enabled' => $_GPC['enabled'], 'servicedisplay' => $_GPC['servicedisplay'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $res = pdo_insert('rhinfo_zyxq_regionmenu', $data);
        }
        if ($res) {
            exit('ok');
        } else {
            exit('内容无变化');
        }
    }
    include $this->mywtpl('menu');
} elseif ($operation == 'payask') {
    $myret = 0;
    $current = '支付申请';
    $id = intval($_GPC['id']);
    $rid = intval($_GPC['rid']);
    $sql = 'select title,pid,category from ' . tablename($tablename) . ' where id = :rid and weid = :weid';
    $region = pdo_fetch($sql, array(':rid' => $rid, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $licimage = $_GPC['licimage'];
        if (!empty($_FILES['upfilelicimage']['name'])) {
            $tmp_file = $_FILES['upfilelicimage']['tmp_name'];
            $file = $_FILES['upfilelicimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $licimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $licimage = '';
            }
        }
        $icardaimage = $_GPC['icardaimage'];
        if (!empty($_FILES['upfileicardaimage']['name'])) {
            $tmp_file = $_FILES['upfileicardaimage']['tmp_name'];
            $file = $_FILES['upfileicardaimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $icardaimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $icardaimage = '';
            }
        }
        $icardbimage = $_GPC['icardbimage'];
        if (!empty($_FILES['upfileicardbimage']['name'])) {
            $tmp_file = $_FILES['upfileicardbimage']['tmp_name'];
            $file = $_FILES['upfileicardbimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $icardbimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $icardbimage = '';
            }
        }
        $doorimage = $_GPC['doorimage'];
        if (!empty($_FILES['upfiledoorimage']['name'])) {
            $tmp_file = $_FILES['upfiledoorimage']['tmp_name'];
            $file = $_FILES['upfiledoorimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $doorimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $doorimage = '';
            }
        }
        $accimage = $_GPC['accimage'];
        if (!empty($_FILES['upfileaccimage']['name'])) {
            $tmp_file = $_FILES['upfileaccimage']['tmp_name'];
            $file = $_FILES['upfileaccimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $accimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $accimage = '';
            }
        }
        $payimage = $_GPC['payimage'];
        if (!empty($_FILES['upfilepayimage']['name'])) {
            $tmp_file = $_FILES['upfilepayimage']['tmp_name'];
            $file = $_FILES['upfilepayimage']['name'];
            $file_types = explode('.', $file);
            $file_type = $file_types[count($file_types) - 1];
            $path = 'images/' . intval($mywe['weid']) . '/' . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $file_type);
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $payimage = $path . $filename;
            if (!copy($tmp_file, $target_file)) {
                $payimage = '';
            }
        }
        $data = array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'company' => $_GPC['company'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'licimage' => $licimage, 'icardaimage' => $icardaimage, 'icardbimage' => $icardbimage, 'doorimage' => $doorimage, 'payimage' => $payimage, 'accimage' => $accimage, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if (!empty($id)) {
            $result = pdo_update('rhinfo_zyxq_region_account', $data, array('id' => $id, 'weid' => $mywe['weid']));
        } else {
            $result = pdo_insert('rhinfo_zyxq_region_account', $data);
            $id = pdo_insertid();
        }
        $this->mysyslog($region['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $rlist, 'category' => $region['category'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region_account') . ' where rid = :rid and weid = :weid';
    $item = pdo_fetch($sql, array(':rid' => $rid, ':weid' => $mywe['weid']));
    $navtitle = $region['title'];
    include $this->mywtpl();
}