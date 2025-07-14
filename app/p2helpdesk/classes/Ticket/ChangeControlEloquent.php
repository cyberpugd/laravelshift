<?php 
namespace App\p2helpdesk\classes\Ticket;

use DB;
use Auth;
use App\ChangeTicket;
use Carbon\Carbon;

class ChangeControlEloquent {

     public function myOpenChangeTickets($search = null, $orderBy = 'end_date')
     {
          $change_tickets = ChangeTicket::where('status', '!=', 'completed')->where('status', '!=', 'cancelled')->where('change_owner_id', Auth::user()->id)->orderBy($orderBy)->search($search, null, true, 1);
          // dd($change_tickets->get());
           return $change_tickets;
     }

     public function allChangeTickets($search = null, $orderBy = 'end_date')
     {
          $change_tickets = ChangeTicket::orderBy($orderBy, 'desc')->search($search, null, true, 1);
          // dd($change_tickets->get());
           return $change_tickets;
     }

     public function closeTicket($ticket, $request)
     {
          $ticket->status = 'completed';
          $ticket->completed_type = $request->completed_type;
          $ticket->completed_notes = $request->completed_notes;
          $ticket->close_date = Carbon::now();
          $ticket->save();
     }
}