@extends('layouts.master')

@section('content')
<div class="panel panel-default">
     <div class="panel-heading">
               <h3 class="panel-title">Cancel Ticket</h3>
     </div>
          <div class="panel-body">
               <div class="col-md-6">
                    
                    <form action="/change-control/cancel/{{$ticket->id}}" method="post">
                         <div class="form-group">
                              <label for="cancelled_reason">Reason for canceling ticket</label>
                              <textarea class="form-control" rows="8" placeholder="Please describe the reason for canceling..." name="cancelled_reason"></textarea>
                         </div>
                         <div class="form-group">                    
                              <a href="/change-control/{{$ticket->id}}" type="submit" class="btn btn-default">Back to Ticket</a>
                              <button type="submit" class="btn btn-success">Submit</button>
                         </div>
                    </form>

               </div>
          </div>
</div>
@endsection