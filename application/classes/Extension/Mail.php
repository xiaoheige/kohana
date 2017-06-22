<?php defined('SYSPATH') or die('No direct script access.');
/**
 * mail
 *
 * @author  xiaohei
 * @update  2017-06-22
 */
class Extension_Mail
{
    public static function instance() {
        require_once dirname(__FILE__) . '/Third/PHPMailer/PHPMailerAutoload.php';
        $instance = new PHPMailer;
        $instance->isSendmail();
        $instance->CharSet = 'utf-8';
        return $instance;
    }

    public static function send($to, $subject, $content, $from = '', $html = false) {
        $instance = Extension_Phpmailer_Mail::instance();

        if (! $from){
            $from = 'xu@xiaohei.me';
        }
        if (! is_array($to)){
            $to = array($to);
        }

        $instance->setFrom($from);
        foreach ($to as $_to){
            $instance->addAddress($_to);
        }
        $instance->Subject = $subject;
        if ($html){
            $instance->msgHTML($content);
            // or
            //$instance->Body = $content;
            //$instance->AltBody = 'The mail client do not have HTML email capability!';
            //$instance->isHTML();
        }else{
            $instance->Body = $content;
        }

        if (! $instance->send()){
            Kohana::$log->add(Log::ALERT, $instance->ErrorInfo);
            return false;
        }
        return true;
    }

}

