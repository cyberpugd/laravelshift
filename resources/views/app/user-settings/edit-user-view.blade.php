@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/css/query-builder.css">
     <section class="content-header">
          <span style="font-size: 24px;">Edit Query</span>
          <span class="pull-right">
               <form action="/views/delete/{{$query->id}}" method="post" class="form-inline">
                    {{csrf_field()}}
                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
               </form>
          </span>
          <div class="clearfix"></div>
     </section>
<section class="content">
 <div class="panel panel-default">
     <div class="panel-body">
          <div id="app">

          <div  v-show="queryType != ''" v-cloak>
               <div class="row">
                    <div class="col-md-4 form-group" :class="{ 'has-error' : errors.name }">
                         <label class="control-label">Query Name</label>
                         <input type="text" class="form-control" value="@{{name}}" v-model="name" required>
                          <span v-if="errors.name" v-text="errors.name" class="text-danger"></span>
                    </div>
               </div>

               <div class="row">
                    <div class="col-md-4" style="overflow-y: auto; height: 440px;">
                         <h4>Available Columns</h4>
                         <ul class="list-group">
                              <li class="list-group-item available" v-for="column in columnsAvailable" v-bind:value="column" @click="addSelected(column)">
                                   @{{column}}
                              </li>
                         </ul>
                    </div>
                    <div class="col-md-4" style="overflow-y: auto; height: 440px;">
                         <h4 :class="{'text-danger' : errors.selectedColumns }">Selected Columns</h4><span :class="{'text-danger' : errors.selectedColumns }" v-if="errors.selectedColumns" v-text="errors.selectedColumns"></span>
                         <ul class="list-group" v-sortable="{ handle: '.handle', onUpdate: onUpdate }">
                              <li class="list-group-item" v-for="column in selectedColumns" v-bind:value="column">
                              <i class="fa fa-arrows handle"></i>
                              <span class="badge" @click="removeSelected(column)">X</span>
                                   @{{column}}
                              </li>
                         </ul>
                    </div>
               </div>
               <div class="row">
                    <div class="col-md-8">
                         <h4>Filters</h4>
                         <div id="builder"></div>
                          <h4>Order By</h4>
                         <div class="col-md-3">
                              <select class="form-control" v-model="sortBy" style="display: inline;" @change="changeSortDirection">
                                   <option value="">None</option>
                                   <option v-for="column in selectedColumns" :value="column">@{{column}}</option>
                              </select>
                         </div>
                         <div class="col-md-2">
                              <select class="form-control" v-model="sortDirection" v-show="sortBy != ''"  style="display: inline;">
                                   <option value="asc">Ascending</option>
                                   <option value="desc">Descending</option>
                              </select>
                         </div>
                         <div class="pull-right">
                              <a href="/views/{{$query->id}}" class="btn btn-default">Back to View</a>
                              <button class="btn btn-success" @click="submitQuery">Save</button>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     </div>
