<div id="date_options" class="modal fade" role="dialog">
     <div class="modal-dialog modal-xl">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Date Options</h4>
               </div>
                         <div class="modal-body">
                              <p>When you choose a date field as a query filter, you have the option to enter a static date in the format shown in the text box or use the "today" keyword. "today" always returns the current date at 12am.</p>
                              <h4>Examples</h4>
                              <div class="list-group">
                                   <span class="list-group-item">
                                   <h4 class="list-group-item-heading">Get Tickets created today</h4>
                                   <p class="list-group-item-text">
                                        <div class="form-inline">
                                             <input type="text" class="form-control" value="Date Created" disabled>
                                             <input type="text" class="form-control" value="between" disabled>
                                             <input type="text" class="form-control" value="today" disabled> and
                                             <input type="text" class="form-control" value="today+1" disabled> 
                                        </div>
                                   </p>
                                   </span>

                                   <span class="list-group-item">
                                   <h4 class="list-group-item-heading">Get overdue tickets</h4>
                                   <p class="list-group-item-text">
                                        <div class="form-inline">
                                             <input type="text" class="form-control" value="Due Date" disabled>
                                             <input type="text" class="form-control" value="<" disabled>
                                             <input type="text" class="form-control" value="today" disabled>
                                        </div>
                                   </p>
                                   </span>

                                   <span class="list-group-item">
                                   <h4 class="list-group-item-heading">Get tickets due in the next 7 days</h4>
                                   <p class="list-group-item-text">
                                        <div class="form-inline">
                                             <input type="text" class="form-control" value="Due Date" disabled>
                                             <input type="text" class="form-control" value="between" disabled>
                                             <input type="text" class="form-control" value="today" disabled> and
                                             <input type="text" class="form-control" value="today+7" disabled>
                                        </div>
                                   </p>
                                   </span>
                              </div>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                         </div>
          </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
     </div>