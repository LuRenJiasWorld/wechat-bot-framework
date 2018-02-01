<?php
/**
* 初始化入口
* init.php
* LuRenJia 2018
*/

global $moduleList;
global $module;

include(CLASSDIR."errorHandle.class.php");
include(CLASSDIR."logSave.class.php");
$errorHandle = new errorHandle();				//加载错误处理，核心模块
$logSave = new logSave();						//加载日志系统，核心模块

/**
 * 检测配置文件
 */
try {
	if (!file_exists(CONFDIR."config.php")) {																//配置文件不存在
		throw new Exception("配置文件不存在！", 10);
	}
	@include(CONFDIR."config.php");																					//加载配置文件
	if (!isset($config["WXConfig"]) && !isset($config["MsgPattern"]) && !isset($config["DbConfig"])) {				//配置文件不完整
		throw new Exception("配置文件有误！", 11);
	}
} catch (Exception $e) {
	$errorHandle->pushError($e);
}


/**
 * 加载类库文件
 * 按照依赖顺序加载
 * 所有系统均依赖错误处理与日志类，因此独立加载
 */
$classes = array(
	"Db",						//数据库抽象api，依赖日志系统
	"dataProcess",				//数据处理，依赖数据库、日志系统
	"getMsg",					//消息获取，依赖日志系统
	"picSynthesis",				//图像合成
	"picUpload",				//图片上传，依赖日志系统，数据库
	"sendMsg",					//消息发送
	"validCheck"				//有效性检验
);
foreach ($classes as $class) {
	include(CLASSDIR.$class.".class.php");
	$$class = new $class;
}

