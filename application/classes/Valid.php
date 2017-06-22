<?php defined('SYSPATH') OR die('No direct script access.');

class Valid extends Kohana_Valid {

    public static function mobile($number)
    {
        return (bool) preg_match('/^1[34578]\d{9}$/', $number);
    }

}

