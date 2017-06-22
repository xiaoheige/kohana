<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base_Html extends Controller_Base_Common {

    public $page_title  = ' - ';
    public $page_view   = null;
    public $page_data   = array();

    public function before()
    {
        $this->response->set_type(Response::CONTENT_TYPE_HTML);

        // login check

        View::bind_global('page_title', $this->page_title);
    }

    public function after()
    {
        // access log

        $this->response->set_view($this->page_view);
        $this->response->out($this->page_data);
    }

}

