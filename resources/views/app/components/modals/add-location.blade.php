<div id="add_location" class="modal fade" role="dialog">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Location</h4>
               </div>
               <form method="POST" action="/admin/locations/add">
                    {!! csrf_field() !!}
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="name">City</label>
                                   <input type="text" name="city" class="form-control" value="" autofocus>
                              </div>
                         </div>
                         <div class="modal-body">
                              <div class="form-group">
                                   <label for="timezone">Timezone</label>
                                   <select name="timezone" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select a timezone">
                                        @foreach($timezones as $key => $value)
                                             <option value="{{$key}}" data-tokens="{{$value}}" @if(old('timezone') == $key) selected @endif>{{$value}}</option>
                                        @endforeach
                                   </select> 
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Add Location</button>
                         </div>
               </form>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
     </div>