<div id="create_announcement" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Announcement</h4>
               </div>
               <form method="POST" action="/admin/announcements/create">
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="name">Type</label>
                                   <select class="form-control" name="type">
                                        <option value="info">Information</option>
                                        <option value="warning">Warning</option>
                                        <option value="danger">Danger</option>
                                   </select>
                              </div>
                              <div class="form-group">
                                   <label for="name">Location</label>
                                   <select class="form-control" name="location">
                                        <option value="agents">Agents</option>
                                        <option value="end_users">End Users</option>
                                        <option value="both">Both</option>
                                   </select>
                              </div>
                              <div class="form-group">
                                   <label for="name">Title</label>
                                   <input type="text" name="title" class="form-control" value="">
                              </div>
                              <div class="form-group">
                                   <label for="name">Message</label>
                                   <textarea class="form-control" name="details" rows="5"></textarea>
                              </div>
                              <div class="form-group">
                                   <label for="date">Start Date</label>
                                   <div class='input-group date col-md-5'>
                                        <input id="start_date" type='text' class="form-control" name="start_date" value="" required />
                                    </div>
                              </div>
                              <div class="form-group">
                                   <label for="date">End Date</label>
                                   <div class='input-group date col-md-5'>
                                        <input id="end_date" type='text' class="form-control" name="end_date" value="" required />
                                    </div>
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Create</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
