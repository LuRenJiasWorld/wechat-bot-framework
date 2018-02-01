<?php
/**
 * Class logSave
 * 日志记录类
 * 日志格式：
 * [get]	2018-01-20 06:00:00 client_ip:171.115.87.72 url:http://static.lurenjia.in/wx?param1=666&param2=6666
 * [error]	2018-01-21 18:00:00 client_ip:171.115.87.72 url:http://static.lurenjia.in/wx?param1=666&param2=6666 CODE:10 file:xxx line:xxx trace:xxx=>sss=>xxx(12)
 * [post]	2018-01-21 19:00:00 client_ip:171.115.87.72 url:http://static.lurenjia.in/wx?param1=666&param2=6666 data:<xml>……</xml>
 * [show]	2018-01-21 20:00:00 url:http://static.lurenjia.in/wx?param1=666&param2=6666 data:echostr<xml></xml>
 * [upload]	2018-01-21 20:00:01 from:http://static.lurenjia.in/wx?param1=666&param2=6666 to:https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE pic:./data/pic/000000001.jpg media_id:xxxxxxxxx
 * [acctok]	2018-01-21 20:20:00 from:http://static.lurenjia.in/wx?param1=666&param2=6666 req:https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET getToken:xxxxxx
 */

$today = array(
	"year"		=>		date("Y"),
	"month"		=>		date("m"),
	"day"		=>		date("d")
);

class logSave {
	public function __construct () {
		/**
		 * 扫描日志文件
		 * 自动生成新日志
		 */

		global $today;
		$logFilePattern = "/^(log)\_[0-9]{4}\_(0?[[1-9]|1[0-2])\_([0][1-9]|[12][0-9]|3[01])\.(log)$/";		//日志文件匹配正则
		$fileList = scandir(LOGDIR);
		static $logFileList = array();
		$haveToday = False;																					//是否已经有今天的日志
		foreach ($fileList as $file) {
			if (preg_match($logFilePattern, $file)) {
				array_merge($logFileList, [$file]);															//满足格式的日志加入列表
				$date = explode("_", substr($file, 4, 10));							//拆分出日志日期
				if ($date[0] == $today["year"] && $date[1] == $today["month"] && $date[2] == $today["day"]) {
					$haveToday = True;																		//若日志文件已经有当日日志
				}
			}
		}
		if ($haveToday == False) {
			$this->newLog($today);
		}
	}

	public function log($mode, $data) {
		$errorHandle = new errorHandle();																	//实例化抛出错误类

		/**
		 * 参数完整性检验
		 */
		try {
			if ($mode == NULL || $data == NULL) {
				new Exception("日志参数不完整！", 90);
			}
		} catch (Exception $e) {
			$errorHandle->pushError($e);
		}

		$data = $this->clearEnters($data);								//去除换行符

		/**
		 * 日志归类放入队列
		 */
		switch ($mode) {
			case 0:														//$mode = 0 => get请求日志
				$logLine = "[get]\t".$this->getDate(). " client_ip:".$this->logIp()." url:".$this->getUrl();
				break;
			case 1:														//$mode = 1 => 微信post数据包日志
				$logLine = "[post]\t".$this->getDate()." client_ip:".$this->logIp()." url:".$this->getUrl()." data:".$data;
				break;
			case 2:														//$mode = 2 => 返回数据包日志
				$logLine = "[show]\t".$this->getDate()." url:".$this->getUrl()." data:".$data;
				break;
			case 3:														//$mode = 3 => 上传图片日志
				$logLine = "[upload]\t".$this->getDate()." from:".$this->getUrl()." to:".$data["toUrl"]." pic:".$data["pic"]." media_id:".$data["media_id"];
				break;
			case 4:														//$mode = 4 => access_token日志
				$logLine = "[acctok]\t".$this->getDate()." from:".$this->getUrl()." req:".$data["requireTo"]." getToken:".$data["acctok"];
				break;
			default:
				try{
					new Exception("日志类别错误！", 91);
				} catch (Exception $e) {
					$errorHandle->pushError($e);
				}
				break;

		}
		$this->cache($logLine);											//放入队列
	}

	public function crashLog ($data) {
		$data = $this->clearEnters($data);
		$detail = " CODE:".$data["errCode"]." file:".$data["errFile"]." line:".$data["errLine"]." trace:".$data["errTrace"];
		$logLine = "[error]\t".$this->getDate()." client_ip:".$this->logIp()." url:".$this->getUrl().$detail;
		$this->cache($logLine);
	}

	public function logIp() {
		$ip = $_SERVER["REMOTE_ADDR"];
		return $ip;
	}

	public function getDate() {
		$date = date('Y-m-d H:i:s',time());
		return $date;
	}

	public function getUrl() {
		$url = 'http';

		if ($_SERVER["HTTPS"] == "on") {
			$url .= "s";
		}
		$url .= "://";

		if ($_SERVER["SERVER_PORT"] != "80") {
			$url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $url;
	}

	private function geneFileName($date) {
		$fileName = "log".$date["year"]."_".$date["month"]."_".$date["day"].".log";
		return $fileName;
	}

	public function cache ($log) {
		global $config;
		global $today;

// 		if ($config["CacheConfig"]["log_cache"] == "On") {			//开启日志缓存系统
			$cache = new Memcached();
			$cache->addServer($config["CacheConfig"]["host"], $config["CacheConfig"]["ServerList"]["cache"]["logs"]);
			for ($i = 0; $i < LOG_CACHE_SRSHD; $i ++) {
				if ($cache->get($i) == FALSE) {
					$cache->add($i, $log);
					break;
				}
			}

			if($cache->get(LOG_CACHE_SRSHD - 1)) {
				$logs = array();
				for ($i = 0; $i < LOG_CACHE_SRSHD; $i ++) {
					$logs = array_merge($logs, array($cache->get($i)));
				}
				for ($i = 0; $i < LOG_CACHE_SRSHD; $i ++) {
					$cache->delete($i);
				}
				$this->saveLog($logs, $today);

			}
// 		} else {														//关闭日志缓存系统
// 			static $logCache = array();
// 			if (count($logCache) == 1) {
// 				$this->saveLog($logCache, $today);
// 				unset ($logCache);
// 			} else {
// 				$newLog = array($log);
// 				$logCache = array_merge($logCache, $newLog);
// 			}
// 		}




	}

	public function saveLog ($logs, $date) {
		$fileName = $this->geneFileName($date);
		$string = "";
		$file = fopen(LOGDIR.$fileName, "a");
		foreach ($logs as $log) {
			$string = $string.$log."\n";
		}
		fwrite($file, $string);
		fclose($file);
	}

	public function newLog ($date) {
		$file = fopen(LOGDIR.$this->geneFileName($date), "a");
		fclose($file);
	}

	public function clearEnters ($data) {
		$data = str_replace(PHP_EOL, '', $data);
		return $data;
	}
}