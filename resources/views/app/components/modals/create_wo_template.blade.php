<div id="create_wo_template" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Template</h4>
               </div>
               <form method="POST" action="/user-settings/create-wo-template">
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="name">Template Name</label>
                                   <input type="text" name="name" class="form-control" value="" autofocus>
                              </div>
                              <div class="form-group">
                                   <label for="share_with">Share With</label><br>
                                    <select name="shared_with[]" class="selectpicker" data-live-search="true" data-size="15" title="Share With" autofocus multiple>
                                        @foreach($users as $user)
                                             @if($user->can('be_assigned_ticket'))
                                                  <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}">{{$user->last_name}}, {{$user->first_name}}</option>
                                             @endif
                                        @endforeach
                                   </select> 
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
