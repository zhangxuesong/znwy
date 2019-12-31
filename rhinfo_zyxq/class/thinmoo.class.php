<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
load()->func('communication');
class ThinMoo
{
    public function __construct($config = array())
    {
        global $_W;
        $this->config = $config;
        return null;
    }
    public function Check()
    {
        if (empty($this->config['thinmoo_token'])) {
            return error(0 - 1, 'TOKEN不能为空');
        }
        return true;
    }
    public function HttpPost($url, $operation = 'GET', $data = '{}', $return = false)
    {
        $res = $this->Check();
        if (is_error($res)) {
            return $res;
        }
        $category = $url;
        $url = 'https://www.doormaster.me:9099/' . $url;
        $jsonStr = "{\r\n\t\t\t\"access_token\":\"" . $this->config['thinmoo_token'] . "\",\r\n\t\t\t\"operation\": \"" . $operation . "\",\r\n\t\t\t\"data\":" . $data . "\r\n\t\t}";
        $response = ihttp_request($url, $jsonStr);
        if (is_error($response)) {
            return error(0 - 1, $response['message']);
        }
        $res = json_decode($response['content'], 1);
        writelog($category, var_export($res, true));
        if ($return) {
            return $res;
        }
        if ($res['ret'] == 0) {
            return $res;
        }
        return error(0 - 1, $res['ret'] . '-' . $res['msg']);
    }
    public function region_init($area = '{}', $region = '{}', $pid = 0, $rid = 0)
    {
        $url = 'doormaster/server/areas';
        $res = $this->HttpPost($url, 'POST', $area);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        $url = 'doormaster/server/community';
        $ret = $this->HttpPost($url, 'POST', $region);
        if (is_error($ret)) {
            return error(0 - 1, $ret['message']);
        }
        pdo_update('rhinfo_zyxq_property', array('door_pid' => $res['id']), array('id' => $pid));
        pdo_update('rhinfo_zyxq_region', array('thinmoo_uuid' => $ret['id']), array('id' => $rid));
        return $res;
    }
    public function region_update($area = '{}', $region = '{}', $pid = 0, $rid = 0)
    {
        $url = 'doormaster/server/areas';
        $res = $this->HttpPost($url, 'PUT', $area, true);
        if ($res['ret'] == 0) {
            $url = 'doormaster/server/community';
            $ret = $this->HttpPost($url, 'PUT', $region);
            if (is_error($ret)) {
                return error(0 - 1, $ret['message']);
            }
            pdo_update('rhinfo_zyxq_region', array('thinmoo_uuid' => $ret['id']), array('id' => $rid));
        } elseif ($res['ret'] == 1113) {
            pdo_update('rhinfo_zyxq_property', array('door_pid' => ''), array('id' => $pid));
            pdo_update('rhinfo_zyxq_region', array('thinmoo_uuid' => ''), array('id' => $rid));
            return error(0 - 1, '区域不存在，请刷新后重试');
        }
        return $res;
    }
    public function add_door($params = '{}', $type = 'video_devices')
    {
        $url = 'doormaster/server/' . $type;
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function delete_door($params = '{}', $type = 'video_devices')
    {
        $url = 'doormaster/server/' . $type;
        $res = $this->HttpPost($url, 'DELETE', $params, true);
        if ($res['ret'] == 0 || $res['ret'] == 1146) {
            return $res;
        }
        return error(0 - 1, $res['ret'] . '-' . $res['msg']);
    }
    public function query_device($params = '{}', $type = 'video_devices')
    {
        $url = 'doormaster/server/' . $type;
        $res = $this->HttpPost($url, 'GET', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function dev_status($params = '{}')
    {
        $url = 'doormaster/server/remote_control';
        $res = $this->HttpPost($url, 'GET', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function open_door($params = '{}')
    {
        $url = 'doormaster/server/remote_control';
        $res = $this->HttpPost($url, 'OPEN_DOOR', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function open_qrcode($params = '{}', $type = 'video_devices')
    {
        $url = 'doormaster/server/' . $type . '/temp_pwd';
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function open_password($params = '{}', $type = 'video_devices')
    {
        $url = 'doormaster/server/' . $type . '/temp_pwd';
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function add_user($params = '{}')
    {
        $url = 'doormaster/server/employees';
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function edit_user($params = '{}')
    {
        $url = 'doormaster/server/employees';
        $res = $this->HttpPost($url, 'PUT', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function delete_user($params = '{}')
    {
        $url = 'doormaster/server/employees';
        $res = $this->HttpPost($url, 'DELETE', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function query_user($params = '{}')
    {
        $url = 'doormaster/server/employees';
        $res = $this->HttpPost($url, 'GET', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function reg_face($params = '{}')
    {
        $url = 'doormaster/server/employee/template';
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function delete_face($params = '{}')
    {
        $url = 'doormaster/server/employee/template';
        $res = $this->HttpPost($url, 'DELETE', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
    public function add_perm($params = '{}')
    {
        $url = 'doormaster/server/video_devices_permissions';
        $res = $this->HttpPost($url, 'POST', $params);
        if (is_error($res)) {
            return error(0 - 1, $res['message']);
        }
        return $res;
    }
}