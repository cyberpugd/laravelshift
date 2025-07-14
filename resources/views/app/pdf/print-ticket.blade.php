
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
                    <small>P2 Energy Solutions Help Desk Ticket</small>
               </h2>
          </div>
          <!-- /.col -->
     </div>
     <!-- info row -->
     <div class="row invoice-info">
          <div class="col-xs-6 invoice-col">
                   <label>Ticket #:</label> {{$ticket->id}}<br>
                   <label>Category:</label> {{$ticket->category->name}}<br>
                   <label>Subcategory:</label> {{$ticket->subcategory->name}}<br>
                   <label>Urgency:</label> {{$ticket->urgency->name}}
          </div>
          <!-- /.col -->
          <div class="col-xs-6 invoice-col">
              <label>Caller:</label> {{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}<br>
              <label>Assigned To:</label>@if($ticket->assignedTo) {{$ticket->assignedTo->first_name}} {{$ticket->assignedTo->last_name}} @else Not Assigned @endif<br>
              <label>Created On:</label> {{$ticket->created_at->toDayDateTimeString()}}<br>
              <label>Due On:</label> {{$ticket->due_date->toDayDateTimeString()}}<br>
              @if($ticket->status == 'closed')
               <label>Closed On:</label> {{$ticket->close_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
               @endif
          </div>
          <!-- /.col -->
     </div>
     <hr>
     <!-- /.row -->
     <div class="row invoice-info">
          <div class="col-xs-12">
               <label>Subject:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->title))) !!}</p>

               <label>Description:</label>
               <p>{!! linkify(nl2br(htmlentities($ticket->description))) !!}</p>

               @if($ticket->status == 'closed')
                    <label>Resolution:</label>
                    <p>{!! nl2br(htmlentities($ticket->resolution)) !!}</p>
               @endif
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
                    </tbody>
                    </table>
     </div>
     </div>
          @endif


@if(!$ticket->conversations->isEmpty())
     <hr>
          <h4>Public Conversation</h4>
     @foreach($ticket->conversations as $conversation)
          <label>Posted by {{ $conversation->created_by }} - {{ $conversation->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</label>
          <p>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</p>
     @endforeach
@endif

@if(!$ticket->conversationsPrivate->isEmpty())
     <hr>
          <h4>Private Conversation</h4>
     @foreach($ticket->conversationsPrivate as $conversation)
          <label>Posted by {{ $conversation->created_by }} - {{ $conversation->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</label>
          <p>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</p>
     @endforeach
@endif

<hr>
<h4>History Log</h4>
@foreach($histories as $history)
     @if($history->key == 'created_at' && !$history->old_value)
          <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} created this ticket at <strong style="color: #696969">{{  Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
     @else
          @if(strpos($history->key, 'date') )
               @if($history->oldValue() != null)
                    <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->oldValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong> to <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
               @endif
          @else
               <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ $history->oldValue() }}</strong> to <strong style="color: #696969">{{ $history->newValue() }}</strong></li>
          @endif
     @endif
@endforeach
</section>

</body>
</html>
