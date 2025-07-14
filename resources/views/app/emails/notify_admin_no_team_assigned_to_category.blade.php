Ticket #{{$ticket->id}} has been assigned to {{$ticket->category->name}}/{{$ticket->subcategory->name}}.

Nobody has been notified of this ticket being created. This can happen for two reasons.

1. There is no team assigned to manage this subcategory.
2. There is a team assigned to the subcategory, but there are no users assigned to the team.

Please fix this issue and assign ticket.

Created By: {!!$ticket->createdBy->first_name!!} {!!$ticket->createdBy->last_name!!}
Location: {{$ticket->createdBy->location->city}}
Category: {{$ticket->category->name}}
Subcategory: {{$ticket->subcategory->name}}
Urgency: {{$ticket->urgency->name}}

Subject: {!! $ticket->title !!}
Description: {!! $ticket->description !!}

{{$ticketurl}}
