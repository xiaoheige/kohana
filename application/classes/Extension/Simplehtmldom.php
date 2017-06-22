<?php defined('SYSPATH') or die('No direct script access.');

class Extension_Simplehtmldom
{
    protected static $_instance;

    public static function instance()
    {
        if (Extension_Simplehtmldom_Simplehtmldom::$_instance === null) {
            require_once(dirname(__FILE__) . '/Third/Simplehtmldom/simple_html_dom.php');
            Extension_Simplehtmldom_Simplehtmldom::$_instance = new simple_html_dom();
        }
        return Extension_Simplehtmldom_Simplehtmldom::$_instance;
    }

}

