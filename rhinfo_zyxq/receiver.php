<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Rhinfo_zyxqModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
        return null;
    }
}