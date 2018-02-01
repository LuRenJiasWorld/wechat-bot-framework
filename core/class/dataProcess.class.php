<?php
/**
 * WechatBot
 * dataProcess.php 数据处理&分类类
 *
 * 数据类型(MsgType)：
 * 1、文本 text
 * 2、图片 image
 * 3、语音 voice
 * 4、视频 video
 * 5、小视频 shortvideo
 * 6、地理位置 location
 * 7、链接 link
 *
 * Copyright © 2018 LiuFuxin
 * in WHUT C&S Depart.
 * e-mail loli@lurenjia.in
 * blog http://untitled.pw/
 */


class dataProcess {
	public function dataProcessPortal ($rawPostData) {
		$errorHandle = new errorHandle();																				//实例化抛出错误类
		$data = simplexml_load_string($rawPostData, 'SimpleXMLElement', LIBXML_NOCDATA);				//解析xml为对象

		switch ($data->MsgType) {																						//数据分类
			case "text":										//文本类型
				return $this->textProcess($data);
				break;
			case "image":										//图片类型
				return $this->imageProcess($data);
				break;
			case "voice":										//语音类型
				return $this->voiceProcess($data);
				break;
			case "video":										//视频类型
				return $this->videoProcess($data);
				break;
			case "shortvideo":									//小视频类型
				return $this->shortvideoProcess($data);
				break;
			case "location":									//地理位置类型
				return $this->locationProcess($data);
				break;
			case "link":										//链接类型
				return $this->linkProcess($data);
				break;
            case "event":
                return $this->eventProcess($data);
			default:											//其他（非法）类型
				try {
					throw new Exception("数据包类型错误！", 30);
				} catch (Exception $e) {
					$errorHandle->pushError($e);
				}
				break;
		}
	}
	private function textProcess ($data) {
		global $Db;
		global $config;
		$picUpload = new picUpload();
		$message = $data->Content;											//获取消息
		$key_pattern = "/^[0-9]+$/";

		if (preg_match($key_pattern, $message)) {
			$returnData = $Db->query("MySQL", $message);						//提交消息到数据库处理类
			if (!$returnData) {													//指令不正确
				$MsgContent = $config["ui"]["key_error"];
				return $this->msgSynthesis("text", $data, $MsgContent);
			} else {
				$picResFile = $returnData;										//获取返回图片文件名
				$mediaId = $picUpload->upload($picResFile);						//上传图片
				return $this->msgSynthesis("image", $data, $mediaId);
			}
		} else {
			return $this->msgSynthesis("text", $data, $config["ui"]["default"]);
		}



	}
	private function imageProcess ($data) {
		return $this->unsupportedProcess("image", $data);
	}

	private function voiceProcess ($data) {
		return $this->unsupportedProcess("voice", $data);
	}

	private function videoProcess ($data) {
		return $this->unsupportedProcess("video", $data);
	}

	private function shortvideoProcess ($data) {
		return $this->unsupportedProcess("shortvideo", $data);
	}

	private function locationProcess ($data) {
		return $this->unsupportedProcess("location", $data);
	}

	private function linkProcess ($data) {
		return $this->unsupportedProcess("link", $data);
	}

	private function eventProcess ($data) {                                 //事件处理
        global $config;
        $event = $data->Event;
        switch ($event) {
            case "subscribe":                                                   //订阅事件
                return $this->msgSynthesis("text", $data, $config["ui"]["subscribe_event"]);
            case "unsubscribe":                                                 //取关事件
                return NULL;
        }
    }

	private function unsupportedProcess ($type, $data) {		//暂不支持类型
		global $config;
		$MsgContent = sprintf($config["ui"]["unsupport_type"], $config["Types"][$type]);
		return $this->msgSynthesis("text", $data, $MsgContent);
	}

	private function msgSynthesis ($type, $originData, $toSendData) {
		global $config;
		switch ($type) {
			case "text":										//文本类型
				$finalMsg = sprintf($config["MsgPattern"]["TextMsg"], $originData->FromUserName, $originData->ToUserName, time(), $toSendData);
				return $finalMsg;
				break;
			case "image":										//图片类型
				$finalMsg = sprintf($config["MsgPattern"]["PicMsg"], $originData->FromUserName, $originData->ToUserName, time(), $toSendData);
				return $finalMsg;
				break;
			case "voice":										//语音类型
				break;
			case "video":										//视频类型
				break;
			case "shortvideo":									//小视频类型
				break;
			case "location":									//地理位置类型
				break;
			case "link":										//链接类型
				break;
			default:											//不回复任何消息
				return NULL;
				break;
		}
	}
}
