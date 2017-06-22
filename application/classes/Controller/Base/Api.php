<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base_Api extends Controller_Base_Common {

    public $page_data   = array();

    public function before()
    {
        $this->response->set_type(Response::CONTENT_TYPE_JSON);

        // 授权验证
        if ($this->request->headers('Authorization') != "akftb3qvo1a3t218v3cg"){
            throw new Request_Exception('error.api.auth');
        }
    }

    public function after()
    {
        // access log

        $this->response->out($this->page_data);
    }

}

