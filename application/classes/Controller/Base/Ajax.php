<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base_Ajax extends Controller_Base_Html {

    public $page_data   = array();

    public function before()
    {
        $this->response->set_type(Response::CONTENT_TYPE_JSON);
    }

    public function after()
    {
        // access log

        $this->response->out($this->page_data);
    }


}

