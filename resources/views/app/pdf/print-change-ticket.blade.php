
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="C:\inetpub\wwwroot\p2helpdesk\public\{{elixir('css/adminlte.css')}}" rel="stylesheet">
</head>
<body>

<section>
     <!-- title row -->
     <div class="row">
          <div class="col-xs-12">
               <h2 class="page-header">
                    <img src='c:/inetpub/wwwroot/p2helpdesk/public/images/p2logo.png' width="50">
                    <small>P2 Energy Solutions Change Ticket</small>
               </h2>
          </div>
          <!-- /.col -->
     </div>
     <!-- info row -->
     <div class="row invoice-info">
          <div class="col-xs-4 invoice-col">
                   <label>Ticket #:</label> {{$ticket->id}}<br>
                   <label>IT Approver:</label> @if($ticket->itApprover){{$ticket->itApprover->first_name}} {{$ticket->itApprover->last_name}} @else None Assigned @endif<br>
                   <label>Created By: </label> {{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}
          </div>
          <!-- /.col -->
          <div class="col-xs-4 invoice-col">
              <label>Ticket Owner:</label> {{$ticket->changeOwner->first_name}} {{$ticket->changeOwner->last_name}}<br>
              <label>Bus. Approver:</label> @if($ticket->busApprover){{$ticket->busApprover->first_name}} {{$ticket->busApprover->last_name}} @else None Assigned @endif<br>
              <label>Change Type: </label> {{$ticket->change_type}}
          </div>
          <!-- /.col -->
          <div class="col-xs-4 invoice-col">
               <label>Start Date: </label> {{$ticket->start_date->toDayDateTimeString()}}<br>
               <label>End Date: </label> {{$ticket->end_date->toDayDateTimeString()}}<br>
               <label>Status: </label> {{ucfirst($ticket->status)}}
          </div>
     </div>
     <div class="row invoice-info">
          <div class="col-xs-8">
               <label>Approval Status:</label><br>
               @foreach($ticket->changeApprovals as $approval)
               @if($approval->approved == 1)
                    Approved by {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}} on {{$approval->date_approved->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}<br>
               @elseif($approval->approved == 2)
                    Rejected by {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}} on {{$approval->date_approved->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}<br>
               @else
                    Needs approval from  {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}}<br>
               @endif
               @endforeach
          </div>
          <div class="col-xs-4">
              <label>Audit Unit: </label> {{$ticket->auditUnit->name}}
          </div>
     </div>
     <hr>
     <!-- /.row -->
     <div class="row invoice-info">
          <div class="col-xs-12">
               @if($ticket->status == 'completed')
                    <label>{{($ticket->completed_type == 'imp_successfully' ? 'Implemented Successfully' : 'Implemented with Errors')}}</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->completed_notes))) !!}</p>
               @endif

               @if($ticket->status == 'cancelled')
                    <label>Cancelled</label>
                    <p>{!! linkify(nl2br(htmlentities($ticket->cancelled_reason))) !!}</p>
               @endif
               <label>Change Description:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->change_description))) !!}</p>

               <label>Reason for Change:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->change_reason))) !!}</p>

               <label>Servers:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->servers))) !!}</p>

               <label>Business Impact:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->business_impact))) !!}</p>

               <label>Roll Out Plan:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->roll_out_plan))) !!}</p>

               <label>Back Out Plan:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->back_out_plan))) !!}</p>

               <label>Test Plan:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->test_plan))) !!}</p>

          </div>
     </div>
     @if(!$ticket->workOrders->isEmpty())
     <hr>
     @can('view_work_orders')
     <!-- Table row -->
     <div class="row">
          <div class="col-xs-12">
               <h4>Work Orders</h4>
               @foreach($ticket->workOrders as $work_order)
               <div class="col-xs-4 invoice-col" style="border-top: 1px solid #e3e3e7;">
                        <label>ID:</label> {{$work_order->id}}<br>
                        <label>Subject:</label> {{$work_order->subject}}<br>
                        <label>Status:</label> {{$work_order->status}}<br>
                        <label>Assigned To:</label> {{$work_order->assignedTo->first_name}} {{$work_order->assignedTo->last_name}}<br>
                        <label>Due Date:</label> {{$work_order->due_date->toDayDateTimeString()}}
               </div>
               <div class="col-xs-8 invoice-col" style="border-top: 1px solid #e3e3e7;">
                    <label>Work Requested</label>
                    <p>{{$work_order->work_requested}}</p>
                     <label>Work Completed</label>
                    <p>{{$work_order->work_completed}}</p>
               </div>
               @endforeach
          </div>
     </div>
     @endcan
     @endif
     <!-- /.row -->
          @if(!$ticket->attachments->isEmpty())
          <hr>
     <div class="row">
          <div class="col-xs-12">
                    <h4>Attachments</h4>
                    <table class="table table-striped">
                    <thead>
                         <th>File Name</th>
                         <th>Source</th>
                         <th>Date Uploaded</th>
                    </thead>
                    <tbody>
                    @foreach($ticket->attachments as $attachment)
                         <tr>
                              <td>{{$attachment->file_name}}</td>
                              <td>{{$attachment->ticketable_type}}</td>
                              <td>{{$attachment->created_at->toDayDateTimeString()}}</td>
                         </tr>
                    @endforeach
                    @foreach($ticket->workOrders as $work_order)
                        @foreach($work_order->attachments as $attachment)
                            <tr>
                                <td>{{$attachment->file_name}}</td>
                                <td>{{substr($attachment->ticketable_type, 4)}}</td>
                                <td>{{$attachment->created_at->toDayDateTimeString()}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                    </table>
     </div>
     </div>
          @endif

<hr>
<h4>History Log</h4>
@foreach($ticket->revisionHistory as $history)
     @if($history->key == 'created_at' && !$history->old_value)
          <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} created this ticket at <strong style="color: #696969">{{  Carbon\Carbon::createFromFormat('Y-m-d H:i:s.0000000', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
     @else
          @if(strpos($history->key, 'date') )
               @if($history->oldValue() != null)
                    <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s.0000000', $history->oldValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong> to <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s.0000000', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
               @endif
          @else
               <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ $history->oldValue() }}</strong> to <strong style="color: #696969">{{ $history->newValue() }}</strong></li>
          @endif
     @endif
@endforeach
</section>

</body>
</html>
<!-- ****************************************************************************************************** -->
