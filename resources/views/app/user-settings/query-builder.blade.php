@extends('layouts.master')
@section('content')
@include('app.components.modals.date_options')
 <div class="panel panel-default">
               <div class="panel-heading">
                    <h3 class="panel-title">IFS EnR Help Desk Query Builder</h3>
               </div>
               <div id="app" class="panel-body">
                    <div class="row">
                    <div class="col-md-2">
                         <div class="form-group">
                              <label for="query_type">Query Type</label>
                              <select class="selectpicker" v-model="queryType" title="Choose Type">
                                   <option value="ticket" @if(old('query_type') == 'ticket') selected @endif>Ticket</option>
                                   <option value="change_control" @if(old('query_type') == 'change_ticket') selected @endif>Change Control</option>
                                   <option value="ticket_work_order" @if(old('query_type') == 'ticket_work_order') selected @endif>Work Order</option>
                              </select>
                         </div>
                    </div>
                    </div>
                    <!-- Ticket Query -->
                    <div v-show="ticket">
                    <form id="ticket_form" action="/user-settings/create-view/ticket" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="query_type" value="ticket">
                         <div class="row">
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="name">Query Name</label>
                                        <input type="text" name="name" class="form-control" value="{{old('name')}}" required>
                                   </div>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns1">Available Columns</label>
                                        <select id="ticket_available_columns" class="form-control" size="10" multiple>
                                             @foreach($ticket_columns as $key => $value)
                                                  <option value="{{$key}}">{{$key}}</option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                              <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="ticket_add" class="btn btn-success btn-sm"><i class="fa fa-arrow-right"></i></button>
                                   <br><br><br>
                                   <button id="ticket_remove" class="btn btn-danger btn-sm"><i class="fa fa-arrow-left"></i></button>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns">Selected Columns</label>
                                        <select id="ticket_selected_columns" name="select_columns[]" class="form-control" size="10" multiple required>
                                            @if(old('select_columns'))
                                                 @foreach(old('select_columns') as $value)
                                                       <option value="{{$value}}">{{$value}}</option>
                                                 @endforeach
                                             @endif
                                        </select>
                                   </div>
                              </div>
                               <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="ticket_order_up" class="btn btn-default btn-sm ticket_order"><i class="fa fa-arrow-up"></i></button>
                                   <br><br><br>
                                   <button id="ticket_order_down" class="btn btn-default btn-sm ticket_order"><i class="fa fa-arrow-down"></i></button>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-md-12">
                              <div class="form-group form-inline">
                                   <label for="filter">Filters</label>
                                   <a class="btn btn-danger btn-xs" @click="removeFilter"><i class="fa fa-minus-square"></i></a>
                                   <a class="btn btn-success btn-xs" @click="addFilter"><i class="fa fa-plus-square"></i></a>
                                   <span><a data-toggle="modal" data-target="#date_options" style="cursor:pointer;">Click here for date options</a></span>
                                    <div class="col-md-12" v-if="filterCount ==0">
                                        <p style="font-size: 16px;">None Selected</p>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=1">
                                        <!-- Insert Component here -->
                                        <filter-criteria></filter-criteria>
                                        <span v-if="filterCount >=2">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=2">
                                        <filter-criteria></filter-criteria>
                                        <span v-if="filterCount >=3">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=3">
                                        <filter-criteria></filter-criteria>
                                        <span v-if="filterCount >=4">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=4">
                                        <filter-criteria></filter-criteria>
                                        <span v-if="filterCount >=5">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=5">
                                        <filter-criteria></filter-criteria>
                                   </div>
                              </div>
                              </div>
                         </div>
                         <hr>
                         <button id="ticket_save" type="submit" class="btn btn-success">Save</button>
                         </form>
                    </div>
                    <!-- End Ticket Query -->
                    <!-- Change Control Query -->
                    <div v-show="changeControl">
                    <form id="change_ticket_form" action="/user-settings/create-view/ticket" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="query_type" value="change_ticket">
                         <div class="row">
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="name">Query Name</label>
                                        <input type="text" name="name" class="form-control" value="{{old('name')}}" required>
                                   </div>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns1">Available Columns</label>
                                        <select id="change_ticket_available_columns" class="form-control" size="10" multiple>
                                             @foreach($change_ticket_columns as $key => $value)
                                                  <option value="{{$key}}">{{$key}}</option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                              <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="change_ticket_add" class="btn btn-success btn-sm"><i class="fa fa-arrow-right"></i></button>
                                   <br><br><br>
                                   <button id="change_ticket_remove" class="btn btn-danger btn-sm"><i class="fa fa-arrow-left"></i></button>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns">Selected Columns</label>
                                        <select id="change_ticket_selected_columns" name="select_columns[]" class="form-control" size="10" multiple required>
                                            @if(old('select_columns'))
                                                 @foreach(old('select_columns') as $value)
                                                       <option value="{{$value}}">{{$value}}</option>
                                                 @endforeach
                                             @endif
                                        </select>
                                   </div>
                              </div>
                               <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="change_ticket_order_up" class="btn btn-default btn-sm change_ticket_order"><i class="fa fa-arrow-up"></i></button>
                                   <br><br><br>
                                   <button id="change_ticket_order_down" class="btn btn-default btn-sm change_ticket_order"><i class="fa fa-arrow-down"></i></button>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-md-12">
                              <div class="form-group form-inline">
                                   <label for="filter">Filters</label>
                                   <a class="btn btn-danger btn-xs" @click="removeFilter"><i class="fa fa-minus-square"></i></a>
                                   <a class="btn btn-success btn-xs" @click="addFilter"><i class="fa fa-plus-square"></i></a>
                                   <span><a data-toggle="modal" data-target="#date_options" style="cursor:pointer;">Click here for date options</a></span>
                                    <div class="col-md-12" v-if="filterCount ==0">
                                        <p style="font-size: 16px;">None Selected</p>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=1">
                                        <!-- Insert Component here -->
                                        <ccfilter-criteria></ccfilter-criteria>
                                        <span v-if="filterCount >=2">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=2">
                                        <ccfilter-criteria></ccfilter-criteria>
                                        <span v-if="filterCount >=3">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=3">
                                        <ccfilter-criteria></ccfilter-criteria>
                                        <span v-if="filterCount >=4">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=4">
                                        <ccfilter-criteria></ccfilter-criteria>
                                        <span v-if="filterCount >=5">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=5">
                                        <ccfilter-criteria></ccfilter-criteria>
                                   </div>
                              </div>
                              </div>
                         </div>
                         <hr>
                         <button id="change_ticket_save" type="submit" class="btn btn-success">Save</button>
                         </form>
                    </div>
                    <!-- End Change Control Query -->
                    <!-- Work Order Query -->
                    <div v-show="ticketWorkOrder">
                         <form id="ticketWO_form" action="/user-settings/create-view/ticket" method="POST">
                         {{csrf_field()}}
                         <input type="hidden" name="query_type" value="ticket_work_order">
                              <div class="row">
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label for="name">Query Name</label>
                                             <input type="text" name="name" class="form-control" value="{{old('name')}}" required>
                                        </div>
                                   </div>
                              </div>
                         <div class="row">
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns1">Available Columns</label>
                                        <select id="ticketWO_available_columns" class="form-control" size="10" multiple>
                                             @foreach($ticket_work_order_columns as $key => $value)
                                                  <option value="{{$key}}">{{$key}}</option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                              <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="ticketWO_add" class="btn btn-success btn-sm"><i class="fa fa-arrow-right"></i></button>
                                   <br><br><br>
                                   <button id="ticketWO_remove" class="btn btn-danger btn-sm"><i class="fa fa-arrow-left"></i></button>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label for="columns">Selected Columns</label>
                                        <select id="ticketWO_selected_columns" name="select_columns[]" class="form-control" size="10" multiple required>
                                        @if(old('select_columns'))
                                            @foreach(old('select_columns') as $value)
                                                  <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                   </div>
                              </div>
                               <div class="col-md-1" style="text-align: center;">
                                   <br><br><br>
                                   <button id="ticketWO_order_up" class="btn btn-default btn-sm ticketWO_order"><i class="fa fa-arrow-up"></i></button>
                                   <br><br><br>
                                   <button id="ticketWO_order_down" class="btn btn-default btn-sm ticketWO_order"><i class="fa fa-arrow-down"></i></button>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-md-12">
                              <div class="form-group form-inline">
                                   <label for="filter">Filters</label>
                                   <a class="btn btn-danger btn-xs" @click="removeFilter"><i class="fa fa-minus-square"></i></a>
                                   <a class="btn btn-success btn-xs" @click="addFilter"><i class="fa fa-plus-square"></i></a>
                                   <span><a data-toggle="modal" data-target="#date_options" style="cursor:pointer;">Click here for date options</a></span>
                                    <div class="col-md-12" v-if="filterCount ==0">
                                        <p style="font-size: 16px;">None Selected</p>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=1">
                                        <!-- Insert Component here -->
                                        <wofilter-criteria></wofilter-criteria>
                                        <span v-if="filterCount >=2">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=2">
                                        <wofilter-criteria></wofilter-criteria>
                                        <span v-if="filterCount >=3">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=3">
                                        <wofilter-criteria></wofilter-criteria>
                                        <span v-if="filterCount >=4">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=4">
                                        <wofilter-criteria></wofilter-criteria>
                                        <span v-if="filterCount >=5">and</span>
                                   </div>
                                   <div class="col-md-12" v-if="filterCount >=5">
                                        <wofilter-criteria></wofilter-criteria>
                                   </div>
                              </div>
                              </div>
                         </div>
                         <hr>
                         <button id="ticketWO_save" type="submit" class="btn btn-success">Save</button>
                         </form>
                    </div>
                    <!-- End Work Order Query -->

               </div>
