<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '系统管理';
$mydo = 'sysset';
$tablename = 'rhinfo_zyxq_sysset';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
if ($operation == 'index') {
    $current = '我的设置';
    $rights = $this->myrights(1, $mydo, 'index');
    $id = intval($_GPC['id']);
    if ($_W['uid']) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition;
        $item = pdo_fetch($sql, $params);
        if ($_W['ispost']) {
            $savepath = IA_ROOT . '/addons/rhinfo_zyxq/cert/wechat/' . $_W['uniacid'] . '/';
            if (!is_dir($savepath)) {
                load()->func('file');
                mkdirs($savepath);
            }
            $apiclient_cert_filepath = $item['apiclient_cert'];
            $apiclient_cert_tmp_file = $_FILES['apiclient_cert']['tmp_name'];
            if (!empty($apiclient_cert_tmp_file)) {
                $apiclient_cert_file = $_FILES['apiclient_cert']['name'];
                copy($apiclient_cert_tmp_file, $savepath . $apiclient_cert_file);
                $apiclient_cert_filepath = MODULE_ROOT . '/cert/wechat/' . $_W['uniacid'] . '/' . $apiclient_cert_file;
            }
            $apiclient_key_filepath = $item['apiclient_key'];
            $apiclient_key_tmp_file = $_FILES['apiclient_key']['tmp_name'];
            if (!empty($apiclient_key_tmp_file)) {
                $apiclient_key_file = $_FILES['apiclient_key']['name'];
                copy($apiclient_key_tmp_file, $savepath . $apiclient_key_file);
                $apiclient_key_filepath = MODULE_ROOT . '/cert/wechat/' . $_W['uniacid'] . '/' . $apiclient_key_file;
            }
            $rootca_filepath = $item['rootca'];
            $rootca_tmp_file = $_FILES['rootca']['tmp_name'];
            if (!empty($rootca_tmp_file)) {
                $rootca_file = $_FILES['rootca']['name'];
                copy($rootca_tmp_file, $savepath . $rootca_file);
                $rootca_filepath = MODULE_ROOT . '/cert/wechat/' . $_W['uniacid'] . '/' . $rootca_file;
            }
            $alipay_life_key = is_array($_GPC['alipay_life_key']) ? iserializer($_GPC['alipay_life_key']) : iserializer(array());
            $alipay_app_key = is_array($_GPC['alipay_app_key']) ? iserializer($_GPC['alipay_app_key']) : iserializer(array());
            $data = array('title' => $_GPC['title'], 'logo' => $_GPC['logo'], 'copyright' => $_POST['copyright'], 'telphone' => $_GPC['telphone'], 'isclose' => $_GPC['isclose'], 'closereason' => $_GPC['closereason'], 'closeurl' => $_GPC['closeurl'], 'wxtitle' => $_GPC['wxtitle'], 'wxcopyright' => $_POST['wxcopyright'], 'statistics' => $_POST['statistics'], 'adminstyle' => $_GPC['adminstyle'], 'style' => $_GPC['style'], 'menucolor' => $_GPC['menucolor'], 'btncolor' => $_GPC['btncolor'], 'bgcolor' => $_GPC['bgcolor'], 'menu1' => $_GPC['menu1'], 'menu2' => $_GPC['menu2'], 'menu3' => $_GPC['menu3'], 'menu4' => $_GPC['menu4'], 'menu5' => $_GPC['menu5'], 'menu1url' => $_GPC['menu1url'], 'menu2url' => $_GPC['menu2url'], 'menu3url' => $_GPC['menu3url'], 'menu4url' => $_GPC['menu4url'], 'menu5url' => $_GPC['menu5url'], 'icon1' => $_GPC['icon1'], 'icon2' => $_GPC['icon2'], 'icon3' => $_GPC['icon3'], 'icon4' => $_GPC['icon4'], 'icon5' => $_GPC['icon5'], 'stetext1' => $_GPC['stetext1'], 'stetext2' => $_GPC['stetext2'], 'stetext3' => $_GPC['stetext3'], 'stetext4' => $_GPC['stetext4'], 'servicedisplay' => $_GPC['servicedisplay'], 'topcolor' => $_GPC['topcolor'], 'textcolor' => $_GPC['textcolor'], 'feecolor' => $_GPC['feecolor'], 'tplid1' => $_GPC['tplid1'], 'tplid2' => $_GPC['tplid2'], 'tplid3' => $_GPC['tplid3'], 'tplid4' => $_GPC['tplid4'], 'tplid5' => $_GPC['tplid5'], 'tplid6' => $_GPC['tplid6'], 'tplid7' => $_GPC['tplid7'], 'tplid8' => $_GPC['tplid8'], 'tplid9' => $_GPC['tplid9'], 'tplid10' => $_GPC['tplid10'], 'tplid11' => $_GPC['tplid11'], 'tplid12' => $_GPC['tplid12'], 'smstype' => $_GPC['smstype'], 'appkey' => $_GPC['appkey'], 'app_key' => $_GPC['app_key'], 'app_serve' => $_GPC['app_serve'], 'signname' => $_GPC['signname'], 'verifyid' => $_GPC['verifyid'], 'repairid' => $_GPC['repairid'], 'suggestid' => $_GPC['suggestid'], 'feeid' => $_GPC['feeid'], 'noticeid' => $_GPC['noticeid'], 'bindverify' => $_GPC['bindverify'], 'shareverify' => $_GPC['shareverify'], 'cost' => $_GPC['cost'], 'minmoney' => $_GPC['minmoney'], 'credit' => $_GPC['credit'], 'credit2' => $_GPC['credit2'], 'apikey' => $_GPC['apikey'], 'apiid' => $_GPC['apiid'], 'bl_apikey' => $_GPC['bl_apikey'], 'bl_apiid' => $_GPC['bl_apiid'], 'mj_apikey' => $_GPC['mj_apikey'], 'mj_apiid' => $_GPC['mj_apiid'], 'iswxpay' => $_GPC['iswxpay'], 'appid' => $_GPC['appid'], 'secret' => $_GPC['secret'], 'merchid' => $_GPC['merchid'], 'merchsecret' => $_GPC['merchsecret'], 'serviceappid' => $_GPC['serviceappid'], 'serviceappsecret' => $_GPC['serviceappsecret'], 'servicemerchid' => $_GPC['servicemerchid'], 'servicemerchsecret' => $_GPC['servicemerchsecret'], 'submerchid' => $_GPC['submerchid'], 'isalipay' => $_GPC['isalipay'], 'aliaccount' => $_GPC['aliaccount'], 'alipartner' => $_GPC['alipartner'], 'alisecret' => $_GPC['alisecret'], 'locationdoor' => $_GPC['locationdoor'], 'regionrange' => $_GPC['regionrange'], 'doorrange' => $_GPC['doorrange'], 'followurl' => $_GPC['followurl'], 'qrcode' => $_GPC['qrcode'], 'sharetitle' => $_GPC['sharetitle'], 'shareicon' => $_GPC['shareicon'], 'sharedesc' => $_GPC['sharedesc'], 'isweaddress' => $_GPC['isweaddress'], 'memberfield' => $_GPC['memberfield'], 'isrraddress' => $_GPC['isrraddress'], 'ishome' => $_GPC['ishome'], 'stewarddisplay' => $_GPC['stewarddisplay'], 'isoneregion' => $_GPC['isoneregion'], 'iswegroup' => $_GPC['iswegroup'], 'service' => $_GPC['service'], 'imagesize' => $_GPC['imagesize'], 'outtime' => $_GPC['outtime'], 'ptitle' => $_GPC['ptitle'], 'plogo' => $_GPC['plogo'], 'listtype' => $_GPC['listtype'], 'app_code' => $_GPC['app_code'], 'alisignname' => $_GPC['alisignname'], 'pagesize' => $_GPC['pagesize'], 'membertext' => $_GPC['membertext'], 'isshowfeebill' => $_GPC['isshowfeebill'], 'binddomain' => $_GPC['binddomain'], 'pbackground' => $_GPC['pbackground'], 'pbgcolor' => $_GPC['pbgcolor'], 'bindlocation' => $_GPC['bindlocation'], 'isplatformcredit' => $_GPC['isplatformcredit'], 'paynopre' => $_GPC['paynopre'], 'apiclient_cert' => $apiclient_cert_filepath, 'apiclient_key' => $apiclient_key_filepath, 'rootca' => $rootca_filepath, 'businesstplid' => $_GPC['businesstplid'], 'redpackettplid' => $_GPC['redpackettplid'], 'siteurl' => $_GPC['siteurl'], 'doortype' => $_GPC['doortype'], 'alipay_appid' => $_GPC['alipay_appid'], 'alipay_rsa2' => $_GPC['alipay_rsa2'], 'alipay_private' => $_GPC['alipay_private'], 'limitqty' => $_GPC['limitqty'], 'iswap' => $_GPC['iswap'], 'wxappid' => $_GPC['wxappid'], 'wxappsecret' => $_GPC['wxappsecret'], 'wxmerchid' => $_GPC['wxmerchid'], 'wxmerchsecret' => $_GPC['wxmerchsecret'], 'access_key_id' => $_GPC['access_key_id'], 'access_key_secret' => $_GPC['access_key_secret'], 'aliaccsignname' => $_GPC['aliaccsignname'], 'isreg' => $_GPC['isreg'], 'movecarid' => $_GPC['movecarid'], 'wxminiappid' => $_GPC['wxminiappid'], 'wxminiappsecret' => $_GPC['wxminiappsecret'], 'wxminimerchid' => $_GPC['wxminimerchid'], 'wxminimerchsecret' => $_GPC['wxminimerchsecret'], 'wxminiappurl' => $_GPC['wxminiappurl'], 'wxminibgimage' => $_GPC['wxminibgimage'], 'devpaytype' => $_GPC['devpaytype'], 'mx_appid' => $_GPC['mx_appid'], 'mx_appkey' => $_GPC['mx_appkey'], 'ds_appid' => $_GPC['ds_appid'], 'ds_appkey' => $_GPC['ds_appkey'], 'yk_appid' => $_GPC['yk_appid'], 'yk_appkey' => $_GPC['yk_appkey'], 'smsprice' => $_GPC['smsprice'], 'isworkersound' => $_GPC['isworkersound'], 'workermanurl' => $_GPC['workemanrurl'], 'wxminititle' => $_GPC['wxminititle'], 'isdisptel' => $_GPC['isdisptel'], 'displayweather' => $_GPC['displayweather'], 'memberheaderstyle' => $_GPC['memberheaderstyle'], 'memberliststyle' => $_GPC['memberliststyle'], 'managemenustyle' => $_GPC['managemenustyle'], 'isdevreg' => $_GPC['isdevreg'], 'isdidplayscan' => $_GPC['isdidplayscan'], 'followtip' => $_GPC['followtip'], 'isshowrepair' => $_GPC['isshowrepair'], 'isshowhouse' => $_GPC['isshowhouse'], 'isshowforum' => $_GPC['isshowforum'], 'isshowvisit' => $_GPC['isshowvisit'], 'isshowtask' => $_GPC['isshowtask'], 'isshowlike' => $_GPC['isshowlike'], 'isshowdevice' => $_GPC['isshowdevice'], 'wethercolor' => $_GPC['wethercolor'], 'forumthumb' => $_GPC['forumthumb'], 'forumicon' => $_GPC['forumicon'], 'forumright' => $_GPC['forumright'], 'forumbottom' => $_GPC['forumbottom'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'qashowtype' => $_GPC['qashowtype'], 'isshowqa' => $_GPC['isshowqa'], 'credit3' => $_GPC['credit3'], 'expressid' => $_GPC['expressid'], 'expresssendid' => $_GPC['expresssendid'], 'nobindhome' => $_GPC['nobindhome'], 'isopenapi' => $_GPC['isopenapi'], 'api_token' => $_GPC['api_token'], 'isshowexpress' => $_GPC['isshowexpress'], 'expresstplid' => $_GPC['expresstplid'], 'isdiscredit' => $_GPC['isdiscredit'], 'ymfmerchid' => $_GPC['ymfmerchid'], 'ymfmerchsecret' => $_GPC['ymfmerchsecret'], 'ymfurl' => $_GPC['ymfurl'], 'rsdmerchid' => $_GPC['rsdmerchid'], 'rsdmerchsecret' => $_GPC['rsdmerchsecret'], 'starkey' => $_GPC['starkey'], 'starorg' => $_GPC['starorg'], 'startrm' => $_GPC['startrm'], 'starmerchid' => $_GPC['starmerchid'], 'isaddregion' => $_GPC['isaddregion'], 'board_password' => $_GPC['board_password'], 'iswepay' => $_GPC['iswepay'], 'forcefollow' => $_GPC['forcefollow'], 'ylb_token' => $_GPC['ylb_token'], 'isproperty' => $_GPC['isproperty'], 'isvisitor' => $_GPC['isvisitor'], 'alipay_type' => $_GPC['alipay_type'], 'alipay_life_appid' => $_GPC['alipay_life_appid'], 'alipay_life_key' => $alipay_life_key, 'alipay_app_appid' => $_GPC['alipay_app_appid'], 'alipay_app_key' => $alipay_app_key, 'alipay_app_title' => $_GPC['alipay_app_title'], 'alipay_app_url' => $_GPC['alipay_app_url'], 'alipay_seller_id' => $_GPC['alipay_seller_id'], 'alipay_app_auth_token' => $_GPC['alipay_app_auth_token']);
            if ($item) {
                $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
            } else {
                $data['weid'] = $mywe['weid'];
                pdo_insert($tablename, $data);
            }
            header('Location:' . $this->createWeburl($mydo, array('op' => 'index')) . $mywe['direct']);
            exit(0);
        }
        if ($item['isproperty'] == 1) {
            $propertyadmin = $_W['siteroot'] . 'web' . substr(mywurl('property', array('direct' => 1), 1), 1, strlen(mywurl('property', array('direct' => 1), 1)));
        } else {
            $propertyadmin = $_W['siteroot'] . 'web' . substr(mywurl('auth', array('direct' => 1), 1), 1, strlen(mywurl('auth', array('direct' => 1), 1)));
        }
        $item['alipay_life_key'] = iunserializer($item['alipay_life_key']);
        $item['alipay_app_key'] = iunserializer($item['alipay_app_key']);
        $property = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'property')));
        $home = $this->my_mobileurl($this->createMobileUrl('home'));
        $notice = $this->my_mobileurl($this->createMobileUrl('notice'));
        $service = $this->my_mobileurl($this->createMobileUrl('service'));
        $steward = $this->my_mobileurl($this->createMobileUrl('steward'));
        $member = $this->my_mobileurl($this->createMobileUrl('member'));
        $myrepair = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'myrepair')));
        $mysuggest = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'mysuggest')));
        $opendoor = $this->my_mobileurl($this->createMobileUrl('opendoor'));
        $payfee = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
        $forum = $this->my_mobileurl($this->createMobileUrl('forum', array('op' => 'index')));
        $repair = $this->my_mobileurl($this->createMobileUrl('steward', array('op' => 'repair')));
        $suggest = $this->my_mobileurl($this->createMobileUrl('steward', array('op' => 'suggest')));
        $bind = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'blist')));
        $fav = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'flist')));
        $myfav = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfav')));
        $paycenter = $this->my_mobileurl($this->createMobileUrl('fee', array('op' => 'index')));
        $article = $this->my_mobileurl($this->createMobileUrl('article', array('op' => 'index')));
        $manage = $this->my_mobileurl($this->createMobileUrl('manage', array('op' => 'index')));
        $myhouse = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myhouse')));
        $myparking = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myparking')));
        $mycar = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'mycar')));
        $region = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'list')));
        $property_qrcode = $this->createqrcode($property);
        $home_qrcode = $this->createqrcode($home);
        $notice_qrcode = $this->createqrcode($notice);
        $service_qrcode = $this->createqrcode($service);
        $steward_qrcode = $this->createqrcode($steward);
        $member_qrcode = $this->createqrcode($member);
        $repair_qrcode = $this->createqrcode($repair);
        $suggest_qrcode = $this->createqrcode($suggest);
        $opendoor_qrcode = $this->createqrcode($opendoor);
        $payfee_qrcode = $this->createqrcode($payfee);
        $forum_qrcode = $this->createqrcode($forum);
        $myrepair_qrcode = $this->createqrcode($myrepair);
        $mysuggest_qrcode = $this->createqrcode($mysuggest);
        $bind_qrcode = $this->createqrcode($bind);
        $fav_qrcode = $this->createqrcode($fav);
        $myfav_qrcode = $this->createqrcode($myfav);
        $paycenter_qrcode = $this->createqrcode($paycenter);
        $article_qrcode = $this->createqrcode($article);
        $manage_qrcode = $this->createqrcode($manage);
        $myhouse_qrcode = $this->createqrcode($myhouse);
        $myparking_qrcode = $this->createqrcode($myparking);
        $mycar_qrcode = $this->createqrcode($mycar);
        $region_qrcode = $this->createqrcode($region);
        $business = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'index')));
        $business_qrcode = $this->createqrcode($business);
        $businesslist = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'list')));
        $businesslist_qrcode = $this->createqrcode($businesslist);
        $businessadmin = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'mindex')));
        $businessadmin_qrcode = $this->createqrcode($businessadmin);
        $tasklist = $this->my_mobileurl($this->createMobileUrl('task', array('op' => 'index')));
        $tasklist_qrcode = $this->createqrcode($tasklist);
        $movecar = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'moveadd')));
        $movecar_qrcode = $this->createqrcode($movecar);
        $charginglist = $this->my_mobileurl($this->createMobileUrl('charging', array('op' => 'list')));
        $charginglist_qrcode = $this->createqrcode($charginglist);
        $weather = $this->my_mobileurl($this->createMobileUrl('weather', array('op' => 'index')));
        $weather_qrcode = $this->createqrcode($weather);
        $selfdevicelist = $this->my_mobileurl($this->createMobileUrl('selfdevice', array('op' => 'list')));
        $selfdevicelist_qrcode = $this->createqrcode($selfdevicelist);
        $leaseindex = $this->my_mobileurl($this->createMobileUrl('lease', array('op' => 'index')));
        $leaseindex_qrcode = $this->createqrcode($leaseindex);
        $proprietorindex = $this->my_mobileurl($this->createMobileUrl('proprietor', array('op' => 'index')));
        $proprietorindex_qrcode = $this->createqrcode($proprietorindex);
        $questionindex = $this->my_mobileurl($this->createMobileUrl('question', array('op' => 'index')));
        $questionindex_qrcode = $this->createqrcode($questionindex);
        $telphoneindex = $this->my_mobileurl($this->createMobileUrl('telphone', array('op' => 'index')));
        $telphoneindex_qrcode = $this->createqrcode($telphoneindex);
        $expressindex = $this->my_mobileurl($this->createMobileUrl('expressp', array('op' => 'list')));
        $expressindex_qrcode = $this->createqrcode($expressindex);
        $expressmindex = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'mindex')));
        $expressmindex_qrcode = $this->createqrcode($expressmindex);
        $expresseindex = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'eindex')));
        $expresseindex_qrcode = $this->createqrcode($expresseindex);
        $myexpress = $this->my_mobileurl($this->createMobileUrl('expressp', array('op' => 'myexpress')));
        $myexpress_qrcode = $this->createqrcode($myexpress);
        $regproperty = $this->my_mobileurl($this->createMobileUrl('auth', array('op' => 'regproperty')));
        $regproperty_qrcode = $this->createqrcode($regproperty);
        $parklist = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'parklist')));
        $parklist_qrcode = $this->createqrcode($parklist);
        $parkpay = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'pay')));
        $parkpay_qrcode = $this->createqrcode($parkpay);
        $parkshare = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'sharelist')));
        $parkshare_qrcode = $this->createqrcode($parkshare);
        $activity = $this->my_mobileurl($this->createMobileUrl('activity', array('op' => 'list')));
        $activity_qrcode = $this->createqrcode($activity);
        load()->model('mc');
        $fans = array();
        $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
        $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
        $item['nickname1'] = $fans['nickname'];
        $scripturl = !empty($item['siteurl']) ? $item['siteurl'] : $_W['siteroot'];
        $scripturl = $scripturl . 'app' . substr($this->createMobileUrl('home', array('op' => 'bindlist')), 1, strlen($this->createMobileUrl('home', array('op' => 'bindlist'))));
        $scripthtml = "<script type=\"text/javascript\" src=\"../addons/rhinfo_zyxq/static/static/lib/jqery/jquery-1.11.1.min.js\"></script>\r\n\t\t\t\t<script type=\"text/javascript\">\r\n\t\t\t\t\$.ajax({   \r\n\t\t\t\t\turl:\"" . $scripturl . "\",   \r\n\t\t\t\t\ttype:\"post\", \r\n\t\t\t\t\tdata:{qrcode:\"" . $bind_qrcode . "\"},\r\n\t\t\t\t\tdataType:\"html\",\r\n\t\t\t\t\tsuccess:function(data){  \r\n\t\t\t\t\t\tif(data.length==0){\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\telse{\r\n\t\t\t\t\t\t\t\$(\"body\").append(data);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}\r\n\t\t\t\t});\r\n\t\t\t\t</script>";
        $scripthtml = htmlspecialchars($scripthtml);
        $adminpath = MODULE_ROOT . '/template/';
        if (is_dir($adminpath)) {
            $adminhandle = opendir($adminpath);
            if (opendir($adminpath)) {
                while (true) {
                    $admintemplatepath = readdir($adminhandle);
                    if (!(false !== readdir($adminhandle))) {
                        break;
                    }
                    if ($admintemplatepath != '.' && $admintemplatepath != '..' && $admintemplatepath != 'mobile' && $admintemplatepath != 'webapp' && $admintemplatepath != 'mywebapp' && $admintemplatepath != 'welcome') {
                        if (is_dir($adminpath . $admintemplatepath)) {
                            $admintemplate[] = $admintemplatepath;
                        }
                    }
                }
            }
        }
        $path = MODULE_ROOT . '/template/mobile/';
        if (is_dir($path)) {
            $handle = opendir($path);
            if (opendir($path)) {
                while (true) {
                    $templatepath = readdir($handle);
                    if (!(false !== readdir($handle))) {
                        break;
                    }
                    if ($templatepath != '.' && $templatepath != '..') {
                        if (is_dir($path . $templatepath)) {
                            $template[] = $templatepath;
                        }
                    }
                }
            }
        }
    } else {
        if ($_W['ispost']) {
            $area = $_GPC['area'];
            $data = array('title' => $_GPC['title'], 'logo' => $_GPC['logo'], 'image' => $_GPC['image'], 'banner' => $_GPC['banner'], 'content' => $_GPC['content'], 'telphone' => $_GPC['telphone'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'website' => $_GPC['website'], 'board_password' => $_GPC['board_password']);
            $glue = 'AND';
            $result = pdo_update('rhinfo_zyxq_property', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
            header('Location:' . $this->createWeburl($mydo, array('op' => 'index')) . $mywe['direct']);
            exit(0);
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where id = :id and weid = :weid';
        $company = pdo_fetch($sql, array(':id' => $mywe['pid'], ':weid' => $mywe['weid']));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secacc') . ' where weid=:weid and status=1';
    $secacc = pdo_fetch($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl();
} elseif ($operation == 'secsys') {
    if ($_W['ispost']) {
        $sysid = $_GPC['sysid'];
        $title = $_GPC['title'];
        $displayorder = $_GPC['displayorder'];
        $isdisplay = $_GPC['isdisplay'];
        if (!empty($sysid)) {
            $k = 0;
            while (!($k >= count($sysid))) {
                $data = array('title' => $title[$k], 'displayorder' => $displayorder[$k], 'isdisplay' => $isdisplay[$k]);
                pdo_update('rhinfo_zyxq_secsys', $data, array('id' => $sysid[$k]));
                ($k += 1) + -1;
            }
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secsys') . ' order by displayorder,id ';
    $secsys = pdo_fetchall($sql);
    include $this->mywtpl('secsys');
} elseif ($operation == 'secprg') {
    if ($_W['ispost']) {
        $prgid = $_GPC['prgid'];
        $title = $_GPC['title'];
        $displayorder = $_GPC['displayorder'];
        $isdismenu = $_GPC['isdismenu'];
        if (!empty($prgid)) {
            $k = 0;
            while (!($k >= count($prgid))) {
                $data = array('title' => $title[$k], 'displayorder' => $displayorder[$k], 'isdismenu' => $isdismenu[$k]);
                pdo_update('rhinfo_zyxq_secprg', $data, array('id' => $prgid[$k]));
                ($k += 1) + -1;
            }
        }
    }
    $sql = 'select s.title as stitle,p.title,p.displayorder,p.id,p.isdismenu from ' . tablename('rhinfo_zyxq_secprg') . ' as p left join ' . tablename('rhinfo_zyxq_secsys') . ' as s on p.sid=s.id where p.isdisplay=1 order by s.displayorder,s.id,p.displayorder,p.id ';
    $secprg = pdo_fetchall($sql);
    include $this->mywtpl('secprg');
} elseif ($operation == 'secpub') {
    $current = '全局设置';
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'logo' => $_GPC['logo'], 'copyright' => $_POST['copyright'], 'ptitle' => $_GPC['ptitle'], 'plogo' => $_GPC['plogo'], 'pbgcolor' => $_GPC['pbgcolor'], 'pbackground' => $_GPC['pbackground'], 'isclose' => $_GPC['isclose'], 'closereason' => $_GPC['closereason'], 'closeurl' => $_GPC['closeurl'], 'wxtitle' => $_GPC['wxtitle'], 'wxcopyright' => $_POST['wxcopyright'], 'statistics' => $_POST['statistics'], 'wecopyright' => $_GPC['wecopyright'], 'board_password' => $_GPC['board_password']);
        if (!empty($_GPC['id'])) {
            pdo_update('rhinfo_zyxq_syspub', $data, array('id' => $_GPC['id']));
        } else {
            pdo_insert('rhinfo_zyxq_syspub', $data);
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_syspub');
    $item = pdo_fetch($sql);
    include $this->mywtpl('public');
} elseif ($operation == 'haina') {
    $navtitle = '腾讯海纳及蚂蚁金服开放平台';
    if ($_W['ispost']) {
        $data = array('ishaina' => $_GPC['ishaina'], 'haina_appid' => $_GPC['haina_appid'], 'haina_secret' => $_GPC['haina_secret'], 'haina_agentid' => $_GPC['haina_agentid'], 'haina_token' => $_GPC['haina_token'], 'haina_encodingaeskey' => $_GPC['haina_encodingaeskey'], 'isalilife' => $_GPC['isalilife'], 'alilife_appid' => $_GPC['alilife_appid'], 'alilife_rsa2' => $_GPC['alilife_rsa2']);
        if (!empty($_GPC['id'])) {
            pdo_update('rhinfo_zyxq_syspub', $data, array('id' => $_GPC['id']));
        } else {
            pdo_insert('rhinfo_zyxq_syspub', $data);
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_syspub');
    $item = pdo_fetch($sql);
    include $this->mywtpl();
} elseif ($operation == 'secagree') {
    $navtitle = '入驻协议';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'content1' => htmlspecialchars_decode($_GPC['content1']), 'content2' => htmlspecialchars_decode($_GPC['content2']), 'content3' => htmlspecialchars_decode($_GPC['content3']), 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if (!empty($_GPC['id'])) {
            pdo_update('rhinfo_zyxq_sysagreement', $data, array('id' => $_GPC['id']));
        } else {
            pdo_insert('rhinfo_zyxq_sysagreement', $data);
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $item = pdo_fetch($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('secagree');
} elseif ($operation == 'account') {
    $current = '公众号授权';
    $myret = 0;
    $condition = '';
    if (!empty($_GPC['keyword'])) {
        $condition = ' where c.name LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_secacc');
    $total = pdo_fetchcolumn($sql);
    if ($total > 0) {
        $sql = 'select s.* from ' . tablename('rhinfo_zyxq_secacc') . ' as s left join ' . tablename('account_wechats') . ' as c on s.weid=c.uniacid ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select name from ' . tablename('account_wechats') . ' where uniacid = :weid';
            $account = pdo_fetchcolumn($sql, array(':weid' => $data[$k]['weid']));
            $data[$k]['account'] = $account;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } elseif ($data[$k]['status'] == 2) {
                $data[$k]['statustxt'] = '禁用';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('account');
} elseif ($operation == 'add') {
    $current = '添加公众号授权';
    if ($_W['isajax']) {
        $menus = $_GPC['menus'];
        $prgs = $_GPC['prgs'];
        $perms = $_GPC['perms'];
        $data = array('weid' => $_GPC['weid'], 'limitqty' => $_GPC['limitqty'], 'ispayset' => $_GPC['ispayset'], 'issysset' => $_GPC['issysset'], 'chargingqty' => $_GPC['chargingqty'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_secacc', $data);
        $id = pdo_insertid();
        $k = 0;
        while (!($k >= count($menus))) {
            $usys = array('weid' => $_GPC['weid'], 'aid' => $id, 'sid' => $menus[$k]);
            pdo_insert('rhinfo_zyxq_secasys', $usys);
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($prgs))) {
            $uprg = array('weid' => $_GPC['weid'], 'aid' => $id, 'sid' => substr($prgs[$k], 0, strrpos($prgs[$k], '.')));
            $pid = substr($prgs[$k], strrpos($prgs[$k], '.') + 1, strlen($prgs[$k]) - strrpos($prgs[$k], '.'));
            $uprg['pid'] = $pid;
            pdo_insert('rhinfo_zyxq_secaprg', $uprg);
            ($k += 1) + -1;
        }
        echo 'ok';
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit(0);
    }
    $sql = 'select c.uniacid,c.name from ' . tablename('account_wechats') . ' as c left join ' . tablename('account') . ' as a on a.uniacid=c.uniacid where a.isdeleted=0 and a.uniacid>0 and a.uniacid not in(select distinct weid from ' . tablename('rhinfo_zyxq_secacc') . ')';
    $account = pdo_fetchall($sql);
    if (empty($account)) {
        $this->mywebmsg('温馨提示', '没有可授权公众号', '', 'info');
    }
    $mymenus = $this->accountmenus();
    include $this->mywtpl('grant');
} elseif ($operation == 'edit') {
    $current = '编辑公众号授权';
    $aid = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secacc') . ' where id = :id';
    $item = pdo_fetch($sql, array(':id' => $aid));
    if ($_W['isajax']) {
        $menus = $_GPC['menus'];
        $prgs = $_GPC['prgs'];
        $perms = $_GPC['perms'];
        $glue = 'AND';
        pdo_delete('rhinfo_zyxq_secasys', array('aid' => $aid, 'weid' => $_GPC['weid']), 'AND');
        $glue = 'AND';
        pdo_delete('rhinfo_zyxq_secaprg', array('aid' => $aid, 'weid' => $_GPC['weid']), 'AND');
        $k = 0;
        while (!($k >= count($menus))) {
            $usys = array('weid' => $item['weid'], 'aid' => $aid, 'sid' => $menus[$k]);
            pdo_insert('rhinfo_zyxq_secasys', $usys);
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($prgs))) {
            $sid = substr($prgs[$k], 0, strrpos($prgs[$k], '.'));
            $uprg = array('weid' => $item['weid'], 'aid' => $aid, 'sid' => $sid);
            $pid = substr($prgs[$k], strrpos($prgs[$k], '.') + 1, strlen($prgs[$k]) - strrpos($prgs[$k], '.'));
            $uprg['pid'] = $pid;
            pdo_insert('rhinfo_zyxq_secaprg', $uprg);
            ($k += 1) + -1;
        }
        $data = array('limitqty' => $_GPC['limitqty'], 'chargingqty' => $_GPC['chargingqty'], 'ispayset' => $_GPC['ispayset'], 'issysset' => $_GPC['issysset'], 'status' => $_GPC['status']);
        $result = pdo_update('rhinfo_zyxq_secacc', $data, array('id' => $aid));
        echo 'ok';
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $aid);
        exit(0);
    }
    $sql = 'SELECT uniacid,name FROM ' . tablename('account_wechats') . ' WHERE uniacid = :weid ';
    $account = pdo_fetchall($sql, array(':weid' => $item['weid']));
    $mymenus = $this->accountmenus_edit($aid, $item['weid']);
    include $this->mywtpl('grant');
} elseif ($operation == 'delete') {
    $current = '删除公众号授权';
    $id = intval($_GPC['id']);
    pdo_delete('rhinfo_zyxq_secaprg', array('aid' => $id));
    pdo_delete('rhinfo_zyxq_secasys', array('aid' => $id));
    $result = pdo_delete('rhinfo_zyxq_secacc', array('id' => $id));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '公众号授权状态';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status']);
    $result = pdo_update('rhinfo_zyxq_secacc', $data, array('id' => $id));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'wxapp') {
    if ($_W['isajax']) {
        $appjson = "{\r\n\t\t\t\t\"pages\": [\r\n\t\t\t\t\t\"pages/index/index\",\r\n\t\t\t\t\t\"pages/pay/pay\",\r\n\t\t\t\t\t\"pages/mypay/pay\",\r\n\t\t\t\t\t\"pages/logs/logs\",\r\n\t\t\t\t\t\"pages/index/navto\",\r\n\t\t\t\t\t\"pages/webview/index\"\r\n\t\t\t\t],\r\n\t\t\t\t\"window\": {\r\n\t\t\t\t\t\"backgroundTextStyle\": \"light\",\r\n\t\t\t\t\t\"navigationBarBackgroundColor\": \"#f7f7f7\",\r\n\t\t\t\t\t\"navigationBarTitleText\": \"" . $_GPC['wxminititle'] . "\",\r\n\t\t\t\t\t\"navigationBarTextStyle\": \"black\"\r\n\t\t\t\t\t},\r\n\t\t\t\t\"networkTimeout\": {\r\n\t\t\t\t\t\"request\": 10000,\r\n\t\t\t\t\t\"downloadFile\": 10000\r\n\t\t\t\t  },\r\n\t\t\t\t  \"debug\": false,\r\n\t\t\t\t  \"navigateToMiniProgramAppIdList\": []\r\n\t\t\t\t}";
        if (!empty($_GPC['wxminititle'])) {
            file_put_contents(IA_ROOT . '/addons/rhinfo_zyxq/static/lib/wxapp/app.json', $appjson);
        }
        $siteinfo = "var siteinfo = {\r\n\t\t\t\t\"uniacid\": \"" . $_W['uniacid'] . "\",\r\n\t\t\t\t\"acid\": \"" . $_W['acid'] . "\",\r\n\t\t\t\t\"multiid\": \"1\",\r\n\t\t\t\t\"version\": \"v1.0\",\r\n\t\t\t\t\"siteroot\": \"" . $_GPC['wxappurl'] . "app/index.php\",\r\n\t\t\t\t\"method_design\" : \"3\",\r\n\t\t\t\t\"regionid\" : \"*\"\r\n\t\t\t\t};\r\n\t\t\tmodule.exports = siteinfo;";
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
    exit('操作异常错误');
} elseif ($operation == 'aliapp') {
    if ($_W['isajax']) {
        $appjson = "{\r\n\t\t\t\t\"pages\": [\r\n\t\t\t\t\t\"pages/index/index\"\t\t\t\t\r\n\t\t\t\t],\r\n\t\t\t\t\"window\": {\r\n\t\t\t\t\t\"defaultTitle\": \"" . $_GPC['aliminititle'] . "\",\r\n\t\t\t\t\t\"pullRefresh\": false\r\n\t\t\t\t\t}\r\n\t\t\t\t}";
        if (!empty($_GPC['aliminititle'])) {
            file_put_contents(IA_ROOT . '/addons/rhinfo_zyxq/static/lib/aliapp/app.json', $appjson);
        }
        $appjs = "App({\r\n\t\t\t\t  onLaunch(options) {   \r\n\t\t\t\t  },  \r\n\t\t\t\t globalData: {\r\n\t\t\t\t\thasLogin: false,\r\n\t\t\t\t\t\"uniacid\": \"" . $_W['uniacid'] . "\",\r\n\t\t\t\t\t\"siteroot\": \"" . $_GPC['aliappurl'] . "\",\t\r\n\t\t\t\t  },\r\n\t\t\t\t});";
        $res = file_put_contents(IA_ROOT . '/addons/rhinfo_zyxq/static/lib/aliapp/app.js', $appjs);
        if ($res > 0) {
            require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/rhinfo/pclzip.php';
            $Path = IA_ROOT . '/addons/rhinfo_zyxq/static/lib/aliapp/';
            $ZipFile = IA_ROOT . '/addons/rhinfo_zyxq/data/aliapp.zip';
            $ZipFile;
            $zip = 'PclZip';
            $v_list = $zip->create($Path, PCLZIP_OPT_REMOVE_PATH, $Path);
            if ($v_list == 0) {
                exit('压缩错误');
            }
            exit('ok');
        } else {
            exit('打包失败，请检查服务器写入权限！');
        }
    }
    exit('操作异常错误');
}