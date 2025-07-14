<div id="add_holiday" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Holiday</h4>
               </div>
               <form method="POST" action="/admin/holiday/add">
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="name">Name</label>
                                   <input type="text" name="name" class="form-control" value="" autofocus>
                              </div>
                              <div class="form-group">
                                   <label for="date">Date</label>
                                   <div class='input-group date col-md-5'>
                                        <input type='text' class="form-control holiday-date" name="date" value="" required />
                                    </div>
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Add</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
     </div>