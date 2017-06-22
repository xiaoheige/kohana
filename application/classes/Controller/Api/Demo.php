<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Demo extends Controller_Base_Api
{
    public function action_index()
    {
        $this->page_data = array('hello' => 'world');
    }
}

