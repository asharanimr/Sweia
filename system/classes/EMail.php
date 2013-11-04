<?php

    /*
     * @author Joshua Kissoon
     * @date 20130808
     * @description Mail class that can be used to send emails throught the website. Includes email templates features, etc
     */

    class EMail
    {

       private $recipients = array();
       public $sender, $subject, $message;

       function __construct()
       {
          
       }

       public function addRecipient($recipient)
       {
          $this->recipients[] = $recipient;
       }

       public function setSender($sender)
       {
          $this->sender = $sender;
       }

       public function setMessage($message)
       {
          $this->message = $message;
       }

       public function setSubject($subject)
       {
          $this->subject = $subject;
       }

       public function sendMail()
       {
          $headers = 'MIME-Version: 1.0' . "\r\n";
          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          $headers .= "From: $this->sender" . "\r\n";
          $recipients = implode(", ", $this->recipients);
          mail($recipients, $this->subject, $this->message, $headers);
       }

       public static function quickMail($recipient, $subject, $message, $from = null)
       {
          /*
           * This function is a function used to send a basic email
           * This function can be used when there is nothing important to be added, or no sanitizing needed, etc
           */
          $headers = 'MIME-Version: 1.0' . "\r\n";
          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          $headers .= "From: $from" . "\r\n";
          mail($recipient, $subject, $message, $headers);
       }

    }