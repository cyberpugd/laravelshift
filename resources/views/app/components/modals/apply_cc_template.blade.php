<div id="choose_template" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               @if(!$myTemplates->isEmpty())
                    <form action="/change-control/apply-template" method="post">
                    <div class="modal-header">
                         <h4>Please select the template you wish to apply</h4>
                    </div>
                    <div class="modal-body">
                         <div class="table-responsive">
                              <table class="table">
                                   <thead>
                                        <tr>
                                             <td></td>
                                             <td><strong>Template Name</strong></td>
                                             <td><strong>Created By</strong></td>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        @foreach($myTemplates as $template)
                                             <tr>
                                                  <td><input type="radio" name="template" value="{{$template->id}}"></td>
                                                  <td>{{$template->name}}</td>
                                                  <td>{{$template->owner->first_name}} {{$template->owner->last_name}}</td>
                                             </tr>
                                        @endforeach
                                   </tbody>
                              </table>
                         </div>
                    </div>
                    <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Apply</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                         </div>
               </form>
               @else
                    <div class="modal-header"></div>
                    <div class="modal-body">
                         <p>You do not have any templates created. To create a template click <a href="/user-settings/cc-template">here</a>.</p>
                    </div>
                    <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                         </div>
               @endif
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
