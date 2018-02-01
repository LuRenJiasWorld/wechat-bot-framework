<?php
/**
 * 总入口/路由
 * 入口数据类型：
 * 微信服务器发送签名认证(get)以及用户消息(post)
 */

/**
 * 参数检查
 */
try {
	if (empty($_GET)){
		throw new Exception("未携带参数！", 20);
	} else if (!isset($_GET['signature']) && !isset($_GET['timestamp']) && !isset($_GET['nonce']) && !isset($_GET['echostr'])){
		throw new Exception("参数不完整！", 21);
	}
} catch (Exception $e) {
	$errorHandle->pushError($e);
}

/**
 * 加载参数
 */
$getReqArray = array(
	"signature"			=>		$_GET['signature'],
	"timestamp"			=>		$_GET['timestamp'],
	"nonce"				=>		$_GET['nonce'],
	"echostr"			=>		$_GET['echostr']
);

/**
 * 参数校验
 */
if($validCheck->valid_check($getReqArray)) {
    echo $_GET['echostr'];
// 	$sendMsg->send_msg($_GET['echostr']);						//输出校验通过码
} else {
	try {
		throw new Exception("非法参数，校验不通过！", 22);
	} catch (Exception $e) {
		$errorHandle->pushError($e);
	}
}


/**
 * 核心路由
 */
$logSave->log(0, "");
$rawPostData = $getMsg->get_msg();										//获取Post数据包
$returnData = $dataProcess->dataProcessPortal($rawPostData);			//获取处理后的回复数据
$sendMsg->send_msg($returnData);										//输出回复数据
