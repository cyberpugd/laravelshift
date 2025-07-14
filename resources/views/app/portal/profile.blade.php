@extends('layouts.portal')
@section('content')
 <div class="panel panel-default">
               <div class="panel-heading">
                    <h3 class="panel-title">{{$user->first_name}} {{$user->last_name}}</h3>
               </div>
               <div class="panel-body">
                    <form action="/helpdesk/user-settings/profile/" method="POST">
                    {!!csrf_field()!!}
                         {{-- Removed this because end users don't need to update their status, only helpdesk agents --}}
                         {{-- <div class="form-group row"> 
                              <label for="roles" class="col-lg-2 control-label">Status</label>
                              <div class="col-lg-3">
                                   <select name="status" class="selectpicker" data-size="15"  required>
                                             <option value="1" @if($user->out_of_office == 1) selected @endif>Out of Office</option>
                                             <option value="0" @if($user->out_of_office == 0) selected @endif>In Office</option>
                                   </select> 
                              </div>
                         </div> --}}
                         <div class="form-group row">
                              <label for="first_name" class="col-md-2 control-label">First Name</label>
                              <div class="col-lg-3">
                                   <input type="text" name="first_name" class="form-control" value="{{$user->first_name}}">
                              </div>
                         </div>
                         <div class="form-group row">
                              <label for="last_name" class="col-md-2 control-label">Last Name</label>
                              <div class="col-lg-3">
                                   <input type="text" name="last_name" class="form-control" value="{{$user->last_name}}">
                              </div>
                         </div>
                         <div class="form-group row">
                              <label for="email" class="col-md-2 control-label">Email</label>
                              <div class="col-lg-3">
                                   <input type="text" name="email" class="form-control" value="{{$user->email}}">
                              </div>
                         </div>
                         <div class="form-group row">
                              <label for="phone_number" class="col-md-2 control-label">Phone Number</label>
                              <div class="col-lg-3">
                                   <input type="text" name="phone_number" class="form-control" value="{{$user->phone_number}}">
                              </div>
                         </div>
                         <div class="form-group row">
                              <label for="location" class="col-md-2 control-label">Location</label>
                              <div class="col-lg-3">
                                   <select name="location" class="selectpicker" data-size="15" required>
                                        @foreach($locations as $location)
                                             <option value="{{$location->id}}" @if($user->location_id == $location->id) selected @endif>{{$location->city}}</option>
                                        @endforeach
                                   </select>
                              </div>
                         </div>
                         <div class="form-group row">
                              <label for="timezone" class="col-md-2 control-label">User Timezone</label>
                              <div class="col-lg-3">
                                   <select name="timezone" class="form-control" data-size="15" required>
                                        @foreach($timezones as $key => $value)
                                             <option value="{{$key}}" @if($user->timezone == $key) selected @endif>{{$value}}</option>
                                        @endforeach
                                   </select>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save</button>
                         </div>
                    </form>
               </div>
</div>
@endsection