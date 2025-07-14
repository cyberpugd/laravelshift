@extends('layouts.master')

@section('content')
@include('app.components.modals.add_user')
<section class="content-header">
     <h1>User Management</h1>
</section>
<section class="content">
<div class="panel panel-default">
          <div class="panel-heading">          
          <div class="col-md-6">
          <form method="GET" action="/admin/users">
         
                         <div class="input-group">                               

                              <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                              
                              <span class="input-group-btn">
                                   @if(isset($search))
                                        <a href="/admin/users" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
                                   @endif
                                   <button class="btn btn-default" type="submit">Go!</button>
                              </span>
                         
                              
                         </div>
                    </form>
               </div>
               <a class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#add_user">New User</a>
               <div class="clearfix"></div>
     </div>
     <div class="panel-body">
          

          <div class="tab-content">
               <ul class="nav nav-tabs" role="tablist" style="margin-top: 15px;">
                    <li role="presentation" @if(!isset($_GET['role'])) class="active" @endif><a href="{{request()->fullUrlWithQuery(['role' => null])}}" role="tab">All</a></li>
                    @foreach($roles as $role)
                         <li role="presentation" @if(isset($_GET['role']) && $_GET['role'] == $role->id) class="active" @endif>
                         {{-- <a href="/admin/users?role={{$role->id}}" role="tab">{{$role->label}}</a> --}}
                         <a href="{{request()->fullUrlWithQuery(['role'=>$role->id])}}" role="tab">{{$role->label}}</a>
                         </li>
                    @endforeach
               </ul>
               <ul class="nav nav-tabs" role="tablist" style="margin-top: 15px;">
                    <li role="presentation" class="active"><a href="#active" aria-controls="active" role="tab" data-toggle="tab">Active</a></li>
                    <li role="presentation"><a href="#inactive" aria-controls="inactive" role="tab" data-toggle="tab">Inactive</a></li>
               </ul>
               <div role="tabpanel" class="tab-pane active" id="active">
               {!! $activeUsers->appends(Input::except('page'))->render() !!}
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Active Directory ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($activeUsers as $user)
                                   <tr data-toggle="collapse" data-target=".{{ $user->id }}" class="show-hand" v-on:click="showUser({{$user->id}})">
                                        <td>{{ $user->ad_id }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                   </tr>
                                   @endforeach
                              </tbody>
                         </table>
                    </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="inactive">
               {!! $inactiveUsers->appends(Input::except('page'))->render() !!}
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                              <thead>
                                   <tr>
                                        <th>Active Directory ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   @foreach($inactiveUsers as $user)
                                   <tr data-toggle="collapse" data-target=".{{ $user->id }}" class="show-hand" v-on:click="showUser({{$user->id}})">
                                        <td>{{ $user->ad_id }}</td>
                                        <td>{{ $user->last_name }}, {{ $user->first_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>@if($user->active == 1) Active @else Inactive @endif</td>
                                   </tr>
                                   @endforeach
                              </tbody>
                         </table>
                    </div>
               </div>
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
               showUser: function(id) {
                    window.location.href = '/admin/users/'+id;
               }
          }
     });
</script>
@endsection