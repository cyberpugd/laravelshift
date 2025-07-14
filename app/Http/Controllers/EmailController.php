<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use PhpImap\Mailbox;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;
use App\Ticket;
use App\AdminSettings;
use URL;
use Mail;

class EmailController extends HelpdeskController
{
     public function getEmail() {
          $settings = AdminSettings::first();
          $mail_server = $settings->mail_server;
          $mail_port = $settings->mail_port;
          $mail_username = $settings->mail_user;
          $mail_password = $settings->mail_password;
          $mail_folder = $settings->mail_folder;
          $mail_processed_folder = $settings->mail_processed_folder;
          $email_address = $settings->email_address;

          $mailbox = new Mailbox("{".$mail_server.":".$mail_port."/novalidate-cert}".$mail_folder, $mail_username, $mail_password, 'C:\Users\kar0101\Documents\websites\p2helpdesk\public\attachments');

// Read unread messages into an array:
          $mailsIds = $mailbox->searchMailbox('ALL');
//If there are no messages
          if(!$mailsIds) {
               $this->info('Mailbox Empty');
          }
// Retrieve unread email
          foreach($mailsIds as $message) {
               $mail = $mailbox->getMail($message);
               if(strpos($mail->subject, 'Incident #')){
                    $body = $mail->textPlain;
                    $message = "";
                    $fromName = $mail->fromName;
                    $toName = $mail->toString;
                    $fromEmail = $mail->fromAddress;
                    $toEmail = $mail->toString;
                    $body_array = explode("\n",$body);
                    foreach($body_array as $key => $value){
//remove hotmail sig
                         if($value == "_________________________________________________________________"){
                              break;
//original message quote
                         } elseif(preg_match("/^-*(.*)Original Message(.*)-*/i",$value,$matches)){
                              break;
//check for date wrote string
                         } elseif(preg_match("/^On(.*)wrote:(.*)/i",$value,$matches)) {
                              break;
//check for From Name email section
                         } elseif(preg_match("/^On(.*)$fromName(.*)/i",$value,$matches)) {
                              break;
//check for To Name email section
                         } elseif(preg_match("/^On(.*)$toName(.*)/i",$value,$matches)) {
                              dd($value);
                              break;
//check for To Email email section
                         } elseif(preg_match("/^(.*)$toEmail(.*)wrote:(.*)/i",$value,$matches)) {
                              break;
//check for From Email email section
                         } elseif(preg_match("/^(.*)$fromEmail(.*)wrote:(.*)/i",$value,$matches)) {
                              break;
//check for quoted ">" section
                         } elseif(preg_match("/^>(.*)/i",$value,$matches)){
                              break;
//check for date wrote string with dashes
                         } elseif(preg_match("/^---(.*)On(.*)wrote:(.*)/i",$value,$matches)){
                              break;                              
//add line to body
                         } elseif(preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', $value, $matches)) {
                              break;
                         }
                         else {
                              $message .= "$value\n";
                         }
                    }
                    $message = substr($message, 0, strpos($message, "--"));
                    dd($message);


//Extract this to it's own class and method
                    
               }
          }
     }
}
