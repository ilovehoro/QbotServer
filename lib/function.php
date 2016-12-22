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
		echo "�û����ݳ�ʼ���ɹ�!\n";
	}
}

function roll($db,$qq) {
	$db = null;
	$qq = null;
	echo "������".rand(1,100).'��';
}

function sign($db,$qq) {
	$b = 1;
	add_user($db,$qq);
	$res = $db->select('user','*',[
		"qq" => $qq
	]);
	$res = $res[0];
	if(date('Y-m-d',strtotime($res['last_sign_date'])) > date('Y-m-d',strtotime('-1 day'))) {
		echo '������Ѿ�ǩ�����ˣ�����������~';
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
	echo 'ǩ���ɹ�!����ǩ��'.$res['count_sign_day'].'�죬��'.$res['count_all_day'].'�죬���λ��'.$b_c.'��ң���'.$res['money'].'���,5�㾭��~';
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
		exit("������'/help ָ����' �鿴������Ϣ");
	}else{
		$res = $db->select('muti-function','*',[
			"name" => $arr[1]
		]);
		if(!$res) {
			exit('δ�ҵ�ָ��: '.$arr[1].',������/�˵����鿴���п���ָ��');
		}else{
			$res = $db->select('function','*',[
				"AND" => [
					'id' => $res[0]['fid'],
					'need_validate' => 0
				]
			]);
			if(!$res){exit('��ѯ��ָ����Ҫ��Ȩ��');}
			$names = $db->select('muti-function','name',[
				"fid" => $res[0]['id']
			]);
			$name_str = '';
			foreach ($names as $key) {
				$name_str .= $key.',';
			}
			echo "\n-----------------HELP-----------------\n";
			echo "ָ������".$res[0]['name']."(".$res[0]['ch_name'].")\n";
			echo "˵����\n";
			echo $res[0]['help_info']."\n";
			echo "�÷���\n";
			echo $res[0]['use_method']."\n";
			echo "����ʣ�\n";
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
			echo "�ǳƣ�";
			echo ($res['name'])?$res['name']:"������";
			echo "\nLevel��".$res['level']."(EXP ".$res['exp']." )\n";
			echo "ս������".$res['combat_effectiveness']."\n";
			echo "����ֵ��".$res['lucky']."\n";
			echo "��ң�".$res['money']."\n";
			echo "����ǩ����".$res['count_sign_day']."�� ��ǩ����".$res['count_all_day']."��\n";
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
			if(strlen($arr[2]) > 16 || strlen($arr[2]) < 2){exit('���ֳ���ֻ��2-16���ַ�Ŷ~');}
			$illegal_name = ['horo','С��','����Ա','admin','������','ϰ��ƽ','������'];
			if(in_array($arr[2], $illegal_name)){exit('��г�ʻ�~	�ѽ�ֹ');}
			$if_has = $db->has('user',[
				"name" => $arr[2]
			]);
			if($if_has){exit('�����Ѵ���~');}
			$res = $db->update('user',[
				"name" => $arr[2]
			],[
				"qq" => $qq
			]);
			if($res){
				exit("�ѽ���������Ϊ��".$arr[2]);
			}else{
				exit("�����쳣����~");
			}
		}
		exit('δ�ҵ�����'.$arr[1]);
	}

	function top($db,$qq,$msg) {
		$arr = explode(" ",$msg);
		if(count($arr) == 1) {top($db,$qq,'top money');}
		$title = '';
		$val='';
		$res='';
		$field = '';
		$limit = 5;
		if($arr[1] == 'money' || $arr[1] == '���'){
			$res = $db->select('user','*',[
				"ORDER" => "money DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'MONEY';
			$val = '���';
			$field = 'money';
		}elseif($arr[1] == 'ce' || $arr[1] == 'ս����'){
			$res = $db->select('user','*',[
				"ORDER" => "combat_effectiveness DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'CE';
			$val = 'ս����';
			$field = 'combat_effectiveness';
		}elseif($arr[1] == 'level' || $arr[1] == '�ȼ�'){
			$res = $db->select('user','*',[
				"ORDER" => "level DESC,id ASC",
				"LIMIT" => $limit
			]);
			$title = 'LEVEL';
			$val = '�ȼ�';
			$field = 'level';
		}elseif($arr[1] == 'sign' || $arr[1] == 'ǩ��'){
			$res = $db->select('user','*',[
				"ORDER" => "count_sign_day DESC,count_all_day DESC",
				"LIMIT" => $limit
			]);
			$title = 'SIGN';
			$val = '����ǩ��';
			$field = 'count_sign_day';
		}else{
			exit('δ�ҵ�������'.$arr[1]);
		}
		echo "\n-----------------TOP - ".$title."-------------\n";
		$i = 1;
		//print_r($res);
		foreach ($res as $key) {
			echo $i.". ";
			echo ($key['name'])?$key['name']:'������';
			echo " - ".$val." ".$key[$field]."\n";
			$i++;
		}
		exit ("--------------------END--------------------");
	}
?>