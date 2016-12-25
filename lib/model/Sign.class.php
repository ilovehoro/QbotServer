<?php

/**
* 
*/
class Sign extends Base
{
	private $coin = 0; //签到获得金币
	private $lucky_coin = 0; //幸运金币
    private $count_sign_day; //连续签到天数
	
	function handle()
	{
	    $this->json_arr['status'] = 2;
	    $this->exists_user();
		$user = $this->db->select('user','*',[
			"qq" => $this->qq
		])[0];

        $this->check_sign_day($user['last_sign_date'],$user['count_sign_day']);
        $this->get_earned_coin($user['count_sign_day'],$user['lucky']);
        $this->update_user_sign_info();
        $res = '连续签到'.$this->count_sign_day.'天，共'.++$user['count_all_day'].'天。本次获得'.$this->coin.'金币，5点经验~';
        if($this->lucky_coin){
            $res .= "\n你得到了阿库娅女神的祝福，额外获得了".$this->lucky_coin."金币";
        }
        $this->set_sign_info('签到成功~',$res);
        $this->to_json();
	}

	//判断连续签到，返回0或连续签到天数
	function check_sign_day($last_sign_date,$count_sign_day)
	{
		if(date('Y-m-d',strtotime($last_sign_date)) > date('Y-m-d',strtotime('-1 day'))) {
			$this->set_sign_info('重复签到~','你今天已经签过到了，明天再来吧~');
			$this->to_json();
		}elseif($last_sign_date == 0 || date('Y-m-d',strtotime($last_sign_date)) < date('Y-m-d',strtotime('-1 day'))) {
			$this->count_sign_day = 1;
		}else{
            $this->count_sign_day = $count_sign_day + 1;
		}
	}

	//计算应得金币数
	function get_earned_coin($count_sign_day,$lucky)
	{
		if($count_sign_day < 7  ){
			$this->coin = rand(1,10);
		}elseif ($count_sign_day >= 7 && $count_sign_day < 30  ) {
            $this->coin = rand(10,30);
		}elseif ($count_sign_day >= 30 && $count_sign_day < 90  ) {
            $this->coin = rand(30,50);
		}elseif ($count_sign_day >= 90 && $count_sign_day < 180  ) {
            $this->coin = rand(50,80);
		}elseif ($count_sign_day >= 180 && $count_sign_day < 360  ) {
            $this->coin = rand(80,100);
		}else{
            $this->coin = 100;
		}
		$r = rand(100,1000);
		if($r <= $lucky){
		    $this->lucky_coin = 100;
        }
	}

	//设置卡片信息
	function set_sign_info($title,$content,$link='http://ss.moehoro.com/')
    {
        $this->cq_card = [
            "code" => 1,
            "title" => $title,
            "content" => $content,
            "img" => 'http://qqbot.getfree.cn/static/horo/'.rand(1,18).'.jpg',
            "link" => $link
        ];
        array_push($this->json_arr['cq'],$this->cq_card);
    }

    //更新签到信息
    function update_user_sign_info()
    {
        $res = $this->db->update('user',[
            "money[+]" => $this->coin + $this->lucky_coin,
			"count_sign_day" => $this->count_sign_day,
			"count_all_day[+]" => 1,
			"exp[+]" => 5,
			"last_sign_date" => date('Y-m-d H:i:s')
        ],[
            "qq" => $this->qq
        ]);
        if(!$res){
            $this->set_sign_info('签到失败','更新用户数据异常');
            $this->to_json();
        }
    }
}