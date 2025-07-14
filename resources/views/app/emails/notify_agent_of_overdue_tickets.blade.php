<!DOCTYPE html>
<html>
<head>
<style>
#tickets {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#tickets td, #tickets th {
    border: 1px solid #ddd;
    padding: 8px;
}

#tickets tr:nth-child(even){background-color: #f2f2f2;}

#tickets tr:hover {background-color: #ddd;}

#tickets th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>
</head>
<body>
<p>
Hello {!!$agent->first_name!!},
</p>
<p>
You have some overdue Help Desk items to take a look at.
</p>
@if($tickets->count() > 0)
<p>
    <h3><a href="{{ url('tickets/open-tickets') }}">Helpdesk Tickets</a></h3>
    <table id="tickets">
  <tr>
    <th>Ticket</th>
    <th>Subject</th>
    <th>Caller</th>
    <th>Location</th>
    <th>Created</th>
    <th>Due</th>
  </tr>
  @foreach($tickets as $ticket)
    <tr>
        <td><a href="{{url('/tickets')}}/{{$ticket->id}}">{{$ticket->id}}</a></td>
        <td>{{$ticket->title}}</td>
        <td>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</td>
        <td>{{$ticket->createdBy->location->city}}</td>
        <td>{{$ticket->created_at->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
        <td>{{$ticket->due_date->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
    </tr>
  @endforeach
</table>
</p>
@endif
@if($change_tickets->count() > 0)
<h3><a href="{{ url('change-control/my-open') }}">Change Tickets</a></h3>
<p>
<table id="tickets">
  <tr>
    <th>Ticket</th>
    <th>Description</th>
    <th>Status</th>
    <th>Start Date</th>
    <th>End Date</th>
  </tr>
  @foreach($change_tickets as $ticket)
    <tr>
        <td><a href="{{url('/change-control')}}/{{$ticket->id}}">{{$ticket->id}}</a></td>
        <td>{{$ticket->change_description}}</td>
        <td>{{$ticket->status}}</td>
        <td>{{$ticket->start_date->setTimezone($ticket->changeOwner->timezone)->toDayDateTimeString()}}</td>
        <td>{{$ticket->end_date->setTimezone($ticket->changeOwner->timezone)->toDayDateTimeString()}}</td>
    </tr>
  @endforeach
</table>
</p>
@endif
@if($work_orders->count() > 0)
<h3>Work Orders</h3>
<p>
<table id="tickets">
  <tr>
    <th>Work Order</th>
    <th>Subject</th>
    <th>For Ticket #</th>
    <th>Type</th>
    <th>Created</th>
    <th>Due Date</th>
  </tr>
  @foreach($work_orders as $work_order)
    <tr>
        <td><a href="{{ url('/tickets/work-order')}}/{{$work_order->id}}">{{$work_order->id}}</a></td>
        <td>{{$work_order->subject}}</td>
        <td><a href="@if($work_order->ticketable_type == 'ChangeTicket') {{url('/change-control')}}/{{$work_order->ticketable_id}} @else {{url('/tickets')}}/{{$work_order->ticketable_id}} @endif">{{$work_order->ticketable_id}}</a></td>
        <td>{{ucwords(str_replace('_', ' ', snake_case($work_order->ticketable_type)))}}</td>
        <td>{{$work_order->created_at->setTimezone($work_order->assignedTo->timezone)->toDayDateTimeString()}}</td>
        <td>{{$work_order->due_date->setTimezone($work_order->assignedTo->timezone)->toDayDateTimeString()}}</td>
    </tr>
  @endforeach
</table>
</p>
@endif
<p>
Thank You, <br>
IT Help Desk
</p>
</body>
</html>

