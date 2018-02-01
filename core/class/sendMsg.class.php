<?php

class sendMsg {
	public function send_msg($msg) {
		global $logSave;
		$logSave->log(2, $msg);
		echo $msg;
	}
}