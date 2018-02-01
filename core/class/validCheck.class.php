<?php

class validCheck {
	public function valid_check($getReqArray) {
		global $config;
		$tmpArr = array($config["WXConfig"]["token"], $getReqArray["timestamp"], $getReqArray["nonce"]);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$key = sha1($tmpStr);
		if ($key == $getReqArray["signature"]) {
			return true;
		} else {
			return false;
		}
	}

	public function getAccToken() {
		global $config;
		global $logSave;
		$cache = new Memcached;
		$cache->addServer($config["CacheConfig"]["host"], $config["CacheConfig"]["ServerList"]["access_token"]);
		$access_token = $cache->get("access_token");							//尝试检索cache中相关字段

		if($access_token) {
			return $access_token;
		} else {
			$requestUrl = sprintf($config["WXApi"]["getAccToken"], $config["WXConfig"]["AppID"], $config["WXConfig"]["AppSecret"]);
			$session = curl_init();														//初始化会话

			curl_setopt($session, CURLOPT_URL, $requestUrl);						//请求url
			curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);				//直接返回自服务器返回的字符串
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, REQ_TIMEOUT);	//超时时间
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);			//不检验ssl对等证书
			curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);			//不验证域合法性
			$dataRecv = curl_exec($session);											//执行cURL请求
			$data = json_decode($dataRecv, true);									//解析session
			curl_close($session);														//关闭会话
			$cache->add("access_token", $data["access_token"], 7200);		//设定一个7200s后过期的缓存
			$logSave->log(4, $cache->get("access_token"));					//日志
			$this->getAccToken();														//递归自举
		}
	}
}