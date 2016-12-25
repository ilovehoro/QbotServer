<?php

$jsonArr = [
	"status" => 0, //BQ表情，0异常，1单消息，2纯q操作，3混合
	"msg" => "",//返回消息主体
	"at_qq" => "",//@qq
	"cq" => [
	//cq操作1
	//cq操作2
	//... cq操作...
	]
];
/* 预设cq 数组 */

$face_arr = ['我最美','小纠结','骚扰','惊喜','喷血','斜眼笑','卖萌','托腮','无奈','泪奔','doge','笑哭','舔'];

$cq_share_link = [
	"code" => "1",
	"link" => "http://baidu.com/",
	"title" => "TEST",
	"content" => "test",
	"img" => "https://r.cqp.me/avatar/000/00/00/01_avatar_middle.jpg"
];

$cq_card = [];
$cq_img = [];
$cq_send_group_msg = [];
$cq_send_private_msg = [];
$cq_set_title = [];