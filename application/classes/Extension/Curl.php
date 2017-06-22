<?php defined('SYSPATH') or die('No direct script access.');

class Extension_Curl
{
    static public function request($url, $post = array(), $headers = array(), $proxy = null, $cookie_file = null, $timeout = 10, $connect_timeout = 3, $retry = 0, $header = false, $debug = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (! empty($post)){
            $post_str = http_build_query($post);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($header){
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        if ($proxy){
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        // headers
        if (! empty($headers)){
            $headers[] = 'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // cookie
        if ($cookie_file){
            if (file_exists($cookie_file)){
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            }else{
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            }
        }

        // debug mode
        if ($debug){
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $content = curl_exec($ch);
        if ($code = curl_errno($ch)){
            for ($_retry = 0; $_retry < $retry; $_retry ++){
                usleep(5000);
                $content = curl_exec($ch);
                $code = curl_errno($ch);
                if (! $code){
                    // todo error log
                    //$error_no = $code;
                    //$error = curl_error($ch);
                    break;
                }
            }
        }

        curl_close($ch);
        return $content;
    }
}

