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
		<td>Hi {!!$ticket->changeOwner->first_name!!},</td>
	</tr>
	<tr>
		<td style="font-size: small; font-weight: bold;">Work Order #{{$work_order->id}} on Change Ticket #{{$ticket->id}} has been closed by {!!$work_order->assignedTo->first_name!!} {!!$work_order->assignedTo->last_name!!}.</td>
	</tr>

<tr>
	<td>
	<table style="font-size: small;">
	<tr>
		<td style="font-weight: bold;">Change Description:</td>
		<td>Change Description:{!!$ticket->change_description!!}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Work Order Subject:</td>
		<td>{!!$work_order->subject!!}</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table>
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
			<tr>
				<td style="font-weight: bold;">
					Work Completed:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($work_order->work_completed))) !!}
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
								<a href="{{$ticketURL}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">View ticket</a>
							</td>
							<td style="padding:10px;background-color:#F39C12;">
								<a href="{{$woURL}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">View work order</a>
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





