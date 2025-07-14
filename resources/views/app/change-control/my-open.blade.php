@extends('layouts.master')

@section('content')
<section class="content-header">
     <h1>My Changes</h1>
</section>
<section class="content">
     <div class="panel panel-default">
               <div class="panel-heading">
                                   <div class="col-md-6">
                    <form method="GET" action="/change-control/my-open">
         
                         <div class="input-group">                               

                              <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                              
                              <span class="input-group-btn">
                                   @if(isset($search))
                                        <a href="/change-control/my-open" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
                                   @endif
                                   <button class="btn btn-default" type="submit">Go!</button>
                                   <a href="{{(Request::getPathInfo() . (Request::getQueryString() ? ('?' . Request::getQueryString()).'&print=true' : '?print=true'))}}" class="btn btn-default" type="submit"><i class="fa fa-print"></i></a>
                              </span>
                         
                              
                         </div>
                    </form>
                    </div>
                    <div class="clearfix"></div>
               </div>
               <div class="panel-body">
               {!! $change_tickets->appends(Input::except('page'))->render() !!}
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
                                   @foreach($change_tickets as $ticket)
                                   <tr data-toggle="collapse" data-target=".{{ $ticket->id }}" class="show-hand">
                                        <td><a href="/change-control/{{$ticket->id}}" target="_blank">{{$ticket->id}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{strtoupper($ticket->status)}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->change_type}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">@if(strlen($ticket->change_description) > 80) {{substr($ticket->change_description, 0, 80)}} ... @else {{$ticket->change_description}} @endif</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->changeOwner->first_name}} {{$ticket->changeOwner->last_name}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->end_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</td>
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