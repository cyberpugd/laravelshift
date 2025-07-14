<div id="show-status-notes" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Status Notes</h4>
               </div>
                         <div class="modal-body">
                         @if($ticket->status == 'completed')
                              @if($ticket->completed_type == 'imp_successfully')
                                   <h4>Implemented Successfully</h4>
                              @endif
                              @if($ticket->completed_type == 'imp_with_errors')
                                   <h4>Implemented with Errors</h4>
                              @endif
                              <p>{{$ticket->completed_notes}}</p>
                         @endif
                         @if($ticket->status == 'cancelled')
                              <p>{{$ticket->cancelled_reason}}</p>
                         @endif
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                         </div>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
