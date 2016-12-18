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
		echo "�û����ݳ�ʼ���ɹ�!";
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
		"last_sign_date" => date('Y-m-d H:i:s')
	],[
		"qq" => $qq
	]);

	$res = $db->select('user','*',[
		"qq" => $qq
	]);
	$res = $res[0];
	echo 'ǩ���ɹ�!����ǩ��'.$res['count_sign_day'].'�죬��'.$res['count_all_day'].'�죬���λ��'.$b_c.'��ң���'.$res['money'].'���';
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

?>