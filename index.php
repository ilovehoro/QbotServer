<?php
require_once 'lib/db.php';
require_once 'lib/function.php';
/* Ԥ����ҳ�� */
header("Content-type: text/html; charset=gbk");
date_default_timezone_set('PRC');
$key = 'iEV7roTD';
/* Ԥ�������� */
if(!isset($_POST['key'])){exit('δ��Ȩ');}
if(!isset($_POST['msg'])){exit('��ȡ��Ϣʧ��');}
if(!isset($_POST['qq'])){exit('��ȡ�û���Ϣʧ��');}
if($_POST['key'] != $key){exit('��Ȩʧ��');}
$qq = $_POST['qq'];
/* ����message */
$msg = substr($_POST['msg'], 1);
$msg = trim($msg);
$arr = explode(" ",$msg);
/* ��������ָ�� */
$res = $db->select('muti-function','*');
/* ɸѡ��ִ��ָ�� */
if(count($arr) == 1) {
	/* ������������ָ�� */
	$res = $db->select('muti-function','*',[
		"name" => $msg
	]);
	if($res){
		$res = $db->select('function','*',[
			"id" => $res[0]['fid']
		]);
		/* �ж�ָ���ʽ */
		if($res[0]['max_param'] > 0){
			call_user_func($res[0]['name'],$db,$qq,$msg);
			exit();
		}else{
			call_user_func($res[0]['name'],$db,$qq);
			exit();
		}
	}
}else{
	/* �����������ָ�� */
	$res = $db->select('muti-function','*',[
		"name" => $arr[0]
	]);
	if($res){
		$res = $db->select('function','*',[
			"id" => $res[0]['fid']
		]);
		/* Ԥ��������������� */
		if($res[0]['max_param'] < count($arr)-1){
			exit("���������������ƣ�����ָ�������'/help ָ����' �鿴������Ϣ:\n");
		}else{
			//���÷���
			call_user_func($res[0]['name'],$db,$qq,$msg);
			exit();
		}
	}
}
/* ���ش�����Ϣ */
exit("δ�ҵ�ָ��:\n".$msg);
?>