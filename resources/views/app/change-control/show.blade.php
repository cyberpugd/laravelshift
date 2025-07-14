@extends('layouts.master')

@section('content')
<style>

     /* Mimic table appearance */
     div.table {
          display: table;
     }
     div.table .file-row {
          display: table-row;
          width: 100%;
     }
     div.table .file-row > div {
          display: table-cell;
          vertical-align: top;
          border-top: 1px solid #ddd;
          padding: 2px;
     }
     div.table .file-row:nth-child(odd) {
          background: #f9f9f9;
     }



     /* The total progress gets shown by event listeners */
     #total-progress {
          opacity: 0;
          transition: opacity 0.3s linear;
     }

     /* Hide the progress bar when finished */
     #previews .file-row.dz-success .progress {
          opacity: 0;
          transition: opacity 0.3s linear;
     }

     /* Hide the delete button initially */
     #previews .file-row .delete {
          display: none;
     }

     /* Hide the start and cancel buttons and show the delete button */

     #previews .file-row.dz-success .start,
     #previews .file-row.dz-success .cancel {
          display: none;
     }
     #previews .file-row.dz-success .delete {
          display: block;
     }


</style>
@include('app.components.modals.create-work-order')
@include('app.components.modals.apply_wo_template')
@include('app.components.modals.email_work_orders')
@if($ticket->status == 'completed' || $ticket->status == 'cancelled')
     @include('app.components.modals.show-status-notes')
@endif
<div id="app" v-cloak>
<form id="start-work" action="/change-control/start-work/{{$ticket->id}}" method="post">
     {{csrf_field()}}
</form>
<form id="propose" action="/change-control/propose/{{$ticket->id}}" method="post">
     {{csrf_field()}}
</form>
<form id="clone" action="/change-control/clone/{{$ticket->id}}" method="post">
     {{csrf_field()}}
</form>
<form id="removewo" action="" method="post">
     {{csrf_field()}}
