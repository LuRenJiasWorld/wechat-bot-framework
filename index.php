<?php
/**
 * WechatBot
 * Copyright © 2018 LiuFuxin
 * in WHUT C&S Depart.
 * e-mail loli@lurenjia.in
 * blog http://untitled.pw/
 */

define("FCPATH", __FILE__);															//获取绝对路径
define("ROOTDIR", dirname(FCPATH));											//获取根路径
define("COREDIR", ROOTDIR.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR);			//获取核心文件路径
define("APPDIR", ROOTDIR.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR);			//获取业务文件路径
define("LOGDIR", ROOTDIR.DIRECTORY_SEPARATOR."log".DIRECTORY_SEPARATOR);			//获取日志文件路径
define("CONFDIR", ROOTDIR.DIRECTORY_SEPARATOR."conf".DIRECTORY_SEPARATOR);			//获取配置文件路径
define("DATADIR", ROOTDIR.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR);			//获取数据文件路径
define("CLASSDIR", COREDIR."class".DIRECTORY_SEPARATOR);							//获取类库路径
define("LOG_CACHE_SRSHD", 1);														//日志缓存阈值 log_cache_threshold
define("REQ_TIMEOUT", 5);															//cURL请求超时时间


/**
 * 系统初始化
 */
include(COREDIR."init.php");														//获取初始化文件
include(APPDIR."portal.php");														//加载入口