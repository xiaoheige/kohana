<?php defined('SYSPATH') OR die('No direct script access.');

class Response extends Kohana_Response {

    const CONTENT_TYPE_JSON   = 'json';
    const CONTENT_TYPE_HTML   = 'html';
    const CONTENT_TYPE_PLAIN  = 'plain';

    public static $_out_type   = null;

    public $_out_code   = 10000;
    public $_out_msg    = '';
    public $_out_view   = null;

    public function set_type($type)
    {
        return self::$_out_type = $type;
    }

    public function set_code($code)
    {
        return $this->_out_code = $code;
    }

    public function set_msg($msg)
    {
        return $this->_out_msg = $msg;
    }

    public function set_view($view)
    {
        return $this->_out_view = $view;
    }

    public function out(array $data)
    {
        if (self::$_out_type == self::CONTENT_TYPE_JSON){
            $this->body_json($data, Request::current()->query('callback'));
        }else if(self::$_out_type == self::CONTENT_TYPE_HTML){
            if (! $this->_out_view){
                $_controller = strtolower(Request::current()->controller());
                $_dir = strtolower(Request::current()->directory());
                $this->_out_view = (empty($_dir) ? '' : ($_dir . '/')) . (empty($_controller) ? '' : ($_controller . '/')) . Request::current()->action();
            }
            $this->body_html($data, $this->_out_view);
        }else if(self::$_out_type == self::CONTENT_TYPE_PLAIN){
            $this->body_plain($data);
        }else{
            $this->body_plain($data);
            //exit('Response content type erroe!');
        }
    }

    public function body_json(array $data, $callback)
    {
        $this->headers('Content-type', 'application/json');
        $json_str = json_encode(array(
            'code'      => $this->_out_code,
            'msg'       => $this->_out_msg,
            'result'    => $data,
        ));
        if (is_string($callback) && $callback){
            $json_str = 'try{' . $callback . '(' . $json_str . ');}catch(e){}';
        }
        $this->body($json_str);
    }

    public function body_html(array $data, $view)
    {
        $this->headers('Content-Type', 'text/html; charset='.Kohana::$charset);
        if (empty($data)){
            $data = array(
                'code'  => $this->_out_code,
                'msg'   => $this->_out_msg,
            );
        }
        $this->body(View::factory($view)->set($data));
    }

    public function body_plain($data)
    {
        $this->headers('Content-Type', 'text/plain');
        $this->body($data ? (string) $data : (date('Y-m-d H:i:s') . ' nothing'));
    }

}

