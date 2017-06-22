<?php defined('SYSPATH') OR die('No direct script access.');

class Log extends Kohana_Log
{
    //public function __construct(){
    //    parent::$write_on_add = true;
    //}

    public function add($level, $message, array $values = NULL, array $additional = NULL)
    {
        if ($values)
        {
            // Insert the values into the message
            $message = strtr($message, $values);
        }

        // Grab a copy of the trace
        if (isset($additional['exception']))
        {
            $trace = $additional['exception']->getTrace();
        }
        else
        {
            // Older php version don't have 'DEBUG_BACKTRACE_IGNORE_ARGS', so manually remove the args from the backtrace
            if ( ! defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
            {
                $trace = array_map(function ($item) {
                        unset($item['args']);
                        return $item;
                        }, array_slice(debug_backtrace(FALSE), 1));
            }
            else
            {
                $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1);
            }
        }

        if ($additional == NULL)
        {
            $additional = array();
        }

        // Create a new message
        $this->_messages[] = array
            (
             'time'       => time(),
             'level'      => $level,
             'body'       => $message,
             'trace'      => $trace,
             'file'       => isset($trace[0]['file']) ? $trace[0]['file'] : NULL,
             'line'       => isset($trace[0]['line']) ? $trace[0]['line'] : NULL,
             'class'      => isset($trace[0]['class']) ? $trace[0]['class'] : NULL,
             'function'   => isset($trace[0]['function']) ? $trace[0]['function'] : NULL,
             //'additional' => $additional,
            );

        if (Log::$write_on_add)
        {
            // Write logs as they are added
            $this->write();
        }

        return $this;
    }
}

