<div id="apply_wo_template" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
          @if(!$templates->isEmpty())
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Choose template to apply</h4>
               </div>
                @if(get_class($ticket) == 'App\Ticket')
                    <form method="POST" action="/tickets/{{$ticket->id}}/work-order/apply-template">
               @else
                    <form method="POST" action="/change-control/{{$ticket->id}}/work-order/apply-template">
               @endif
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for=""></label>
								   <select name="template" class="selectpicker" data-live-search="true" data-size="15" title="Choose a Template">
									@foreach($templates as $index => $templateType)
									<optgroup label="{{ $index }}">
										 @foreach($templateType as $template)
										 <option value="{{ $template->id }}" data-tokens="{{ $template->name }} {{ $template->template_owner }}">{{ $template->name }} @if($index == 'Shared With Me') ({{ $template->template_owner }}) @endif</option>
										 @endforeach
									</optgroup>
									@endforeach

							   </select>
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Apply Template</button>
                         </div>
               </form>
               @else
                    <div class="modal-body">
                         <p>You don't have any templates created, click <a href="/user-settings/wo-template">here</a> to create one.</p>
                    </div>
                    <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                         </div>
               @endif
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
