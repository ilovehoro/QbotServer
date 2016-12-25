<?php

class Base
{
	
	public $db;
	public $json_arr;
	public $argument_arr;
	public $table;

	public $qq;
	public $msg;
	public $respose_msg;

	public $cq_card;
	public $cq_img;
	public $cq_send_group_msg;
	public $cq_send_private_msg;
	public $cq_set_title;

	public $need_validate;
	public $max_param;

	//初始化参数
	function __construct($db,$qq,$msg)
	{
		$this->db = $db;
		$this->qq = $qq;
		$this->msg = $msg;
		$this->argument_arr = explode(" ",$msg);
		$this->json_arr = [
			"status" => 1, //h不处理，0异常，1单消息，2纯q操作，3混合
			"msg" => "",//返回消息主体
			"at_qq" => $qq,//@qq
			"cq" => [
			//cq操作1
			//cq操作2
			//... cq操作...
			]
		];
		$this->handle();
	}

	//初始化用户数据到数据库
	function exists_user()
	{
		$res = $this->db->has('user',[
			"qq" => $this->qq
		]);
		if(!$res){
			$this->db->insert('user',[
				"qq" => $this->qq,
				"reg_date" =>  date('Y-m-d H:i:s')
			]);
			$this->respose_msg = "数据初始化成功!\n";
			$this->json_arr['status'] = 3;
		}
	}

	//处理类
	function handle()
	{
		return 0;
	}

	//json转换
	function to_json()
	{
	    $this->json_arr['msg'] = $this->respose_msg;
		echo json_encode($this->json_arr,JSON_UNESCAPED_UNICODE);
		exit();
	}
}