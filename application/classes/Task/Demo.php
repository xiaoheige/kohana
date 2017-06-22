<?php defined('SYSPATH') or die('No direct script access.');

class Task_Demo extends Minion_Task
{
    const TYPE = 'default type';

    protected $_options = array(
        'type'  => self::TYPE,
    );

    protected function _execute(array $params)
    {
        exit($params['type']);
    }
}

