<?php

error_reporting(0);
define('IN_MOBILE', true);
require '../../framework/bootstrap.inc.php';
require './vendor/rhinfo/rhinfo.php';
$input = file_get_contents('php://input');
$post = json_decode($input, true);
$_W['weid'] = $_GPC['i'];
$_W['uniacid'] = $_GPC['i'];
$_W['account'] = uni_fetch($_W['uniacid']);
$_W['uniaccount'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$sql = 'select * from ' . tablename('rhinfo_zyxq_car_iolog') . ' where weid=:weid and ioid=:ioid and io=2 order by id desc';
$iolog = pdo_fetch($sql, array(':weid' => $_W['uniacid'], 'ioid' => $_GPC['ioid']));
if (!empty($iolog) && $iolog['status'] == 1 && $iolog['paystatus'] == 0) {
    pdo_update('rhinfo_zyxq_car_iolog', array('paystatus' => 1), array('weid' => $_W['uniacid'], 'id' => $iolog['id']));
    echo '{"Response_AlarmInfoPlate":{"info":"ok","content":"' . $iolog['carno'] . '","is_pay":"true"}}';
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where weid = :weid';
    $sysset = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    $topcolor = empty($sysset['topcolor']) ? '#FF683F' : $sysset['topcolor'];
    $textcolor = empty($sysset['textcolor']) ? '#000' : $sysset['textcolor'];
    $postdata = array('first' => array('value' => '您的爱车已出场'), 'keyword1' => array('value' => '亲，您的车辆已出', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i'), 'color' => $textcolor), 'keyword3' => array('value' => $iolog['carno'], 'color' => $textcolor), 'remark' => array('value' => $iolog['carno'] . ',感谢您的支持，谢谢!'));
    load()->classs('weixin.account');
    load()->func('communication');
    $obj = new WeiXinAccount();
    $access_token = $obj->fetch_available_token();
    $data = array('touser' => $iolog['payopenid'], 'template_id' => $sysset['tplid1'], 'url' => '', 'topcolor' => $topcolor, 'data' => $postdata);
    $json = json_encode($data);
    $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
    $res = ihttp_post($url, $json);
    exit('success');
}
if (!empty($post)) {
    if (!empty($post['AlarmInfoPlate'])) {
        $isnotice = true;
        $res = $post['AlarmInfoPlate'];
        load()->func('file');
        $fileurl = '';
        if (isset($res['result']['PlateResult']['imageFragmentFile'])) {
            $small_image = $res['result']['PlateResult']['imageFragmentFile'];
            if ($small_image != null) {
                $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
                mkdirs(IA_ROOT . '/attachment/' . $path);
                $filename = file_random_name(IA_ROOT . '/attachment/' . $path, 'jpg');
                $fileurl = $path . $filename;
                $filepath = IA_ROOT . '/attachment/' . $path . $filename;
                $fs_image = fopen($filepath, 'w');
                if ($fs_image) {
                    $fileurl = '';
                }
                $simage_decoded = base64_decode($small_image);
                $flag = fwrite($fs_image, $simage_decoded);
                fclose($fs_image);
            }
        }
        if (isset($res['result']['PlateResult']['imageFile'])) {
            $image = $res['result']['PlateResult']['imageFile'];
            if ($image != null) {
                $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
                mkdirs(IA_ROOT . '/attachment/' . $path);
                $filename = file_random_name(IA_ROOT . '/attachment/' . $path, 'jpg');
                $fileurl = $path . $filename;
                $filepath = IA_ROOT . '/attachment/' . $path . $filename;
                $fp_image = fopen($filepath, 'w');
                if (!$fp_image) {
                    $fileurl = '';
                }
                $image_decoded = base64_decode($image);
                $flag = fwrite($fp_image, $image_decoded);
                fclose($fp_image);
            }
        }
        $parkinglot = pdo_get('rhinfo_zyxq_parkingio', array('weid' => $_W['uniacid'], 'id' => $_GPC['ioid']));
        if ($parkinglot['io'] == 1) {
            $carno = $res['result']['PlateResult']['license'];
            $data = array('weid' => $_W['uniacid'], 'parklotid' => $parkinglot['lotid'], 'ioid' => $parkinglot['id'], 'io' => 1, 'carno' => $carno, 'intime' => time(), 'image' => $fileurl, 'status' => 0);
            pdo_insert('rhinfo_zyxq_car_iolog', $data);
            echo '{"Response_AlarmInfoPlate":{"info":"ok","content":"' . $carno . '","is_pay":"true"}}';
        } elseif ($parkinglot['io'] == 2) {
            $carno = $res['result']['PlateResult']['license'];
            $inlog = pdo_get('rhinfo_zyxq_car_iolog', array('weid' => $_W['uniacid'], 'io' => 1, 'carno' => $carno, 'status' => 0));
            if (!empty($inlog)) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:parkid ';
                $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $parkinglot['lotid']));
                $timediff = time() - strtotime($inlog['intime']);
                $days = intval($timediff / 86400);
                $hours = intval($timediff % 86400 / 3600);
                $minutes = intval($timediff % 86400 % 3600 / 60);
                $sql = 'select * from ' . tablename('rhinfo_zyxq_car_whitelist') . ' where weid = :weid and parklotid=:parklotid and carno=:carno';
                $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parklotid' => $park['id'], ':carno' => $carno));
                if (!empty($monthcar)) {
                    if (!(time() > $monthcar['endtime'])) {
                        $money = 0;
                    }
                } else {
                    $park['minute'] = empty($park['minute']) ? 1 : $park['minute'];
                    if ($park['minute'] > 0 && !(intval($timediff / 60) > $park['minute'])) {
                        $money = 0;
                    } else {
                        $mystoptime = round($timediff / 3600, 2);
                        $price = $park['price'];
                        $stopstart = date('H', $inlog['intime']);
                        $stopend = date('H', time());
                        $istoday = 0;
                        if (date('Y-m-d', $inlog['intime']) == date('Y-m-d')) {
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
                        if ($inlog['payfee'] > 0) {
                            $money = $stopfee - $inlog['payfee'];
                        } else {
                            $money = $stopfee;
                        }
                    }
                }
            }
            pdo_update('rhinfo_zyxq_car_iolog', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $inlog['id']));
            $data = array('weid' => $_W['uniacid'], 'parklotid' => $parkinglot['lotid'], 'ioid' => $parkinglot['id'], 'carno' => $carno, 'intime' => $inlog['intime'], 'status' => 0, 'image' => $fileurl, 'outtime' => time());
            if ($money > 0) {
                pdo_insert('rhinfo_zyxq_car_iolog', $data);
                exit('{"Response_AlarmInfoPlate":{"info":"wait","content":"wait...","is_pay":"false"}}');
            } else {
                $data['status'] = 1;
                $data['paystatus'] = 1;
                pdo_insert('rhinfo_zyxq_car_iolog', $data);
                exit('{"Response_AlarmInfoPlate":{"info":"ok","content":"' . $carno . '","is_pay":"true"}}');
            }
        }
    } elseif (!empty($post['AlarmGioIn'])) {
        $res = $post['AlarmGioIn'];
    }
    exit('success');
}
exit('fail');