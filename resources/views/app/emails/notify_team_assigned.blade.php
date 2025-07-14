<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>

	<style>

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
<table id="mainTable" cellpadding="8">
	<tr>
		<td style="font-weight: bold;">Ticket #{{$ticket->id}} has been assigned to {{$ticket->category->name}}/{{$ticket->subcategory->name}}.</td>
	</tr>
	<tr>
		<td style="font-size: small;">
			A team you are a member of has responsibility for this category.<br>
			Please click the button below and assign to the correct support agent.
	</td>
	</tr>

<tr>
	<td>
	<table style="font-size: small;">
	<tr>
		<td style="font-weight: bold;">Ticket #:</td>
		<td>{{ $ticket->id }}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Caller:</td>
		<td>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}} - {{$ticket->createdBy->location->city}} <a href="https://teams.microsoft.com/l/chat/0/0?users={{$ticket->createdBy->sip}}" style="text-decoration: none; color: #337ab7;">(Chat in Teams)</a></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Urgency:</td>
		<td>{{$ticket->urgency->name}}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Category:</td>
		<td>{{$ticket->category->name}}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Subcategory:</td>
		<td>{{$ticket->subcategory->name}}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Due:</td>
		<td>{{$ticket->due_date->setTimezone($ticket->createdBy->timezone)->toDayDateTimeString()}}</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td><span style="font-weight: bold;">Subject:</span> {{ $ticket->title }}</td>
			</tr>
			<tr>
				<td style="font-weight: bold; ">
					Description:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($ticket->description))) !!}
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td style="padding:10px;background-color:#F39C12;">
					<a href="{{$ticketurl}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">Click here to view</a>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr style="font-size: small; color: #737373;">
	<td>*Replying directly to helpdesk emails is no longer available, please <strong>connect to the VPN</strong> and click the button above to view.</td>
</tr>
</table>
</center>
		</td>
	</tr>
</table>
</body>
</html>



