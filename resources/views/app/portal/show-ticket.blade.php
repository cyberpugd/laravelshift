@extends('layouts.portal')

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
<div id="page">
    @if($ticket->urgency->name == 'Critical')
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span>Critical Ticket</span>
            <p style="margin-top: 15px;">
            Please call the Help Desk at {{App\AdminSettings::first()->phone_number}}
            </p>
        </div>
    @endif
<div style="margin-bottom: 5px;">
<a href="/helpdesk/dashboard" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Home</a>
@if($ticket->status != 'closed')
<button href="/helpdesk/dashboard" class="btn btn-sm btn-default" @click="closeTicket">Close Ticket</button>
@endif
</div>
<div id="ticket-details" class="panel panel-default">
     <div class="panel-heading">
          <div class="row">
               <div class="col-md-12">
                    <div class="col-md-5">
                         <h4 class="panel-title">Ticket Information</h4>
                    </div>
               </div>
          </div>
     </div>
     <div class="panel-body">
          <form action="/tickets/{{$ticket->id}}" method="post">
          {!!csrf_field()!!}
           <div class="col-md-12">
               <div class="col-md-6 child-div-padding">
                         <div class="col-sm-12"><strong>Ticket No:</strong> {{$ticket->id}}</div>
                         <div class="col-sm-12"><strong>Category:</strong> {{$ticket->category->name}}</div>
                         <div class="col-sm-12"><strong>Subcategory:</strong> {{$ticket->subcategory->name}}</div>
                         <div class="col-sm-12"><strong>Urgency:</strong> {{$ticket->urgency->name}}</div>
               </div>

               <div class="col-md-6 child-div-padding">

                         <div class="col-sm-12">
                              <strong>Caller:</strong>
                         <span id="createdBy"  class="pointer" data-sip="{{$ticket->createdBy->email}}"
                              onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->createdBy->email}}', 0, 10, 10) }"
                              onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                              <a style="color: #337ab7;">{{$ticket->createdBy->first_name}} {{$ticket->createdBy->last_name}}</a>
                                   <!--  <a class="btn btn-default btn-sm" href="sip:{{$ticket->createdBy->sip}}" title="{{$ticket->createdBy->sip}}"><i class="fa fa-comment-o"></i></a>&nbsp;
                                   <a class="btn btn-default btn-sm" href="tel:{{$ticket->createdBy->phone_number}}" title="{{$ticket->createdBy->phone_number}}"><i class="fa fa-phone"></i></a>&nbsp;
                                   <a class="btn btn-default btn-sm" href="mailto:{{$ticket->createdBy->email}}" title="{{$ticket->createdBy->email}}"><i class="fa fa-envelope-o"></i></a> -->
                         </span>
                         </div>
                         <div class="col-sm-12">
                              <strong>Assigned To:</strong>
                         @if($ticket->assignedTo)
                         <span id="assignedTo" class="pointer" data-sip="{{$ticket->assignedTo->email}}"
                         onclick="if(nameCtrl) {nameCtrl.ShowOOUI('{{$ticket->assignedTo->email}}', 0, 10, 10)}"
                         onmouseout="if(nameCtrl) {nameCtrl.HideOOUI()}">
                               <a style="color: #337ab7;">{{$ticket->assignedTo->first_name}} {{$ticket->assignedTo->last_name}}</a>
                                 <!--   <a class="btn btn-default btn-sm" href="sip:{{$ticket->assignedTo->sip}}" title="{{$ticket->assignedTo->sip}}"><i class="fa fa-comment-o"></i></a>&nbsp;
                                   <a class="btn btn-default btn-sm" href="tel:{{$ticket->assignedTo->phone_number}}" title="{{$ticket->assignedTo->phone_number}}"><i class="fa fa-phone"></i></a>&nbsp;
                                   <a class="btn btn-default btn-sm" href="mailto:{{$ticket->assignedTo->email}}" title="{{$ticket->assignedTo->email}}"><i class="fa fa-envelope-o"></i></a> -->

                         </span>
                         @else <span v-show="!editMode" >Not Assigned</span> @endif
                         </div>


                         <div class="col-sm-12">
                              <strong>Created On:</strong>
                               {{$ticket->created_at->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
                         </div>

                          <div class="col-sm-12">
                              <strong>Due On:</strong>
                               {{$ticket->due_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}
                         </div>


               </div>
          </div>

          </form>
     </div>
</div>

<div class="panel panel-default">
     <div class="panel-heading">
          <h3 class="panel-title">Ticket Details</h3>
     </div>
     <div class="panel-body">
          <div class="col-md-12">
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

<div id="accordion" class="panel-group">
     <div class="panel panel-default">
          <div class="panel-heading toggle-panel" data-toggle="collapse" data-parent="#accordion" href="#attachments">
               <h3 class="panel-title">Attachments <span id="attachmentbadge" class="badge" style="background-color: #337ab7;">{{$attachments->count()}} </span><span id="success" style="color:#82BE5A;"></span></h3>
          </div>
          <div id="attachments" class="panel-collapse collapse">
               <div class="panel-body">
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
                                                  <td></td>
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
                                   </tr>
                                   @endforeach
                              </table>
                              </div>
                         </div>

                    </div>
               </div>
          </div>
     </div>

     <div class="panel panel-default">
          <div class="panel-heading toggle-panel" data-toggle="collapse" data-parent="#accordion" href="#conversation">
               <h3 class="panel-title">Conversation <span class="badge" style="background-color: #337ab7;">{{$conversations->count()}} </span></h3>
          </div>
          <div id="conversation" class="panel-collapse collapse">
               <div class="panel-body">
                    <!-- Begin Panel Body -->
                    <br>
                    <div class="col-md-12">
                         <div class="col-md-12">
                              <form action="/helpdesk/tickets/post-message/{{$ticket->id}}" method="POST">
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
                                   <label>Posted by {{ $conversation->created_by }} - {{ $conversation->created_at->setTimezone(Auth::user()->timezone)->diffForHumans() }}</label>
                                   <label class="pull-right">Source: {{ $conversation->source }}</label>
                                   <p>{!! linkify(nl2br(htmlentities($conversation->message))) !!}</p>
                              </div>
                              @endforeach
                         </div>
                    </div>
                    <!-- End Panel Body -->
               </div>
          </div>
     </div>
</div>
</div>
@include('app.portal.close-ticket-modal')

@endsection

@section('footer')
<script>
     new Vue({
     el: '#page',
     data: {
          postingPublic: false,
          publicMessage: ''
     },
     methods: {
          disablePublic: function() {
               this.postingPublic = true;
               if(this.publicMessage == '') {
                    this.postingPublic = false;
               }
          },
          closeTicket: function(id) {
            $('#closeModal').modal('show');
          }
     }
});
</script>

<script src="/js/dropzone.js"></script>
<script>

     $(document).ready( function() {
          //Initialize datetime pickers
          $('#datetimepicker1').datetimepicker();

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
url: "/helpdesk/tickets/{{$ticket->id}}/attachments", // Set the url
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
