<?php
function getRequest($key) {
	$request = null;
	if (isset ( $_GET [$key] ) && ! empty ( $_GET [$key] )) {
		$request = $_GET [$key];
	} elseif (isset ( $_POST [$key] ) && ! empty ( $_POST [$key] )) {
		$request = $_POST [$key];
	}
	return $request;
}
function sendPostRequest($url, $data) {
	$postdata = http_build_query ( $data );
	$opts = array (
		'http' => array (
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata 
		) 
	);		
	$context = stream_context_create ( $opts );	
	$result = file_get_contents ( $url, false, $context );
	return $result;
}

function alikeytostr($key,$keytype = 0) {
	if (empty($key)){
		return $key;
	}
	if ($keytype==1){
		if (strexists($key,'-----BEGIN PRIVATE KEY-----')){
			$key = str_replace(array("-----BEGIN PRIVATE KEY-----", "-----END PRIVATE KEY-----"), '',$key);
		}
	}
	elseif($keytype==2){
		if (strexists($key,'-----BEGIN RSA PRIVATE KEY-----')){
			$key = str_replace(array("-----BEGIN RSA PRIVATE KEY-----", "-----END RSA PRIVATE KEY-----"), '',$key);
		}
	}
	else{
		if (strexists($key,'-----BEGIN PUBLIC KEY-----')){
			$key = str_replace(array("-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"), '',$key);
		}
	}
	$key = str_replace(array("\r\n", "\r", "\n"), '',trim($key));
	return $key;
}
//转换编码
function aopcharacet($data) {
	if (! empty ( $data )) {
		$fileType = mb_detect_encoding ( $data, array (
				'UTF-8',
				'GBK',
				'GB2312',
				'LATIN1',
				'BIG5' 
		) );
		if ($fileType != 'UTF-8') {
			$data = mb_convert_encoding ( $data, 'UTF-8', $fileType );
		}
	}
	return $data;
}

/**
 * 加密方法
 * @param string $str
 * @return string
 */
 function encrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$screct_key = base64_decode($screct_key);
	$str = trim($str);
	$str = addPKCS7Padding($str);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	return base64_encode($encrypt_str);
}

/**
 * 解密方法
 * @param string $str
 * @return string
 */
 function decrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$str = base64_decode($str);
	$screct_key = base64_decode($screct_key);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	$encrypt_str = trim($encrypt_str);

	$encrypt_str = stripPKSC7Padding($encrypt_str);
	return $encrypt_str;

}

/**
 * 填充算法
 * @param string $source
 * @return string
 */
function addPKCS7Padding($source){
	$source = trim($source);
	$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

	$pad = $block - (strlen($source) % $block);
	if ($pad <= $block) {
		$char = chr($pad);
		$source .= str_repeat($char, $pad);
	}
	return $source;
}
/**
 * 移去填充算法
 * @param string $source
 * @return string
 */
function stripPKSC7Padding($source){
	$source = trim($source);
	$char = substr($source, -1);
	$num = ord($char);
	if($num==62)return $source;
	$source = substr($source,0,-$num);
	return $source;
}

function aopclient_request_execute($request,$config,$token = NULL) {
	$aop = new AopClient ();
	$aop->gatewayUrl = $config ['gatewayUrl'];
	$aop->appId = $config ['app_id'];
	$aop->rsaPrivateKey=$config['merchant_private_key'];
	$aop->alipayrsaPublicKey=$config['alipay_public_key'];
	$aop->signType=$config['sign_type'];
	$aop->apiVersion = "1.0";
	$result = $aop->execute ( $request, $token );
	WeUtility::logging("response",var_export($result,true));
	return $result;
}

class AopClient {
	//应用ID
	public $appId;
	
	//私钥文件路径
	public $rsaPrivateKeyFilePath;

	//私钥值
	public $rsaPrivateKey;

	//网关
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	//返回数据格式
	public $format = "json";
	//api版本
	public $apiVersion = "1.0";

	// 表单提交字符集编码
	public $postCharset = "UTF-8";

	//使用文件读取文件格式，请只传递该值
	public $alipayPublicKey = null;

	//使用读取字符串格式，请只传递该值
	public $alipayrsaPublicKey;


	public $debugInfo = false;

	private $fileCharset = "UTF-8";

	private $RESPONSE_SUFFIX = "_response";

	private $ERROR_RESPONSE = "error_response";

	private $SIGN_NODE_NAME = "sign";


	//加密XML节点名称
	private $ENCRYPT_XML_NODE_NAME = "response_encrypted";

	private $needEncrypt = false;


	//签名类型
	public $signType = "RSA";


	//加密密钥和类型

	public $encryptKey;

	public $encryptType = "AES";

	protected $alipaySdkVersion = "alipay-sdk-php-20180705";

	public function generateSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	public function rsaSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	public function getSignContent($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}

