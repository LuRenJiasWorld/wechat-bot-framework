<?php
/**
 * 错误处理类
 */

define("NEWLINE", "</br>");

class errorHandle {
	public function pushError ($e) {
		$errorMsg = array(
			"errMessage"		=>			$e->getMessage(),
			"errCode"			=>			$e->getCode(),
			"errFile"			=>			$e->getFile(),
			"errLine"			=>			$e->getLine(),
			"errTrace"			=>			$e->getTraceAsString()
		);
// 		$this->logError($errorMsg);
//		$this->crash();														//todo 上线后开启该断言模式
	}

	private function logError ($errorMsg) {
		$logSave = new logSave;
		$logSave->crashLog($errorMsg);
	}

	private function crash () {
		exit("系统崩溃，请参考日志排查故障！");
	}

}
