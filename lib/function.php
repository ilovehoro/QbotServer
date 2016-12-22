<?php

function add_user($db,$qq) {
	$res = $db->has('user',[
		"qq" => $qq
	]);
	if(!$res){
		$db->insert('user',[
			"qq" => $qq,
			"reg_date" =>  date('Y-m-d H:i:s')
		]);
		echo "用户数据初始化成功!\n";
	}
}

function roll($db,$qq) {
	$db = null;
	$qq = null;
	echo "掷出了".rand(1,100).'点';
}

function sign($db,$qq) {
	$b = 1;
	add_user($db,$qq);
	$res = $db->select('user','*',[
		"qq" => $qq
	]);
	$res = $res[0];
	if(date('Y-m-d',strtotime($res['last_sign_date'])) > date('Y-m-d',strtotime('-1 day'))) {
		echo '你今天已经签过到了，明天再来吧~';
		return 0;
	}elseif($res['last_sign_date'] == 0 || date('Y-m-d',strtotime($res['last_sign_date'])) < date('Y-m-d',strtotime('-1 day'))) {
		$count_sign_day = 1;
	}else{
		$count_sign_day = $res['count_sign_day'] + 1;
	}

	if($count_sign_day >=1 && $count_sign_day <7  ){
		$b_c = rand(1,10);
	}elseif ($count_sign_day >=7 && $count_sign_day <30  ) {
		$b_c = rand(10,30);
	}elseif ($count_sign_day >=30 && $count_sign_day <90  ) {
		$b_c = rand(30,50);
	}elseif ($count_sign_day >=90 && $count_sign_day <180  ) {
		$b_c = rand(50,80);
	}elseif ($count_sign_day >=180 && $count_sign_day <360  ) {
		$b_c = rand(80,100);
	}else{
		$b_c = 100;
	}
	$db->update('user',[
		"money[+]" => $b_c,
		"count_sign_day" => $count_sign_day,
		"count_all_day[+]" => 1,
		"exp[+]" => 5,
		"last_sign_date" => date('Y-m-d H:i:s')
	],[
		"qq" => $qq
	]);

	$res = $db->select('user','*',[
		"qq" => $qq
	]);
	$res = $res[0];
	echo '签到成功!连续签到'.$res['count_sign_day'].'天，共'.$res['count_all_day'].'天，本次获得'.$b_c.'金币，共'.$res['money'].'金币,5点经验~';
}

function menu($db,$qq) {
	$res = $db->select('function','*',[
		"AND" => [
			"need_validate" => 0,
			"is_hidden" => 0,
		],
		"ORDER" => "order ASC"
	]);
	echo "\n-----------------MENU-----------------\n";
	$i = 1;
	foreach ($res as $key) {
		echo $i.'. /'.$key['name'].'  -  '.$key['ch_name'].'  -  '.$key['help_info']."\n";
		$i++;
	}
	//print_r($res);
	echo "-------------------END------------------";
}

function help($db,$qq,$msg) {
	$arr = explode(" ",$msg);
	if(count($arr) == 1 ) {
		exit("请输入'/help 指令名' 查看帮助信息");
	}else{
		$res = $db->select('muti-function','*',[
			"name" => $arr[1]
		]);
		if(!$res) {
			exit('未找到指令: '.$arr[1].',请输入/菜单，查看所有可用指令');
		}else{
			$res = $db->select('function','*',[
				"AND" => [
					'id' => $res[0]['fid'],
					'need_validate' => 0
				]
			]);
			if(!$res){exit('查询的指令需要授权！');}
			$names = $db->select('muti-function','name',[
				"fid" => $res[0]['id']
			]);
			$name_str = '';
			foreach ($names as $key) {
				$name_str .= $key.',';
			}
			echo "\n-----------------HELP-----------------\n";
			echo "指令名：".$res[0]['name']."(".$res[0]['ch_name'].")\n";
			echo "说明：\n";
			echo $res[0]['help_info']."\n";
			echo "用法：\n";
			echo $res[0]['use_method']."\n";
			echo "近义词：\n";
			echo rtrim($name_str,',')."\n";
			echo "-------------------END------------------";
			}
		}
	}

	function me($db,$qq,$msg) {
		$arr = explode(" ",$msg);
		if(count($arr) == 1 ) {
			$res = $db->select('user','*',[
				"qq" => $qq
			]);
			if(!$res){
				add_user($db,$qq);
				me($db,$qq,$msg);
				exit();
			}
			$res = $res[0];
			echo "\n-----------------PROFILE-----------------\n";
			echo "昵称：";
			echo ($res['name'])?$res['name']:"无名氏";
			echo "\nLevel：".$res['level']."(EXP ".$res['exp']." )\n";
			echo "战斗力：".$res['combat_effectiveness']."\n";
			echo "幸运值：".$res['lucky']."\n";
			echo "金币：".$res['money']."\n";
			echo "连续签到：".$res['count_sign_day']."天 总签到：".$res['count_all_day']."天\n";
			exit ("--------------------END--------------------");
		}
		if($arr[1] == 'name'){
			$res = $db->select('user','*',[
				"qq" => $qq
			]);
			if(!$res){
				add_user($db,$qq);
				me($db,$qq,$msg);
			}
			if(strlen($arr[2]) > 16 || strlen($arr[2]) < 2){exit('名字长度只能2-16个字符哦~');}
			$illegal_name = ['horo','小可','管理员','admin','江泽民','习近平','无名氏'];
			if(in_array($arr[2], $illegal_name)){exit('和谐词汇~	已禁止');}
			$if_has = $db->has('user',[
				"name" => $arr[2]
			]);
			if($if_has){exit('名称已存在~');}
			$res = $db->update('user',[
				"name" => $arr[2]
			],[
				"qq" => $qq
			]);
			if($res){
				exit("已将名称设置为：".$arr[2]);
			}else{
				exit("遇到异常错误~");
			}
		}
		exit('未找到参数'.$arr[1]);
	}

	function top($db,$qq,$msg) {
		$arr = explode(" ",$msg);
		if(count($arr) == 1) {top($db,$qq,'top money');}
		$title = '';
		$val='';
		$res='';
		$field = '';
		$limit = 5;
		if($arr[1] == 'money' || $arr[1] == '金币'){
			$res = $db->select('user','*',[
				"ORDER" => "money DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'MONEY';
			$val = '金币';
			$field = 'money';
		}elseif($arr[1] == 'ce' || $arr[1] == '战斗力'){
			$res = $db->select('user','*',[
				"ORDER" => "combat_effectiveness DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'CE';
			$val = '战斗力';
			$field = 'combat_effectiveness';
		}elseif($arr[1] == 'level' || $arr[1] == '等级'){
			$res = $db->select('user','*',[
				"ORDER" => "level DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'LEVEL';
			$val = '等级';
			$field = 'level';
		}elseif($arr[1] == 'sign' || $arr[1] == '签到'){
			$res = $db->select('user','*',[
				"ORDER" => "count_sign_day DESC,count_all_day DESC",
				"LIMIT" => $limit
			]);
			$title = 'SIGN';
			$val = '连续签到';
			$field = 'count_sign_day';
		}else{
			exit('未找到参数：'.$arr[1]);
		}
		echo "\n-----------------TOP - ".$title."-------------\n";
		$i = 1;
		//print_r($res);
		foreach ($res as $key) {
			echo $i.". ";
			echo ($key['name'])?$key['name']:'无名氏';
			echo " - ".$val." ".$key[$field]."\n";
			$i++;
		}
		exit ("--------------------END--------------------");
	}
?>