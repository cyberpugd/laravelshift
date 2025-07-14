<div id="email_work_orders" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
                @if(get_class($ticket) == 'App\Ticket')
                    <form action="/tickets/send-work-order-emails/{{$ticket->id}}" method="post">
               @else
                    <form action="/change-control/send-work-order-emails/{{$ticket->id}}" method="post">
               @endif
                    <div class="modal-header">
                         <h4>Please select the work orders you would like to email</h4>
                    </div>
                    <div class="modal-body">
                         <div class="table-responsive">
                              <table class="table">
                                   <thead>
                                        <tr>
                                             <td></td>
                                             <td>ID</td>
                                             <td>Subject</td>
                                             <td>Assigned To</td>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        @foreach($work_orders as $work_order)
                                             <tr>
                                                  <td><input type="checkbox" name="work_order_id[]" value="{{$work_order->id}}" checked></td>
                                                  <td>{{$work_order->id}}</td>
                                                  <td>{{$work_order->subject}}</td>
                                                  <td>{{$work_order->assignedTo->first_name}} {{$work_order->assignedTo->last_name}}</td>
                                             </tr>
                                        @endforeach
                                   </tbody>
                              </table>
                         </div>
                    </div>
                    <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Send Emails</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