</div>
@endsection

@section('footer')
<script id="filters" type="text/x-template">
     <div class="form-group" style="margin-bottom: 3px;">
          <select name="filter_column[@{{_uid}}][]" class="form-control" v-model="columnChosen" required>
                @foreach($ticket_columns as $key => $value)
                    <option value="{{$key}}">{{$key}}</option>
               @endforeach
          </select>

          <select name="filter_column[@{{_uid}}][]" v-model="criteria" class="form-control" required>
               <option value="=" selected>=</option>
               <option value="!=">!=</option>
               <option value=">">></option>
               <option value=">=">>=</option>
               <option value="<"><</option>
               <option value="<="><=</option>
               <option value="like">Contains</option>
               <option value="between" v-show="columnChosen == 'Due Date' || columnChosen == 'Date Created' || columnChosen == 'Date Closed' ">Between</option>
          </select>
          
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose a Category" v-if="columnChosen == 'Category'" required>
               @foreach($categories as $category)
                              <option value="{{ $category->name }}">{{ $category->name }}</option>
               @endforeach
          </select> 
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose a Category" v-if="columnChosen == 'Subcategory'" required>
               @foreach($subcategories as $subcategory)
                              <option value="{{ $subcategory->name }}">{{ $subcategory->name }}</option>
               @endforeach
          </select> 
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Assigned To'" required>
          <option value="Not Assigned">Not Assigned</option>
               @foreach($users as $user)
                    @if($user->can('be_assigned_ticket'))
                         <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endif
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Created By'" required>
               @foreach($users as $user)
                    <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Assigned To Location'" required>
               @foreach($locations as $location)
                    <option value="{{$location->city}}">{{$location->city}}</option>
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Created By Location'" required>
               @foreach($locations as $location)
                    <option value="{{$location->city}}">{{$location->city}}</option>
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Status'" required>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Urgency'" required>
                    @foreach($urgencies as $urgency)
                         <option value="{{$urgency->name}}">{{$urgency->name}}</option>
                    @endforeach
          </select>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Description'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Resolution'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Subject'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="!between && columnChosen == 'Due Date' || columnChosen == 'Date Created' || columnChosen == 'Date Closed'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="between && columnChosen == 'Due Date'" required>
           <span v-if="between">and</span> <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="between && (columnChosen == 'Due Date' || columnChosen == 'Date Created' || columnChosen == 'Date Closed')" required>
           
     </div>
