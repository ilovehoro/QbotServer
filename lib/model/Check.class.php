<?php
/**
* 检查类
*/
class Check
{
	
	private $db;
	private $json_arr;

	//初始化参数
	function __construct($db,$qq,$msg)
	{
		$this->db = $db;
		$argument_arr = explode(" ",$msg);
		$this->json_arr = [
			"status" => 0,
			"at_qq" =>$qq,
			"msg" => "",
		];

		/* 加载所有指令 */
		$res = $db->select('muti-command','*');
		/* 筛选并执行指令 */
		if(count($argument_arr) == 1) {
			/* 处理不带参数的指令 */
			$res = $db->select('muti-command','*',[
				"name" => $msg
			]);
			if(!$res){
				$this->json_arr['msg'] = "未找到命令：".$argument_arr[0];
				$this->to_json();
			}
		}else{
			/* 处理带参数的指令 */
			$res = $db->select('muti-command','*',[
				"name" => $argument_arr[0]
			]);
			if($res){
				$res = $db->select('command','*',[
					"id" => $res[0]['fid']
				]);
				/* 预处理参数数量问题 */
				if($res[0]['max_param'] < count($argument_arr)-1){
					$this->json_arr->msg = "参数数量超过限制，请检查指令，可输入'/help 指令名' 查看帮助信息:\n";
					$this->to_json();
				}
			}else{
				$this->json_arr['msg'] = "未找到命令：".$argument_arr[0];
				$this->to_json();
			}
		}
	}

	//json转换
	function to_json()
	{
		echo json_encode($this->json_arr,JSON_UNESCAPED_UNICODE);
		exit();
	}

	//判断是否新版表情
	function check_is_new_face($msg)
	{
		$new_face_arr = ['我最美','小纠结','骚扰','惊喜','喷血','斜眼笑','卖萌','托腮','无奈','泪奔','doge','笑哭','舔'];

		if(in_array($msg, $new_face_arr) || in_array(mb_substr($msg,0,0,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,1,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,2,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,3,'utf-8'), $new_face_arr))
		{
			$this->json_arr->status = 'h';
			$this->to_json();
		}
	}

	//返回命令对象
	function get_command_class($msg)
	{
		$res = $this->db->select('muti-command','*',[
			"name" => $msg
		]);
		$res = $this->db->select('command','*',[
			"id" => $res[0]['fid']
		]);
		return $res[0]['class'];
	}
}