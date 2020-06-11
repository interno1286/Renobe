<?php

class tools_email {
    
    static function send($email, $subject, $message, $from = '', $reply = '') {
        
        $config = Zend_Registry::get('cnf');
        
        Zend_Mail::setDefaultTransport( new Zend_Mail_Transport_Smtp( $config->smtp_ip ) );
            
        $mail = new Zend_Mail('UTF-8');
        
        $mail->setFrom(($from) ? $from : $config->manager_email);
        
        if ($reply)
            $mail->setReplyTo($reply);
        
        $mail->setSubject($subject);
        $mail->setBodyHtml($message);

        $mail->addTo($email);

        $mail->send();
    }
    
}