</div>
</script>
<script id="ccfilters" type="text/x-template">
     <div class="form-group" style="margin-bottom: 3px;">
     
          <select name="filter_column[@{{_uid}}][]" class="form-control" v-model="columnChosen" required>
                @foreach($change_ticket_columns as $key => $value)
                    <option value="{{$key}}">{{$key}}</option>
               @endforeach
          </select>

          <select name="filter_column[@{{_uid}}][]" v-model="criteria" class="form-control" required>
               <option value="=" selected>=</option>
               <option value="!=">!=</option>
               <option value=">">></option>
               <option value=">=">>=</option>
               <option value="<"><</option>
               <option value="<="><=</option>
               <option value="like">Contains</option>
               <option value="between" v-show="columnChosen == 'End Date' || columnChosen == 'Date Created' || columnChosen == 'Start Date'">Between</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Audit Unit'" required>
               @foreach($audit_units as $unit)
                         <option value="{{$unit->name}}">{{$unit->name}}</option>
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Is Audited'" required>
               <option value="yes">Yes</option>
               <option value="no">No</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Approver" v-if="columnChosen == 'Bus. Approver'" required>
          <option value="Not Assigned">Not Assigned</option>
               @foreach($users as $user)
                    @if($user->can('approve_change_ticket'))
                         <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endif
               @endforeach
          </select>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Cancelled Reason'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Change Description'" required>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Change Owner'" required>
               @foreach($users as $user)
                    @if($user->can('be_assigned_ticket'))
                         <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endif
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Change Type'" required>
               <option value="planned">Planned</option>
               <option value="emergency">Emergency</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Completed Type'" required>
               <option value="imp_successfully">Implemented Successfully</option>
               <option value="imp_with_errors">Implemented with Errors</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Created By'" required>
               @foreach($users as $user)
                    <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
               @endforeach
          </select>
          <input id="datepick" type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control datepick" v-if="!between && (columnChosen == 'End Date' || columnChosen == 'Date Created' || columnChosen == 'Start Date')" required>
          <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control datepick" v-if="between && (columnChosen == 'End Date' || columnChosen == 'Start Date' || columnChosen == 'Date Created')" required>
           <span v-if="between">and</span> <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control datepick" v-if="between && (columnChosen == 'End Date' || columnChosen == 'Date Created' || columnChosen == 'Start Date')" required>

          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Approver" v-if="columnChosen == 'IT Approver'" required>
          <option value="Not Assigned">Not Assigned</option>
               @foreach($users as $user)
                    @if($user->can('approve_change_ticket'))
                         <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endif
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Status'" required>
                    <option value="deferred">Deferred</option>
                    <option value="proposed">Proposed</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="in-progress">In-progress</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
          </select>          
     </div>
