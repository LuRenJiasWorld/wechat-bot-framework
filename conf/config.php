<?php
/**
 * WechatBot
 * config.php 全局配置文件
 * Copyright © 2018 LiuFuxin
 * in WHUT C&S Depart.
 * e-mail loli@lurenjia.in
 * blog http://untitled.pw/
 */

$config["WXConfig"] = array(
	"AppID"			=>			"",
	"AppSecret"		=>			"",
	"url"			=>			"",
	"token"			=>			"",
	"EncodingAESKey"=>			""
);

$config["WXApi"] = array(
	"UploadMedia"	=>			"https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s",
	"DownloadMedia"	=>			"https://api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s",
	"getAccToken"	=>			"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s"
);


$config["MsgPattern"] = array(
	"TextMsg"		=>			"<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>",
	"PicMsg"		=>			"<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>"
);

$config["DbConfig"] = array(
	"type"			=>			"MySQL",
	"Server"		=>			"localhost",
	"port"			=>			"3306",
	"DbName"		=>			"",
	"User"			=>			"",
	"Password"		=>			"",
);

$config["CacheConfig"] = array(
	"log_cache"		=>			"Off",
	"type"			=>			"MemCached",
	"host"			=>			"127.0.0.1",				//Memcached地址（本地）
	"ServerList"	=>			array(						//不同业务使用的Memcached端口列表
		"access_token"		=>			"11211",			//存储AccessToken的数据库端口
		"logs"				=>			"11211"				//存储日志缓存的数据库端口
	)
);


$config["Types"] = array(
	"text"			=>			"文本",
	"image"			=>			"图片",
	"voice"			=>			"语音",
	"video"			=>			"视频",
	"shortvideo"	=>			"小视频",
	"location"		=>			"位置",
	"link"			=>			"链接"
);

$config["ui"] = array(
	"key_error"				=>			"请输入正确的指令！",                                                  //key错误
	"unsupport_type"		=>			"抱歉，我现在还无法理解你发送的%s消息，请换用文字与我交流~",                  //不支持的类型
    "default"               =>          "这是默认消息",                                                                 //默认回复
    "subscribe_event"       =>          "关注事件触发",                                                                 //关注事件
);
