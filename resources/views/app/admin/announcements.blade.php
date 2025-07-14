@extends('layouts.master')

@section('content')
@include('app.components.modals.create_announcement')
@include('app.components.modals.edit_announcement')
<section class="content-header">
          <span style="font-size: 24px;">Announcements</span>
          <span class="btn btn-success form-group" data-toggle="modal" data-target="#create_announcement" style="float:right;">Create Announcement</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body table-responsive">
          <table class="table table-striped">
               <thead>
                    <tr>
                         <td>Status</td>
                         <td>Type</td>
                         <td>Location</td>
                         <td>Title</td>
                         <td>Start Date</td>
                         <td>End Date</td>
                    </tr>
               </thead>
               <tbody>
                    @foreach($announcements as $announcement)
                         <tr>
                              <td>@if(Carbon\Carbon::now() >= $announcement->start_date && Carbon\Carbon::now() <= $announcement->end_date) Active @else @if(Carbon\Carbon::now() < $announcement->start_date) Pending @endif @if(Carbon\Carbon::now() > $announcement->end_date) Expired @endif @endif</td>
                              <td>{{$announcement->type}}</td>
                              <td>{{deSnake($announcement->location)}}</td>
                              <td>{{$announcement->title}}</td>
                              <td>{{$announcement->start_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</td>
                              <td>{{$announcement->end_date->setTimezone(Auth::user()->timezone)->toDayDateTimeString()}}</td>
                              <td>
                              <div class="pull-right">
                              <div class="col-md-2">
                                   <a class="btn btn-default btn-xs editAnnouncement" 
                                        data-toggle="modal" 
                                        data-target="#edit_announcement"
                                        data-id="{{$announcement->id}}"
                                        data-title="{{$announcement->title}}"
                                        data-message="{{$announcement->details}}"
                                        data-startdate="{{$announcement->start_date->setTimezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}"
                                        data-enddate="{{$announcement->end_date->setTimezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}"
                                        data-type="{{$announcement->type}}"
                                        data-location="{{$announcement->location}}"
                                        ><i class="fa fa-pencil"></i></a>
                              </div>
                              <div class="col-md-2">
                                   <form method="post" action="/admin/announcements/delete/{{$announcement->id}}">
                                        {!! csrf_field() !!}
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                   </form>
                              </div>
                              <div class="col-md-2">
                                   @if(Carbon\Carbon::now() < $announcement->end_date)
                                        <form method="post" action="/admin/announcements/expire/{{$announcement->id}}">
                                             {!! csrf_field() !!}
                                             <button type="submit" class="btn btn-default btn-xs">Expire</button>
                                        </form>
                                   @endif
                              </div>
                              </div>
                              </td>
                         </tr>
                    @endforeach
               </tbody>
          </table>
          {!! $announcements->render() !!}
     </div>      
</div>    
</section>
@endsection
@section('footer')
<script>
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

      jQuery('#edit_start_date').datetimepicker({
               onShow:function( ct ){
                    var endDate = moment(jQuery('#edit_end_date').val()).format('YYYY/MM/DD h:mm a');
                    var endTime = moment(jQuery('#edit_end_date').val()).format('hh:mm');
                    this.setOptions({
                         maxDate:(jQuery('#edit_end_date').val()?endDate:false),
                    })
               },
               format:'m/d/Y g:i a',
               formatTime: 'g:ia',
               timepicker:true
          });
          jQuery('#edit_end_date').datetimepicker({
               onShow:function( ct ){
                    var startDate = moment(jQuery('#edit_start_date').val()).format('YYYY/MM/DD h:mm a');
                    var startTime = moment(jQuery('#edit_start_date').val()).format('h:mm');
                    this.setOptions({
                         minDate:(jQuery('#edit_start_date').val()?startDate:false),
                    })
               },
               format:'m/d/Y g:i a',
               formatTime: 'g:ia',
               timepicker:true
          });

      $('.editAnnouncement').on("click", function() {
          var announcementID = $(this).data('id');
          var title = $(this).data('title');
          var type = $(this).data('type');
          var location = $(this).data('location');
          var message = $(this).data('message');
          var startdate = $(this).data('startdate');
          var enddate = $(this).data('enddate');
          console.log(message);
          $(".modal-body #type").val( type );
          $(".modal-body #location").val( location );
          $(".modal-body #title").val( title );
          $(".modal-body #message").val( message );
          $(".modal-body #edit_start_date").val( startdate );
          $(".modal-body #edit_end_date").val( enddate );
          $(".modal-content #editAnnouncementForm").attr("action", "/admin/announcements/edit/" + announcementID);

     });
</script>
@endsection