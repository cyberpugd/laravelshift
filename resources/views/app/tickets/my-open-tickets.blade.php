@extends('layouts.master')

@section('content')
<section class="content-header">
     <h1>My Open Tickets</h1>
</section>
<section class="content">
     <div class="panel panel-default">
               <div class="panel-heading">
                                   <div class="col-md-6">
                    <form method="GET" action="/tickets/open-tickets">
         
                         <div class="input-group">                               

                              <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                              
                              <span class="input-group-btn">
                                   @if(isset($search))
                                        <a href="/tickets/open-tickets" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
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
               {!! $tickets->appends(Input::except('page'))->render() !!}
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Ticket #</th>
                                        <th>Urgency</th>
                                        <th>Subject</th>
                                        <th>Category / Subcategory</th>
                                        <th>Caller</th>
                                        <th>Created</th>
                                        <th>Due</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($tickets as $ticket)
                                   <tr data-toggle="collapse" data-target=".{{ $ticket->id }}" class="show-hand">
                                        <td><a href="/tickets/{{$ticket->id}}" target="_blank">{{$ticket->id}}</a></td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->urgency->name}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->title}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->category->name}} / {{$ticket->subcategory->name}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->created_at->setTimezone(Auth::user()->timezone)->diffForHumans()}}</td>
                                        <td v-on:click="showTicket({{$ticket->id}})">{{$ticket->due_date->setTimezone(Auth::user()->timezone)->diffForHumans()}}</td>

                                   </tr>
                                   @endforeach
                              </tbody>
                         </table>
                    </div>
               </div>
     </div>
</section>
@endsection
@section('footer')
<script>
     new Vue({
          el: 'body',
          methods: {
               showTicket: function(id) {
                    window.location.href = '/tickets/'+id;
               }
          }
     });
</script>
@endsection