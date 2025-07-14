<?php
namespace App\p2helpdesk\transformers;

use App\WorkOrder;

class WorkOrderTransformer {

     public function transform($work_order)
     {
          $thisWorkOrder = WorkOrder::where('id', $work_order['id'])->firstOrFail();

          return [
               'Work Order #' => $thisWorkOrder->id,
               'Ticket Type' => $thisWorkOrder->ticketable_type,
               'Status' => ucfirst($thisWorkOrder->status),
               'Subject' => $thisWorkOrder->subject,
               'Work Requested' => $thisWorkOrder->work_requested,
               'Work Completed' => $thisWorkOrder->work_completed,
               'Due Date' => $thisWorkOrder->due_date->toDateString(),
               'Date Completed' => ($thisWorkOrder->date_completed != null ? $thisWorkOrder->date_completed->toDateString() : ''),
          ];

     }

     public function transformCollection(array $items)
     {
          return array_map([$this, 'transform'], $items);
     }
}