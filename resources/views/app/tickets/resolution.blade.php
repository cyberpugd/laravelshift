@extends('layouts.master')

@section('content')
<section class="content-header">
     <h1>Close ticket # {{$ticket->id}}</h1>
</section>
<section class="content">
<div class="panel panel-default">
          <div class="panel-body">
               <div class="col-lg-6">
                    <form action="/tickets/close/{{$ticket->id}}" method="POST" class="form-horizontal">
                         {{csrf_field()}}
                         <div class="form-group">
                              <strong>Subject:</strong> {{$ticket->title}}
                         </div>
                         <div class="form-group">
                                         <label>Description:</label>
                                        <div id="ticketDescription" class="well">
                                             {!! nl2br(htmlentities($ticket->description)) !!}
                                        </div>
                         </div>
                         <div class="form-group">
                              <label for="resolution">Please provide a resolution</label>
                              <textarea name="resolution" class="form-control" placeholder="Please provide the resolution" rows="10" required>{{$ticket->resolution}}</textarea>
                         </div>
                         <div class="form-group">
                              <button type="submit" class="btn btn-success">Close Ticket</button>
                         </div>
                    </form>
               </div>
          </div>
     </div>
</section>
@endsection
