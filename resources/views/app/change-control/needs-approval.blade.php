@extends('layouts.master')

@section('content')
     <div class="panel panel-default">
               <div class="panel-heading">
                    <h3 class="panel-title">Change tickets awaiting approval from {{Auth::user()->first_name}} {{Auth::user()->last_name}}</h3>
               </div>
               <div class="panel-body">
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Ticket #</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Change Owner</th>
                                        <th>End Date</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($approvals as $approval)
                                   <tr data-toggle="collapse" data-target="{{ $approval->changeTicket->id }}" class="show-hand">
                                        <td><a href="/change-control/{{$approval->changeTicket->id}}" target="_blank">{{$approval->changeTicket->id}}</a></td>
                                        <td v-on:click="showTicket({{$approval->changeTicket->id}})">{{strtoupper($approval->changeTicket->status)}}</td>
                                        <td v-on:click="showTicket({{$approval->changeTicket->id}})">{{$approval->changeTicket->change_type}}</td>
                                        <td v-on:click="showTicket({{$approval->changeTicket->id}})">@if(strlen($approval->changeTicket->change_description) > 80) {{substr($approval->changeTicket->change_description, 0, 80)}} ... @else {{$approval->changeTicket->change_description}} @endif</td>
                                        <td v-on:click="showTicket({{$approval->changeTicket->id}})">{{$approval->changeTicket->changeOwner->first_name}} {{$approval->changeTicket->changeOwner->last_name}}</td>
                                        <td v-on:click="showTicket({{$approval->changeTicket->id}})">{{$approval->changeTicket->end_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</td>
                                   </tr>
                                   @endforeach
                              </tbody>
                         </table>
                    </div>
               </div>
     </div>
@endsection
@section('footer')
<script>
     new Vue({
          el: 'body',
          methods: {
               showTicket: function(id) {
                    window.location.href = '/change-control/'+id;
               }
          }
     });
</script>
@endsection