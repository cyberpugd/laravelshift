@extends('layouts.master')

@section('content')
<section class="content-header">
     <h1>My Work Orders</h1>
</section>
<section class="content">
     <div class="panel panel-default">
               <div class="panel-heading">
                                                       <div class="col-md-6">
                                        
                                         @if(Request::getPathInfo() == '/change-control/work-orders')
                                             <form method="GET" action="/change-control/work-orders">
                                        @else
                                             <form method="GET" action="/tickets/work-orders">
                                        @endif                             
                                             <div class="input-group">                               

                                                  <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                                                  
                                                  <span class="input-group-btn">
                                                       @if(isset($search))
                                                             @if(Request::getPathInfo() == '/change-control/work-orders')
                                                            <a href="/change-control/work-orders" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
                                                            @else
                                                                 <a href="/tickets/work-orders" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
                                                            @endif 
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
               {!! $work_orders->appends(Input::except('page'))->render() !!}
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Work Order #</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Ticket #</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($work_orders as $work_order)
                                   <tr class="show-hand">
                                        <td><a href="/tickets/work-order/{{$work_order->id}}" target="_blank">{{$work_order->id}}</a></td>
                                        <td @if($work_order->ticketable_type == 'Ticket') v-on:click="showWorkOrder({{$work_order->id}})" @else v-on:click="showWorkOrderCC({{$work_order->id}})" @endif>{{$work_order->subject}}</td>
                                        <td @if($work_order->ticketable_type == 'Ticket') v-on:click="showWorkOrder({{$work_order->id}})" @else v-on:click="showWorkOrderCC({{$work_order->id}})" @endif>{{ucfirst($work_order->status)}}</td>
                                        <td @if($work_order->ticketable_type == 'Ticket') v-on:click="showWorkOrder({{$work_order->id}})" @else v-on:click="showWorkOrderCC({{$work_order->id}})" @endif>{{$work_order->due_date->format('m/d/Y g:i A')}}</td>
                                        @if($work_order->ticketable_type == 'Ticket')
                                             <td><a href="/tickets/{{$work_order->ticketable->id}}">{{$work_order->ticketable->id}}</a></td>
                                        @else
                                             <td><a href="/change-control/{{$work_order->ticketable->id}}">{{$work_order->ticketable->id}}</a></td>
                                        @endif
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
               showWorkOrder: function(id) {
                    window.location.href = '/tickets/work-order/'+id;
               },
                showWorkOrderCC: function(id) {
                    window.location.href = '/change-control/work-order/'+id;
               }
          }
     });
</script>
@endsection