<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Base_Html {

	public function action_index()
	{
        /* 封装内容
        1. 输入、输出
        2. Controller继承封装
        3. 错误码定义、读取
        4. 异常处理
        */

        /*
        $id     = $this->get('id', 'digit');
        $page   = $this->get('page', 'digit', '1');
        $s      = $this->get('s', array(array('not_empty'),array('min_length', array(':value',5))), '-');
        */
        $this->page_title = '首页';

        // 错误码读取方式待优化
        $conf = Kohana::$config->load('error/api')->get('succ');

        //require '../../Extension/PHPMailer/PHPMailerAutoload.php';
        //$mail = new PHPMailer;
        //var_dump($mail);exit;
        //var_dump($this->request->uri(),$this->request->query(),$this->request->param());

        $params = array('data' => 'world');
        $this->page_view = 'index';
        $this->page_data = $params;
	}

}

