<?php

class picUpload {
	public function upload($fileName) {
		global $config;
		global $validCheck;
		global $logSave;

		$access_token = $validCheck->getAccToken();											//获取access_token
		$trueFile = DATADIR."pic/".$fileName;												//生成绝对地址
		$imageObj = array("media" => new \CURLFile($trueFile));								//生成多媒体对象
		$url = sprintf($config["WXApi"]["UploadMedia"], $access_token, "image");			//生成媒体上传url

		$uploadSession = curl_init();														//初始化上传会话
		curl_setopt($uploadSession, CURLOPT_URL, $url);								//请求url
		curl_setopt($uploadSession, CURLOPT_POST, 1);							//post模式开关
		curl_setopt($uploadSession, CURLOPT_RETURNTRANSFER, 1);				//直接返回自服务器返回的字符串
		curl_setopt($uploadSession, CURLOPT_CONNECTTIMEOUT, REQ_TIMEOUT);		//超时时间
		curl_setopt($uploadSession, CURLOPT_SSL_VERIFYPEER, FALSE);			//不检验ssl对等证书
		curl_setopt($uploadSession, CURLOPT_SSL_VERIFYHOST, false);			//不验证域合法性
		curl_setopt($uploadSession, CURLOPT_POSTFIELDS, $imageObj);					//post数据来源
		$resultData = json_decode(curl_exec($uploadSession), true);								//执行cURL请求

		$data = array(
			"toUrl"		=>		$url,
			"pic"		=>		$trueFile,
			"media_id"	=>		$resultData["media_id"]
		);
		$logSave->log(3, $data);

		return $resultData["media_id"];														//返回media_id

	}
}