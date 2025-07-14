@extends('layouts.master')

@section('content')
     <section class="content-header">
          <span  style="font-size: 24px;">{{$view->name}}</span>
          <a href="/views/edit/{{$view->id}}" class="btn btn-default pull-right" type="submit"><i class="fa fa-pencil"></i></a>
     </section>
     <section class="content">
     <div class="panel panel-default">
               <div class="panel-heading">
                                   <div class="col-md-6">
                    <form method="GET" action="/views/{{$view->id}}">
                                                  <div class="input-group">                               

                              <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                              
                              <span class="input-group-btn">
                                   @if(isset($search))
                                        <a href="/views/{{$view->id}}" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
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
               {!! $results->appends(Input::except('page'))->render() !!}
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                              @if(isset($results[0]))
                                   <tr>
                                       @foreach(collect($results[0])->keys() as $column_name)
                                       @if($column_name != 'row_num')
                                         <th><strong>{{view_sort_by($column_name, $column_name, 'myView', $view->id, $search) }}@if(isset($_GET['sortBy'])) @if($column_name == $_GET['sortBy']) @if($_GET['direction'] == 'asc') <i class="fa fa-arrow-down" style="color: #82BE5A;"></i> @else <i class="fa fa-arrow-up" style="color: #82BE5A;"></i> @endif @endif @endif</strong></th>
                                         @endif
                                     @endforeach
                                   </tr>
                              @endif
                              </thead>
                              <tbody>
                                   @if($view->query_type == 'work_order')
                                        @foreach($results as $result)
                                             <tr class="show-hand">
                                                  @foreach(collect($results[0])->keys() as $key => $value)
                                                       @if($value != 'row_num')
                                                            @if($value == 'ID')
                                                                 <td><a href="/tickets/work-order/{{$result->ID}}" target="_blank">{{$result->ID}}</a></td>
                                                            @else
                                                                 <td v-on:click="showTicket({{$result->ID}})">@if(strpos($value, 'Date') !== false && $result->$value != null) {{$result->$value->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}} @else {{$result->$value}} @endif</td>
                                                            @endif
                                                       @endif
                                                  @endforeach
                                             </tr>
                                        @endforeach
                                   @else
                                        @foreach($results as $result)
                                             <tr class="show-hand">
                                                  @foreach(collect($results[0])->keys() as $key => $value)
                                                       @if($value != 'row_num')
                                                            @if($value == 'ID')
                                                                 <td>@if($view->query_type == 'ticket') 
                                                                                <a href="/tickets/{{$result->$value}}" target="_blank">{{$result->$value}}</a>
                                                                           @else
                                                                                <a href="/change-control/{{$result->$value}}" target="_blank">{{$result->$value}}</a>
                                                                           @endif
                                                                 </td>
                                                            @else
                                                            <td v-on:click="showTicket({{$result->ID}})">@if(strpos($value, 'Date') !== false && $result->$value != null) {{$result->$value->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}} @else {{$result->$value}}@endif</td> 
                                                            @endif
                                                       @endif
                                                  @endforeach
                                             </tr>
                                        @endforeach
                                   @endif
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
                    if('{{$view->query_type}}' == 'ticket') {
                         window.location.href = '/tickets/'+id;
                    } 
                    if('{{$view->query_type}}' == 'work_order') {
                              window.location.href = '/tickets/work-order/'+id;
                    }
                    if('{{$view->query_type}}' == 'change_ticket') {
                         window.location.href = '/change-control/'+id;
                    }
               }
          }
     });
</script>
@endsection