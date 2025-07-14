<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap\Mailbox;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;
use App\p2helpdesk\classes\Email\Html2Text;
use App\Ticket;
use App\AdminSettings;
use Mail;
use Config;
use URL;
use App\Conversation;
use App\p2helpdesk\classes\Ticket\TicketEloquent;

class GetEmail extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'p2helpdesk:getmail';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Retrieve email from server and generate conversation messages on a ticket.';

    /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {
        $settings = AdminSettings::first();
        $mail_server = $settings->mail_server;
        $mail_port = $settings->mail_port;
        $mail_username = $settings->mail_user;
        $mail_password = $settings->mail_password;
        $mail_folder = $settings->mail_folder;
        $mail_processed_folder = $settings->mail_processed_folder;
        $email_address = $settings->email_address;

        $mailbox = new Mailbox("{".$mail_server.":".$mail_port."/novalidate-cert}".$mail_folder, $mail_username, $mail_password, Config::get('filesystems.disks.attachments.root'));

        // Read unread messages into an array:
        $mailsIds = $mailbox->searchMailbox('ALL');
        //If there are no messages
        if (!$mailsIds) {
            $this->info('Mailbox Empty');
        }
        // Retrieve unread email
        foreach ($mailsIds as $message) {
            $mail = $mailbox->getMail($message);
            if (!strpos($mail->subject, 'Help Desk Ticket #')) {
                $mailbox->moveMail($mail->id, 'Deleted Items');
            } else {
                if ($mail->textPlain != null) {
                    $body = $mail->textPlain;
                } else {
                    $html = new Html2Text($mail->textHtml);
                    $body = $html->getText();
                }
                $message = "";
                $fromName = $mail->fromName;
                $toName = $mail->toString;
                $fromEmail = $mail->fromAddress;
                $toEmail = $mail->toString;
                $body_array = explode("\n", $body);
                foreach ($body_array as $key => $value) {
                    //remove hotmail sig
                    if ($value == "_________________________________________________________________") {
                        break;
                        //original message quote
                    } elseif (preg_match("/^-*(.*)Original Message(.*)-*/i", $value, $matches)) {
                        break;
                        //check for date wrote string
                    } elseif (preg_match("/^On(.*)wrote:(.*)/i", $value, $matches)) {
                        break;
                        //check for From Name email section
                    } elseif (preg_match("/^On(.*)$fromName(.*)/i", $value, $matches)) {
                        break;
                        //check for To Name email section
                    } elseif (preg_match("/^On(.*)$toName(.*)/i", $value, $matches)) {
                        dd($value);
                        break;
                        //check for To Email email section
                    } elseif (preg_match("/^(.*)$toEmail(.*)wrote:(.*)/i", $value, $matches)) {
                        break;
                        //check for From Email email section
                    } elseif (preg_match("/^(.*)$fromEmail(.*)wrote:(.*)/i", $value, $matches)) {
                        break;
                        //check for quoted ">" section
                    } elseif (preg_match("/^>(.*)/i", $value, $matches)) {
                        break;
                        //check for date wrote string with dashes
                    } elseif (preg_match("/^---(.*)On(.*)wrote:(.*)/i", $value, $matches)) {
                        break;
                        //add line to body
                    } elseif (preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', $value, $matches)) {
                        break;
                    } else {
                        $message .= "$value\n";
                    }
                }
                //compare before and after
                //Here is where we will update the ticket. Add message to coversation thread
                $string = $mail->subject;
                $start = '#';
                $end = ' ';
                // dd($mail->subject);
                $startpos = strpos($string, $start) + strlen($start);
                if (strpos($string, $start) !== false) {
                    $endpos = strpos($string, $end, $startpos);
                    if (strpos($string, $end, $startpos) !== false) {
                        $ticketNumber = substr($string, $startpos, $endpos - $startpos);
                    }
                }

                $ticket = Ticket::where(['id' => $ticketNumber])->first();
                // dd($ticket);
                if ($ticket) {
                    //Right now anyone can put #ticketnumber in a subject line and it will post a comment on the ticket
                    // Ask about only allowing the ticket owner and agent/team to comment on tickets
                    $ticket->conversations()->create([
                              'message' => $message,
                              'source' => 'Email',
                              'created_by' => $fromName
                              ]);

                    //Code below is to get attachments and attach them to the ticket
                    $attachments = $mail->getAttachments();
                    // dd($attachments);
                    foreach ($attachments as $attachment) {
                        //get the file path of the attachment
                        $name = substr($attachment->filePath, strpos($attachment->filePath, "attachments\\")+12);
                        //Get the file that was saved
                        $file = $attachment->filePath;
                        //Get Size of file
                        $size = filesize($file)/1000;

                        //Move the file to an attachments folder in the public directory
                        // $file->move('attachments', $name);
                        //Save the attachment to the database
                        $attachment = $ticket->attachments()->create([
                                   'file_name' => $attachment->name,
                                   'file' => $name,
                                   'file_size' => $size,
                                   ]);
                    }
                }
                // Move mail to processed folder
                $mailbox->moveMail($mail->id, $mail_processed_folder);

                $nextRecipient = null;
                $usersToEmail = null;
                $ticketURL = null;
                if ($ticket->agent_id == 0) {
                    if ($fromEmail == $ticket->createdBy->email) {
                        $ticketEloquent = new TicketEloquent;
                        $usersToEmail = $ticketEloquent->getTeamMembersToEmail($ticket, $fromEmail);
                    } else {
                        $ticketEloquent = new TicketEloquent;
                        $usersToEmail = $ticketEloquent->getTeamMembersToEmail($ticket, $fromEmail);
                        $nextRecipient = $ticket->createdBy;
                        $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticketNumber;
                    }
                } else {
                    if ($fromEmail == $ticket->createdBy->email) {
                        $nextRecipient = $ticket->assignedTo;
                        $ticketURL = URL::to('/') . '/tickets/' . $ticketNumber;
                    } else {
                        $nextRecipient = $ticket->createdBy;
                        $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticketNumber;
                    }
                }
                //Extract this to it's own class and method
                if ($nextRecipient) {
                    $data = [
                              'ticketurl' => $ticketURL,
                              'ticket' => $ticket,
                               'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->get()
                         ];

                    Mail::send(['text' => 'app.emails.conversation_updated'], $data, function ($m) use ($nextRecipient, $ticket, $email_address) {
                        $m->from($email_address);
                        $m->to($nextRecipient->email, $nextRecipient->first_name . ' ' . $nextRecipient->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' has a new conversation message');
                    });
                }
                if ($usersToEmail) {
                    $data2 = [
                              'ticketurl' => URL::to('/') . '/tickets/' . $ticketNumber,
                              'ticket' => $ticket,
                               'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->get()
                         ];

                    Mail::send(['text' => 'app.emails.conversation_updated'], $data2, function ($m) use ($usersToEmail, $ticket, $email_address) {
                        $m->from($email_address);
                        $m->bcc($usersToEmail->toArray())->subject('Help Desk Ticket #' . $ticket->id . ' has a new conversation message');
                    });
                }
            }
        }
    }
}
