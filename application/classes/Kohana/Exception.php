<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception {

    public function __construct($message = "", array $variables = NULL, $code = 0, Exception $previous = NULL)
    {
        $infos = $this->_message($message);
        if ($infos[1] !== null){
            $code = $infos[1];
        }
        parent::__construct($infos[0], $variables, $code, $previous);
    }

    public static function _message($message = '')
    {
        $code = null;
        $msg  = $message;

        if(strpos($message, "error.") === 0)
        {
            $_path = explode('.', $message);
            if (isset($_path[2]))
            {
                $infos = Kohana::$config->load("{$_path[0]}/{$_path[1]}")->get($_path[2]);
                if (is_array($infos)){
                    if (isset($infos['code'])){
                        $code = $infos['code'];
                    }
                    if (isset($infos['msg'])){
                        $msg = $infos['msg'];
                    }
                }
            }
        }

        return array($msg, $code);
    }

    public static function response(Exception $e)
    {
        try
        {
            // Get the exception information
            $class   = get_class($e);
            $code    = $e->getCode();
            $message = $e->getMessage();
            $file    = $e->getFile();
            $line    = $e->getLine();
            $trace   = $e->getTrace();

            // Prepare the response object.
            $response = Response::factory();

            // Set the response status
            $response->status(($e instanceof HTTP_Exception) ? $e->getCode() : 500);

            // Set the response headers
            $response->headers('Content-Type', Kohana_Exception::$error_view_content_type.'; charset='.Kohana::$charset);

            $response->set_code($code);
            $response->set_msg($message);
            $response->set_view('errors/error');
            $response->out(array());
        }
        catch (Exception $e)
        {
            /**
             * Things are going badly for us, Lets try to keep things under control by
             * generating a simpler response object.
             */
            $response = Response::factory();
            $response->status(500);
            $response->headers('Content-Type', 'text/plain');
            $response->body(Kohana_Exception::text($e));
        }

        return $response;
    }

}