		unset ($k, $v);
		return $stringToBeSigned;
	}


	//此方法对value做urlencode
	public function getSignContentUrlencode($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . urlencode($v);
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
				}
				$i++;
			}
		}

		unset ($k, $v);
		return $stringToBeSigned;
	}

	protected function sign($data, $signType = "RSA") {
		if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
			$priKey=$this->rsaPrivateKey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}else {
			$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 

		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}

    /**
     * RSA单独签名方法，未做字符串处理,字符串处理见getSignContent()
     * @param $data 待签名字符串
     * @param $privatekey 商户私钥，根据keyfromfile来判断是读取字符串还是读取文件，false:填写私钥字符串去回车和空格 true:填写私钥文件路径 
     * @param $signType 签名方式，RSA:SHA1     RSA2:SHA256 
     * @param $keyfromfile 私钥获取方式，读取字符串还是读文件
     * @return string 
     * @author mengyu.wh
     */
	public function alonersaSign($data,$privatekey,$signType = "RSA",$keyfromfile=false) {

		if(!$keyfromfile){
			$priKey=$privatekey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}
		else{
			$priKey = file_get_contents($privatekey);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 

		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if($keyfromfile){
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}


	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		$encodeArray = Array();
		$postMultipart = false;


		if (is_array($postFields) && 0 < count($postFields)) {

			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
				{

					$postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
					$encodeArray[$k] = $this->characet($v, $this->postCharset);
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
				{
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {

			$headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);




		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {

			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}

		curl_close($ch);
		return $reponse;
	}

	protected function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}


	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$localIp = isset ($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";
		$logData = array(
			date("Y-m-d H:i:s"),
			$apiName,
			$this->appId,
			$localIp,
			PHP_OS,
			$this->alipaySdkVersion,
			$requestUrl,
			$errorCode,
			str_replace("\n", "", $responseTxt)
		);
		WeUtility::logging( "error",var_export($logData,true));
	}

    /**
     * 生成用于调用收银台SDK的字符串
     * @param $request SDK接口的请求参数对象
     * @return string 
     * @author guofa.tgf
     */
	public function sdkExecute($request) {
		
		$this->setupCharsets($request);

		$params['app_id'] = $this->appId;
		$params['method'] = $request->getApiMethodName();
		$params['format'] = $this->format; 
		$params['sign_type'] = $this->signType;
		$params['timestamp'] = date("Y-m-d H:i:s");
		$params['alipay_sdk'] = $this->alipaySdkVersion;
		$params['charset'] = $this->postCharset;

		$version = $request->getApiVersion();
		$params['version'] = $this->checkEmpty($version) ? $this->apiVersion : $version;

		if ($notify_url = $request->getNotifyUrl()) {
			$params['notify_url'] = $notify_url;
		}

		$dict = $request->getApiParas();
		$params['biz_content'] = $dict['biz_content'];

		ksort($params);

		$params['sign'] = $this->generateSign($params, $this->signType);

		foreach ($params as &$value) {
			$value = $this->characet($value, $params['charset']);
		}
		
		return http_build_query($params);
	}

	/*
		页面提交执行方法
		@param：跳转类接口的request; $httpmethod 提交方式。两个值可选：post、get
		@return：构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
		auther:笙默
	*/
	public function pageExecute($request,$httpmethod = "POST") {

		$this->setupCharsets($request);

		if (strcasecmp($this->fileCharset, $this->postCharset)) {

			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}

		$iv=null;

		if(!$this->checkEmpty($request->getApiVersion())){
			$iv=$request->getApiVersion();
		}else{
			$iv=$this->apiVersion;
		}

		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["return_url"] = $request->getReturnUrl();
		$sysParams["charset"] = $this->postCharset;

		//获取业务参数
		$apiParams = $request->getApiParas();

		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			$sysParams["encrypt_type"] = $this->encryptType;

			if ($this->checkEmpty($apiParams['biz_content'])) {

				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}

			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {

				throw new Exception(" encryptType and encryptKey must not null! ");
			}

			if ("AES" != $this->encryptType) {

				throw new Exception("加密类型只支持AES");
			}

			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;

		}

		//print_r($apiParams);
		$totalParams = array_merge($apiParams, $sysParams);
		
		//待签名字符串
		$preSignStr = $this->getSignContent($totalParams);

		//签名
		$totalParams["sign"] = $this->generateSign($totalParams, $this->signType);

		if ("GET" == strtoupper($httpmethod)) {
			
			//value做urlencode
			$preString=$this->getSignContentUrlencode($totalParams);
			//拼接GET请求串
			$requestUrl = $this->gatewayUrl."?".$preString;
			
			return $requestUrl;
		} else {
			//拼接表单字符串
			return $this->buildRequestForm($totalParams);
		}


	}


	/**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
	protected function buildRequestForm($para_temp) {
		
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."?charset=".trim($this->postCharset)."' method='POST'>";
		while (list ($key, $val) = each ($para_temp)) {
			if (false === $this->checkEmpty($val)) {
				//$val = $this->characet($val, $this->postCharset);
				$val = str_replace("'","&apos;",$val);
				//$val = str_replace("\"","&quot;",$val);
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
			}
        }

		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
		
		return $sHtml;
	}


	public function execute($request, $authToken = null, $appInfoAuthtoken = null) {

		$this->setupCharsets($request);

		//		//  如果两者编码不一致，会出现签名验签或者乱码
		if (strcasecmp($this->fileCharset, $this->postCharset)) {

			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}

		$iv = null;

		if (!$this->checkEmpty($request->getApiVersion())) {
			$iv = $request->getApiVersion();
		} else {
			$iv = $this->apiVersion;
		}


		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["auth_token"] = $authToken;
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["charset"] = $this->postCharset;
		$sysParams["app_auth_token"] = $appInfoAuthtoken;


		//获取业务参数
		$apiParams = $request->getApiParas();

			if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			$sysParams["encrypt_type"] = $this->encryptType;

			if ($this->checkEmpty($apiParams['biz_content'])) {

				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}

			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {

				throw new Exception(" encryptType and encryptKey must not null! ");
			}

			if ("AES" != $this->encryptType) {

				throw new Exception("加密类型只支持AES");
			}

			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;

		}

		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);


		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);


		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {

			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}

		//解析AOP返回结果
		$respWellFormed = false;


		// 将返回结果转换本地文件编码
		$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);



		$signData = null;

		if ("json" == $this->format) {

			$respObject = json_decode($r);
			if (null !== $respObject) {
				$respWellFormed = true;
				$signData = $this->parserJSONSignData($request, $resp, $respObject);
			}
		} else if ("xml" == $this->format) {
			$disableLibxmlEntityLoader = libxml_disable_entity_loader(true);
			$respObject = @ simplexml_load_string($resp);
			if (false !== $respObject) {
				$respWellFormed = true;

				$signData = $this->parserXMLSignData($request, $resp);
			}
			libxml_disable_entity_loader($disableLibxmlEntityLoader);
		}


		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp);
			return false;
		}

		// 验签
		$this->checkResponseSign($request, $signData, $resp, $respObject);

		// 解密
		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			if ("json" == $this->format) {


				$resp = $this->encryptJSONSignSource($request, $resp);

				// 将返回结果转换本地文件编码
				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$respObject = json_decode($r);
			}else{

				$resp = $this->encryptXMLSignSource($request, $resp);

				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$disableLibxmlEntityLoader = libxml_disable_entity_loader(true);
				$respObject = @ simplexml_load_string($r);
				libxml_disable_entity_loader($disableLibxmlEntityLoader);

			}
		}

		return $respObject;
	}

	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {
		
		if (!empty($data)) {
			$fileType = $this->fileCharset;
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}


		return $data;
	}

	/*
	public function exec($paramsArray) {
		if (!isset ($paramsArray["method"])) {
			trigger_error("No api name passed");
		}
		$inflector = new LtInflector;
		$inflector->conf["separator"] = ".";
		$requestClassName = ucfirst($inflector->camelize(substr($paramsArray["method"], 7))) . "Request";
		if (!class_exists($requestClassName)) {
			trigger_error("No such api: " . $paramsArray["method"]);
		}

		$session = isset ($paramsArray["session"]) ? $paramsArray["session"] : null;

		$req = new $requestClassName;
		foreach ($paramsArray as $paraKey => $paraValue) {
			$inflector->conf["separator"] = "_";
			$setterMethodName = $inflector->camelize($paraKey);
			$inflector->conf["separator"] = ".";
			$setterMethodName = "set" . $inflector->camelize($setterMethodName);
			if (method_exists($req, $setterMethodName)) {
				$req->$setterMethodName ($paraValue);
			}
		}
		return $this->execute($req, $session);
	}--rhao*/

	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	/** rsaCheckV1 & rsaCheckV2
	 *  验证签名
	 *  在使用本方法前，必须初始化AopClient且传入公钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaCheckV1($params, $rsaPublicKeyFilePath,$signType='RSA') {
		$sign = $params['sign'];
		$params['sign_type'] = null;
		$params['sign'] = null;
		return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath,$signType);
	}
	public function rsaCheckV2($params, $rsaPublicKeyFilePath, $signType='RSA') {
		$sign = $params['sign'];
		$params['sign'] = null;
		return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath, $signType);
	}

	function verify($data, $sign, $rsaPublicKeyFilePath, $signType = 'RSA') {

		if($this->checkEmpty($this->alipayPublicKey)){

			$pubKey= $this->alipayrsaPublicKey;
			$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
		}else {
			//读取公钥文件
			$pubKey = file_get_contents($rsaPublicKeyFilePath);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($pubKey);
		}

		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');  

		//调用openssl内置方法验签，返回bool值

		$result = FALSE;
		if ("RSA2" == $signType) {
			$result = (openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256)===1);
		} else {
			$result = (openssl_verify($data, base64_decode($sign), $res)===1);
		}

		if(!$this->checkEmpty($this->alipayPublicKey)) {
			//释放资源
			openssl_free_key($res);
		}

		return $result;
	}

/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function checkSignAndDecrypt($params, $rsaPublicKeyPem, $rsaPrivateKeyPem, $isCheckSign, $isDecrypt, $signType='RSA') {
		$charset = $params['charset'];
		$bizContent = $params['biz_content'];
		if ($isCheckSign) {
			if (!$this->rsaCheckV2($params, $rsaPublicKeyPem, $signType)) {
				echo "<br/>checkSign failure<br/>";
				exit;
			}
		}
		if ($isDecrypt) {
			return $this->rsaDecrypt($bizContent, $rsaPrivateKeyPem, $charset);
		}

		return $bizContent;
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function encryptAndSign($bizContent, $rsaPublicKeyPem, $rsaPrivateKeyPem, $charset, $isEncrypt, $isSign, $signType='RSA') {
		// 加密，并签名
		if ($isEncrypt && $isSign) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$sign = $this->sign($encrypted, $signType);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type><sign>$sign</sign><sign_type>$signType</sign_type></alipay>";
			return $response;
		}
		// 加密，不签名
		if ($isEncrypt && (!$isSign)) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>$signType</encryption_type></alipay>";
			return $response;
		}
		// 不加密，但签名
		if ((!$isEncrypt) && $isSign) {
			$sign = $this->sign($bizContent, $signType);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$bizContent</response><sign>$sign</sign><sign_type>$signType</sign_type></alipay>";
			return $response;
		}
		// 不加密，不签名
		$response = "<?xml version=\"1.0\" encoding=\"$charset\"?>$bizContent";
		return $response;
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaEncrypt($data, $rsaPublicKeyPem, $charset) {
		if($this->checkEmpty($this->alipayPublicKey)){
			//读取字符串
			$pubKey= $this->alipayrsaPublicKey;
			$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
		}else {
			//读取公钥文件
			$pubKey = file_get_contents($rsaPublicKeyFilePath);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($pubKey);
		}

		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确'); 
		$blocks = $this->splitCN($data, 0, 30, $charset);
		$chrtext  = null;
		$encodes  = array();
		foreach ($blocks as $n => $block) {
			if (!openssl_public_encrypt($block, $chrtext , $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$encodes[] = $chrtext ;
		}
		$chrtext = implode(",", $encodes);

		return base64_encode($chrtext);
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaDecrypt($data, $rsaPrivateKeyPem, $charset) {
		
		if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
			//读字符串
			$priKey=$this->rsaPrivateKey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}else {
			$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
			$res = openssl_get_privatekey($priKey);
		}
		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 
		//转换为openssl格式密钥
		$decodes = explode(',', $data);
		$strnull = "";
		$dcyCont = "";
		foreach ($decodes as $n => $decode) {
			if (!openssl_private_decrypt($decode, $dcyCont, $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$strnull .= $dcyCont;
		}
		return $strnull;
	}

	function splitCN($cont, $n = 0, $subnum, $charset) {
		//$len = strlen($cont) / 3;
		$arrr = array();
		for ($i = $n; $i < strlen($cont); $i += $subnum) {
			$res = $this->subCNchar($cont, $i, $subnum, $charset);
			if (!empty ($res)) {
				$arrr[] = $res;
			}
		}

		return $arrr;
	}

	function subCNchar($str, $start = 0, $length, $charset = "gbk") {
		if (strlen($str) <= $length) {
			return $str;
		}
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		return $slice;
	}

	function parserResponseSubCode($request, $responseContent, $respObject, $format) {

		if ("json" == $format) {

			$apiName = $request->getApiMethodName();
			$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
			$errorNodeName = $this->ERROR_RESPONSE;

			$rootIndex = strpos($responseContent, $rootNodeName);
			$errorIndex = strpos($responseContent, $errorNodeName);

			if ($rootIndex > 0) {
				// 内部节点对象
				$rInnerObject = $respObject->$rootNodeName;
			} elseif ($errorIndex > 0) {

				$rInnerObject = $respObject->$errorNodeName;
			} else {
				return null;
			}

			// 存在属性则返回对应值
			if (isset($rInnerObject->sub_code)) {

				return $rInnerObject->sub_code;
			} else {

				return null;
			}


		} elseif ("xml" == $format) {

			// xml格式sub_code在同一层级
			return $respObject->sub_code;

		}


	}

	function parserJSONSignData($request, $responseContent, $responseJSON) {

		$signData = new SignData();

		$signData->sign = $this->parserJSONSign($responseJSON);
		$signData->signSourceData = $this->parserJSONSignSource($request, $responseContent);


		return $signData;

	}

	function parserJSONSignSource($request, $responseContent) {

		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;

		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);


		if ($rootIndex > 0) {

			return $this->parserJSONSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserJSONSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	function parserJSONSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strrpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex;
		if ($indexLen < 0) {

			return null;
		}

		return substr($responseContent, $signDataStartIndex, $indexLen);

	}

	function parserJSONSign($responseJSon) {

		return $responseJSon->sign;
	}

	function parserXMLSignData($request, $responseContent) {


		$signData = new SignData();

		$signData->sign = $this->parserXMLSign($responseContent);
		$signData->signSourceData = $this->parserXMLSignSource($request, $responseContent);


		return $signData;


	}

	function parserXMLSignSource($request, $responseContent) {


		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;


		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");


		if ($rootIndex > 0) {

			return $this->parserXMLSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserXMLSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	function parserXMLSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;
		$signIndex = strrpos($responseContent, "<" . $this->SIGN_NODE_NAME . ">");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex + 1;

		if ($indexLen < 0) {
			return null;
		}


		return substr($responseContent, $signDataStartIndex, $indexLen);


	}

	function parserXMLSign($responseContent) {
		$signNodeName = "<" . $this->SIGN_NODE_NAME . ">";
		$signEndNodeName = "</" . $this->SIGN_NODE_NAME . ">";

		$indexOfSignNode = strpos($responseContent, $signNodeName);
		$indexOfSignEndNode = strpos($responseContent, $signEndNodeName);


		if ($indexOfSignNode < 0 || $indexOfSignEndNode < 0) {
			return null;
		}

		$nodeIndex = ($indexOfSignNode + strlen($signNodeName));

		$indexLen = $indexOfSignEndNode - $nodeIndex;

		if ($indexLen < 0) {
			return null;
		}

		// 签名
		return substr($responseContent, $nodeIndex, $indexLen);

	}

	/**
	 * 验签
	 * @param $request
	 * @param $signData
	 * @param $resp
	 * @param $respObject
	 * @throws Exception
	 */
	public function checkResponseSign($request, $signData, $resp, $respObject) {

		if (!$this->checkEmpty($this->alipayPublicKey) || !$this->checkEmpty($this->alipayrsaPublicKey)) {


			if ($signData == null || $this->checkEmpty($signData->sign) || $this->checkEmpty($signData->signSourceData)) {

				throw new Exception(" check sign Fail! The reason : signData is Empty");
			}


			// 获取结果sub_code
			$responseSubCode = $this->parserResponseSubCode($request, $resp, $respObject, $this->format);


			if (!$this->checkEmpty($responseSubCode) || ($this->checkEmpty($responseSubCode) && !$this->checkEmpty($signData->sign))) {

				$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);


				if (!$checkResult) {

					if (strpos($signData->signSourceData, "\\/") > 0) {

						$signData->signSourceData = str_replace("\\/", "/", $signData->signSourceData);

						$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);

						if (!$checkResult) {
							throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
						}

					} else {

						throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
					}

				}
			}


		}
	}

	private function setupCharsets($request) {
		if ($this->checkEmpty($this->postCharset)) {
			$this->postCharset = 'UTF-8';
		}
		$str = preg_match('/[\x80-\xff]/', $this->appId) ? $this->appId : print_r($request, true);
		$this->fileCharset = mb_detect_encoding($str, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK';
	}

	// 获取加密内容

	private function encryptJSONSignSource($request, $responseContent) {

		$parsetItem = $this->parserEncryptJSONSignSource($request, $responseContent);

		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);

		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);
		return $bodyIndexContent . $bizContent . $bodyEndContent;

	}


	private function parserEncryptJSONSignSource($request, $responseContent) {

		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;

		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);


		if ($rootIndex > 0) {

			return $this->parserEncryptJSONItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserEncryptJSONItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}


	private function parserEncryptJSONItem($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;

		if ($signDataEndIndex < 0) {

			$signDataEndIndex = strlen($responseContent)-1 ;
		}

		$indexLen = $signDataEndIndex - $signDataStartIndex;

		$encContent = substr($responseContent, $signDataStartIndex+1, $indexLen-2);


		$encryptParseItem = new EncryptParseItem();

		$encryptParseItem->encryptContent = $encContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $signDataEndIndex;

		return $encryptParseItem;

	}

	// 获取加密内容

	private function encryptXMLSignSource($request, $responseContent) {

		$parsetItem = $this->parserEncryptXMLSignSource($request, $responseContent);

		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);
		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);

		return $bodyIndexContent . $bizContent . $bodyEndContent;

	}

	private function parserEncryptXMLSignSource($request, $responseContent) {


		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;


		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");


		if ($rootIndex > 0) {

			return $this->parserEncryptXMLItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserEncryptXMLItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	private function parserEncryptXMLItem($responseContent, $nodeName, $nodeIndex) {

		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;

		$xmlStartNode="<".$this->ENCRYPT_XML_NODE_NAME.">";
		$xmlEndNode="</".$this->ENCRYPT_XML_NODE_NAME.">";

		$indexOfXmlNode=strpos($responseContent,$xmlEndNode);
		if($indexOfXmlNode<0){

			$item = new EncryptParseItem();
			$item->encryptContent = null;
			$item->startIndex = 0;
			$item->endIndex = 0;
			return $item;
		}

		$startIndex=$signDataStartIndex+strlen($xmlStartNode);
		$bizContentLen=$indexOfXmlNode-$startIndex;
		$bizContent=substr($responseContent,$startIndex,$bizContentLen);

		$encryptParseItem = new EncryptParseItem();
		$encryptParseItem->encryptContent = $bizContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $indexOfXmlNode+strlen($xmlEndNode);

		return $encryptParseItem;

	}

	function echoDebug($content) {

		if ($this->debugInfo) {
			echo "<br/>" . $content;
		}

	}
}

require_once IA_ROOT . '/addons/rhinfo_zyxq/request/AlipayOpenPublicMessageCustomSendRequest.php';
class AliPushMsg {
	public function __construct($config = array()) {
		global $_W;
		$this->config = $config;
	}
	// 测试
	public function test() {
		$image_text_msg1 = $this->mkImageTextMsg ( "标题", "描述", "http://wap.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
		$image_text_msg2 = $this->mkImageTextMsg ( "标题", "描述", "http://wap.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
		// 组装多条图文信息
		$image_text_msg = array (
				$image_text_msg1,
				$image_text_msg2 
		);		
		$toUserId = "xLF-4RvtNKGlYDC8xLgTnI97w0QKRHRl-OmymTOxsGHnKDWiwQekMHiEi06tEbjg01";
		$biz_content = $this->mkImageTextBizContent ( $toUserId, $image_text_msg );
		print_r ( $this->sendRequest ($biz_content ) );
	}
	
	// 纯文本消息
	public function mkTextMsg($content) {
		$text = array (
				'content' => $content 
		);
		return $text;
	}
	
	// 图文消息，
	// $authType=loginAuth时，用户点击链接会将带有auth_code，可以换取用户信息
	public function mkImageTextMsg($title, $desc, $url, $imageUrl, $authType) {
		$articles_arr = array (
				'actionName' => "立即查看" ,
				'desc' =>  $desc ,
				'imageUrl' => $imageUrl,
				'title' =>  $title ,
				'url' => $url,
				'authType' => $authType 
		);
		return $articles_arr;
	}
	
	/**
	 * 返回图文消息的biz_content
	 *
	 * @param string $toUserId        	
	 * @param array $articles        	
	 * @return string
	 */
	public function mkImageTextBizContent($toUserId, $articles) {
		$biz_content = array (
				'msgType' => 'image-text',
				'createTime' => time (),
				'articles' => $articles 
		);
		return $this->toBizContentJson ( $biz_content, $toUserId );
	}
	/**
	 * 返回纯文本消息的biz_content
	 *
	 * @param unknown $toUserId        	
	 * @param unknown $text        	
	 * @return string
	 */
	public function mkTextBizContent($toUserId, $text) {
		$biz_content = array (
				'msgType' => 'text',
				'text' => $text 
		);
		return $this->toBizContentJson ( $biz_content, $toUserId );
	}
	private function toBizContentJson($biz_content, $toUserId) {
		// 如果toUserId为空，则是发给所有关注的而用户，且不可删除，慎用
		if (isset ( $toUserId ) && ! empty ( $toUserId )) {
			$biz_content ['toUserId'] = $toUserId;
		}
		
		$content = $this->JSON ( $biz_content );
		return $content;
	}
	/**
	 * 使用sdk中的异步单发消息接口，发送组装好的信息
	 *
	 * @param unknown $biz_content        	
	 */
	public function sendRequest($biz_content) {
		$custom_send = new AlipayOpenPublicMessageCustomSendRequest();
		$custom_send->setBizContent ( $biz_content );
		
		return aopclient_request_execute ($custom_send,$this->config);
	}

	function is_utf8($text) {
		$e = mb_detect_encoding ( $text, array (
				'UTF-8',
				'GBK' 
		) );
		switch ($e) {
			case 'UTF-8' : // 如果是utf8编码
				return true;
			case 'GBK' : // 如果是gbk编码
				return false;
		}
	}
	
	/**
	 * 下载用户发送过来的图片
	 *
	 * @param unknown $biz_content        	
	 * @param unknown $fileName        	
	 */
	public function downMediaRequest($biz_content,$fileName) {
		$config = $this->config;		
		date_default_timezone_set ( PRC );
		$paramsArray = array (
				'method' => "alipay.mobile.public.multimedia.download",
				'biz_content' => $biz_content,
				'charset' => $config ['charset'],
				'sign_type' => $config['sign_type'],
				'app_id' => $config ['app_id'],
				'timestamp' => date ( 'Y-m-d H:i:s', time () ),
				'version' => "1.0" 
		);
		
		require_once 'AopSdk.php';
		$as = new AopClient();
		$signContent=$as->getSignContent($paramsArray);
		$sign=$as->alonersaSign($signContent,$config['merchant_private_key'],$config['sign_type']);
		$paramsArray ['sign'] = $sign;
		$url = "https://openfile.alipay.com/chat/multimedia.do?";
		
		WeUtility::logging('log',(sendPostRequest ( $url, $paramsArray )));
		file_put_contents ( $fileName, sendPostRequest ( $url, $paramsArray ) );
	}
	
	/**
	 * ************************************************************
	 *
	 * 使用特定function对数组中所有元素做处理
	 *
	 * @param
	 *        	string &$array 要处理的字符串
	 * @param string $function
	 *        	要执行的函数
	 * @return boolean $apply_to_keys_also 是否也应用到key上
	 * @access public
	 *        
	 *         ***********************************************************
	 */
	protected function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
		foreach ( $array as $key => $value ) {
			if (is_array ( $value )) {
				$this->arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
			} else {
				$array [$key] = $function ( $value );
			}
			
			if ($apply_to_keys_also && is_string ( $key )) {
				$new_key = $function ( $key );
				if ($new_key != $key) {
					$array [$new_key] = $array [$key];
					unset ( $array [$key] );
				}
			}
		}
	}
	
	/**
	 * ************************************************************
	 *
	 * 将数组转换为JSON字符串（兼容中文）
	 *
	 * @param array $array
	 *        	要转换的数组
	 * @return string 转换得到的json字符串
	 * @access public
	 *        
	 *         ***********************************************************
	 */
	protected function JSON($array) {
		$this->arrayRecursive ( $array, 'urlencode', true );
		$json = json_encode ( $array );
		return urldecode ( $json );
	}
}

class AliMessage {
	public function __construct($config = array()) {
		global $_W;
		$this->config = $config;
	}
	public function Message($biz_content) {
		header ( "Content-Type: text/xml;charset=GBK" );
		
		$UserInfo = $this->getNode ( $biz_content, "UserInfo" );
		$FromUserId = $this->getNode ( $biz_content, "FromAlipayUserId" );
		$AppId = $this->getNode ( $biz_content, "AppId" );
		$CreateTime = $this->getNode ( $biz_content, "CreateTime" );
		$MsgType = $this->getNode ( $biz_content, "MsgType" );
		$EventType = $this->getNode ( $biz_content, "EventType" );
		$AgreementId = $this->getNode ( $biz_content, "AgreementId" );
		$ActionParam = $this->getNode ( $biz_content, "ActionParam" );
		$AccountNo = $this->getNode ( $biz_content, "AccountNo" );
		
		$push = new AlipushMsg($this->config);
		// 收到用户发送的对话消息
		if ($MsgType == "text") {
			
			$text = $this->getNode ( $biz_content, "Text" );
			WeUtility::logging('收到的文本', var_export($text, true));
			
			$text_msg = $push->mkTextMsg ( "你好，这是对话消息" );
			
			// 发给这个关注的用户
			$biz_content = $push->mkTextBizContent ( $FromUserId, $text_msg );
			
			WeUtility::logging('biz_content', var_export($biz_content, true));
			$return_msg = $push->sendRequest ($biz_content );

			// 日志记录
			WeUtility::logging('发送对话消息返回', var_export($return_msg, true));
		}
		
		// 接收用户发送的 图片消息
		if ($MsgType == "image") {
			
			$mediaId = $this->getNode ( $biz_content, "MediaId" );
			$format = $this->getNode ( $biz_content, "Format" );
			
			$biz_content = "{\"mediaId\":\"" . $mediaId . "\"}";
			
			$fileName = realpath ( "img" ) . "/$mediaId.$format";
			// 下载保存图片
			$push->downMediaRequest ( $biz_content, $fileName );

			WeUtility::logging('收到的图片路径', var_export($fileName, true));
			
			$text_msg = $push->mkTextMsg ( "你好，图片已接收。" );
			
			// 发给这个关注的用户
			$biz_content = $push->mkTextBizContent ( $FromUserId, $text_msg );
			
			$return_msg = $push->sendRequest ( $biz_content );
			// 日志记录
			WeUtility::logging('发送对话消息返回', var_export($return_msg, true));
		}
		
		// 收到用户发送的关注消息
		if ($EventType == "follow") {
			// 处理关注消息
			// 一般情况下，可推送一条欢迎消息或使用指导的消息。
			// 如：
			$image_text_msg1 = $push->mkImageTextMsg ( "标题，感谢关注", "描述", "http://m.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
			$image_text_msg2 = $push->mkImageTextMsg ( "标题", "描述", "http://m.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
			// 组装多条图文信息
			$image_text_msg = array (
					$image_text_msg1,
					$image_text_msg2 
			);
			// 发给这个关注的用户
			$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );
			
			$return_msg = $push->sendRequest ( $biz_content );
			// 日志记录
			WeUtility::logging('发送对话消息返回', var_export($return_msg, true));
		} elseif ($EventType == "unfollow") {
			// 处理取消关注消息
		} elseif ($EventType == "enter") {
			
			// 处理进入消息，扫描二维码进入,获取二维码扫描传过来的参数
			
			$arr = json_decode ( $ActionParam );
			if ($arr != null) {
				writeLog ( "二维码传来的参数：" . var_export ( $arr, true ) );
				
				$sceneId = $arr->scene->sceneId;
				WeUtility::logging('二维码传来的参数,场景ID', var_export($sceneId, true));
				// 这里可以根据定义场景ID时指定的规则，来处理对应事件。
				// 如：跳转到某个页面，或记录从什么来源(哪种宣传方式)来关注的本服务窗
			}
			// 处理关注消息
			// 一般情况下，可推送一条欢迎消息或使用指导的消息。
			// 如：
			$image_text_msg1 = $push->mkImageTextMsg ( "标题，进入服务窗", "描述：进入服务窗", "http://m.taobao.com", "", "loginAuth" );
			// $image_text_msg2 = $push->mkImageTextMsg ( "标题", "描述", "http://m.taobao.com", "https://i.alipayobjects.com/e/201310/1H9ctsy9oN_src.jpg", "loginAuth" );
			// 组装多条图文信息
			$image_text_msg = array (
					$image_text_msg1 
				 // $image_text_msg2
				);
			
			// 发给这个关注的用户
			$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );
			
			$return_msg = $push->sendRequest ( $biz_content );
			// 日志记录
			WeUtility::logging('发送对话消息返回', var_export($return_msg, true));
		} elseif ($EventType == "click") {
			// 处理菜单点击的消息
			
			// 在服务窗后台配置一个菜单，菜单类型为调用服务，菜单参数为sendmsg，用户点击次菜单后，就会调用到这里
			if ($ActionParam == "sendmsg") {
				$image_text_msg1 = $push->mkImageTextMsg ( "标题，发送消息测试", "描述：发送消息测试", "http://m.taobao.com", "", "loginAuth" );
				// 组装多条图文信息
				$image_text_msg = array (
						$image_text_msg1 
				);
				
				// 发给这个关注的用户
				$biz_content = $push->mkImageTextBizContent ( $FromUserId, $image_text_msg );				
				$return_msg = $push->sendRequest ( $biz_content );
				// 日志记录
				WeUtility::logging('发送对话消息返回', var_export($return_msg, true));
			}
			
			//服务窗顶部添加会员号点击后，直接返回XML
			elseif ($ActionParam == "authentication"){
				$redirect_url = "http://m.taobao.com";
				$biz_content = "<XML><ToUserId><![CDATA[".$FromUserId."]]></ToUserId><AgreementId><![CDATA[]]></AgreementId><AppId><![CDATA[".$AppId."]]></AppId><CreateTime>".time()."</CreateTime><MsgType><![CDATA[image-text]]></MsgType><ArticleCount>1</ArticleCount><Articles><Item><Title><![CDATA[]]></Title><Desc><![CDATA[]]></Desc><ImageUrl><![CDATA[]]></ImageUrl><Url><![CDATA[".$redirect_url."]]></Url></Item></Articles><Push><![CDATA[false]]></Push></XML>";
				echo $biz_content;
				WeUtility::logging('output', var_export($return_msg, true));
				exit();
			}
		}
		
		// 给支付宝返回ACK回应消息，不然支付宝会再次重试发送消息,再调用此方法之前，不要打印输出任何内容
		echo self::mkAckMsg ( $FromUserId );
		exit ();
	}
	public function mkAckMsg($toUserId) {
		$as = new AopClient();
		$config = $this->config;
		$response_xml = "<XML><ToUserId><![CDATA[" . $toUserId . "]]></ToUserId><AppId><![CDATA[" . $config ['app_id'] . "]]></AppId><CreateTime>" . time () . "</CreateTime><MsgType><![CDATA[ack]]></MsgType></XML>";
		
		$mysign=$as->alonersaSign($response_xml,$config['merchant_private_key'],$config['sign_type']);
		$return_xml = "<?xml version=\"1.0\" encoding=\"".$config['charset']."\"?><alipay><response>".$response_xml."</response><sign>".$mysign."</sign><sign_type>".$config['sign_type']."</sign_type></alipay>";

		WeUtility::logging('response_xml', var_export($return_xml, true));
		return $return_xml;
	}
	
	/**
	 * 直接获取xml中某个结点的内容
	 *
	 * @param unknown $xml        	
	 * @param unknown $node        	
	 */
	public function getNode($xml, $node) {
		$xml = "<?xml version=\"1.0\" encoding=\"GBK\"?>" . $xml;
		$dom = new DOMDocument ( "1.0", "GBK" );
		$dom->loadXML ( $xml );
		$event_type = $dom->getElementsByTagName ( $node );
		return $event_type->item ( 0 )->nodeValue;
	}
}

class SignData {
    public $signSourceData=null;
    public $sign=null;
} 
class EncryptParseItem {
	public $startIndex;
	public $endIndex;
	public $encryptContent;
}
class EncryptResponseData {
	public $realContent;
	public $returnContent;
} 

require_once IA_ROOT . '/addons/rhinfo_zyxq/request/AlipaySystemOauthTokenRequest.php';
require_once IA_ROOT . '/addons/rhinfo_zyxq/request/AlipayUserUserinfoShareRequest.php';
class UserInfo {
	public function __construct($config = array()) {
		global $_W;
		$this->config = $config;
	}
	public function getUserInfo($auth_code) {
		$token = $this->requestToken($auth_code);
		if(isset($token->alipay_system_oauth_token_response)){			
			$token_str = $token->alipay_system_oauth_token_response->access_token;
			$user_info = $this->requestUserInfo($token_str);			
			if (isset($user_info->alipay_user_userinfo_share_response )) {
				$user_info_resp = $user_info->alipay_user_userinfo_share_response;				
				$user_id = $user_info_resp->user_id;				
				$deliver_fullname = characet ( $user_info_resp->deliver_fullname );
				$deliver_mobile = $user_info_resp->deliver_mobile;
				return $user_info_resp;
			}
			return $token->alipay_system_oauth_token_response;
		}
		elseif(isset($token->error_response)) {
			return error(-1,$token->error_response->sub_msg);
		}
		else{
			return error(-1,'操作异常');
		}
	}
	public function requestUserInfo($token) {
		$AlipayUserUserinfoShareRequest = new AlipayUserUserinfoShareRequest ();		
		$result = aopclient_request_execute ( $AlipayUserUserinfoShareRequest,$this->config, $token );
		return $result;
	}
	public function requestToken($auth_code) {
		$AlipaySystemOauthTokenRequest = new AlipaySystemOauthTokenRequest ();
		$AlipaySystemOauthTokenRequest->setCode ( $auth_code );
		$AlipaySystemOauthTokenRequest->setGrantType ( "authorization_code" );		
		$result = aopclient_request_execute ($AlipaySystemOauthTokenRequest,$this->config );
		return $result;
	}
}