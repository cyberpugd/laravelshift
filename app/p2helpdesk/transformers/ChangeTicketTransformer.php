<?php
namespace App\p2helpdesk\transformers;

use App\ChangeTicket;
use Auth;
class ChangeTicketTransformer {

     public function transform($ticket)
     {
          $thisTicket = ChangeTicket::where('id', $ticket['id'])->firstOrFail();

          return [
               'Ticket #' => $thisTicket->id,
               'Status' => ucfirst($thisTicket->status),
               'Type' => ucfirst($thisTicket->change_type),
               'Audit Unit' => $thisTicket->auditUnit->name,
               'Created By' => $thisTicket->createdBy->first_name . ' ' . $thisTicket->createdBy->last_name,
               'IT Approver' => ($thisTicket->itApprover != null ? $thisTicket->itApprover->first_name . ' ' . $thisTicket->itApprover->last_name : 'None Assigned'),
               'Bus. Approver' => ($thisTicket->busApprover != null ? $thisTicket->busApprover->first_name . ' ' . $thisTicket->busApprover->last_name : 'None Assigned'),
               'Start Date' => $thisTicket->start_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a'),
               'End Date' => $thisTicket->end_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a'),
               'Close Date' => ($thisTicket->close_date != null ? $thisTicket->close_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a') : ''),
               'Change Description' => $thisTicket->change_description,
               'Reason for Change' => $thisTicket->reason_for_change,
               'Servers' => $thisTicket->servers,
               'Business Impact' => $thisTicket->business_impact,
               'Roll Out Plan' => $thisTicket->roll_out_plan,
               'Back Out Plan' => $thisTicket->back_out_plan,
               'Test Plan' => $thisTicket->test_plan,
               'Affected Groups' => $thisTicket->affected_groups,
          ];

     }

     public function transformCollection(array $items)
     {
          return array_map([$this, 'transform'], $items);
     }
}