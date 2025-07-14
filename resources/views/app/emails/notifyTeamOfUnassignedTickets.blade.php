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
Hello,
</p>
<p>
The Help Desk team {!! $team->name !!} currently has {{$tickets->count()}} tickets that are not assigned to an agent.
Please click the link below or ticket number and assign or take ownership of all unassigned tickets.
</p>
<p>
{{ url('tickets/team-tickets') }}
</p>
<p>
    <table id="tickets">
  <tr>
    <th>Ticket</th>
    <th>Urgency</th>
    <th>Subject</th>
    <th>Caller</th>
    <th>Location</th>
    <th>Created</th>
    <th>Due</th>
  </tr>
  @foreach($tickets as $ticket)
    <tr>
        <td><a href="{{url('/tickets')}}/{{$ticket->id}}">{{$ticket->id}}</a></td>
        <td>{{$ticket->urgency->name}}</td>
        <td>{{$ticket->title}}</td>
        <td>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</td>
        <td>{{$ticket->createdBy->location->city}}</td>
        <td>{{$ticket->created_at->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
        <td>{{$ticket->due_date->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
    </tr>
  @endforeach
</table>
</p>
<p>
Thank You, <br>
IT Help Desk
</p>
</body>
</html>

