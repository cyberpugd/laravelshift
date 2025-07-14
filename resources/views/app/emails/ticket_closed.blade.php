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
		}#wrapper {
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
		<td>Hi {!!$ticket->createdBy->first_name!!},</td>
	</tr>
	<tr>
		<td>Ticket #{{$ticket->id}} has been closed by {{$ticket->assignedTo->first_name}} {{$ticket->assignedTo->last_name}}.</td>
	</tr>

<tr>
	<td>
	<table style="font-size: small;">
	<tr>
		<td style="font-weight: bold;">Category:</td>
		<td>{{$ticket->category->name}}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Subcategory:</td>
		<td>{{$ticket->subcategory->name}}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Urgency:</td>
		<td>{{$ticket->urgency->name}}</td>
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
				<td style="font-weight: bold;">
					Description:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($ticket->description))) !!}
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">
					Resolution:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($ticket->resolution))) !!}
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td>If you are still having an issue, add a message using the button below.</td>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td style="padding:10px;background-color:#F39C12;">
								<a href="{{$ticketurl}}#conversation" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">New message</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			@if($ticket->assignedTo->surveyGroup)
			<tr style="font-size: small; color: #737373;">
				<td>
					Please take a minute to fill out this customer satisfaction survey. Your responses will help us to improve the quality of our services.

				</td>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td style="padding:10px;background-color:#337ab7;">
								<a href="{{url('/survey?ticket_no='. $ticket->id )}}" style="color:white; font-weight: bold; font-size: small; text-decoration: none; ">Take Survey</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			@endif
		</table>
	</td>
</tr>
<tr style="font-size: small; color: #737373;">
	<td>*Replying directly to helpdesk emails is no longer available, please <strong>connect to the VPN</strong> and click "New Message" button above.</td>
</tr>
</table>
</center>
		</td>
	</tr>
</table>
</body>
</html>