</div>
</section>
 @endsection
 @section('footer')

 <script src="/js/extendext.js"></script>
 <script src="/js/dot.js"></script>
 <script src="/js/sql-parser.js"></script>
 <script src="/js/query-builder.js"></script>

 <script>
 new Vue({
     el: '#app',
  data: {
          selected: null,
          queryType: '',
          name: '',
          errors: {},
          sortBy: '',
          sortDirection: '',
          ticketColumns: {!!$ticket_columns!!},
          changeColumns: {!!$change_ticket_columns!!},
          woColumns: {!!$ticket_work_order_columns!!},
          selectedColumns: [{{$query->select_columns}}],
          columnsAvailable: [],
          whereClause: "{!!$query->where_clause!!}",
          builderInit: false,
  },
  methods: {
          getQueryTypeData: function() {
                    this.initBuilder();
               if(this.queryType == 'ticket') {
                    this.columnsAvailable = this.ticketColumns;
               }
               if(this.queryType == 'change_control') {
                    this.columnsAvailable = this.changeColumns;
               }
               if(this.queryType == 'work_order') {
                    this.columnsAvailable = this.woColumns;
               }
          },
          changeSortDirection: function() {
               if(this.sortBy == '' && this.sortDirection != '') {
                    this.sortDirection = '';
               }
               if(this.sortBy != '' && this.sortDirection == '') {
                    this.sortDirection = 'asc';
               }
          },
          removeSelected: function(column) {
               index = this.selectedColumns.indexOf(column);
               this.selectedColumns.splice(index, 1);
               this.columnsAvailable.push(column);
          },
          addSelected: function(column) {
               index = this.columnsAvailable.indexOf(column);
               this.columnsAvailable.splice(index, 1);
               this.selectedColumns.push(column);
          },
          onUpdate: function(event) {
               this.selectedColumns.splice(event.newIndex, 0, this.selectedColumns.splice(event.oldIndex, 1)[0]);
          },
          notify: function(title, text, type) {
                swal({
                    title: title,
                    text: text,
                    type: type,
                    timer: 1500,
                    showConfirmButton: false
                  });
          },
          submitQuery: function() {
               self = this;
               this.errors = {};
               var result = $('#builder').queryBuilder('getSQL');
                 if (result.sql.length) {
                   self.whereClause = result.sql;
                   this.$http.post('/views/edit/{{$query->id}}', JSON.stringify(self.payload)).then(function(response){
                         self.notify('Success', 'View updated', 'success');
                   }).catch(function(error) {
                        this.errors = JSON.parse(error.data);
                   });
                 }
          },
          initBuilder: function() {
               $('#builder').queryBuilder('destroy');
               $('#builder').queryBuilder({
                    plugins: [
                        'bt-tooltip-errors',
                        'not-group'
                      ],
                   filters: this.activeFilters,
                 });
          }
  },
  computed: {
     payload: function() {
          return {
                    'selectedColumns': this.selectedColumns,
                    'whereClause': this.whereClause,
                    'queryType': this.queryType,
                    'name': this.name,
                    'query_id': {{$query->id}},
                    'sortBy': this.sortBy,
                    'sortDirection': this.sortDirection,
               }
     },
     activeFilters: function() {
          if(this.queryType == 'ticket') {
               return [
                    {id:'DueDate', label: 'Due Date', type: 'datetime'},
                    {id:'DateClosed', label: 'Date Closed', type: 'date'},
                    {id:'DateCreated', label: 'Date Created', type: 'date'},
                    {id:'Description', label: 'Description', type: 'string'},
                    {id:'Subject', label: 'Subject', type: 'string'},
                    {id:'Resolution', label: 'Resolution', type: 'string'},
                    {id:'Category', label: 'Category', type: 'string', input: 'select', multiple: true, values: {!!$categories->pluck('name')!!}},
                    {id:'Subcategory', label: 'Subcategory', type: 'string', input: 'select', multiple: true, values: {!!$subcategories->pluck('name')!!}},
                    {id:'AssignedTo', label: 'Assigned To', type: 'string', input: 'select', multiple: true, values: {!!$users->pluck('name')!!} },
                    {id:'CreatedBy', label: 'Created By', type: 'string', input: 'select', multiple: true, values: {!!$users->pluck('name')!!} },
                    {id:'AssignedToLocation', label: 'Assigned To Location', type: 'string', input: 'select', multiple: true, values: {!!$locations->pluck('city')!!} },
                    {id:'CreatedByLocation', label: 'Created By Location', type: 'string', input: 'select', multiple: true, values: {!!$locations->pluck('city')!!} },
                    {id:'Status', label: 'Status', type: 'string', input: 'select', multiple: true, values: ['Open', 'Closed'] },
                    {id:'Urgency', label: 'Urgency', type: 'string', input: 'select', multiple: true, values: {!!$urgencies->pluck('name')!!} },
                    ];
          }
          if(this.queryType == 'change_control'){
                    return [
                         {id:'AuditUnit', label: 'Audit Unit', type: 'string', input: 'select', multiple: true, values: {!!$audit_units->pluck('name')!!} },
                         {id:'BusApprover', label: 'Bus Approver', type: 'string', input: 'select', multiple: true, values: {!!$approvers->pluck('name')!!} },
                         {id:'CancelledReason', label: 'Cancelled Reason', type: 'string'},
                         {id:'ChangeDescription', label: 'Change Description', type: 'string'},
                         {id:'ChangeOwner', label: 'Change Owner', type: 'string', input: 'select', multiple: true, values: {!!$agents->pluck('name')!!} },
                         {id:'ChangeType', label: 'Change Type', type: 'string', input: 'select', multiple: true, values: ['Planned', 'Emergency'] },
                         {id:'CreatedBy', label: 'Created By', type: 'string', input: 'select', multiple: true, values: {!!$agents->pluck('name')!!} },
                         {id:'CreatedDate', label: 'Created Date', type: 'date'},
                         {id:'DateCompleted', label: 'Date Completed', type: 'date'},
                         {id:'EndDate', label: 'End Date', type: 'date'},
                         {id:'ITApprover', label: 'IT Approver', type: 'string', input: 'select', multiple: true, values: {!!$agents->pluck('name')!!} },
                         {id:'IsAudited', label: 'Is Audited', type: 'string', input: 'select', multiple: false, values: ['Yes', 'No']},
                         {id:'Status', label: 'Status', type: 'string', input: 'select', multiple: true, values: ['Deferred', 'Proposed', 'Scheduled', 'In-Progress', 'Cancelled', 'Completed']},
                    ];
          }
           if(this.queryType == 'work_order') {
               return [
                    {id:'AssignedTo', label: 'Assigned To', type: 'string', input: 'select', multiple: true, values: {!!$agents->pluck('name')!!} },
                    {id:'CreatedBy', label: 'Created By', type: 'string', input: 'select', multiple: true, values: {!!$agents->pluck('name')!!} },
                    {id:'DateCompleted', label: 'Date Completed', type: 'date'},
                    {id:'Due Date', label: 'Due Date', type: 'date'},
                    {id:'Status', label: 'Status', type: 'string', input: 'select', multiple: true, values: ['Open', 'Closed'] },
                    {id:'Subject', label: 'Subject', type: 'string'},
                    {id:'Type', label: 'Type', type: 'string', input: 'select', values: ['Ticket', 'ChangeTicket'] },
                    {id:'WorkCompleted', label: 'Work Completed', type: 'string'},
                    {id:'WorkRequested', label: 'Work Requested', type: 'string'},
               ];
          }
     }
  },
  created: function(){

          this.name = "{!!str_replace('"', "'", $query->name)!!}";
          this.queryType = '{!!$query->query_type!!}';
          this.selectedColumns =  {!!json_encode(explode(',', str_replace(']', '', str_replace('[', '', str_replace(', ', ',', $query->columns)))))!!};
          this.getQueryTypeData();
            $('#builder').queryBuilder("setRulesFromSQL", this.whereClause);
               this.sortBy = '{!!$query->sort_by!!}';
          this.sortDirection = '{!!$query->sort_direction!!}';

  }
});
 </script>
@endsection
