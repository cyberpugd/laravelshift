@extends('layouts.portal')

@section('content')
<h3>Hi {{$user->first_name}},</h3>
<p>Welcome to the IFS EnR Help Desk!</p>
{{-- <p align="center"><strong>Use the menu to the left to navigate or <a href="/helpdesk/tickets/create-ticket">create a ticket</a>.</strong></p> --}}

<div style="border: 1px solid gray; padding: 10px; border-radius: 5px;">
<h3>My Closed Tickets 
<div class="pull-right">
     <a  class="btn btn-default btn-sm" href="/helpdesk/dashboard">Open Tickets</a>
     <a class="btn btn-success btn-sm" href="/helpdesk/tickets/create-ticket">Create a Ticket</a>
</div>
</h3>
<div class="table-responsive">
 <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Ticket #</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Created At</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($closed_tickets as $ticket)
                                        <tr data-toggle="collapse" data-target=".{{ $ticket->id }}" class="show-hand" v-on:click="showTicket({{$ticket->id}})">
                                             <td>{{$ticket->id}}</td>
                                             <td>{{$ticket->title}}</td>
                                             <td>{{strtoupper($ticket->status)}}</td>
                                             <td>{{$ticket->category->name}}</td>
                                             <td>{{$ticket->subcategory->name}}</td>
                                             <td>{{$ticket->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</td>
                                             
                                        </tr>
                                   @endforeach
                              </tbody>
                         </table>
               </div>
                         {!! $closed_tickets->render() !!}

          </div>
@endsection

@section('footer')
<script>
     new Vue({
          el: 'body',
          methods: {
               showTicket: function(id) {
                    window.location.href = '/helpdesk/tickets/'+id;
               }
          }
     });
</script>
@endsection

