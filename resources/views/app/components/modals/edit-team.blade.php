<div id="edit_team" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Team</h4>
               </div>
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="name">Team Name</label>
                                   <input class="form-control" type="text" v-model="team.name">
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary" @click="saveTeamName" data-dismiss="modal">Save</button>
                         </div>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
     </div>