@extends('layouts.master')

@section('content')
@include('app.components.modals.apply_cc_template')
<section class="content-header">
     <span style="font-size: 24px;">Create Change Ticket</span>
     <div class="btn-group pull-right">
          <span class="btn btn-default" data-toggle="modal" data-target="#choose_template">Apply Template</span>
     </div>
</section>
<section class="content">
@include('app.partials.errors')
<form action="/change-control/create" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <div id="app"  class="panel panel-default">
          <div class="panel-body">
               <div class="col-md-6">
                         <div class="col-md-6">
                         <label for="audit_unit" class="control-label">Audit Unit</label>
                              <select name="audit_unit" class="selectpicker form-control" data-size="15" title="Please Choose" autofocus>
                                   @foreach($audit_units as $unit)
                                             <option value="{{$unit->id}}" @if($template) @if($template->audit_unit == $unit->id) selected @endif  @else @if(old('audit_unit') == $unit->id) selected @endif @endif>{{$unit->name}}</option>
                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-6">
                         <label for="change_owner" class="control-label">Owner</label>
                              <select name="change_owner" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Agent">
                                   @foreach($agents as $user)
                                   <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($template) @if($template->change_owner_id == $user->id) selected @endif @else @if(old('change_owner') == $user->id) selected @endif @endif>{{$user->first_name}} {{$user->last_name}}</option>

                                   @endforeach
                              </select>
                         </div>

                         <div class="col-md-6">
                         <label for="it_approver" class="control-label">IT Approver</label>
                              <select name="it_approver" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                   @foreach($approvers as $approver)
                                   <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($template) @if($template->it_approver_id == $approver->id) selected @endif @else @if(old('it_approver') == $approver->id) selected @endif @endif>{{$approver->first_name}} {{$approver->last_name}}</option>
                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-6">
                         <label for="bus_approver" class="control-label">Bus. Approver</label>
                              <select name="bus_approver" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                   @foreach($approvers as $approver)
                                   <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($template) @if($template->bus_approver_id == $approver->id) selected @endif @else @if(old('bus_approver') == $approver->id) selected @endif @endif>{{$approver->first_name}} {{$approver->last_name}}</option>

                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-6">
                         <label for="change_type" class="control-label">Change Type</label>
                              <select name="change_type" class="selectpicker form-control" title="Select Type">
                                   <option value="planned" @if($template) @if($template->change_type == 'planned') selected @endif @else @if(old('change_type') == 'planned') selected @endif @endif>Planned/Scheduled</option>
                                   <option value="emergency" @if($template) @if($template->change_type == 'emergency') selected @endif @else @if(old('change_type') == 'emergency') selected @endif @endif>Emergency</option>
                              </select>
                         </div>
                         <div class="col-md-6" style="margin-top: 15px;">
                              <strong>Create in Deferred Status:</strong>
                              <input type='checkbox' name="deferred" @if($template) @if($template->status) checked @endif @endif />
                         </div>
                    </div>
                    <div class="col-md-6">
                         <div class="col-md-6">
                         <label for="" class=" control-label">Start Date</label>
                                   <input id='start_date' type='text' class="form-control" name="start_date" autocomplete="off"
                                        @if($template)@if($template->start_date) value="{{$template->start_date->timezone(Auth::user()->timezone)->format('m/d/Y h:i a')}}"@endif @else value="{{old('start_date')}}"@endif>
                         </div>
                         <div class="col-md-6">
                         <label for="" class="control-label">End Date</label>
                                   <input id='end_date' type='text' class="form-control" name="end_date" autocomplete="off" @if($template)@if($template->end_date) value="{{$template->end_date->timezone(Auth::user()->timezone)->format('m/d/Y h:i a')}}"@endif @else value="{{old('end_date')}}"@endif>
                         </div>

                    </div>
                    <div class="col-md-12">
                    <hr>
                         <div class="col-md-6">
                              <label for="change_description" class="control-label">Change Description</label>
                              <textarea name="change_description" class="form-control" placeholder="Please describe the change you are requesting." rows="8">@if($template){{$template->change_description}}@else{{old('change_description')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="roll_out_plan" class="control-label">Roll Out Plan</label>
                              <textarea name="roll_out_plan" class="form-control" placeholder="What's your plan of action?" rows="8">@if($template){{$template->roll_out_plan}}@else{{old('roll_out_plan')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="change_reason" class="control-label">Reason for Change</label>
                              <textarea name="change_reason" class="form-control" placeholder="Why is this change needed?" rows="8">@if($template){{$template->change_reason}}@else{{old('change_reason')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="back_out_plan" class="control-label">Back Out Plan</label>
                              <textarea name="back_out_plan" class="form-control" placeholder="How do you plan to reverse the change should things go south?" rows="8">@if($template){{$template->back_out_plan}}@else{{old('back_out_plan')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="servers" class="control-label">Servers</label>
                              <textarea name="servers" class="form-control" placeholder="What servers are involved?" rows="8">@if($template){{$template->servers}}@else{{old('servers')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="test_plan" class="control-label">Test Plan</label>
                              <textarea name="test_plan" class="form-control" placeholder="What will be your test plan to prove that this change was successful?" rows="8">@if($template){{$template->test_plan}}@else{{old('test_plan')}}@endif</textarea>
                         </div>

                         <div class="col-md-6">
                              <label for="business_impact" class="control-label">Business Impact</label>
                              <textarea name="business_impact" class="form-control" placeholder="How will this impact P2 or the customer(s)?" rows="8">@if($template){{$template->business_impact}}@else{{old('business_impact')}}@endif</textarea>
                         </div>
                         <div class="col-md-6">
                              <label for="affected_groups" class="control-label">Affected Groups</label>
                              <textarea name="affected_groups" class="form-control" placeholder="Who will be angry if you break something?" rows="8">@if($template){{$template->affected_groups}}@else{{old('affected_groups')}}@endif</textarea>
                         </div>
                    </div>
          </div>
          <div class="panel-footer">
               <div class="form-group">
                    <div class="col-lg-12">
                         <div style="text-align: right;">
                              <button v-show="!creating" id="create-ticket" type="submit" class="btn btn-success" v-on:click="disableButton">Create Ticket</button>
                              <a v-show="creating" class="btn btn-success" v-on:click="disableButton" disabled><i class="fa fa-spin fa-cog"></i> Creating ticket please wait</a>
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
