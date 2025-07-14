<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>

	<style>
		#conversation {
			border: 1px solid #d3d3d3;
			padding-left: 10px;
			padding-right: 10px;
		}

		#mainTable {
			width:auto;
			max-width: 600px;
			min-width: 400px;
			background-color: white;
			border-collapse: collapse;
			padding-right: 25px;
			padding-left: 25px;
		}
		#wrapper {
			width: 100%;
			background-color:  #659BBF;
			padding: 50px;
		}
	</style>

</head>
<body style="font-family: 'Helvetica Neue', Verdana, Geneva, Tahoma, sans-serif;">
<table id="wrapper">
	<tr>
		<td>
<center>
<table id="mainTable" cellpadding="10">
	<tr>
		<td style="font-size: x-large; border-bottom: 1px solid #d3d3d3">Ticket #{{$ticket->id}} has a new message.</td>
	</tr>

<tr>
	<td>
	<table>
	<tr>
		<td><strong>Ticket #:</strong></td>
		<td>{{ $ticket->id }}</td>
	</tr>
	<tr>
		<td><strong>Subject:</strong></td>
		<td>{{ $ticket->title }}</td>
	</tr>
	<tr>
		<td><strong>Caller:</strong></td>
		<td>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}} - {{$ticket->createdBy->location->city}} @if(auth()->user()->id == $ticket->createdBy->id)<a href="https://teams.microsoft.com/l/chat/0/0?users={{$ticket->createdBy->sip}}" style="text-decoration: none; color: #337ab7;">(Chat in Teams)</a>@endif</td>
	</tr>
	<tr>
		<td><strong>Agent:</strong></td>
		<td>@if($ticket->agent){{$ticket->agent->first_name}} {{$ticket->agent->last_name}} @if(auth()->user()->id == $ticket->agent->id)<a href="https://teams.microsoft.com/l/chat/0/0?users={{$ticket->agent->sip}}" style="text-decoration: none; color: #337ab7;">(Chat in Teams)</a>@endif @else Not Assigned @endif</td>
	</tr>
	<tr>
		<td><strong>Urgency:</strong></td>
		<td>{{$ticket->urgency->name}}</td>
	</tr>
	<tr>
		<td><strong>Category:</strong></td>
		<td>{{$ticket->category->name}}</td>
	</tr>
	<tr>
		<td><strong>Subcategory:</strong></td>
		<td>{{$ticket->subcategory->name}}</td>
	</tr>
	<tr>
		<td><strong>Due:</strong></td>
		<td>{{$ticket->due_date->setTimezone(($ticket->agent ? $ticket->agent->timezone : $ticket->createdBy->timezone))->toDayDateTimeString()}}</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table>
@foreach($conversations as $index => $conversation)
	<tr>
		<td>
	@if($index == 0)
		<tr style="font-size: large;">
			<td><strong>New Message</strong></td>
		</tr>
	@endif
	@if($index == 1)
		<tr>
			<td style="padding-top: 10px; border-top: 2px solid #337ab7; font-size: large;"><strong>Last {{ $conversations->count() - 1 }} messages</strong></td>
		</tr>
	@endif
	<tr>
		<td>
			<table id="conversation" width="100%">
				<tr>
					<td style="font-size: small; color: #737373;">Posted by {{$conversation->created_by}} on {{$conversation->created_at->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
				</tr>
				<tr>
					<td>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</td>
				</tr>
			</table>
		</td>
	</tr>
	@if($index == 0)
	<tr>
		<td>
			<table>
				<tr>
					<td style="padding:10px;background-color:#F39C12;">
						<a href="{{$ticketurl . '#conversation'}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">Click here to respond</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr style="font-size: small; color: #737373;">
		<td style="padding-top: 25px;">*Replying directly to helpdesk emails is no longer available, please <strong>connect to the VPN</strong> and click the button above to respond.</td>
	</tr>
	@endif
		</td>
	</tr>
@endforeach
		</table>
	</td>
</tr>
</table>
</center>
		</td>
	</tr>
</table>
</body>
</html>
