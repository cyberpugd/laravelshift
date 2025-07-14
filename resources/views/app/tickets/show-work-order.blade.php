@extends('layouts.master')

@section('content')
<section class="content-header">
     <h3 class="panel-title">Work Order #{{$work_order->id}}</h3>
</section>
<section class="content">
    @include('app.partials.errors')
<form action="/tickets/work-order/{{$work_order->id}}" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <div id="app" class="panel panel-default">
          <div class="panel-body">
               <!-- Nav tabs -->
               <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#wo-details" aria-controls="public" role="tab" data-toggle="tab" @click="showButtons = true">Details</a></li>
                    <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab" @click="showButtons = false">Attachments <span id="attachmentbadge" class="badge" style="background-color: #337ab7;">{{$attachments->count()}} </span><span id="success" style="color:#82BE5A;"></span></a></li>
                    <li role="presentation"><a href="#history" aria-controls="public" role="tab" data-toggle="tab" @click="showButtons = true">History</a></li>
               </ul>
               <div class="tab-content">
                    <!-- TICKET DETAILS TAB -->
                    <div role="tabpanel" class="tab-pane active" id="wo-details">
                         <div class="panel-body">
                              <div class="child-div-padding col-md-6">
                                   <div>
                                        <label>Ticket:</label>
                                        @if($work_order->ticketable_type == 'ChangeTicket')
                                        <a href="/change-control/{{$work_order->ticketable_id}}">#{{$work_order->ticketable_id}}</a>
                                        @else
                                        <a href="/tickets/{{$work_order->ticketable_id}}">#{{$work_order->ticketable_id}}</a>
                                        @endif
                                   </div>
                                   <div class="col-md-12">
                                        @if($work_order->ticketable_type == 'ChangeTicket')
                                             @if($work_order->ticketable->canEdit())
                                                  @if($work_order->status != 'closed')
                                                       <label>Subject:</label>
                                                       <input name="subject" class="form-control" value="{{$work_order->subject}}">
                                                  @else
                                                       <label>Subject:</label>
                                                       {{$work_order->subject}}
                                                  @endif
                                                  @else
                                                       <label>Subject:</label>
                                                       {{$work_order->subject}}
                                                  @endif
                                             @else
                                             <label>Subject:</label>
                                             {{$work_order->subject}}
                                        @endif
                                   </div>

                                   <div class="col-md-12">

                                        @if($work_order->status == 'closed')
                                        <label for="due_date">Completed On:</label> {{$work_order->completed_date->format('m/d/Y g:i a')}}
                                        @endif
                                   </div>
                              </div>
                              <div class="child-div-padding col-md-3 form-group" style="margin-top: 15px;">
                                   <div>
                                        <label>Status:</label>
                                        @if($work_order->status == 'open')
                                        @if(old('status'))
                                             <select name="status" class="selectpicker form-control">
                                                  <option value="open" @if(old('status') == 'open') selected @endif>Open</option>
                                                  <option value="closed" @if(old('status') == 'closed') selected @endif>Closed</option>
                                             </select>
                                        @else
                                             <select name="status" class="selectpicker form-control">
                                                  <option value="open" @if($work_order->status == 'open') selected @endif>Open</option>
                                                  <option value="closed" @if($work_order->status == 'closed') selected @endif>Closed</option>
                                             </select>
                                        @endif
                                        @else
                                        {{ucfirst($work_order->status)}} <br>
                                        @endif
                                   </div>

                                   <div>
                                        <label>Assigned To:</label>
                                        @if($work_order->status == 'open') <br>
                                        <select name="assigned_to" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select an Agent">
                                             @foreach($users as $user)
                                             <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($work_order->assigned_to == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                             @endforeach
                                        </select>
                                        @else
                                        {{$work_order->assignedTo->first_name}} {{$work_order->assignedTo->last_name}}<br>
                                        @endif
                                   </div>
                                   <div>
                                        <label>Due On:</label>
                                        @if($work_order->status == 'open')
                                        <input id='wo-due-date' type='text' autocomplete="off" class="form-control" name="due_date" value="{{$work_order->due_date->setTimezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}" />
                                        @else

                                        {{$work_order->due_date->format('m/d/Y g:i a')}}

                                        @endif
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   @if($work_order->ticketable_type == 'ChangeTicket')
                                   @if($work_order->ticketable->canEdit())
                                   @if($work_order->status != 'closed')
                                   <label>Work Requested:</label>
                                   <textarea name="work_requested" class="form-control">{{$work_order->work_requested}}</textarea>
                                   @else
                                   <label>Work Requested:</label>
                                   <div class="well">
                                        {!! nl2br(htmlentities($work_order->work_requested)) !!}
                                   </div>
                                   @endif
                                   @else
                                   <label>Work Requested:</label>
                                   <div class="well">
                                        {!! nl2br(htmlentities($work_order->work_requested)) !!}
                                   </div>
                                   @endif
                                   @else
                                   <label>Work Requested:</label>
                                   <div class="well">
                                        {!! nl2br(htmlentities($work_order->work_requested)) !!}
                                   </div>
                                   @endif
                              </div>
                              <div class="col-md-12" style="padding-top: 20px;">
                                   <label>Work Completed:</label>
                                   @if($work_order->status == 'open')
                                   <div>
                                        <textarea class="form-control" rows="7" name="work_completed">{{$work_order->work_completed}}</textarea>
                                   </div>
                                   @else
                                   <div class="well">
                                        {!! nl2br(htmlentities($work_order->work_completed)) !!}
                                   </div>
                                   @endif
                              </div>
                         </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="attachments">
                         <div class="panel-body">
                              <p>Click Add files or drag files into browser to start uploading</p>
                              <div id="actions" class="row">

                                   <div class="form-inline col-lg-7">
                                        <!-- The fileinput-button span is used to style the file input field as button -->

                                             <a class="btn btn-success fileinput-button">
                                                  <i class="fa fa-plus"></i>
                                                  <span>Add files...</span>
                                                  </a>
                                             <a type="submit" class="btn btn-primary start">
                                                  <i class="fa fa-upload"></i>
                                                  <span>Upload all</span>
                                             </a>
                                             <a type="reset" class="btn btn-warning cancel">
                                                  <i class="fa fa-ban"></i>
                                                  <span>Cancel upload</span>
                                             </a>
                                   </div>

                                   <div class="col-lg-5">
                                        <!-- The global file processing state -->
                                        <span class="fileupload-process">
                                             <div id="total-progress" class="progress progress-striped active" style="display: none;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
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
                                             <span data-dz-remove class="delete successfulUpload"  style="display: none;">
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
                                                  <td></td>
                                             </tr>
                                        </thead>
                                        @foreach($attachments as $attachment)
                                        <tr class="file-row">
                                             <td>
                                                  @if($work_order->ticketable_type == 'Ticket')
                                                  <p class="name"><a href="/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
                                                  @else
                                                  <p class="name"><a href="/cc/attachments/{{$attachment->file}}" target="_blank">{{$attachment->file_name}}</a></p>
                                                  @endif
                                             </td>
                                             <td>
                                                  <p>{{$attachment->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</p>
                                             </td>
                                             <td>
                                                  <p class="size"><strong>{{number_format($attachment->file_size, 1)}} KB</strong></p>
                                             </td>
                                        </tr>
                                        @endforeach
                                   </table>
                              </div>
                         </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="history">
                        <div class="panel-body">
                        @foreach($histories as $history)
                         @if($history->key == 'created_at' && !$history->old_value)
                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} created this work order on <strong style="color: #696969">{{  Carbon\Carbon::parse($history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}</strong></li>
                         @else
                         @if(strpos($history->key, 'date') )
                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }}
                                changed <strong style="color: #696969">{{ $history->fieldName() }}</strong>
                                from <strong style="color: #696969">
                                        @if(!$history->oldValue()) Nothing @else {{ Carbon\Carbon::parse($history->oldValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}
                                        @endif
                                    </strong>
                                to <strong style="color: #696969">
                                    @if(!$history->newValue()) Nothing @else {{ Carbon\Carbon::parse($history->newValue())->timezone(Auth::user()->timezone)->format('m/d/Y g:i A') }}
                                    @endif
                                   </strong></li>
                             @else
                             <li>{{ $history->userResponsible()->first_name }} {{ $history->userResponsible()->last_name }} changed <strong style="color: #696969">{{ $history->fieldName() }}</strong> from <strong style="color: #696969">{{ $history->oldValue() }}</strong> to <strong style="color: #696969">{{ $history->newValue() }}</strong></li>
                             @endif
                             @endif
                             @endforeach
                        </div>
                   </div>
               </div>

          </div>
          <div class="panel-footer">
               <div class="pull-right">
                    @if($work_order->ticketable_type == 'ChangeTicket')
                    <a href="/change-control/{{$work_order->ticketable_id}}" class="btn btn-default">Back to Ticket</a>
                    <a href="/change-control/work-orders" class="btn btn-default">Back to My Work Orders</a>
                    @endif
                    @if($work_order->ticketable_type == 'Ticket')
                    <a href="/tickets/{{$work_order->ticketable_id}}" class="btn btn-default">Back to Ticket</a>
                    <a href="/tickets/work-orders" class="btn btn-default">Back to My Work Orders</a>
                    @endif
                    @if($work_order->status == 'open')
                    <button type="submit" class="btn btn-success" v-show="showButtons">Save</button>
                    @else
                    <a id="open" data-woid="{{$work_order->id}}" class="btn btn-primary" v-show="showButtons">Re-open</a>
                    @endif
               </div>
               <div class="clearfix"></div>
          </div>

     </div>
</form>
</section>
@endsection
@section('footer')
<script src="/js/dropzone.js"></script>
<script>
     new Vue({
          el: '#app',
          data: {
               showButtons: true,
          },
          methods: {

          }
     });
     $('#wo-due-date').datetimepicker({
          format: 'm/d/Y g:i a',
          formatTime: 'g:ia'
     });

     $('#open').click(function() {
          woid = $('#open').data('woid');
          $.ajax({
               type: 'POST',
               url: '/tickets/work-order/open/'+woid,
               success: function(data){
                    window.location.reload();
               },
               error: function(data) {
               }
          });
     })

     $(document).ready( function() {
// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
@if($work_order->ticketable_type == 'Ticket')
url: "/tickets/{{$work_order->ticketable->id}}/work-order/{{$work_order->id}}/attachments", // Set the url
@else
url: "/change-control/{{$work_order->ticketable->id}}/work-order/{{$work_order->id}}/attachments",
@endif
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
     @if($work_order->ticketable_type == 'Ticket')
     $('#dropZoneTable tr:first').after('<tr> <td><p class="name"><a href="/attachments/'+response.file+'" target="_blank">'+response.file_name+'</a></p></td><td><p>Now</p></td><td> <p class="size"><strong>'+response.file_size.toPrecision(3)+' KB</strong></p></td></tr>');
     @else
     $('#dropZoneTable tr:first').after('<tr> <td><p class="name"><a href="/cc/attachments/'+response.file+'" target="_blank">'+response.file_name+'</a></p></td><td><p>Now</p></td><td> <p class="size"><strong>'+response.file_size.toPrecision(3)+' KB</strong></p></td></tr>');
     @endif
     $('.successfulUpload').show();
});

myDropzone.on("addedfile", function(file) {
// Hookup the start button
file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
$('#filesReady').show();
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
     $('#total-progress').show();
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
</script>
@endsection
