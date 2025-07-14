<?php
namespace App\p2helpdesk\classes\Email;

use DB;
use Auth;
use Mail;
use URL;
use App\User;
use Carbon\Carbon;
use App\AdminSettings;
use App\Conversation;
use App\p2helpdesk\classes\Ticket\TicketEloquent;

class EmailProvider
{
    public function sendTicketCreatedConfirmation($ticket)
    {
        $user = User::where('id', $ticket->created_by)->firstOrFail();
        $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'user' => $user
               ];

        Mail::send(['html' => 'app.emails.ticket_created_confirmation'], $data, function ($m) use ($ticket, $user, $email_address) {
            $m->from($email_address);
            $m->to($user->email, $user->first_name . ' ' . $user->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' has been created');
        });
    }

    /**
     * Notify user that the conversation has been updated from the web
     * @param  [type] $ticket [description]
     * @return [type]         [description]
     */
    public function sendConversationUpdatedEmail($ticket)
    {
        $recipient = null;
        $usersToEmail = null;
        $ticketURL = null;

        if ($ticket->agent_id != 0) {
            if (Auth::user()->id == $ticket->created_by) {
                $recipient = $ticket->assignedTo;
                $ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
            } else {
                $recipient = $ticket->createdBy;
                $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticket->id;
            }
        } else {
            if (Auth::user()->id == $ticket->created_by) {
                $ticketEloquent = new TicketEloquent;
                $usersToEmail = $ticketEloquent->getTeamMembersToEmail($ticket, Auth::user()->email);
				$ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
            } else {
                $ticketEloquent = new TicketEloquent;
                $usersToEmail = $ticketEloquent->getTeamMembersToEmail($ticket, Auth::user()->email);
                $recipient = $ticket->createdBy;
				$ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticket->id;
            }
        }
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;


        //If a recipient exists, send the email
        if ($recipient) {
            $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->limit(6)->get()
               ];

            Mail::send(['html' => 'app.emails.conversation_updated'], $data, function ($m) use ($ticket, $email_address, $recipient) {
                $m->from($email_address);
                $m->to($recipient->email, $recipient->first_name . ' ' . $recipient->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' has a new message');
            });
        }
        //If usersToEmail exists, email the team members
        if ($usersToEmail) {
            $data2 = [
                    'ticketurl' => URL::to('/') . '/tickets/' . $ticket->id,
                    'ticket' => $ticket,
                    'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->limit(6)->get()
               ];

            Mail::send(['html' => 'app.emails.conversation_updated'], $data2, function ($m) use ($ticket, $email_address, $usersToEmail) {
                $m->from($email_address);
                $m->bcc($usersToEmail->toArray())->subject('Help Desk Ticket #' . $ticket->id . ' has a new message');
            });
        }
    }

    public function notifyIncorrectCategory($ticket, $oldTicket)
    {
        $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'oldTicket' => $oldTicket,
               ];
        // dd($ticket);
        // Get the recipient of the email
        $recipient = $ticket->createdBy;
        //If a recipient exists, send the email
        if ($recipient) {
            Mail::send(['text' => 'app.emails.incorrect_category'], $data, function ($m) use ($ticket, $email_address, $recipient) {
                $m->from($email_address);
                $m->to($recipient->email, $recipient->first_name . ' ' . $recipient->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' - Category Changed');
            });
        }
    }

    public function notifyChangeOwnerWorkOrderClosed($work_order)
    {
        $ticketURL = URL::to('/') . '/change-control/' . $work_order->ticketable->id;
        $woURL = URL::to('/') . '/change-control/work-order/' . $work_order->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketURL' => $ticketURL,
                    'woURL' => $woURL,
                    'ticket' => $work_order->ticketable,
                    'work_order' => $work_order,
               ];
        Mail::send(['html' => 'app.emails.notify_change_owner_work_order_closed'], $data, function ($m) use ($work_order, $email_address) {
            $m->from($work_order->assignedTo->email, $work_order->assignedTo->first_name . ' ' . $work_order->assignedTo->last_name);
            $m->to($work_order->ticketable->changeOwner->email, $work_order->ticketable->changeOwner->first_name)->subject('Change Work Order #'.$work_order->id.' has been closed');
        });
    }

    public function notifyTicketOwnerOfPrivateMessage($ticket, $sender, $message)
    {
        $ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
        $settings = AdminSettings::first();

        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'agent' =>  $ticket->assignedTo,
                    'sender' => $sender->first_name . ' ' . $sender->last_name,
                    'theMessage' => $message
               ];

        Mail::send(['html' => 'app.emails.notify_private_message'], $data, function ($m) use ($ticket, $settings, $sender) {
            $m->from($sender->email);
            $m->to($ticket->assignedTo->email, $ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name)
                    ->subject('Help Desk Ticket #' . $ticket->id . ' has a new private conversation message');
        });
    }

    public function notifyAgentAssgned($ticket)
    {
        $ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'agent' =>  $ticket->assignedTo
               ];
        // dd($ticket);
        // Get the recipient of the email
        $recipient = $ticket->assignedTo;
        //If a recipient exists, send the email
        if ($recipient) {
            Mail::send(['html' => 'app.emails.notify_agent_assigned'], $data, function ($m) use ($ticket, $email_address, $recipient) {
                $m->from($email_address);
                $m->to($recipient->email, $recipient->first_name . ' ' . $recipient->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' has been assigned to you');
            });
        }
    }

    public function sendCloseTicketNotification($ticket)
    {
        $ticketURL = URL::to('/') . '/helpdesk/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'settings' => $settings,
               ];
        // dd($ticket);
        // Get the recipient of the email
        $recipient = $ticket->createdBy;
        //If a recipient exists, send the email
        if ($recipient) {
            Mail::send(['html' => 'app.emails.ticket_closed'], $data, function ($m) use ($ticket, $email_address, $recipient) {
                $m->from($email_address);
                $m->to($recipient->email, $recipient->first_name . ' ' . $recipient->last_name)->subject('Help Desk Ticket #' . $ticket->id . ' has been closed');
            });
        }
    }

    public function notifyWorkOrderAssigned($work_order)
    {
        $workorderURL = URL::to('/') . '/tickets/work-order/' . $work_order->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'workorderURL' => $workorderURL,
                    'work_order' => $work_order,
                    'agent' =>  $work_order->assignedTo,
               ];

        $subject = 'Work Order #' . $work_order->id. ' for ' . trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $work_order->ticketable_type)) . ' #' . $work_order->ticketable->id . ' has been assigned to you.';

        // dd($ticket);
        // Get the recipient of the email
        $recipient = $work_order->assignedTo;
        //If a recipient exists, send the email
        if ($recipient) {
            Mail::send(['html' => 'app.emails.notify_agent_work_order_assigned'], $data, function ($m) use ($work_order, $subject, $email_address, $recipient) {
                $m->from($email_address);
                $m->to($recipient->email, $recipient->first_name . ' ' . $recipient->last_name)->subject($subject);
            });
        }
    }

    public function notifyAgentsAssgned($ticket, $usersToEmail)
    {
        $ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
               ];
        Mail::send(['html' => 'app.emails.notify_team_assigned'], $data, function ($m) use ($ticket, $email_address, $usersToEmail) {
            $m->from($email_address);
            $m->bcc($usersToEmail)->subject('A new ticket has been created for ' . $ticket->category->name . '/' . $ticket->subcategory->name);
        });
    }

    public function notifyAgentWorkOrderClosed($work_order)
    {
        $ticketURL = URL::to('/') . '/tickets/' . $work_order->ticketable->id;
        $woURL = URL::to('/') . '/tickets/work-order/' . $work_order->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketURL' => $ticketURL,
                    'woURL' => $woURL,
                    'ticket' => $work_order->ticketable,
                    'work_order' => $work_order,
               ];
        Mail::send(['html' => 'app.emails.notify_agent_work_order_closed'], $data, function ($m) use ($work_order, $email_address) {
            $m->from($work_order->assignedTo->email, $work_order->assignedTo->first_name . ' ' . $work_order->assignedTo->last_name);
            $m->to($work_order->ticketable->agent->email, $work_order->ticketable->agent->first_name)->subject('Help Desk Work Order #'.$work_order->id.' has been closed');
        });
    }

    public function notifyAdminNoTeam($ticket)
    {
        $usersToEmail = User::whereHas('roles', function ($query) {
            $query->where('label', 'Help Desk Administrator');
        })->lists('email')->toArray();

        $ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
        $settings = AdminSettings::first();
        $email_address = $settings->email_address;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'subcategory' =>  $ticket->subcategory->name
               ];
        Mail::send(['text' => 'app.emails.notify_admin_no_team_assigned_to_category'], $data, function ($m) use ($ticket, $email_address, $usersToEmail) {
            $m->from($email_address);
            $m->to($usersToEmail)->subject('ACTION NEEDED: Ticket #' . $ticket->id . ' needs to be assigned');
        });
    }

    public function notifyChangeOwnerApproval($ticket, $approver)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'approver' => $approver,
               ];
        Mail::send(['html' => 'app.emails.notify_change_owner_approval'], $data, function ($m) use ($ticket, $settings) {
            $m->from($settings->email_address);
            $m->cc($ticket->createdBy->email);
            $m->to($ticket->changeOwner->email)->subject('Change Ticket #' . $ticket->id . ' re: ' . substr($ticket->change_description, 0, 20) .'... has been approved');
        });
    }

    public function notifyChangeApproverAmended($ticket, $approver)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $approver = User::where('id', $approver->id)->firstOrFail();
        // dd($approvers->lists('email'));
        $data = [
               'ticketurl' => $ticketURL,
               'ticket' => $ticket,
          ];
        Mail::send(['html' => 'app.emails.notify_approvers_amended'], $data, function ($m) use ($ticket, $settings, $approver) {
            $m->from($settings->email_address);
            $m->to($approver->email)->subject('Change Ticket #' . $ticket->id . ' has been amended');
        });
    }

    public function notifyChangeOwnerRejected($ticket, $approver)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
                    'approver' => $approver,
               ];
        Mail::send(['html' => 'app.emails.notify_change_owner_rejected'], $data, function ($m) use ($ticket, $settings) {
            $m->from($settings->email_address);
            $m->to($ticket->changeOwner->email)->subject('Change Ticket #' . $ticket->id . ' has been rejected');
        });
    }

    public function notifyChangeOwnerAssigned($ticket)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $data = [
                    'ticketurl' => $ticketURL,
                    'ticket' => $ticket,
               ];
        Mail::send(['html' => 'app.emails.notify_change_owner_assigned'], $data, function ($m) use ($ticket, $settings) {
            $m->from($settings->email_address);
            $m->to($ticket->changeOwner->email)->subject('Change Ticket #' . $ticket->id . ' has been created');
        });
    }

    public function notifyApprovers($ticket)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $approvers = User::whereIn('id', $ticket->changeApprovals->lists('approver')->toArray())->get();
        // dd($approvers->lists('email'));
        $data = [
               'ticketurl' => $ticketURL,
               'ticket' => $ticket,
          ];
        Mail::send(['html' => 'app.emails.notify_approvers'], $data, function ($m) use ($ticket, $settings, $approvers) {
            $m->from($settings->email_address);
            $m->to($approvers->lists('email')->toArray())->subject('Please approve Change Ticket #' . $ticket->id);
        });
    }

    public function notifySingleApprover($approver_id, $ticket)
    {
        $settings = AdminSettings::first();
        $ticketURL = URL::to('/') . '/change-control/' . $ticket->id;
        $approver = User::where('id', $approver_id)->firstOrFail();
        // dd($approvers->lists('email'));
        $data = [
               'ticketurl' => $ticketURL,
               'ticket' => $ticket,
          ];
        Mail::send(['html' => 'app.emails.notify_approvers'], $data, function ($m) use ($ticket, $settings, $approver) {
            $m->from($settings->email_address);
            $m->to($approver->email)->subject('Please approve Change Ticket #' . $ticket->id);
        });
    }

    public function notifyTeamOfUnassignedTickets($emails, $tickets, $team)
    {
        $settings = AdminSettings::first();
        $data = [
            'tickets' => $tickets,
            'team' => $team
        ];
        Mail::send(['html' => 'app.emails.notifyTeamOfUnassignedTickets'], $data, function ($m) use ($emails, $settings) {
            $m->from($settings->email_address);
            $m->to($emails);
            $m->subject('Your team has unassigned tickets in its queue.');
        });
    }

    public function notifyAgentOfOverdueTickets($agent, $overdue_tickets, $overdue_work_orders, $overdue_change_tickets)
    {
        $settings = AdminSettings::first();
        $data = [
            'tickets' => $overdue_tickets,
            'work_orders' => $overdue_work_orders,
            'change_tickets' => $overdue_change_tickets,
            'agent' => $agent
        ];
        // Get the current day in the timezone of the agent.
        $day = Carbon::now()->setTimezone($agent->timezone)->format('l');

        // Get holidays for user
        $isHoliday = 'false';
        $agent->location->holidays->each(function ($holiday) use (&$isHoliday, $agent) {
            if (Carbon::now()->setTimezone($agent->timezone)->toDateString() == $holiday->date->toDateString()) {
                $isHoliday = 'true';
            }
        });
        // Send notification, but not on weekends or holidays
        if ($day != 'Sunday' && $day != 'Saturday' && $isHoliday === 'false') {
            Mail::send(['html' => 'app.emails.notify_agent_of_overdue_tickets'], $data, function ($m) use ($agent, $settings) {
                $m->from($settings->email_address);
                $m->to($agent->email);
                $m->subject('You have overdue Help Desk items.');
            });
        }
    }
}
