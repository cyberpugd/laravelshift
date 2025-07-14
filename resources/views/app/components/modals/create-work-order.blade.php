<div id="create_work_order" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Work Order</h4>
               </div>
                @if(get_class($ticket) == 'App\Ticket')
                    <form method="POST" action="/tickets/{{$ticket->id}}/work-order/create">
               @else
                    <form method="POST" action="/change-control/{{$ticket->id}}/work-order/create">
               @endif
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="subject">Subject</label>
                                   <input id="subject" type="text" name="subject" class="form-control" value="" autofocus required>
                              </div>
                              <div class="form-group">
                                   <label for="work_requested">Work Requested</label>
                                   <textarea name="work_requested" class="form-control" value="" rows="10" required></textarea>
                              </div>
                              <div class="form-group">
                                   <label for="assigned_to">Assigned To</label><br>
                                    <select name="assigned_to" class="selectpicker" data-live-search="true" data-size="15" title="Assigned To" required autofocus>
                                        @foreach($agents as $user)
                                                  <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if(Auth::user()->id == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                        @endforeach
                                   </select> 
                              </div>
                              <div class="form-group">
                                   <label for="due_date">Due On</label>
                                   <div class='input-group date col-md-5'>
                                   @if(get_class($ticket) == 'App\Ticket')
                                        <input id="wo_due_date" type='text' class="form-control" name="due_date" value="{{$ticket->due_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}" required />
                                   @else
                                        <input id="wo_due_date" type='text' class="form-control" name="due_date" value="{{$ticket->end_date->timezone(Auth::user()->timezone)->format('m/d/Y g:i a')}}" required />
                                   @endif
                                    </div>
                              </div>
                              @if(get_class($ticket) == 'App\ChangeTicket')
                                   @if($ticket->status == 'in-progress')
                                   <div class="form-group">
                                        <input type="checkbox" name="send_email">
                                        <label for="work_requested">Send notification email</label>
                                   </div>
                                   @endif
                              @else
                                   <div class="form-group">
                                        <input type="checkbox" name="send_email">
                                        <label for="work_requested">Send notification email</label>
                                   </div>
                              @endif
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-success">Create Work Order</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

