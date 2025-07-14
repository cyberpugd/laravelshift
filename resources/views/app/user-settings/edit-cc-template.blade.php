@extends('layouts.master')

@section('content')
<section class="content-header">
               <h1>Edit Change Ticket Template</h1>
</section>
<section class="content">
<form action="/user-settings/cc-template/{{$template->id}}" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <div id="app"  class="panel panel-default">
          <div class="panel-body">
               <div class="col-md-12" style="padding-bottom: 15px;">
                    <div class="col-md-6">
                         <label for="audit_unit" class="control-label">Template Name</label>
                         <input type="text" name="name" class="form-control" value="{{$template->name}}">
                    </div>
                    <div class="col-md-6">
                         <label for="share_with" class="control-label">Share With</label>
                         <select name="share_with[]" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select users to share with" multiple>
                              @foreach($users as $user)
                                   <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if(in_array($user->id, $template->sharedWith->lists('id')->toArray())) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                              @endforeach
                         </select> 
                    </div>
               </div>
               <div class="col-md-6">
                         <div class="col-md-6">
                         <label for="audit_unit" class="control-label">Audit Unit</label>
                              <select name="audit_unit" class="selectpicker form-control" data-size="15" title="Please Choose" autofocus>
                                   @foreach($audit_units as $unit)
                                        <option value="{{$unit->id}}" @if($template->audit_unit == $unit->id) selected @endif>{{$unit->name}}</option>
                                   @endforeach
                              </select> 
                         </div>
                         <div class="col-md-6">
                         <label for="change_owner" class="control-label">Owner</label>
                              <select name="change_owner_id" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Agent">
                                   @foreach($users as $user)
                                   @if($user->can('be_assigned_ticket'))
                                   <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($template->change_owner_id == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                   @endif
                                   @endforeach
                              </select> 
                         </div>
                         
                         <div class="col-md-6">
                         <label for="it_approver" class="control-label">IT Approver</label>
                              <select name="it_approver_id" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                   @foreach($users as $approver)
                                   @if($approver->can('approve_change_ticket'))
                                   <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($template->it_approver_id == $approver->id) selected @endif>{{$approver->first_name}} {{$approver->last_name}}</option>
                                   @endif
                                   @endforeach
                              </select> 
                         </div>
                         <div class="col-md-6">
                         <label for="bus_approver" class="control-label">Bus. Approver</label>
                              <select name="bus_approver_id" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                   @foreach($users as $approver)
                                   @if($approver->can('approve_change_ticket'))
                                   <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($template->bus_approver_id == $approver->id) selected @endif>{{$approver->first_name}} {{$approver->last_name}}</option>
                                   @endif
                                   @endforeach
                              </select> 
                         </div>
                         <div class="col-md-6">
                         <label for="change_type" class="control-label">Change Type</label>
                              <select name="change_type" class="selectpicker form-control" title="Select Type">
                                   <option value="planned" @if($template->change_type == 'planned') selected @endif>Planned/Scheduled</option>
                                   <option value="emergency"@if($template->change_type == 'emergency') selected @endif>Emergency</option>
                              </select>
                         </div>
                         <div class="col-md-6" style="margin-top: 15px;">
                              <strong>Create in Deferred Status:</strong>
                              <input type='checkbox' name="status" @if($template->status) checked @endif/>
                         </div>
                    </div>
                    <div class="col-md-6">

                         <div class="col-md-6">
                         <label for="" class=" control-label">Start Date</label>
                              @if($template->start_date)
                                   <input id='start_date' type='text' class="form-control" name="start_date" value="{{$template->start_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}" v-model="startDate"/>
                              @else
                                   <input id='start_date' type='text' class="form-control" name="start_date" value="" v-model="startDate"/>
                              @endif
                         </div>
                         <div class="col-md-6">
                         <label for="" class="control-label">End Date</label>
                              @if($template->end_date)
                                   <input id='end_date' type='text' class="form-control" name="end_date" value="{{$template->end_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}" v-model="endDate"/>
                              @else
                                   <input id='end_date' type='text' class="form-control" name="end_date" value="" v-model="endDate"/>
                              @endif
                         </div>
                         
                    </div>
                    <div class="col-md-12">
                    <hr>
                         <div class="col-md-6">
                              <label for="change_description" class="control-label">Change Description</label>
                              <textarea name="change_description" class="form-control" placeholder="Please describe the change you are requesting." rows="8">{{$template->change_description}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="roll_out_plan" class="control-label">Roll Out Plan</label>
                              <textarea name="roll_out_plan" class="form-control" placeholder="What's your plan of action?" rows="8">{{$template->roll_out_plan}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="change_reason" class="control-label">Reason for Change</label>
                              <textarea name="change_reason" class="form-control" placeholder="Why is this change needed?" rows="8">{{$template->change_reason}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="back_out_plan" class="control-label">Back Out Plan</label>
                              <textarea name="back_out_plan" class="form-control" placeholder="How do you plan to reverse the change should things go south?" rows="8">{{$template->back_out_plan}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="servers" class="control-label">Servers</label>
                              <textarea name="servers" class="form-control" placeholder="What servers are involved?" rows="8">{{$template->servers}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="test_plan" class="control-label">Test Plan</label>
                              <textarea name="test_plan" class="form-control" placeholder="What will be your test plan to prove that this change was successful?" rows="8">{{$template->test_plan}}</textarea>
                         </div>

                         <div class="col-md-6">
                              <label for="business_impact" class="control-label">Business Impact</label>
                              <textarea name="business_impact" class="form-control" placeholder="How will this impact P2 or the customer(s)?" rows="8">{{$template->business_impact}}</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="affected_groups" class="control-label">Affected Groups</label>
                              <textarea name="affected_groups" class="form-control" placeholder="Who will be angry if you break something?" rows="8">{{$template->affected_groups}}</textarea>
                         </div>
                    </div>
          </div>
          <div class="panel-footer">
               <div class="form-group">
                    <div class="col-lg-12">
                         <div style="text-align: right;">
                              <button v-show="!creating" id="create-ticket" type="submit" class="btn btn-success" v-on:click="disableButton">Save Template</button>
                              <a v-show="creating" class="btn btn-success" v-on:click="disableButton" disabled><i class="fa fa-spin fa-cog"></i> Saving template please wait</a>
                         </div>
                    </div>
               </div>

          </div>

     </div>
</form>
</section>
@endsection
@section('footer')
<script>
     new Vue({
          el: '#app',
          data: {
               creating: false,
               startDate: '',
               endDate: '',
          },
          methods: {
               disableButton: function() {
                    this.creating = true;
               },
          }
     });

     

$(document).ready( function() {
     jQuery('#start_date').datetimepicker({
          onShow:function( ct ){
               var endDate = moment(jQuery('#end_date').val()).format('YYYY/MM/DD h:mm a');
               var endTime = moment(jQuery('#end_date').val()).format('hh:mm');
               this.setOptions({
                    maxDate:(jQuery('#end_date').val()?endDate:false),
               })
          },
          format:'m/d/Y h:i a',
          formatTime: 'g:ia',
          timepicker:true,
          defaultTime:'09:00'
     });
     jQuery('#end_date').datetimepicker({
          onShow:function( ct ){
               var startDate = moment(jQuery('#start_date').val()).format('YYYY/MM/DD h:mm a');
               var startTime = moment(jQuery('#start_date').val()).format('h:mm');
               this.setOptions({
                    minDate:(jQuery('#start_date').val()?startDate:false),
               })
          },
          format:'m/d/Y h:i a',
          formatTime: 'g:ia',
          timepicker:true,
          defaultTime:'09:00'
     });
});
</script>
@endsection