</div>
</script>
<script id="wofilters" type="text/x-template">
     <div class="form-group" style="margin-bottom: 3px;">
     
          <select name="filter_column[@{{_uid}}][]" class="form-control" v-model="columnChosen" required>
                @foreach($ticket_work_order_columns as $key => $value)
                    <option value="{{$key}}">{{$key}}</option>
               @endforeach
          </select>

          <select name="filter_column[@{{_uid}}][]" v-model="criteria" class="form-control" required>
               <option value="=" selected>=</option>
               <option value="!=" >!=</option>
               <option value=">">></option>
               <option value=">=">>=</option>
               <option value="<"><</option>
               <option value="<="><=</option>
               <option value="like">Contains</option>
               <option value="between" v-show="columnChosen == 'Date Completed' || columnChosen == 'Due Date'">Between</option>
          </select>

          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an Agent" v-if="columnChosen == 'Assigned To'" required>
               <option value="Not Assigned">Not Assigned</option>
               @foreach($users as $user)
                    @if($user->can('be_assigned_ticket'))
                         <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endif
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Created By'" required>
               @foreach($users as $user)
                    <option value="{{$user->first_name}} {{$user->last_name}}">{{$user->first_name}} {{$user->last_name}}</option>
               @endforeach
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Type'" required>
                    <option value="Ticket">Ticket</option>
                    <option value="ChangeTicket">Change Ticket</option>
          </select>
          <select name="filter_column[@{{_uid}}][]" class="form-control" title="Choose an User" v-if="columnChosen == 'Status'" required>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
          </select>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Work Requested'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Work Completed'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" class="form-control" v-if="columnChosen == 'Subject'" required>
          <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="!between && (columnChosen == 'Date Completed' || columnChosen == 'Due Date')" required>
          <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="between && (columnChosen == 'Date Completed' || columnChosen == 'Due Date')" required>
           <span v-if="between">and</span> <input type="text" name="filter_column[@{{_uid}}][]" placeholder="MM/DD/YYYY" class="form-control" v-if="between && (columnChosen == 'Date Completed' || columnChosen == 'Due Date')" required>
           
     </div>
