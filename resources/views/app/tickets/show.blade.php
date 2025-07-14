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
<div id="ticket-details" v-cloak>
     <section class="content-header">
          <span style="font-size: 24px;">Ticket #{{$ticket->id}}</span>
          <div class="btn-group pull-right">
               @if($ticket->agent_id != Auth::user()->id && $ticket->status == 'open')
               <a class="btn btn-default btn-sm" v-on:click="assignToMe">Assign to Me</a>
               @endif
               <a href="/tickets/print/{{$ticket->id}}" class="btn btn-default btn-sm" title="Print Ticket" target="_blank"><i class="fa fa-print"></i></a>
               @if($ticket->status == 'open')
               <a id="editTicket" v-show="!editMode" class="btn btn-default btn-sm" v-on:click="toggleEditMode" style="display: inline-block;" title="Edit Ticket"><i class="fa fa-pencil"></i></a>
               <button v-show="editMode" class="btn btn-default btn-sm" v-on:click="toggleEditMode" style="display: inline-block;" title="Cancel Edit"><i class="fa fa-ban"></i></button>
               @endif
               <button type="submit" class="btn btn-success btn-sm" v-show="editMode" v-on:click="saveTicket"><i class="fa fa-floppy-o"></i> Save</button>
               @if($ticket->status == 'open')
               @can('close_ticket')
               <a href="/tickets/resolution/{{$ticket->id}}" class="btn btn-danger btn-sm">Close Ticket</a>
               @endcan
               @endif
               @if($ticket->status == 'closed')
               <form action="/tickets/open/{{$ticket->id}}" method="POST" style="display: inline-block;">
                    @can('reopen_ticket')
                    <button class="btn btn-success btn-sm">Re-open Ticket</button>
                    @endcan
               </form>
               @endif
          </div>
     </section>
     <section class="content">
          <form id="assignToMe" action="/tickets/assignToMe/{{$ticket->id}}" method="post"></form>
          <div class="panel panel-default">
               <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                         <li class="active"><a href="#ticket_details" data-toggle="tab">Ticket Details</a></li>
                         <li><a href="#work_orders" data-toggle="tab">Work Orders <span class="badge" style="background-color: #337ab7;">{{$work_orders->count()}}</span></a></li>
                         <li><a href="#attachments" data-toggle="tab">Attachments <span id="attachmentbadge" class="badge" style="background-color: #337ab7;">{{$attachments->count()+$woattachments->count()}} </span><span id="success" style="color:#82BE5A;"></span></a></li>
                         <li><a href="#conversation" data-toggle="tab">Conversation <span class="badge" style="background-color: #337ab7;">{{$conversations->count()}} </span> <span class="badge" style="background-color: #82BE5A;">{{$conversations_private->count()}} </span></a></li>
                         <li><a href="#history" data-toggle="tab">History Log</a></li>
                    </ul>
               </div>

               <div class="panel-body">
                    <div class="tab-content">
                         <div class="tab-pane active" id="ticket_details">
                              <form id="ticketForm" action="/tickets/{{$ticket->id}}" method="post">
                                   {!!csrf_field()!!}
                                   <div class="col-md-12">
                                        <div class="col-md-6 child-div-padding">
                                             <div class="col-sm-12"><strong>Ticket No:</strong> {{$ticket->id}}</div>
                                             <div class="col-sm-12" v-show="!editMode"><strong>Category:</strong> {{$ticket->category->name}}</div>
                                             <div class="col-sm-12" v-show="editMode"><strong>Category:</strong>
                                                  <select name="sub_category" class="selectpicker" data-live-search="true" data-size="15" title="Choose a Category">
                                                       @foreach($categories as $category)
                                                       <optgroup label="{{ $category->name }}">
                                                            @foreach($category->subcategories as $subcategory)
                                                            <option value="{{ $subcategory->id }}" data-tokens="{{ $category->name }} {{$subcategory->name}} {{ $subcategory->tags }}" @if($ticket->sub_category_id == $subcategory->id) selected @endif>{{ $subcategory->name }}</option>
                                                            @endforeach
                                                       </optgroup>
                                                       @endforeach

                                                  </select>
                                             </div>
                                             <div class="col-sm-12" v-show="!editMode"><strong>Subcategory:</strong> {{$ticket->subcategory->name}}</div>

                                             <div class="col-sm-12" v-show="editMode"><strong>Urgency:</strong>
                                                  <select name="urgency" class="selectpicker" title="Select Severity" data-width="fit">
                                                       @foreach($urgencyrows as $urgency)
                                                       <option value="{{$urgency->id}}" @if($ticket->urgency->id == $urgency->id) selected @endif>
                                                            {{$urgency->name}} - {{$urgency->description}}
                                                       </option>
                                                       @endforeach
                                                  </select>
                                             </div>
                                             <div class="col-sm-12" v-show="!editMode">
                                                <strong>Urgency:</strong> {{$ticket->urgency->name}}
                                                @if($ticket->urgency->name == 'Critical')
                                                    <i class="fa fa-info-circle"
                                                        style="color: #dd4b39;"
                                                        title="Please call the Help Desk at {{App\AdminSettings::first()->phone_number}}"></i>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 child-div-padding">
                                             @can('edit_caller')
                                             <div class="col-sm-12" v-show="!editMode">
                                                  @else
                                                  <div class="col-sm-12">
                                                       @endcan
                                                       <strong>Caller:</strong>
                                                       <span id="createdBy"  class="pointer" data-sip="{{$ticket->createdBy->email}}"
                                                            onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->createdBy->email}}', 0, 10, 10) }"
                                                            onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                                            <a style="color: #337ab7;">{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</a>
                                                       </span>
                                                       <i class="fa fa-info-circle" style="color: #337ab7;" title="City: {{$ticket->createdBy->location->city}} &#013;Phone: {{$ticket->createdBy->phone_number}} &#013;UserId: {{$ticket->createdBy->ad_id}}"></i>
                                                  </div>
                                                  @can('edit_caller')
                                                  <div class="col-sm-12" v-show="editMode">
                                                       <strong>Caller:</strong>
                                                       <select name="caller" class="selectpicker" data-live-search="true" data-live-search-placeholder="Search" data-size="15" title="Select a Caller" autofocus>
                                                            @foreach($users as $caller)
                                                            <option value="{{$caller->id}}" data-tokens="{{$caller->first_name}} {{$caller->last_name}}" @if($ticket->created_by == $caller->id) selected @endif   @if($caller->out_of_office == 1) style="color: #CD5555;" @endif>{{$caller->first_name}} {{$caller->last_name}}@if($caller->out_of_office == 1) (Out of Office) @endif</option>
                                                            @endforeach
                                                       </select>
                                                  </div>
                                                  @endcan
                                                  <div class="col-sm-12" v-show="!editMode">
                                                       <strong>Assigned To:</strong>
                                                       @if($ticket->assignedTo)
                                                       <span id="assignedTo" class="pointer" data-sip="{{$ticket->assignedTo->email}}"
                                                            onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->assignedTo->email}}', 0, 10, 10)}"
                                                            onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                                            <a style="color: #337ab7;">{{$ticket->assignedTo->first_name}} {{$ticket->assignedTo->last_name}}</a>
                                                       </span>
                                                       @else
                                                       <span>Not Assigned</span>
                                                       @endif
                                                  </div>
                                                  <div class="col-sm-12" v-show="editMode">
                                                       <strong>Assigned To:</strong>
                                                       <select name="agent" class="selectpicker" data-live-search="true" data-size="15" title="Select an Agent" autofocus>
                                                            <option value="0">None Selected</option>
                                                            @foreach($agents as $user)
                                                            <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($ticket->agent_id == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                                            @endforeach
                                                       </select>
                                                  </div>

                                                  <div class="col-sm-12">
                                                       <strong>Created On:</strong> {{$ticket->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
                                                  </div>

                                                  <div class="col-sm-12" v-show="!editMode">
                                                       <strong>Due On:</strong> {{$ticket->due_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
                                                  </div>

                                                  <div class="col-sm-6" v-show="editMode">
                                                       <strong>Due On:</strong>
                                                       <input id='datetimepicker1' type='text' class="form-control" autocomplete="off" name="due_date" value="{{$ticket->due_date->setTimezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}">
                                                  </div>

                                                  @if($ticket->status == 'closed')
                                                  <div class="col-sm-12" v-show="!editMode">
                                                       <strong>Closed On:</strong> {{$ticket->close_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
                                                  </div>
                                                  @endif
                                             </div>

                                        </form>
                                        <div class="col-md-12">
                                             <hr>
                                             <div class="child-div-padding">
                                                  <div class="col-md-12">
                                                       <strong>Subject:</strong>

                                                       {{$ticket->title}}
                                                  </div>
                                                  <div class="col-md-12">
                                                       <label>Description:</label>
                                                       <div id="ticketDescription" class="well" style="overflow-wrap: break-word;">
                                                            {!! linkify(nl2br(htmlentities($ticket->description))) !!}
                                                       </div>
                                                  </div>
                                                  @if($ticket->status == 'closed')
                                                  <div class="col-md-12">
                                                       <label>Resolution:</label>
                                                       <div class="well">
                                                            {!! nl2br(htmlentities($ticket->resolution)) !!}
                                                       </div>
                                                  </div>
                                                  @endif
                                             </div>


                                        </div>
                                   </div>
                              </div>

                              <div class="tab-pane" id="work_orders">
                              @can('view_work_orders')
                                   <div class="col-md-12 child-div-padding">
                                        @if($ticket->status == 'open')
                                        @can('create_work_orders')
                                        <div style="float: right;">
                                             @if(!$ticket->workOrders->isEmpty())
                                             <button class="btn btn-default" data-toggle="modal" data-target="#email_work_orders">Email Work Orders</button>
                                             @endif
                                             <button class="btn btn-primary" data-toggle="modal" data-target="#apply_wo_template">Apply Template</button>
                                             <button class="btn btn-success" data-toggle="modal" data-target="#create_work_order">Create New</button>
                                        </div>
                                        @endcan
                                        @endif
                                   </div>
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
                                                  </thead>
                                                  <tbody>
                                                       @foreach($work_orders as $key => $value)
                                                       <tr>
                                                            <td>{{$value->id}}</td>
                                                            <td><a href="/tickets/work-order/{{$value->id}}">{{$value->subject}}</a></td>
                                                            <td>{{ucfirst($value->status)}}</td>
                                                            <td><div class="pointer" id="wo{{$key}}" data-sip="{{$value->assignedTo->sip}}"
                                                                 onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$value->assignedTo->email}}', 0, 10, 10)}"
                                                                 onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                                                                 <a>{{$value->assignedTo->first_name}} {{$value->assignedTo->last_name}}</a></div></td>
                                                                 <td>{{$value->due_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i A')}}</td>
                                                            </tr>
                                                            @endforeach
                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>
                                   @endcan
                                   </div>


                                   <div class="tab-pane" id="attachments">

                                        <p>Click Add files or drag files into browser to start uploading</p>
                                             <div class="col-md-12">

                                                  <div class="col-md-12">
                                                       <br>
                                                       <div id="actions" class="row">

                                                            <div class="form-inline col-lg-7">
                                                                 <!-- The fileinput-button span is used to style the file input field as button -->
                                                                 <div class="form-group">
                                                                      <button class="btn btn-success fileinput-button">
                                                                           <i class="fa fa-plus"></i>
                                                                           <span>Add files...</span>
                                                                      </button>
                                                                 </div>
                                                                 <div class="form-group">
                                                                      <button type="submit" class="btn btn-primary start">
                                                                           <i class="fa fa-upload"></i>
                                                                           <span>Upload all</span>
                                                                      </button>
                                                                 </div>
                                                                 <div class="form-group">
                                                                      <button type="reset" class="btn btn-warning cancel">
                                                                           <i class="fa fa-ban"></i>
                                                                           <span>Cancel upload</span>
                                                                      </button>
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
                                                                      <button class="btn btn-primary btn-xs start">
                                                                           <i class="fa fa-upload"></i>
                                                                           <span>Start</span>
                                                                      </button>
                                                                      <button data-dz-remove class="btn btn-warning btn-xs cancel">
                                                                           <i class="fa fa-ban"></i>
                                                                           <span>Cancel</span>
                                                                      </button>
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
                                                                      </tr>
                                                                 </thead>
                                                                 @foreach($attachments as $attachment)
                                                                 <tr class="file-row">
                                                                      <td>
                                                                           <p class="name"><a href="/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
                                                                      </td>
                                                                      <td>
                                                                           <p>{{$attachment->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                                                                      </td>
                                                                      <td>
                                                                           <p class="size"><strong>{{number_format($attachment->file_size, 1)}} KB</strong></p>
                                                                      </td>
                                                                      <td>Ticket</td>
                                                                 </tr>
                                                                 @endforeach
                                                                 @foreach($woattachments as $attachment)
                                                                 <tr class="file-row">
                                                                      <td>
                                                                           <p class="name"><a href="/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
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
                                                                 </tr>
                                                                 @endforeach
                                                            </table>
                                                       </div>
                                                  </div>

                                             </div>
                                   </div>

                                   <div class="tab-pane" id="conversation">
                                        <!-- Begin Panel Body -->
                                             <!-- Insert public and private tabs -->
                                             <!-- Nav tabs -->
                                             <ul class="nav nav-tabs" role="tablist">
                                                  <li role="presentation" class="active"><a href="#public" aria-controls="public" role="tab" data-toggle="tab">Public</a></li>
                                                  <li role="presentation"><a href="#private" aria-controls="private" role="tab" data-toggle="tab">Private</a></li>
                                             </ul>
                                             <div class="tab-content">
                                                  <!-- PUBLIC TAB CONVERATION PANEL -->
                                                  <div role="tabpanel" class="tab-pane active" id="public">
                                                       <br>
                                                       <form action="/tickets/post-message/{{$ticket->id}}" method="POST">
                                                            {{csrf_field()}}
                                                            <div class="form-group">
                                                                 <textarea v-model="publicMessage" name="message" class="form-control" rows="5" placeholder="{{$placeholder->message}}" required></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                 <button v-show="!postingPublic" type="submit" class="btn btn-success" v-on:click="disablePublic"><i class="fa fa-save"></i> Post</button>
                                                                 <a v-show="postingPublic" class="btn btn-default" disabled><i class="fa fa-spin fa-cog"></i> Posting...</a>
                                                            </div>
                                                       </form>
                                                       @foreach($conversations as $conversation)
                                                       <div class="well">
                                                            <label>Posted by {{ $conversation->created_by }} - {{ $conversation->created_at->setTimezone(Auth::user()->timezone)->diffForHumans()}}</label>
                                                            <label class="pull-right">Source: {{ $conversation->source }}</label>
                                                            <p>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</p>
                                                       </div>
                                                       @endforeach
                                                  </div>
                                                  <!-- PRIVATE TAB CONVERSATION PANEL -->
                                                  <div role="tabpanel" class="tab-pane" id="private">
                                                       <br>
                                                       <form action="/tickets/post-message-private/{{$ticket->id}}" method="POST">
                                                            {{csrf_field()}}
                                                            <div class="form-group">
                                                                 <textarea v-model="privateMessage"  name="message" class="form-control" rows="5" placeholder="This is a private conversation message. Only support agents will see these." required></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                 <button v-show="!postingPrivate" type="submit" class="btn btn-success" v-on:click="disablePrivate"><i class="fa fa-save"></i> Post</button>
                                                                 <a v-show="postingPrivate" class="btn btn-default" disabled><i class="fa fa-spin fa-cog"></i> Posting...</a>
                                                                 <button v-show="!postingPrivateNotify"
                                                                    type="submit"
                                                                    class="btn btn-primary"
                                                                    :disabled="!hasOwner"
                                                                    :title="postTitle"
                                                                    v-on:click.prevent="postPrivateNotify">
                                                                        <i class="fa fa-save"></i>
                                                                        Post & Notify
                                                                 </button>
                                                                 <a v-show="postingPrivateNotify" class="btn btn-default" disabled><i class="fa fa-spin fa-cog"></i> Posting...</a>
                                                            </div>
                                                       </form>
                                                       @foreach($conversations_private as $conversation)
                                                       <div class="well">
                                                            <label>Posted by {{ $conversation->created_by }} on {{ $conversation->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString() }}</label>
                                                            <label class="pull-right">Source: {{ $conversation->source }}</label>
                                                            <p>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</p>
                                                       </div>
                                                       @endforeach
                                                  </div>
                                             </div> <!-- end tab content -->
                                   </div> <!-- End Tab pane conversation -->

                                   <div class="tab-pane" id="history">
                                        @foreach($histories as $history)
                                             @if($history->key == 'created_at' && !$history->old_value)
                                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} created this ticket at <strong style="color: #696969">{{  Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
                                             @else
                                             @if(strpos($history->key, 'date') )
                                             @if($history->oldValue() != null)
                                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->oldValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong> to <strong style="color: #696969">{{ Carbon\Carbon::createFromFormat('m/d/Y g:i A', $history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
                                             @endif
                                             @else
                                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ $history->oldValue() }}</strong> to <strong style="color: #696969">{{ $history->newValue() }}</strong></li>
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
                         el: '#ticket-details',
                         data: {
                              editMode: false,
                            postingPrivate: false,
                              postingPrivateNotify: false,
                              hasOwner: {{($ticket->assignedTo != null && $ticket->assignedTo->id != Auth::user()->id ? 1 : 0)}},
                              postTitle: '{{($ticket->assignedTo != null ? 'Post message and notify ticket owner' : 'Ticket must have a ticket owner to post and notify')}}',
                              postingPublic: false,
                              privateMessage: '',
                              publicMessage: ''
                         },
                         methods: {
                              toggleEditMode: function() {
                                   this.editMode = !this.editMode;
                              },
                              assignToMe: function() {
                                   $('#assignToMe').submit();
                              },
                              saveTicket: function() {
                                   $('#ticketForm').submit();
                              },
                              disablePrivate: function() {
                                   this.postingPrivate = true;
                                   if(this.privateMessage == '') {
                                        this.postingPrivate = false;
                                   }
                              },
                              disablePublic: function() {
                                   this.postingPublic = true;
                                   if(this.publicMessage == '') {
                                        this.postingPublic = false;
                                   }
                              },
                              postPrivateNotify: function() {
                                var self = this;
                                self.postingPrivateNotify = true;
                                this.$http.post('/tickets/post-message-private-notify/{{$ticket->id}}', {'message': self.privateMessage}).then(function() {
                                    window.location.href = '/tickets/{{$ticket->id}}';
                                });
                              }
                         }
                    });
               </script>
               <script src="/js/dropzone.js"></script>
               <script>

                    (function()
                    {
                         var ua = window.navigator.userAgent;
                         var msie = ua.indexOf("MSIE ");

                         if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))
                         {

                         }
else  // If another browser, return 0
{
     $('#internetExplorer').show();
}

return false;
})();

$('#create_work_order').on('shown.bs.modal', function () {
     $('#subject').focus();
})
$('.remove').on('click', function(e){
     button = $(this);
     deleteAttachment(button[0]);
});

function deleteAttachment(button) {
     swal({
          title: "Are you sure?",
          text: "You are about to delete attachment "+$('#'+button.id).closest('.list-group-item').find('a').text(),
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false },
          function(){
               $.ajax({
                    type: "POST",
                    url: "/tickets/attachment/"+button.id+"?"+$.param({"ticket_id": button.dataset.ticket}),
                    success: function(data){

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



$(document).ready( function() {
//Initialize datetime pickers
$('#datetimepicker1').datetimepicker({
     format: 'm/d/Y g:i a',
     formatTime: 'g:ia'
});
$('#wo_due_date').datetimepicker({
     format: 'm/d/Y g:i a',
     formatTime: 'g:ia'
});

$('#create_work_order').on('shown.bs.modal', function () {
     $('#subject').focus();
})
$('.remove').on('click', function(e){
     button = $(this);
     console.log(button);
     deleteAttachment(button[0]);
});


// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
url: "/tickets/{{$ticket->id}}/attachments", // Set the url
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
     $('#dropZoneTable tr:first').after('<tr> <td><p class="name"><a href="/attachments/'+response.file+'" target="_blank">'+response.file_name+'</a></p></td><td><p>Now</p></td><td> <p class="size"><strong>'+response.file_size.toPrecision(3)+' KB</strong></p></td></tr>');
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
});
</script>  <!-- js for the add files area /-->
@endsection
