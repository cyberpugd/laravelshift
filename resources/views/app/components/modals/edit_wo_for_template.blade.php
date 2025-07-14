<div id="edit_wo_for_template" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Work Order</h4>
               </div>
               <form method="POST" action="/user-settings/wo-detail/update/@{{wo_id}}">
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="subject">Subject</label>
                                   <input id="subject" type="text" name="subject" class="form-control" value="@{{subject}}" autofocus required>
                              </div>
                              <div class="form-group">
                                   <label for="work_requested">Work Requested</label>
                                   <textarea name="work_requested" class="form-control" rows="10" required>@{{workRequested}}</textarea>
                              </div>
                              <div class="form-group">
                                   <label for="assigned_to">Assigned To</label><br>
                                    <select name="assigned_to" class="form-control" title="Assigned To" v-model="assignedTo" required>
                                        @foreach($users as $user)
                                             @if($user->can('be_assigned_ticket'))
                                                  <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}">{{$user->last_name}}, {{$user->first_name}}</option>
                                             @endif
                                        @endforeach
                                   </select> 
                              </div>
                              <div class="form-group">
                                   <label for="due_date">Due On</label>
                                   <div class='input-group date col-md-5'>
                                        <select name="due_in" class="form-control" v-model="dueOn" required>
                                                       <option value="-1">Ticket Due Date</option>
                                             @for($i = 0; $i <= 20; $i++)
                                                  @if($i == 0)
                                                       <option value="{{$i}}">Ticket Create Date</option>
                                                  @else
                                                       <option value="{{$i}}">{{$i}}@if($i > 1) days after ticket created @else day after ticket created @endif</option>
                                                  @endif
                                             @endfor
                                        </select>
                                    </div>
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Save</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
