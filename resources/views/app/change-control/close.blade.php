@extends('layouts.master')

@section('content')
<div class="panel panel-default">
     <div class="panel-heading">
               <h3 class="panel-title">Close Ticket</h3>
     </div>
          <div class="panel-body">
               <div class="col-md-6">
                    
                    <form action="/change-control/close/{{$ticket->id}}" method="post">
                         <div class="form-group">
                              <div class="col-md-6">
                                   <label for="completed_type">Result</label>
                                   <select class="form-control" name="completed_type">
                                        <option value="imp_successfully">Implemented Successfully</option>
                                        <option value="imp_with_errors">Implemented with Errors</option>
                                   </select>
                              </div>
                         </div>

                         <div class="form-group col-md-12" style="margin-top: 15px;">
                              <label for="completed_notes">Notes</label>
                              <textarea class="form-control" rows="8" placeholder="Any closing remarks?" name="completed_notes"></textarea>
                         </div>
                         <div class="form-group col-md-12">                    
                              <a href="/change-control/{{$ticket->id}}" type="submit" class="btn btn-default">Back to Ticket</a>
                              <button type="submit" class="btn btn-success">Submit</button>
                         </div>
                    </form>

               </div>
          </div>
</div>
@endsection