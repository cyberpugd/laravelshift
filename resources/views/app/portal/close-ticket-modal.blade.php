<div class="modal fade" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Close Ticket #{{$ticket->id}}</h4>
            </div>
            <form action="/helpdesk/tickets/{{$ticket->id}}/close" method="POST">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="resolution">Please provide a resolution</label>
                        <textarea name="resolution" class="form-control" placeholder="Please provide the resolution" rows="10">{{$ticket->resolution}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Close Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
