<?php
/**
 * 数据库处理类
 * query方法mode参数：
 * 0、只读查询
 * 1、带计数标记查询					//查询一次计数器+1
 * 2、………………
 */

class Db {
	public function __construct() {
		$supportDb = array("MySQL");								//支持数据库类型
		global $config;												//导入配置文件
		global $errorHandle;										//导入错误处理文件
		try {
			if (!in_array($config["DbConfig"]["type"], $supportDb)) {
				throw new Exception("不受支持的数据库类型",40);
			}
		} catch (Exception $e) {
			$errorHandle->pushError($e);
		}
		$this->connectDb($config["DbConfig"]["type"]);
	}

	public function query($mode, $key) {
		$sql = "SELECT * FROM `info` WHERE `key_no` = \"".$key."\"";		//查询语句
		$update = "UPDATE `info` SET  `query_times` =  '%s' WHERE  `info`.`stuno` =  '%s';";
		$DbObj = $this->connectDb($mode);							//连接数据库
		$DbObj->set_charset('utf8mb4');
		$resultObj = $DbObj->query($sql);
		$result = $resultObj->fetch_assoc();
		if (!$result) {												//测试用
			return NULL;
		} else {
			$requireNum = $result["query_times"];
			$requireNum = (int)$requireNum + 1;
			$update = sprintf($update, $requireNum, $result["stuno"]);
			$DbObj->query($update);
			return $result["img"];
		}
	}


	private function connectDb($DbType) {
		global $config;
		global $errorHandle;
		try {
			if ($DbType = "MySQL") {
				$Db = new mysqli($config["DbConfig"]["Server"], $config["DbConfig"]["User"], $config["DbConfig"]["Password"],$config["DbConfig"]["DbName"], $config["DbConfig"]["Port"]);
			}
			if (!$Db) {
				throw new Exception("数据库连接失败", 41);
			}
		} catch (Exception $e) {
			$errorHandle->pushError($e);
		}
		return $Db;
	}
}