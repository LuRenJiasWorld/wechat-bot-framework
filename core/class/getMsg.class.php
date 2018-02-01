<?php

class getMsg {
	public function get_msg($param = NULL) {
		global $logSave;
		$msg = file_get_contents("php://input");
		$logSave->log(1, $msg);
		return $msg;
	}
}