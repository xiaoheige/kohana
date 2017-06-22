<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base_Common extends Controller {

    public function get_num($key, $default = null, $error_info = false)
    {
        return $this->get($key, 'numeric', $default);
    }

    public function get_int($key, $default = null, $error_info = false)
    {
        return $this->get($key, 'digit', $default);
    }

    public function post_num($key, $default = null, $error_info = false)
    {
        return $this->post($key, 'numeric', $default);
    }

    public function post_int($key, $default = null, $error_info = false)
    {
        return $this->post($key, 'digit', $default);
    }

    public function get($key, $rule = null, $default = null, $error_info = false)
    {
        return $this->params(Request::GET, $key, $rule, $default, $error_info);
    }

    public function post($key, $rule = null, $default = null, $error_info = false)
    {
        return $this->params(Request::POST, $key, $rule, $default, $error_info);
    }

    // desc: $rule和$default都不为null时，有query参数检查rule，没有query参数则返回默认值
    // 请使用$this->get() 和 $this->post() 获取参数
    // $rule 的类型可以是字符串、一维数组、二维数组，rule内容参照Valid类
    // $error_info 默认false，即遇到错误参数就抛异常；否则会返回参数的错误信息
    /* example for $rule
    $rule = 'not_empty';
    $rule = array('not_empty', 'ip');
    $rule = array(
        array('not_empty'),
        array('in_array', array(':value', array('red','green','blue'))),
    );
    参照文档: https://kohanaframework.org/3.3/guide/kohana/security/validation
    */
    public function params($type, $key, $rule = null, $default = null, $error_info = false)
    {
        $_methods = array(
            Request::GET    => 'query',
            Request::POST   => 'post',
        );
        if (! in_array($type, array_keys($_methods))){
            $type = Request::GET;
        }

        if ($this->request->{$_methods[$type]}($key) === null && $default !== null){
            return $default;
        }

        if (! $rule){
            // 没有定义检查规则，直接返回参数
            return $this->request->{$_methods[$type]}($key);
        }else if (is_string($rule)){
            $_rule = array(array($rule));
            if ($rule != 'not_empty'){
                // 自动添加非空校验
                $_rule[] = array('not_empty');
            }
            $rule = $_rule;
        }else if (is_array($rule)){
            if (is_string($rule[array_rand($rule)])){
                $_rule = array();
                foreach ($rule as $v){
                    $_rule[] = array($v);
                }
                if (! in_array('not_empty', $_rule)){
                    // 自动添加非空校验
                    $_rule[] = array('not_empty');
                }
                $rule = $_rule;
            }
        }
        $validation = Validation::factory(array($key => $this->request->{$_methods[$type]}($key)))
                        ->rules($key, $rule);
        if (! $validation -> check()){
            if ($error_info){
                return $validation->errors();
            }
            throw new Validation_Exception($validation, 'error.api.param', array('#param#' => $key));
        }

        return $this->request->{$_methods[$type]}($key);
    }

}

