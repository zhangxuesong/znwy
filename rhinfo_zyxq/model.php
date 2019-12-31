<?php

if(!defined('IN_IA')) {
	exit('Access Denied');
}

class My_loader{	
	private $cache = array();
	public function func($name)	{	
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/rhinfo_zyxq/function/' . $name . '.func.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['func'][$name] = true;
			return true;
		}
		trigger_error('Invalid Function /addons/rhinfo_zyxq/function/' . $name . '.func.php', 256);
		return false;
	}
	public function model($name){	
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/rhinfo_zyxq/model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['model'][$name] = true;
			return true;
		}
		trigger_error('Invalid Model /addons/rhinfo_zyxq/model/' . $name . '.mod.php', 1024);

		return false;

	}
	public function classs($name) {
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/rhinfo_zyxq/class/' . $name . '.class.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['class'][$name] = true;
			return true;
		}
		trigger_error('Invalid Class /addons/rhinfo_zyxq/class/' . $name . '.class.php', 256);
		return false;
	}
	public function request($name) {
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/rhinfo_zyxq/request/' . $name . '.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['class'][$name] = true;
			return true;
		}
		trigger_error('Invalid Request /addons/rhinfo_zyxq/request/' . $name . '.php', 256);
		return false;
	}
}

function myload() {  //加载函数及类
	static $mloader;
	if(empty($mloader)) {
		$mloader = new My_loader();
	}
	return $mloader;
}

function writelog($level = 'info', $message = '') {
	$filename = IA_ROOT . '/addons/rhinfo_zyxq/data/logs/' . date('Ymd') . '.log';
	load()->func('file');
	mkdirs(dirname($filename));
	$content = date('Y-m-d H:i:s') . " {$level} :\n------------\n";
	if(is_string($message) && !in_array($message, array('post', 'get'))) {
		$content .= "String:\n{$message}\n";
	}
	if(is_array($message)) {
		$content .= "Array:\n";
		foreach($message as $key => $value) {
			$content .= sprintf("%s : %s ;\n", $key, $value);
		}
	}
	if($message === 'get') {
		$content .= "GET:\n";
		foreach($_GET as $key => $value) {
			$content .= sprintf("%s : %s ;\n", $key, $value);
		}
	}
	if($message === 'post') {
		$content .= "POST:\n";
		foreach($_POST as $key => $value) {
			$content .= sprintf("%s : %s ;\n", $key, $value);
		}
	}
	$content .= "\n";

	$fp = fopen($filename, 'a+');
	fwrite($fp, $content);
	fclose($fp);
}
?>