</form>

     <section class="content-header">
               <span style="font-size: 24px;">Change Ticket #{{$ticket->id}}</span>

          <div class="btn-group pull-right">
               @foreach($ticket->changeApprovals as $approval)
               @if($approval->approver == Auth::user()->id && $approval->approved == 0)
                    <form id="approveForm" class="form-inline" action="/change-control/approve/{{$ticket->id}}" method="post">{{csrf_field()}}</form>
                    <form id="rejectForm" class="form-inline" action="/change-control/reject/{{$ticket->id}}" method="post">{{csrf_field()}}</form>
                    <a id="approveTicket" class="btn btn-success btn-sm" data-action="approve" v-cloak>Approve</a>
                    <a id="rejectTicket" class="btn btn-danger btn-sm" data-action="reject" v-cloak>Reject</a>
               @endif
               @endforeach
               @if($ticket->canEdit())

                         @if($ticket->status != 'cancelled' && $ticket->status != 'completed')
                              <a href="/change-control/cancel/{{$ticket->id}}" v-show="!editMode" id="cancelTicket" class="btn btn-danger btn-sm" data-action="cancel" v-cloak>Cancel Change Ticket</a>
                         @endif
                          @if($ticket->status == 'scheduled')
                              <button v-show="!editMode" id="start-work" type="submit" class="btn btn-success btn-sm" v-on:click="startWork" v-cloak>Start Work</button>
                         @endif
                         @if($ticket->status == 'deferred')
                              <button v-show="!editMode" type="submit" class="btn btn-success btn-sm" v-on:click="propose" v-cloak>Propose</button>
                         @endif
                         @if($ticket->status == 'in-progress')
                              <a href="/change-control/close/{{$ticket->id}}" v-show="!editMode" type="submit" class="btn btn-success btn-sm" v-cloak>Close Change Ticket</a>
                         @endif
                     @if($ticket->status == 'rejected')
                         <button v-show="!editMode" class="btn btn-default btn-sm" v-on:click="toggleEdit" v-cloak>Amend</button>
                         <button v-show="saveButton" id="save-ticket" type="submit" class="btn btn-success btn-sm" v-on:click="submitForApproval">Submit for Approval</button>
                         <a v-show="saving" class="btn btn-success btn-sm" disabled><i class="fa fa-spin fa-cog"></i> Saving changes</a>
                         <button v-show="editMode" class="btn btn-default btn-sm" v-on:click="toggleEdit" v-cloak><i class="fa fa-ban"></i></button>
                    @else
                         @if((!deniedPermission('change_ticket_auditor')) || (($ticket->status != 'cancelled' && $ticket->status != 'completed') && Auth::user()->id == $ticket->change_owner_id))
                              <button v-show="saveButton" id="save-ticket" type="submit" class="btn btn-success btn-sm" v-on:click="saveTicket" v-cloak>Save</button>
                              <a v-show="saving" class="btn btn-success btn-sm" disabled v-cloak><i class="fa fa-spin fa-cog"></i> Saving ticket please wait</a>
                              <button v-show="!editMode" class="btn btn-default btn-sm" v-on:click="toggleEdit" v-cloak><i class="fa fa-pencil"></i></button>
                              <button v-show="editMode" class="btn btn-default btn-sm" v-on:click="toggleEdit" v-cloak><i class="fa fa-pencil"></i></button>
                         @endif

                    @endif
               @endif
               @can('clone_change_ticket')
                         <button type="button" class="btn btn-default btn-sm" v-on:click="clone" v-cloak title="Clone to New Ticket"><i class="fa fa-files-o"></i></button>
               @endcan
               <a href="/change-control/print/{{$ticket->id}}" class="btn btn-default btn-sm" title="Print Ticket" target="_blank"><i class="fa fa-print"></i></a>
               </div>
          </section>
     <section class="content">
     <div class="panel panel-default">
     <div class="panel-body">
     <div class="col-md-12" v-cloak v-show="!editMode">
          <div class="col-md-6 child-div-padding">
               <div class="col-md-6">
                    <strong>Audit Unit: </strong>
                    {{$ticket->auditUnit->name}}
               </div>
               <div class="col-md-6">
                    <strong>Owner: </strong>
                    <span class="pointer" data-sip="{{$ticket->changeOwner->sip}}"
                                                       onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->changeOwner->email}}', 0, 10, 10)}"
                                                       onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                                       <a>{{$ticket->changeOwner->first_name}} {{$ticket->changeOwner->last_name}}</a></span>
               </div>
               <div class="col-md-6">
                    <strong>IT Approver: </strong>
                    @if($ticket->it_approver_id)
                         <span class="pointer" data-sip="{{$ticket->itApprover->sip}}"
                              onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->itApprover->email}}', 0, 10, 10)}"
                              onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                              <a>{{$ticket->itApprover->first_name}} {{$ticket->itApprover->last_name}}</a></span>
                    @else
                    None Assigned
                    @endif
               </div>
               <div class="col-md-6">
                    <strong>Bus. Approver: </strong>
                    @if($ticket->bus_approver_id)
                         <span class="pointer" data-sip="{{$ticket->busApprover->sip}}"
                              onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->busApprover->email}}', 0, 10, 10)}"
                              onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                              <a>{{$ticket->busApprover->first_name}} {{$ticket->busApprover->last_name}}</a></span>
                    @else
                    None Assigned
                    @endif
               </div>
               <div class="col-md-6">
                    <strong>Created By: </strong>
                         <span class="pointer" data-sip="{{$ticket->createdBy->sip}}"
                              onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->createdBy->email}}', 0, 10, 10)}"
                              onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                              <a>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</a></span>
               </div>
               <div class="col-md-6">
                    <strong>Change Type: </strong>
                    {{ucfirst($ticket->change_type)}}
               </div>
               <div class="col-md-6">
                    <strong>Status: </strong>
                    {{ucfirst($ticket->status)}}
                    @if($ticket->status == 'completed' || $ticket->status == 'cancelled')
                         <a data-toggle="modal" data-target="#show-status-notes" style="cursor: pointer;"><span class="fa fa-info-circle"></span></a>
                    @endif
               </div>
               @if($ticket->status == 'completed' || $ticket->status == 'cancelled')
               <div class="col-md-6">
                    <strong>{{ucfirst($ticket->status)}} On:</strong>
                    {{$ticket->close_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
               </div>
               @endif
          </div>
          <div class="col-md-6 child-div-padding">
               <div class="col-md-12">
                    <strong>Start Date:</strong>
                    {{$ticket->start_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
               </div>
               <div class="col-md-12">
                    <strong>End Date:</strong>
                    {{$ticket->end_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
               </div>
               @can('change_ticket_auditor')
               <div class="col-md-12">
                    <strong>Audited:</strong>
                    <input type='checkbox' {{($ticket->is_audited == 'yes' ? 'checked' : '')}}
                        @click="clickAudited">
               </div>
               @endcan
               <div class="col-md-4">
                    <strong>Approval Status:</strong>
               </div>
               <div class="col-md-10 well">
                    @foreach($ticket->changeApprovals as $approval)
                    @if($approval->approved == 1)
                    <p>Approved by {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}} on {{$approval->date_approved->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                    @elseif($approval->approved == 2)
                         <p>Rejected by {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}} on {{$approval->date_approved->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                    @else
                    <p>Needs approval from  {{$approval->approvedBy->first_name}} {{$approval->approvedBy->last_name}}</p>
                    @endif
                    @endforeach
               </div>
          </div>
          </div>
          <form id="saveTicket" action="/change-control/{{$ticket->id}}" method="post">
          {{csrf_field()}}
          <div class="col-md-12" v-cloak v-show="editMode" style="margin-bottom: 20px;">
               <div class="col-md-6">
                         <div class="col-md-6" style="display: inline-block;">
                         <label for="audit_unit" class="control-label">Audit Unit</label>
                              <select name="audit_unit" class="selectpicker form-control" data-size="15" title="Please Choose" autofocus>
                                   @foreach($audit_units as $unit)
                                        <option value="{{$unit->id}}" @if($unit->id == $ticket->audit_unit) selected @endif>{{$unit->name}}</option>
                                   @endforeach
                              </select>
                         </div>
                         <div class="col-md-6">
                         <label for="change_owner" class="control-label">Owner</label>
                              <select id="owner" name="change_owner" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Owner">
                                   @foreach($agents as $user)
                                   <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($ticket->change_owner_id == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                   @endforeach
                              </select>
                         </div>

                         <input id="itApprover" type="hidden" value="{{$ticket->it_approver_id}}">
                         <input id="busApprover" type="hidden" value="{{$ticket->bus_approver_id}}">
                         <div class="col-md-6">
                         @if($ticket->isItApproved())
                              <div  style="padding-top: 15px; padding-bottom: 15px;">
                              <strong>IT Approver: </strong>
                              @if($ticket->it_approver_id)
                              <span class="pointer" data-sip="{{$ticket->itApprover->sip}}"
                                        onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->itApprover->email}}', 0, 10, 10)}"
                                        onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                        <a>{{$ticket->itApprover->first_name}} {{$ticket->itApprover->last_name}}</a></span>
                              @else
                              None Assigned
                              @endif
                              </div>
                         @else
                         <label for="it_approver" class="control-label">IT Approver</label>
                              <select name="it_approver" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                   <option value="0">None Selected</option>
                                   @foreach($approvers as $approver)
                                   <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($ticket->it_approver_id == $approver->id) selected @endif>{{$approver->first_name}} {{$approver->last_name}}</option>
                                   @endforeach
                              </select>
                         @endif
                         </div>
                         <div class="col-md-6">
                          @if($ticket->isBusApproved())
                              <div  style="padding-top: 15px; padding-bottom: 15px;">
                                   <strong>Bus. Approver: </strong>
                                   @if($ticket->bus_approver_id)
                                   <span class="pointer" data-sip="{{$ticket->busApprover->sip}}"
                                        onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->busApprover->email}}', 0, 10, 10)}"
                                        onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                        <a>{{$ticket->busApprover->first_name}} {{$ticket->busApprover->last_name}}</a></span>
                                   @else
                                   None Assigned
                                   @endif
                              </div>
                              @else
                                   <label for="bus_approver" class="control-label">Bus. Approver</label>
                                   <select name="bus_approver" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Approver">
                                        <option value="0">None Selected</option>
                                        @foreach($approvers as $approver)
                                                  <option value="{{$approver->id}}" data-tokens="{{$approver->first_name}} {{$approver->last_name}}" @if($ticket->bus_approver_id == $approver->id) selected @endif>{{$approver->first_name}} {{$approver->last_name}}</option>
                                        @endforeach
                                   </select>
                         @endif
                         </div>
                         <div class="col-md-6">
                              <strong>Created By: </strong>
                                   <span class="pointer" data-sip="{{$ticket->createdBy->sip}}"
                                        onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->createdBy->email}}', 0, 10, 10)}"
                                        onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                        <a>{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</a></span>
                         </div>
                         <div class="col-md-6">
                         <label for="change_type" class="control-label">Change Type</label>
                              <select name="change_type" class="form-control" title="Select Type">
                                   <option value="planned" @if($ticket->change_type == 'Planned') selected @endif>Planned/Scheduled</option>
                                   <option value="emergency"@if($ticket->change_type == 'Emergency') selected @endif>Emergency</option>
                              </select>
                         </div>
                        <div class="col-md-6" style="padding-top: 15px; padding-bottom: 15px;">
                              <strong>Status: </strong>
                              {{ucfirst($ticket->status)}}
                         </div>
                    </div>
                    <div class="col-md-6">
                         <div class="col-md-6">
                         <label for="" class=" control-label">Start Date</label>
                              <div class='input-group date'>
                                   <input id='start_date' type='text' class="form-control" autocomplete="off" name="start_date" value="{{$ticket->start_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}"/>

                              </div>
                         </div>
                         <div class="col-md-6">
                         <label for="" class="control-label">End Date</label>
                              <div class='input-group date'>
                                   <input id='end_date' type='text' class="form-control" autocomplete="off" name="end_date" value="{{$ticket->end_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}"/>

                              </div>
                         </div>
                    </div>
          </div>

          <div class="panel-body">
               <!-- Begin Panel Body -->
               <!-- Nav tabs -->
               <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#ticket-details" aria-controls="public" role="tab" data-toggle="tab">Ticket Details</a></li>
                    <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments <span id="attachmentbadge" class="badge" style="background-color: #337ab7;">{{$attachments->count()+$woattachments->count()}}</span></a></li>
                    <li role="presentation"><a href="#work-orders" aria-controls="work-orders" role="tab" data-toggle="tab">Work Orders <span class="badge" style="background-color: #337ab7;">{{$work_orders->count()}}</span></a></li>
                    <li role="presentation"><a href="#history-log" aria-controls="history-log" role="tab" data-toggle="tab">History Log</a></li>
               </ul>
               <div class="tab-content">
               <!-- TICKET DETAILS TAB -->
                    <div role="tabpanel" class="tab-pane active" id="ticket-details">
                         <div class="col-md-12" v-cloak v-show="!editMode" style="padding: 0px;">
                              <hr>
                              <div class="col-md-6" style="padding-left: 0px;">
                                   <label for="change_description" class="control-label">Change Description: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->change_description))) !!}</div>

                                   <label for="change_reason" class="control-label">Reason for Change: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->change_reason))) !!}</div>

                                   <label for="servers" class="control-label">Servers: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->servers))) !!}</div>

                                   <label for="business_impact" class="control-label">Business Impact: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->business_impact))) !!}</div>

                              </div>
                              <div class="col-md-6" style="padding-right: 0px;">
                                   <label for="roll_out_plan" class="control-label">Roll Out Plan: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->roll_out_plan))) !!}</div>

                                   <label for="back_out_plan" class="control-label">Back Out Plan: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->back_out_plan))) !!}</div>

                                   <label for="test_plan" class="control-label">Test Plan: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->test_plan))) !!}</div>

                                   <label for="affected_groups" class="control-label">Affected Groups: </label>
                                   <div class="well">{!! linkify(nl2br(htmlentities($ticket->affected_groups))) !!}</div>
                              </div>
                         </div>
                         <div class="col-md-12" v-cloak v-show="editMode"  style="margin-bottom: 20px; padding: 0px;">
                         <hr>
                              <div class="col-md-6" style="padding-left: 0px;">
                                   <label for="change_description" class="control-label">Change Description</label>
                                   <textarea name="change_description" class="form-control" placeholder="Please describe the change you are requesting." rows="8">{{$ticket->change_description}}</textarea>

                                   <label for="change_reason" class="control-label">Reason for Change</label>
                                   <textarea name="change_reason" class="form-control" placeholder="Why is this change needed?" rows="8">{{$ticket->change_reason}}</textarea>

                                   <label for="servers" class="control-label">Servers</label>
                                   <textarea name="servers" class="form-control" placeholder="What servers are involved?" rows="8">{{$ticket->servers}}</textarea>

                                   <label for="business_impact" class="control-label">Business Impact</label>
                                   <textarea name="business_impact" class="form-control" placeholder="How will this impact P2?" rows="8">{{$ticket->business_impact}}</textarea>
                              </div>
                              <div class="col-md-6" style="padding-right: 0px;">
                                   <label for="roll_out_plan" class="control-label">Roll Out Plan</label>
                                   <textarea name="roll_out_plan" class="form-control" placeholder="What's your plan of action?" rows="8">{{$ticket->roll_out_plan}}</textarea>

                                   <label for="back_out_plan" class="control-label">Back Out Plan</label>
                                   <textarea name="back_out_plan" class="form-control" placeholder="How do you plan to reverse the change should things go south?" rows="8">{{$ticket->back_out_plan}}</textarea>

                                   <label for="test_plan" class="control-label">Test Plan</label>
                                   <textarea name="test_plan" class="form-control" placeholder="Have you tested this? If so what did you do to test?" rows="8">{{$ticket->test_plan}}</textarea>

                                   <label for="affected_groups" class="control-label">Affected Groups</label>
                                   <textarea name="affected_groups" class="form-control" placeholder="Who will be angry if you break something?" rows="8">{{$ticket->affected_groups}}</textarea>
                              </div>
                         </div>
                    </div>
               </form>
                    <div role="tabpanel" class="tab-pane" id="attachments">
                    <hr>
                    {{-- @if($ticket->workStarted()) --}}
                              <div class="panel-body">
                    <p>Click Add files or drag files into browser to start uploading</p>
                    <div class="col-md-12">

                         <div class="col-md-12">
                              <br>
                              <div id="actions" class="row">

                                   <div class="form-inline col-lg-7">
                                        <!-- The fileinput-button span is used to style the file input field as button -->
                                        <div class="form-group">
                                             <a class="btn btn-success fileinput-button">
                                                  <i class="fa fa-plus"></i>
                                                  <span>Add files...</span>
                                             </a>
                                        </div>
                                        <div class="form-group">
                                             <a type="submit" class="btn btn-primary start">
                                                  <i class="fa fa-upload"></i>
                                                  <span>Upload all</span>
                                             </a>
                                        </div>
                                        <div class="form-group">
                                             <a type="reset" class="btn btn-warning cancel">
                                                  <i class="fa fa-ban"></i>
                                                  <span>Cancel upload</span>
                                             </a>
                                        </div>
                                   </div>

                                   <div class="col-lg-5">
                                        <!-- The global file processing state -->
                                        <span class="fileupload-process">
                                             <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                  <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                             </div>
                                        </span>
                                   </div>

                              </div>
                              <p id="filesReady" style="display: none;"><strong>Files ready for upload</strong></p>
                              <div class="files" id="previews">
                                   <div id="template" class="file-row row">
                                        <div class="col-md-4">
                                             <p class="name" data-dz-name></p>
                                             <strong class="error text-danger" data-dz-errormessage></strong>
                                        </div>

                                        <div class="col-md-3">
                                             <a class="btn btn-primary btn-xs start">
                                                  <i class="fa fa-upload"></i>
                                                  <span>Start</span>
                                             </a>
                                             <a data-dz-remove class="btn btn-warning btn-xs cancel">
                                                  <i class="fa fa-ban"></i>
                                                  <span>Cancel</span>
                                             </a>
                                             <span data-dz-remove class="delete">
                                                  <span style="color: #82BE5A;">Upload Successful</span>
                                             </span>
                                        </div>
                                   </div>
                                   </div>
                                   <div class="table-responsive">
                                   <table id="dropZoneTable" class="table">
                                        <thead>
                                             <tr>
                                                  <td><strong>Filename</strong></td>
                                                  <td><strong>Date Uploaded</strong></td>
                                                  <td><strong>Filesize</strong></td>
                                                  <td><strong>Source</strong></td>
                                                  <td></td>
                                             </tr>
                                        </thead>
                                   @foreach($attachments as $attachment)
                                   <tr class="file-row">
                                        <td>
                                             <p class="name"><a href="/cc/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
                                        </td>
                                        <td>
                                             <p>{{$attachment->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                                        </td>
                                        <td>
                                             <p class="size"><strong>{{number_format($attachment->file_size, 1)}} KB</strong></p>
                                        </td>
                                        <td>Change Ticket</td>
                                        <td>
                                             @can('change_ticket_auditor')
                                                  <a id="{{$attachment->id}}" data-name="{{$attachment->file_name}}" data-ticketid="{{$ticket->id}}" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></a>
                                             @endcan
                                        </td>
                                   </tr>
                                   @endforeach
                                   @foreach($woattachments as $attachment)
                                   <tr class="file-row">
                                        <td>
                                             <p class="name"><a href="/cc/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
                                        </td>
                                        <td>
                                             <p>{{$attachment->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                                        </td>
                                        <td>
                                             <p class="size"><strong>{{number_format($attachment->file_size, 1)}} KB</strong></p>
                                        </td>
                                        <td>
                                             <p><a href="/tickets/work-order/{{$attachment->ticketable_id}}">Work Order #{{$attachment->ticketable_id}}</a></p>
                                        </td>
                                        <td>
                                             @can('change_ticket_auditor')
                                                  <a id="{{$attachment->id}}" data-name="{{$attachment->file_name}}" data-ticketid="{{$ticket->id}}" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></a>
                                             @endcan
                                        </td>
                                   </tr>
                                   @endforeach
                              </table>
                              </div>
                         </div>

                    </div>
               </div>
              {{--  @else
               <p style="margin-top: 15px;">Attachments cannot be added until ticket is In-progress.</p>
               @endif --}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="work-orders">
                              <hr>
                         {{-- @if($ticket->workStarted()) --}}
                         @if(($ticket->status != 'completed' && $ticket->status != 'cancelled'))
                              @can('create_work_orders')
                              <div class="col-md-12">
                                   <div style="float: right;" class="buttonSpace">
                                        @if(!$ticket->workOrders->isEmpty())
                                             @if($ticket->status == 'in-progress')
                                                  <a class="btn btn-default" data-toggle="modal" data-target="#email_work_orders">Email Work Orders</a>
                                             @endif
                                        @endif
                                        <a class="btn btn-primary" data-toggle="modal" data-target="#apply_wo_template">Apply Template</a>
                                        <a class="btn btn-success" data-toggle="modal" data-target="#create_work_order">Create New</a>
                                   </div>
                              </div>
                              @endcan
                         @endif
                    <!-- Begin Panel Body -->
                    <div class="col-md-12">
                    <div class="table-responsive">
                         <table class="table">
                              <thead>
                                   <td><strong>ID</strong></td>
                                   <td><strong>Subject</strong></td>
                                   <td><strong>Status</strong></td>
                                   <td><strong>Assigned To</strong></td>
                                   <td><strong>Due Date</strong></td>
                                   <td></td>
                              </thead>
                              <tbody>
                                   @foreach($work_orders as $key => $value)
                                        <tr>
                                             <td>{{$value->id}}</td>
                                             <td><a href="/change-control/work-order/{{$value->id}}">{{$value->subject}}</a></td>
                                             <td>{{ucfirst($value->status)}}</td>
                                             <td><div class="pointer" data-sip="{{$value->assignedTo->sip}}"
                                                       onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$value->assignedTo->email}}', 0, 10, 10)}"
                                                       onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                                       <a>{{$value->assignedTo->first_name}} {{$value->assignedTo->last_name}}</a></div></td>
                                             <td>{{$value->due_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i A')}}</td>
                                             @if($ticket->status == 'completed' || $ticket->status == 'cancelled')
                                                  @if(!deniedPermission('change_ticket_auditor'))
                                                       <td>
                                                            <a class="btn btn-button btn-danger btn-xs" data-id="{{$value->id}}" @click="removewo({{$value->id}})"><i class="fa fa-trash"></i></a>
                                                       </td>
                                                  @endif
                                             @else
                                             @if($ticket->canEdit())
                                                  <td>
                                                       <a class="btn btn-button btn-danger btn-xs" data-id="{{$value->id}}" @click="removewo({{$value->id}})"><i class="fa fa-trash"></i></a>
                                                  </td>
                                             @endif
                                             @endif
                                        </tr>
                                   @endforeach
                              </tbody>
                         </table>
                         </div>
                    </div>
                         {{-- @else
                              <p style="margin-top: 15px;">Work orders cannot be worked until ticket is In-progress.</p>
                         @endif --}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="history-log">
                              <hr>
                              @foreach($histories as $history)
                                @if($history->key == 'created_at' && !$history->old_value)
                                  <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} created this change ticket at {{ $history->created_at->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</li>
                                   @elseif($history->key == 'change_control')
                                   <li>{{$history->newValue()}} on {{$history->created_at->timezone(Auth::user()->timezone)->format('m/d/Y g:i A')}}</li>
                                @else
                                     @if(strpos($history->key, 'date') )
                                        <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed {{ $history->fieldName() }} from <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', substr($history->oldValue(), 0, strpos($history->oldValue(), ".")))->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong> to <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', substr($history->newValue(), 0, strpos($history->newValue(), ".")))->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong> on {{$history->created_at->timezone(Auth::user()->timezone)->format('m/d/Y g:i A')}}</li>
                                     @else
                                       <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed {{ $history->fieldName() }} from <strong style="color: #696969">{{ ucfirst($history->oldValue()) }}</strong> to <strong style="color: #696969">{{ ucfirst($history->newValue()) }}</strong> on {{$history->created_at->timezone(Auth::user()->timezone)->format('m/d/Y g:i A')}}</li>
                                       @endif
                                @endif
                              @endforeach
                    </div>
               </div>
          </div>
     </section>
     </div>
@endsection
@section('footer')
<script>
     new Vue({
          el: '#app',
          data: {
               editMode: false,
               saving: false,
          },
          methods: {
               toggleEdit: function() {
                    this.editMode = !this.editMode;
               },
               clickAudited: function(event) {
                    var self = this;
                    this.$http.post('/change-control/'+{{$ticket->id}}+'/toggle-audit').then(function(response) {
                        swal({
                            title: "",
                            text: "Audited checkbox updated successfully.",
                            type: "success",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    });
               },
               saveTicket: function(){
                    if($('#owner').val() == $('#itApprover').val() || $('#owner').val() == $('#busApprover').val() ){
                          swal({
                              title: "Are you sure?",
                              text: "You are about to change the owner to the same person as an approver.<br><br> This will clear out all approvals and change the ticket status to Proposed. <br><br> Do you want to proceed?",
                              type: "warning",
                              html: true,
                              showCancelButton: true,
                              confirmButtonColor: "#DD6B55",
                              confirmButtonText: "Yes",
                              closeOnConfirm: false }, function() {
                                   this.saving = true;
                                   $('#saveTicket').submit();
                              });
                    } else {
                         this.saving = true;
                         $('#saveTicket').submit();
                    }
               },
               submitForApproval: function() {
                    this.saving = true;
                    $('#saveTicket').submit();
               },
               startWork: function() {
                    $('#start-work').submit();
               },
               propose: function() {
                    $('#propose').submit();
               },
               clone: function() {
                    swal({
                              title: "Are you sure?",
                              text: "You are about to clone this ticket and create a new change ticket in a deferred status.",
                              type: "warning",
                              html: true,
                              showCancelButton: true,
                              confirmButtonColor: "#DD6B55",
                              confirmButtonText: "Yes",
                              closeOnConfirm: false }, function() {
                                   $('#clone').submit();
                              });

               },
               removewo: function(id) {
                    $("#removewo").attr("action", "/change-control/wo/remove/"+id);
                    $('#removewo').submit();
               }
          },
          computed: {
               saveButton: function() {
                    if(this.editMode == true && this.saving == false) {
                         return true;
                    }
                         return false;
               }
          }
     });

     $(document).ready( function() {
     //Initialize datetime pickers
      jQuery('#start_date').datetimepicker({
               onShow:function( ct ){
                    var endDate = moment(jQuery('#end_date').val()).format('YYYY/MM/DD h:mm a');
                    var endTime = moment(jQuery('#end_date').val()).format('hh:mm');
                    this.setOptions({
                         maxDate:(jQuery('#end_date').val()?endDate:false),
                    })
               },
               format:'m/d/Y g:i a',
               formatTime: 'g:ia',
               timepicker:true
          });
          jQuery('#end_date').datetimepicker({
               onShow:function( ct ){
                    var startDate = moment(jQuery('#start_date').val()).format('YYYY/MM/DD h:mm a');
                    var startTime = moment(jQuery('#start_date').val()).format('h:mm');
                    this.setOptions({
                         minDate:(jQuery('#start_date').val()?startDate:false),
                    })
               },
               format:'m/d/Y g:i a',
               formatTime: 'g:ia',
               timepicker:true
          });
          $('#wo_due_date').datetimepicker({
               format: 'm/d/Y h:i a',
               formatTime: 'g:ia',
          });

//Approve Click
$('#approveTicket').click(function() {
     // event.preventDefault();
     $('#approveForm').submit();
});

//Reject Click
$('#rejectTicket').click(function() {
     // event.preventDefault();
     $('#rejectForm').submit();
});

$('.remove').on('click', function(e){
     button = $(this);
     deleteAttachment(button[0]);
});

function deleteAttachment(button) {
     swal({
          title: "Are you sure?",
          text: "You are about to delete attachment "+$('#'+button.id).data('name'),
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false },
          function(){
               $.ajax({
                    type: "POST",
                    url: "/tickets/attachment/"+button.id+"?"+$.param({"ticket_id": $('#'+button.id).data('ticketid')}),
                    success: function(data){
                         swal({
                              title: "",
                              text: data.statusText,
                              type: "success",
                              timer: 1500,
                              showConfirmButton: false
                         });
                         setTimeout(function(){
                              $('#'+button.id).closest('.file-row').fadeOut();
                              $('.badge').html(data.count);
                         }, 500);
                    },
                    error: function(data){
                         var response = JSON.parse(data.responseText);
                         swal({
                              title: response.title,
                              text: response.statusText,
                              type: response.type,
                              showConfirmButton: true,
                              confirmButtonText: response.confirmButton
                         }, function(){
                              if(response.redirect)
                              {
                                   window.location.href = response.redirect;
                              }
                         });
                    }
               });
          });
}
});
</script>
<script src="/js/dropzone.js"></script>
<script>
     // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
url: "/change-control/{{$ticket->id}}/attachments", // Set the url
thumbnailWidth: 80,
thumbnailHeight: 80,
parallelUploads: 20,
previewTemplate: previewTemplate,
autoQueue: false, // Make sure the files aren't queued until manually added
previewsContainer: "#previews", // Define the container to display the previews
clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
});



myDropzone.on("success", function(file, response) {
     $('.myclass').wrapInner('<p></p>');
     $('#attachmentbadge').html(response.count);
     $('#dropZoneTable tr:first').after('<tr> <td><p class="name"><a href="/cc/attachments/'+response.file+'" target="_blank">'+response.file_name+'</a></p></td><td><p>Now</p></td><td> <p class="size"><strong>'+response.file_size.toPrecision(3)+' KB</strong></p></td></tr>');
});

myDropzone.on("addedfile", function(file) {
// Hookup the start button
file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
$('#filesReady').show();
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
     document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
});

myDropzone.on("sending", function(file) {
// Show the total progress bar when upload starts
document.querySelector("#total-progress").style.opacity = "1";
// And disable the start button
file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");

});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
     document.querySelector("#total-progress").style.opacity = "0";

});

// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
document.querySelector("#actions .start").onclick = function() {
     myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

};
document.querySelector("#actions .cancel").onclick = function() {
     myDropzone.removeAllFiles(true);
};
</script>
@endsection
