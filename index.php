<?php
require_once 'lib/db.php';
require_once 'lib/function.php';
/* 预处理页面 */
header("Content-type: text/html; charset=gbk");
date_default_timezone_set('PRC');
$key = 'iEV7roTD';
/* 预处理请求 */
if(!isset($_POST['key'])){exit('未授权');}
if(!isset($_POST['msg'])){exit('获取消息失败');}
if(!isset($_POST['qq'])){exit('获取用户信息失败');}
if($_POST['key'] != $key){exit('授权失败');}
$qq = $_POST['qq'];
/* 处理message */
$msg = substr($_POST['msg'], 1);
$msg = trim($msg);
$arr = explode(" ",$msg);
/* 加载所有指令 */
$res = $db->select('muti-function','*');
/* 筛选并执行指令 */
if(count($arr) == 1) {
	/* 处理不带参数的指令 */
	$res = $db->select('muti-function','*',[
		"name" => $msg
	]);
	if($res){
		$res = $db->select('function','*',[
			"id" => $res[0]['fid']
		]);
		/* 判断指令处理方式 */
		if($res[0]['max_param'] > 0){
			call_user_func($res[0]['name'],$db,$qq,$msg);
			exit();
		}else{
			call_user_func($res[0]['name'],$db,$qq);
			exit();
		}
	}
}else{
	/* 处理带参数的指令 */
	$res = $db->select('muti-function','*',[
		"name" => $arr[0]
	]);
	if($res){
		$res = $db->select('function','*',[
			"id" => $res[0]['fid']
		]);
		/* 预处理参数数量问题 */
		if($res[0]['max_param'] < count($arr)-1){
			exit("参数数量超过限制，请检查指令，可输入'/help 指令名' 查看帮助信息:\n");
		}else{
			//调用方法
			call_user_func($res[0]['name'],$db,$qq,$msg);
			exit();
		}
	}
}
/* 返回错误信息 */
exit("未找到指令:\n".$msg);
?>