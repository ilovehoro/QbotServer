<?php

/**
* 
*/
class Roll extends Base
{
	
	function handle()
	{
		$this->json_arr['status'] = 1;
		$this->json_arr['msg'] = '掷出了'.rand(1,100).'点';
		$this->to_json();
	}
}