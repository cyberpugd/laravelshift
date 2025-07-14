<?php
namespace App\p2helpdesk\transformers;

use App\Ticket;
use Auth;
class TicketTransformer {

     public function transform($ticket)
     {
          $thisTicket = Ticket::where('id', $ticket['id'])->firstOrFail();

          return [
               'Ticket #' => $thisTicket->id,
               'Subject' => $thisTicket->title,
               'Description' => $thisTicket->description,
               'Urgency' => $thisTicket->urgency->name,
               'Status' => ucfirst($thisTicket->status),
               'Category' => $thisTicket->category->name,
               'Subcategory' => $thisTicket->subcategory->name,
               'Caller' => $thisTicket->createdBy->first_name . ' ' . $thisTicket->createdBy->last_name,
               'Agent' => ($thisTicket->agent_id == 0 ? 'Not Assigned' : $thisTicket->assignedTo->first_name . ' ' . $thisTicket->assignedTo->last_name),
               'Due On' => $thisTicket->due_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a'),
               'Created At' => $thisTicket->created_at->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')
          ];

     }

     public function transformCollection(array $items)
     {
          return array_map([$this, 'transform'], $items);
     }
}