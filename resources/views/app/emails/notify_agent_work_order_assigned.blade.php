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
<table id="mainTable" cellpadding="10">
	<tr>
		<td>Hi {!!$agent->first_name!!},</td>
	</tr>
	<tr>
		<td style="font-size: small; font-weight: bold;">Work Order #{{$work_order->id}} for {{trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $work_order->ticketable_type))}} #{{$work_order->ticketable->id}} has been assigned to you.</td>
	</tr>

<tr>
	<td>
	<table style="font-size: small;">
	<tr>
		<td style="font-weight: bold;">Due Date:</td>
		<td>{{$work_order->due_date->setTimezone($agent->timezone)->toDayDateTimeString() }}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Requested By:</td>
		<td>@if($work_order->ticketable_type == 'ChangeTicket') {!!$work_order->ticketable->changeOwner->first_name!!} {!!$work_order->ticketable->changeOwner->last_name!!} @else {!!$work_order->ticketable->assignedTo->first_name!!} {!!$work_order->ticketable->assignedTo->last_name!!}  @endif</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td><span style="font-weight: bold;">Subject:</span> {!! $work_order->subject !!}</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">
					Work Requested:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($work_order->work_requested))) !!}
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td>
					<table>
						<tr>
							<td style="padding:10px;background-color:#F39C12;">
								<a href="{{$workorderURL}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">View work order</a>
							</td>
							<td style="padding:10px;background-color:#F39C12;">
								@if($work_order->ticketable_type == 'ChangeTicket')
									<a href="{{url('/')}}/change-control/{{$work_order->ticketable->id}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">View change ticket</a>
								@else
									<a href="{{url('/')}}/tickets/{{$work_order->ticketable->id}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">View ticket</a>
								@endif
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr style="font-size: small; color: #737373;">
	<td>*Replying directly to helpdesk emails is no longer available, please <strong>connect to the VPN</strong> and click the buttons above to view.</td>
</tr>
</table>
</center>
		</td>
	</tr>
</table>
</body>
</html>





