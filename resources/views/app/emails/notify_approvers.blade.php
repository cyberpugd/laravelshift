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
		<td>Hello,</td>
	</tr>
	<tr>
		<td style="font-size: small; font-weight: bold;">
			Change Ticket #{{$ticket->id}} is waiting for your approval
		</td>
	</tr>

<tr>
	<td>
	<table style="font-size: small;">
	<tr>
		<td style="font-weight: bold;">Start Date:</td>
		<td>{{$ticket->start_date->timezone($ticket->changeOwner->timezone)->toDayDateTimeString() }}</td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Change Owner:</td>
		<td>{!!$ticket->changeOwner->first_name!!} {!!$ticket->changeOwner->last_name!!}</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td style="font-weight: bold;">
					Change Description:
				</td>
			</tr>
			<tr>
				<td>
					{!! linkify(nl2br(htmlentities($ticket->change_description))) !!}
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table>
			<tr>
				<td>Please click the button below to approve or reject.</td>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td style="padding:10px;background-color:#F39C12;">
								<a href="{{$ticketurl}}" style="color:#673800; font-weight: bold; font-size: small; text-decoration: none; ">Approve/Reject</a>
							</td>
						</tr>
					</table>
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






