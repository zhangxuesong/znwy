<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$curr = 'business';
$mydo = 'business';
$user = $this->getmyinfo($_W['member']['uid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$res = $this->getarrearage($_W['member']['uid']);
if ($res) {
    if ($res['arrearagelimit8']) {
        header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
        exit(0);
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid=:pid and rid=:rid and btype=8 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now()))  order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    if (empty($banners)) {
        $sql = 'select link,thumb,0 as `pid`,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=8 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid=:weid and ((pid = 0 and rid=0) or (pid=:pid and rid=:rid)) and parentid=0 and enabled=1' . ' ORDER BY displayorder desc ,id asc ';
    $category = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $k = 0;
    while (!($k >= count($category))) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid=:weid and parentid=:parentid and isrecommand=1 and enabled=1' . ' ORDER BY displayorder desc ,id asc ';
        $category[$k]['category'] = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':parentid' => $category[$k]['id']));
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and ((pid = 0 and rid=0) or (pid=:pid and rid=:rid)) and isrecommand = 1 and status=1';
    $recommands = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $temp_recommands = array();
    $k = 0;
    while (!($k >= count($recommands))) {
        $isremove = false;
        $regionarr = iunserializer($recommands[$k]['region']);
        if (!empty($regionarr)) {
            if (!in_array($user['rid'], $regionarr)) {
                $isremove = true;
            }
        } else {
            $recommands[$k]['follows'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_business_follow') . ' where weid=:weid and bid=:bid limit 1', array(':weid' => $_W['uniacid'], ':bid' => $recommands[$k]['id']));
            $level = 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $recommands[$k]['id']));
            if ($total > 0) {
                $sql = 'select sum(stars) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid ';
                $stars = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $recommands[$k]['id']));
                $level = $stars / $total;
            }
            $recommands[$k]['level'] = $level;
        }
        if ($isremove == false) {
            $temp_recommands[] = $recommands[$k];
        }
        ($k += 1) + -1;
    }
    $recommands = $temp_recommands;
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    $range = $_GPC['range'];
    $lbs = $_GPC['lbs'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $sorttype = $_GPC['sorttype'];
        $lbs = empty($sortype) ? '' : 'none';
        if (empty($range)) {
            $range = 500;
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        if (!empty($_GPC['cateid'])) {
            $condition .= ' AND ( cateid =' . $_GPC['cateid'] . ' OR cateid in(select id from ' . tablename('rhinfo_zycj_business_cate') . ' where parentid=' . $_GPC['cateid'] . '))';
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where status=1 and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if (empty($lbs)) {
                if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                    $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                    if (!(0 >= $range) && !($range >= $distance)) {
                        $isout = true;
                    }
                    $data[$k]['distance'] = $distance;
                } else {
                    $data[$k]['distance'] = 100000;
                }
            } else {
                $data[$k]['distance'] = 1000000;
            }
            $data[$k]['businessurl'] = $this->createMobileurl($mydo, array('op' => 'detail', 'id' => $data[$k]['id']));
            $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $data[$k]['id']));
            $data[$k]['thumb'] = tomedia($data[$k]['thumb']);
            $level = 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $data[$k]['id']));
            if ($total > 0) {
                $sql = 'select sum(stars) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid ';
                $stars = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $data[$k]['id']));
                $level = $stars / $total;
            }
            $data[$k]['level'] = $level;
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
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid=:weid and ((pid = 0 and rid=0) or (pid=:pid and rid=:rid)) and parentid=0 and enabled=1' . ' ORDER BY `ID` ASC ';
    $category = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $k = 0;
    while (!($k >= count($category))) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid=:weid and parentid=:parentid and enabled=1' . ' ORDER BY `ID` ASC ';
        $category[$k]['category'] = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':parentid' => $category[$k]['id']));
        ($k += 1) + -1;
    }
    include $this->mymtpl('list');
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
} elseif ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $item['banner'] = iunserializer($item['banner']);
    pdo_update('rhinfo_zycj_business', array('views' => $item['views'] + 1), array('id' => $id, 'weid' => $_W['uniacid']));
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid=:weid and parentid=0 and ((pid=:pid and rid=:rid) or (pid=0 and rid=0))';
    $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $item['follows'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_business_follow') . ' where weid=:weid and bid=:bid limit 1', array(':weid' => $_W['uniacid'], ':bid' => $id));
    $count = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_business_follow') . ' where weid=:weid and bid=:bid and (openid=:openid or uid=:uid) limit 1', array(':weid' => $_W['uniacid'], ':bid' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if ($count > 0) {
        $isfollow = true;
    } else {
        $isfollow = false;
    }
    $level = 0;
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
    if ($total > 0) {
        $sql = 'select sum(stars) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid=:bid ';
        $stars = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
        $level = $stars / $total;
    }
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    if (!empty($item['openid'])) {
        if (pdo_tableexists('messikefu_set')) {
            $sql = 'select count(*) from ' . tablename('messikefu_cservice') . ' where weid=:weid and ctype=1 and content=:openid';
            $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $item['openid']));
            if ($count > 0) {
                $item['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $item['openid'] . '&do=chat&m=cy163_customerservice';
            } else {
                $item['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $item['openid'] . '&do=sanchat&qudao=zhiyunwuye&m=cy163_customerservice';
            }
        }
    }
    include $this->mymtpl('detail');
} elseif ($operation == 'intro') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $content = stripslashes($item['content']);
    $content = html_entity_decode($content);
    include $this->mymtpl('intro');
} elseif ($operation == 'activity') {
    $id = intval($_GPC['id']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_activity') . ' where weid=:weid and bid = :bid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_activity') . ' where weid=:weid and bid = :bid order by ctime desc ' . $limit;
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
        $k = 0;
        while (!($k >= count($list))) {
            if ($list[$k]['starttime'] > TIMESTAMP && !empty($list[$k]['starttime'])) {
                $list[$k]['status'] = '未开始';
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
            }
            if (!($list[$k]['starttime'] > TIMESTAMP) && $list[$k]['endtime'] >= TIMESTAMP && !empty($list[$k]['starttime']) && !empty($list[$k]['endtime'])) {
                $list[$k]['status'] = '进行中';
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
            }
            if (!($list[$k]['endtime'] >= TIMESTAMP) && !empty($list[$k]['endtime'])) {
                $list[$k]['status'] = '已结束';
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
            }
            $list[$k]['starttime'] = date('Y-m-d H:i', $list[$k]['starttime']);
            $list[$k]['endtime'] = date('Y-m-d H:i', $list[$k]['endtime']);
            $list[$k]['url'] = $this->createMobileUrl('business', array('op' => 'actdetail', 'id' => $list[$k]['id']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    } else {
        show_json(0);
    }
} elseif ($operation == 'actdetail') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $data = array('weid' => $_W['uniacid'], 'bid' => $item['bid'], 'aid' => $id, 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'uid' => $_W['member']['uid'], 'signtime' => TIMESTAMP);
        if (!empty($item)) {
            if (!empty($_GPC['realname']) && !empty($_GPC['mobile'])) {
                $res = pdo_insert('rhinfo_zycj_business_activitylog', $data);
                $subid = pdo_insertid();
                if ($res) {
                    $business = pdo_get('rhinfo_zycj_business', array('weid' => $_W['uniacid'], 'id' => $item['bid']), array('openid', 'title', 'telphone'));
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => '活动报名通知'), 'keyword1' => array('value' => '有新的用户报名了', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i'), 'color' => $textcolor), 'keyword3' => array('value' => $_GPC['realname'] . $_GPC['mobile'], 'color' => $textcolor), 'remark' => array('value' => '新的用户报名成功,请确认!'));
                    $postdata1 = array('first' => array('value' => '报名成功通知'), 'keyword1' => array('value' => '您报名成功了', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i'), 'color' => $textcolor), 'keyword3' => array('value' => '你已经成功报名' . $item['title'], 'color' => $textcolor), 'remark' => array('value' => '报名成功,请联系商家' . $business['title'] . $business['telphone']));
                    $url = $this->createMobileurl($mydo, array('op' => 'signup', 'id' => $item['bid']));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($business['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        $this->send_mysendtplnotice($_W['openid'], $this->syscfg['tplid1'], $postdata1, '', $topcolor);
                    }
                    show_json(1);
                } else {
                    show_json(0, '提交失败');
                }
            }
            show_json(0);
        } else {
            show_json(0, '未找到相关活动');
        }
    }
    $content = stripslashes($item['content']);
    $content = html_entity_decode($content);
    $res = $this->send_redpacket($item['strategyid'], $_W['openid'], 1);
    include $this->mymtpl('actdetail');
} elseif ($operation == 'comment') {
    $id = intval($_GPC['id']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid = :bid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_comment') . ' where weid=:weid and bid = :bid order by ctime desc ' . $limit;
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':bid' => $id));
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['noname']) {
            $list[$k]['nickname'] = '匿名';
        }
        $list[$k]['ctime'] = timeBefore($list[$k]['ctime']);
        ($k += 1) + -1;
    }
    show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
} elseif ($operation == 'addcomment') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $data = array('bid' => $id, 'comment' => $_GPC['comment'], 'stars' => $_GPC['stars'], 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'noname' => $_GPC['noname'] ? 0 : 1, 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zycj_business_comment', $data);
        if ($res) {
            pdo_update('rhinfo_zycj_business', array('comment' => $item['comment'] + 1), array('id' => $id, 'weid' => $_W['uniacid']));
            show_json(1);
        } else {
            show_json(0, '提交失败');
        }
    }
    include $this->mymtpl('comment');
} elseif ($operation == 'follow') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        show_json(0, '找不到商家');
    }
    $follow = pdo_fetch('select id from ' . tablename('rhinfo_zycj_business_follow') . ' where weid=:weid and bid=:bid and (openid=:openid or uid=:uid) limit 1', array(':weid' => $_W['uniacid'], ':bid' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (!empty($follow)) {
        pdo_delete('rhinfo_zycj_business_follow', array('id' => $follow['id'], 'weid' => $_W['uniacid']));
        show_json(1, array('isfollow' => false));
    } else {
        $data = array('weid' => $_W['uniacid'], 'bid' => $id, 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'ctime' => time());
        pdo_insert('rhinfo_zycj_business_follow', $data);
        show_json(1, array('isfollow' => true));
    }
} elseif ($operation == 'register') {
    if ($_W['isajax']) {
        show_json(0);
    }
    include $this->mymtpl('register');
} elseif ($operation == 'pay') {
    $id = intval($_GPC['id']);
    $storeid = intval($_GPC['storeid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $fee = $_GPC['money'];
        $exchange = $_GPC['exchange'];
        if (!empty($item['credit']) && !empty($item['cost'])) {
            $credit = intval($fee * $item['credit'] / $item['cost']);
            if ($credit > $item['onhand']) {
                show_json(0, '商家积分余额不足');
            }
        }
        if ($exchange == 1) {
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $returl = !empty($item['paysuccessurl']) ? $item['paysuccessurl'] : $returl;
            $params = array('money' => $_GPC['money'], 'title' => '向商家付款', 'feetype' => 8, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'bid' => $id);
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
            $credit_data = array('weid' => $_W['uniacid'], 'bid' => $id, 'io' => 2, 'credit' => $credit, 'status' => 0, 'title' => '消费' . $fee . '元送积分' . $credit, 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_business_creditlog', $credit_data);
            $creditid = pdo_insertid();
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => '消费送积分申请'), 'keyword1' => array('value' => $fee, 'color' => $topcolor), 'keyword2' => array('value' => date('YmdHis', TIMESTAMP) . $creditid, 'color' => $textcolor), 'remark' => array('value' => '消费' . $fee . '元送积分' . $credit . ',请审核.'));
            if (!empty($this->syscfg['businesstplid'])) {
                $url = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'audit', 'id' => $creditid)));
                $this->send_mysendtplnotice($item['openid'], $this->syscfg['businesstplid'], $postdata, $url, $topcolor);
            } else {
                show_json(0, '模板消息未设置');
            }
            show_json(1);
        }
    }
    include $this->mymtpl('pay');
} elseif ($operation == 'audit') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_creditlog') . ' where weid=:weid and id = :id and status=0';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $url = $this->my_mobileurl($this->createMobileurl('service', array('op' => 'credit1')));
        load()->model('mc');
        $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
        $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['bid']));
        $retcredit = round($item['credit'] * $business['retcredit'] / 100, 2);
        $credit = $item['credit'] - $retcredit;
        if (!($credit > 0)) {
            show_json(0, '赠送积分为0,请检查配置');
        }
        $res = mc_credit_update($item['cuid'], 'credit1', $credit, array(0, '消费送积分', 'rhinfo_zyxq'));
        if ($res) {
            $sql = 'update ' . tablename('rhinfo_zycj_business') . ' set onhand = onhand - ' . $item['credit'] . ',outqty = outqty + ' . $item['credit'] . ',outtime = ' . TIMESTAMP . ' where  weid = :weid and id=:bid';
            pdo_query($sql, array(':weid' => $_W['uniacid'], ':bid' => $item['bid']));
            pdo_update('rhinfo_zycj_business_creditlog', array('status' => 1, 'fromopenid' => $_W['openid'], 'fromuid' => $_W['member']['uid']), array('id' => $id, 'weid' => $_W['uniacid']));
            if ($retcredit > 0) {
                $credit_log_data = array('weid' => $_W['uniacid'], 'io' => 4, 'credit' => $retcredit, 'title' => '消费送积分', 'uid' => $item['touid'], 'openid' => $item['toopenid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                pdo_insert('rhinfo_zycj_platform_credit_log', $credit_log_data);
            }
            mc_notice_credit1($item['toopenid'], $item['cuid'], $credit, '消费送积分', $url, '谢谢惠顾，点击查看详情');
        }
        show_json(1);
    }
    if (empty($item)) {
        $this->mymsg('error', '温馨提示', '该消费已经审核或失效', 'close');
    } else {
        include $this->mymtpl('audit');
    }
} elseif ($operation == 'charge') {
    $paytype = $this->paytype;
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if ($_W['isajax']) {
        $payfees = floatval($_GPC['payfee']);
        if (!($payfees > 0)) {
            echo '支付金额错误';
            exit(0);
        }
        $data['weid'] = $_W['uniacid'];
        $data['uid'] = $_W['member']['uid'];
        $data['openid'] = $_W['openid'];
        $data['title'] = $business['title'];
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
        $data['bid'] = $_GPC['id'];
        $data['sid'] = 0;
        $data['feetype'] = 108;
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
            $res = $this->my_scancode_pay($params, array(), $business);
            if ($res['errno'] == 1) {
                echo $res['message'];
                exit(0);
            }
        }
        pdo_update('rhinfo_zyxq_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $logid));
        show_json(1, '收款成功');
    }
    $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Property';
    $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
    $payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
    $payno = createnum(substr($payno, strlen($paynopre), 14));
    $payno = $paynopre . $payno;
    include $this->mymtpl('charge');
} elseif ($operation == 'mindex') {
    if (!empty($_GPC['id'])) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
        $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and (openid = :openid or uid=:uid)';
        $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        $_GPC['id'] = $business['id'];
    }
    if ($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid']) {
        load()->model('mc');
        $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
        include $this->mymtpl('mindex');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
} elseif ($operation == 'get_today') {
    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and bid=:bid and paytime between :ctime1 and :ctime2';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zycj_business_creditlog') . ' where weid = :weid and bid=:bid and io=2 and  ctime between :ctime1 and :ctime2';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zycj_business_follow') . ' where weid = :weid and bid=:bid and ctime between :ctime1 and :ctime2';
    $params = array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid'], ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $today_repair = pdo_fetchcolumn($sql1, $params);
    $today_suggest = pdo_fetchcolumn($sql2, $params);
    $today_member = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('today_repair' => $today_repair . '笔', 'today_suggest' => $today_suggest . '笔', 'today_member' => $today_member . '人'));
} elseif ($operation == 'get_fee') {
    $starttime30 = strtotime('now -30days');
    $starttime7 = strtotime('now -7days');
    $endtime = TIMESTAMP;
    $sql1 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and feetype=8 and bid=:bid and status=1 and paytime>0 and paytime between :starttime and :endtime ';
    $sql2 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and feetype=8 and bid=:bid and status=1 and paytime>0 and paytime between :starttime and :endtime ';
    $sql3 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and feetype=8 and bid=:bid and status=1 and paytime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paytime),\'%y-%m-%d\')) =0';
    $payfee_week = pdo_fetchcolumn($sql1, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid'], ':starttime' => $starttime7, ':endtime' => $endtime));
    $payfee_week = empty($payfee_week) ? 0 : $payfee_week;
    $payfee_month = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid'], ':starttime' => $starttime30, ':endtime' => $endtime));
    $payfee_month = empty($payfee_month) ? 0 : $payfee_month;
    $payfee_today = pdo_fetchcolumn($sql3, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid']));
    $payfee_today = empty($payfee_today) ? 0 : $payfee_today;
    show_json(1, array('payfee_week' => $payfee_week, 'payfee_month' => $payfee_month, 'payfee_today' => $payfee_today));
} elseif ($operation == 'get_credit') {
    $starttime30 = strtotime('now -30days');
    $starttime7 = strtotime('now -7days');
    $endtime = TIMESTAMP;
    $sql1 = 'select sum(credit) from ' . tablename('rhinfo_zycj_business_creditlog') . ' where weid = :weid and bid=:bid and io=2 and ctime>0 and ctime between :starttime and :endtime ';
    $sql2 = 'select sum(credit) from ' . tablename('rhinfo_zycj_business_creditlog') . ' where weid = :weid and bid=:bid and io=2 and ctime>0 and ctime between :starttime and :endtime ';
    $sql3 = 'select sum(credit) from ' . tablename('rhinfo_zycj_business_creditlog') . ' where weid = :weid and bid=:bid and io=2 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0';
    $paycredit_week = pdo_fetchcolumn($sql1, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid'], ':starttime' => $starttime7, ':endtime' => $endtime));
    $paycredit_week = empty($paycredit_week) ? 0 : $paycredit_week;
    $paycredit_month = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid'], ':starttime' => $starttime30, ':endtime' => $endtime));
    $paycredit_month = empty($paycredit_month) ? 0 : $paycredit_month;
    $paycredit_today = pdo_fetchcolumn($sql3, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['bid']));
    $paycredit_today = empty($paycredit_today) ? 0 : $paycredit_today;
    show_json(1, array('paycredit_week' => $paycredit_week, 'paycredit_month' => $paycredit_month, 'paycredit_today' => $paycredit_today));
} elseif ($operation == 'myset') {
    if ($_W['isajax']) {
        if (empty($_GPC['title'])) {
            show_json(0, '请输入商家名称');
        }
        if (empty($_GPC['telphone'])) {
            show_json(0, '请输入电话');
        }
        if (empty($_GPC['mobile'])) {
            show_json(0, '请输入手机号码');
        }
        $data = array('title' => $_GPC['title'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'thumb' => $_GPC['thumb']);
        pdo_update('rhinfo_zycj_business', $data, array('weid' => $_W['uniacid'], 'id' => $_GPC['id']));
        show_json(1);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    include $this->mymtpl('myset');
} elseif ($operation == 'credit') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_business_creditlog') . ' WHERE weid=:weid and bid= :bid ';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':bid' => $_GPC['id']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business_creditlog') . ' WHERE weid=:weid and bid= :bid ORDER BY ctime DESC ' . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $_W['weid'], ':bid' => $_GPC['id']));
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credittitle = $creditnames[$behavior['activity']]['title'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    include $this->mymtpl('credit');
} elseif ($operation == 'recharge') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    if ($_W['isajax']) {
        $fee = $_GPC['money'];
        if ($this->syscfg['cost'] > 0 && $this->syscfg['credit'] > 0) {
            $credit = intval($fee * $this->syscfg['credit'] / $this->syscfg['cost']);
            $sql = 'select * from ' . tablename('rhinfo_zycj_platform_credit') . ' where weid=:weid';
            $platcredit = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
            if (!($platcredit['onhand'] >= $credit)) {
                show_json(0, '支付失败', '平台积分余额不足');
            }
        } else {
            show_json(0, '支付失败', '积分充值比例未设置');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $charging['rid']));
        $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
        $params = array('money' => $fee, 'title' => '账户充值', 'feetype' => 7, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'bid' => $business['id']);
        $res = $this->my_single_pay($params);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    include $this->mymtpl('recharge');
} elseif ($operation == 'qrcode') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if ($business['uid'] == $_W['member']['uid']) {
        $qrcode_url = $this->createMobileurl($mydo, array('op' => 'pay', 'id' => $_GPC['id']));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
        include $this->mymtpl('qrcode');
    } else {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
} elseif ($operation == 'payfee') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and feetype=8 and bid=:bid ';
        $params[':bid'] = $_GPC['bid'];
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
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    include $this->mymtpl('payfee');
} elseif ($operation == 'paycredit') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and bid=:bid ';
        $params[':bid'] = $_GPC['bid'];
        if (!empty($_GPC['paydate'])) {
            $condition .= ' and ctime >=' . strtotime($_GPC['paydate']);
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_business_creditlog') . ' where io=2 and ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business_creditlog') . ' where io=2 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['paydate'] = empty($list[$k]['ctime']) ? '' : date('Y-m-d', $list[$k]['ctime']);
            $list[$k]['toopenid'] = empty($list[$k]['toopenid']) ? $list[$k]['touid'] : $list[$k]['toopenid'];
            $fans = mc_fansinfo($list[$k]['toopenid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    include $this->mymtpl('paycredit');
} elseif ($operation == 'signup') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and bid=:bid ';
        $params[':bid'] = $_GPC['bid'];
        if (!empty($_GPC['signtime'])) {
            $condition .= ' and signtime >=' . strtotime($_GPC['signtime']);
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_business_activitylog') . ' where ' . $condition, $params);
        $condition .= ' order by signtime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business_activitylog') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['signtime'] = date('Y-m-d H:i', $list[$k]['signtime']);
            $fans = mc_fansinfo($list[$k]['uid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    include $this->mymtpl('signup');
} elseif ($operation == 'givecredit') {
    if ($_W['ispost']) {
        if ($_GPC['consume'] == 1) {
            header('Location:' . $this->createMobileurl($mydo, array('op' => 'givesubmit1', 'id' => $_GPC['id'], 'mobile' => $_GPC['mobile'], 'credit' => $_GPC['credit'])));
        } else {
            header('Location:' . $this->createMobileurl($mydo, array('op' => 'givesubmit', 'id' => $_GPC['id'], 'mobile' => $_GPC['mobile'], 'credit' => $_GPC['credit'])));
        }
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    $mycredit = $business['onhand'];
    if ($_GPC['consume'] == 1 && $business['creditrule'] == 0) {
        if (!empty($business['cost']) && !empty($business['credit'])) {
            $mycredit = intval($business['onhand'] * $business['cost'] / $business['credit']);
        } else {
            $mycredit = 0;
        }
    }
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    if ($_GPC['consume'] == 1) {
        include $this->mymtpl('givecredit1');
    } else {
        include $this->mymtpl('givecredit');
    }
} elseif ($operation == 'checkmobile') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile and deleted=0 and status=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    if (empty($member)) {
        show_json(0, '未找到用户或业主还未通过认证，请确认手机号是否正确');
    }
    show_json(1);
} elseif ($operation == 'givesubmit') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    load()->model('mc');
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and uid = :uid';
        $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_GPC['uid']));
        if (empty($member)) {
            show_json(0, '未找到用户');
        }
        $fan = mc_fansinfo($member['uid'], $_W['acid'], $_W['uniacid']);
        $credit = $_GPC['credit'];
        $url = $this->createMobileurl('service', array('op' => 'credit1'));
        $url = str_replace('addons/rhinfo_zyxq/', '', $this->my_mobileurl($url));
        $sql = 'update ' . tablename('rhinfo_zycj_business') . ' set onhand = onhand - ' . $credit . ', outqty = outqty - ' . $credit . ', outtime = ' . TIMESTAMP . ' where weid=:weid and id=:bid';
        $res_from = pdo_query($sql, array(':weid' => $_W['uniacid'], ':bid' => $business['id']));
        if ($business['retcredit'] > 0 && !($business['retcredit'] >= 100)) {
            $retcredit = round($credit * $business['retcredit'] / 100, 2);
            $credit_log_data = array('weid' => $_W['uniacid'], 'io' => 4, 'credit' => $retcredit, 'title' => '消费送积分', 'uid' => $_GPC['uid'], 'openid' => $fan['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_platform_credit_log', $credit_log_data);
            $getcredit = $credit - $retcredit;
        } else {
            $getcredit = $credit;
        }
        if ($res_from) {
            $credit_data = array('weid' => $_W['uniacid'], 'bid' => $_GPC['id'], 'io' => 2, 'credit' => $credit, 'title' => '赠送积分' . $credit, 'toopenid' => $fan['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_business_creditlog', $credit_data);
            $res_to = mc_credit_update($_GPC['uid'], 'credit1', $getcredit, array(0, '获得' . $business['title'] . '赠送积分', 'rhinfo_zyxq'));
            if ($res_to) {
                $fan = mc_fansinfo($_GPC['uid'], $_W['acid'], $_W['uniacid']);
                mc_notice_credit1($fan['openid'], $_GPC['uid'], $getcredit, '获得' . $business['title'] . '赠送积分', $url, '点击查看详情');
            }
        }
        show_json(1, '赠送成功');
    }
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile and deleted=0 and status=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    $fans = mc_fansinfo($member['uid'], $_W['acid'], $_W['uniacid']);
    $member['avatar'] = $fans['avatar'];
    include $this->mymtpl('givesubmit');
} elseif ($operation == 'givesubmit1') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid=:weid and id = :id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    load()->model('mc');
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and uid = :uid';
        $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_GPC['uid']));
        if (empty($member)) {
            show_json(0, '未找到用户');
        }
        $credit = 0;
        if (!empty($business['cost']) && !empty($business['credit'])) {
            $credit = round($_GPC['credit'] * $business['credit'] / $business['cost'], 2);
            if ($credit > $business['onhand']) {
                show_json(0, '积分余额不足');
            }
        } else {
            show_json(0, '没有设定兑换比例');
        }
        if (empty($credit)) {
            show_json(0, '积分为0');
        }
        $fan = mc_fansinfo($member['uid'], $_W['acid'], $_W['uniacid']);
        $url = $this->createMobileurl('service', array('op' => 'credit1'));
        $url = str_replace('addons/rhinfo_zyxq/', '', $this->my_mobileurl($url));
        $sql = 'update ' . tablename('rhinfo_zycj_business') . ' set onhand = onhand - ' . $credit . ', outqty = outqty - ' . $credit . ', outtime = ' . TIMESTAMP . ' where weid=:weid and id=:bid';
        $res_from = pdo_query($sql, array(':weid' => $_W['uniacid'], ':bid' => $business['id']));
        if ($business['retcredit'] > 0 && !($business['retcredit'] >= 100)) {
            $retcredit = round($credit * $business['retcredit'] / 100, 2);
            $credit_log_data = array('weid' => $_W['uniacid'], 'io' => 4, 'credit' => $retcredit, 'title' => '消费送积分', 'uid' => $_GPC['uid'], 'openid' => $fan['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_platform_credit_log', $credit_log_data);
            $getcredit = $credit - $retcredit;
        } else {
            $getcredit = $credit;
        }
        if ($res_from) {
            $credit_data = array('weid' => $_W['uniacid'], 'bid' => $_GPC['id'], 'io' => 2, 'credit' => $credit, 'title' => '赠送积分' . $credit, 'toopenid' => $fan['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_business_creditlog', $credit_data);
            $res_to = mc_credit_update($_GPC['uid'], 'credit1', $getcredit, array(0, '获得' . $business['title'] . '赠送积分', 'rhinfo_zyxq'));
            if ($res_to) {
                $fan = mc_fansinfo($_GPC['uid'], $_W['acid'], $_W['uniacid']);
                mc_notice_credit1($fan['openid'], $_GPC['uid'], $getcredit, '获得' . $business['title'] . '赠送积分', $url, '点击查看详情');
            }
        }
        show_json(1, '赠送成功');
    }
    if (!($business['openid'] == $_W['openid'] || $business['uid'] == $_W['member']['uid'])) {
        $this->mymsg('error', '温馨提示', '您还没有商家权限', 'close');
    }
    $credit = 0;
    if (!empty($business['cost']) && !empty($business['credit'])) {
        $credit = round($_GPC['credit'] * $business['credit'] / $business['cost'], 2);
        if ($credit > $business['onhand']) {
            $this->mymsg('error', '温馨提示', '积分余额不足', 'close');
        }
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile and deleted=0 and status=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    $fans = mc_fansinfo($member['uid'], $_W['acid'], $_W['uniacid']);
    $member['avatar'] = $fans['avatar'];
    include $this->mymtpl('givesubmit1');
}