</div>
</script>
<script>
     Vue.component('filter-criteria', {
          template: '#filters',
          data: function () {
               return {
                    criteria: '',
                    columnChosen: '',
               }
          },
          computed: {
               between: function() {
                    if(this.criteria == 'between') {
                         return true;
                    }
               }
          }
     });
     Vue.component('ccfilter-criteria', {
          template: '#ccfilters',
          data: function () {
               return {
                    criteria: '',
                    columnChosen: '',
               }
          },
          computed: {
               between: function() {
                    if(this.criteria == 'between') {
                         return true;
                    }
               }
          }
     });
      Vue.component('wofilter-criteria', {
          template: '#wofilters',
          data: function () {
               return {
                    criteria: '',
                    columnChosen: '',
               }
          },
          computed: {
               between: function() {
                    if(this.criteria == 'between') {
                         return true;
                    }
               }
          }
     });
     new Vue({
          el: '#app',
          data: {
               queryType: '{{old("query_type")}}',
               filterCount: 0,
          },
           methods: {
               addFilter: function() {
                    this.filterCount = this.filterCount + 1;
                    if(this.filterCount > 5)
                         {this.filterCount = 5;}
               },
               removeFilter: function() {
                    this.filterCount = this.filterCount - 1;
                    if(this.filterCount < 0)
                         {this.filterCount = 0;}
               }
          },
          computed: {
               ticket: function() {
                    if(this.queryType == "ticket") {
                         return true;
                    }
               },
               changeControl: function() {
                    if(this.queryType == "change_control") {
                         return true;
                    }
               },
               ticketWorkOrder: function() {
                    if(this.queryType == "ticket_work_order") {
                         return true;
                    }
               }
          }
     });
</script>
<script>
     $('#ticket_add').click(function() {
          event.preventDefault();
          return !$('#ticket_available_columns option:selected').remove().appendTo('#ticket_selected_columns');            
     });
     $('#ticket_remove').click(function() {
          event.preventDefault();
          return !$('#ticket_selected_columns option:selected').remove().appendTo('#ticket_available_columns');            
     });
     $('.ticket_order').click(function() {
          event.preventDefault();
           var $op = $('#ticket_selected_columns option:selected'),
                 $this = $(this);
             if($op.length){
                 ($this.attr('id') == 'ticket_order_up') ? 
                     $op.first().prev().before($op) : 
                     $op.last().next().after($op);
             }
     });
     $('#ticket_save').click(function() {
          event.preventDefault();
          $("#ticket_selected_columns option").prop("selected",true);
          $('#ticket_form').submit();
     });
//***************** Ticket Work Order ***************************
$('#ticketWO_add').click(function() {
          event.preventDefault();
          return !$('#ticketWO_available_columns option:selected').remove().appendTo('#ticketWO_selected_columns');            
     });
     $('#ticketWO_remove').click(function() {
          event.preventDefault();
          return !$('#ticketWO_selected_columns option:selected').remove().appendTo('#ticketWO_available_columns');            
     });
     $('.ticketWO_order').click(function() {
          event.preventDefault();
           var $op = $('#ticketWO_selected_columns option:selected'),
                 $this = $(this);
             if($op.length){
                 ($this.attr('id') == 'ticketWO_order_up') ? 
                     $op.first().prev().before($op) : 
                     $op.last().next().after($op);
             }
     });
     $('#ticketWO_save').click(function() {
          event.preventDefault();
          $("#ticketWO_selected_columns option").prop("selected",true);
          $('#ticketWO_form').submit();
     });
//*****************Change Tickets*****************
     $('#change_ticket_add').click(function() {
          event.preventDefault();
          return !$('#change_ticket_available_columns option:selected').remove().appendTo('#change_ticket_selected_columns');            
     });
     $('#change_ticket_remove').click(function() {
          event.preventDefault();
          return !$('#change_ticket_selected_columns option:selected').remove().appendTo('#change_ticket_available_columns');            
     });
     $('.change_ticket_order').click(function() {
          event.preventDefault();
           var $op = $('#change_ticket_selected_columns option:selected'),
                 $this = $(this);
             if($op.length){
                 ($this.attr('id') == 'change_ticket_order_up') ? 
                     $op.first().prev().before($op) : 
                     $op.last().next().after($op);
             }
     });
     $('#change_ticket_save').click(function() {
          event.preventDefault();
          $("#change_ticket_selected_columns option").prop("selected",true);
          $('#change_ticket_form').submit();
     });

     $(document).ready( function() {

});
</script>
@endsection    