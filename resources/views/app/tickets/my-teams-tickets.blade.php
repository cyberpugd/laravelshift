@extends('layouts.master')

@section('content')
<section class="content-header">
          <h1>My Teams' Unassigned Tickets </h1>
</section>
<section class="content">
     <div class="panel panel-default">
               <div class="panel-heading">
                                   <div class="col-md-6">
                    <form method="GET" action="/tickets/team-tickets">
         
                         <div class="input-group">                               

                              <input type="text" class="form-control" name="search" @if(isset($search)) placeholder="{{$search}}" @else placeholder="Search" @endif aria-describedby="basic-addon2">
                              
                              <span class="input-group-btn">
                                   @if(isset($search))
                                        <a href="/tickets/team-tickets" class="btn btn-default" title="Clear Search"><i class="fa fa-times"></i></a>
                                   @endif
                                   <button class="btn btn-default" type="submit">Go!</button>
                                   <a href="{{(Request::getPathInfo() . (Request::getQueryString() ? ('?' . Request::getQueryString()).'&print=true' : '?print=true'))}}" class="btn btn-default" type="submit"><i class="fa fa-print"></i></a>
                              </span>
                         
                              
                         </div>
                    </form>
                    </div>
                    <div class="clearfix"></div>


               </div>
               <div class="panel-body" v-cloak>
                    <div class="table table-responsive">
                         <table class="table table-striped table-hover show-pointer">
                         <caption>My Location and Responsibility
                         <a class="btn btn-xs pull-right btn-success" 
                              v-show="form.selectedTicketsMy.length > 0 || form.selectedTicketsOther.length > 0" 
                              @click="assignTickets"
                              :disabled="assigning">
                                   <i class="fa fa-cog fa-spin" v-show="assigning"></i> Assign To me
                         </a>
                         </caption>
                              <thead>
                                   <tr>
                                        <th style="width: 25px;">
                                             <span class="btn">
                                                       <i class="fa fa-square-o" v-show="form.selectedTicketsMy.length < myLocationTickets.length" @click="checkAllMyLocation"></i>
                                                       <i class="fa fa-check-square-o" v-show="form.selectedTicketsMy.length == myLocationTickets.length" @click="uncheckAllMy"></i>
                                                  </span>
                                        </th>
                                        <th>Ticket #</th>
                                        <th>Urgency</th>
                                        <th>Subject</th>
                                        <th>Category / Subcategory</th>
                                        <th>Caller</th>
                                        <th>Caller Location</th>
                                        <th>Created</th>
                                        <th>Due</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   
                                        <tr class="pointer" v-for="ticket in myLocationTickets">
                                             <td style="width: 25px;">
                                                  <span class="btn" @click="toggleCheckMy(ticket)">
                                                       <i class="fa fa-square-o" v-show="!isCheckedMy(ticket)"></i>
                                                       <i class="fa fa-check-square-o" v-show="isCheckedMy(ticket)"></i>
                                                  </span>
                                             </td>
                                             <td><a :href="'/tickets/'+ticket.id" target="_blank">@{{ticket.id}}</a></td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.urgency.name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.title}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.category.name}} / @{{ticket.subcategory.name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.created_by.first_name}} @{{ticket.created_by.last_name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.created_by.location.city}}</td>
                                             <td @click="showTicket(ticket.id)">@{{moment(ticket.created_at)}}</td>
                                             <td @click="showTicket(ticket.id)">@{{moment(ticket.due_date)}}</td>
                                        </tr>
                                   {{-- @endif
                                   @endforeach --}}
                              </tbody>
                         </table>

                         <table class="table table-striped table-hover show-pointer">
                         <caption>Other Locations</caption>
                              <thead>
                                   <tr>
                                        <th style="width: 25px;">
                                             <span class="btn">
                                                       <i class="fa fa-square-o" v-show="form.selectedTicketsOther.length < otherLocationTickets.length" @click="checkAllOtherLocation"></i>
                                                       <i class="fa fa-check-square-o" v-show="form.selectedTicketsOther.length == otherLocationTickets.length" @click="uncheckAllOtherLocation"></i>
                                                  </span>
                                        </th>
                                        <th>Ticket #</th>
                                        <th>Urgency</th>
                                        <th>Subject</th>
                                        <th>Category / Subcategory</th>
                                        <th>Caller</th>
                                        <th>Caller Location</th>
                                        <th>Created</th>
                                        <th>Due</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   
                                        <tr class="pointer" v-for="ticket in otherLocationTickets">
                                             <td style="width: 25px;">
                                                  <span class="btn" @click="toggleCheckOther(ticket)">
                                                       <i class="fa fa-square-o" v-show="!isCheckedOther(ticket)"></i>
                                                       <i class="fa fa-check-square-o" v-show="isCheckedOther(ticket)"></i>
                                                  </span>
                                             </td>
                                             <td><a :href="'/tickets/'+ticket.id" target="_blank">@{{ticket.id}}</a></td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.urgency.name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.title}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.category.name}} / @{{ticket.subcategory.name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.created_by.first_name}} @{{ticket.created_by.last_name}}</td>
                                             <td @click="showTicket(ticket.id)">@{{ticket.created_by.location.city}}</td>
                                             <td @click="showTicket(ticket.id)">@{{moment(ticket.created_at)}}</td>
                                             <td @click="showTicket(ticket.id)">@{{moment(ticket.due_date)}}</td>
                                        </tr>
                                   {{-- @endif
                                   @endforeach --}}
                              </tbody>
                         </table>
                    </div>
               </div>
     </div>
</section>
@endsection
@section('footer')
<script>
     new Vue({
          el: 'body',
          data: {
               form: {
                    selectedTicketsMy: [],
                    selectedTicketsOther: [],
               },
               assigning: false,
               tickets: {!! json_encode($tickets) !!},
               myLocationTickets: [],
               otherLocationTickets: [],
               user: {!! json_encode($user) !!},
          },
          created: function() {
               var self = this;
               this.tickets.forEach(function(ticket) {
                    if(self.myLocation(ticket)) {
                         self.myLocationTickets.push(ticket);
                    }
                    if(self.otherLocations(ticket)) {
                         self.otherLocationTickets.push(ticket);
                    }
               });
          },
          methods: {
               showTicket: function(id) {
                    window.location.href = '/tickets/'+id;
               },
               toggleCheckMy: function(ticket) {
                    if(!this.form.selectedTicketsMy.includes(ticket)) {
                         this.form.selectedTicketsMy.push(ticket);
                    } else {
                         var index = this.form.selectedTicketsMy.indexOf(ticket);
                         this.form.selectedTicketsMy.splice(index, 1);
                    }

               },
               toggleCheckOther: function(ticket) {
                    if(!this.form.selectedTicketsOther.includes(ticket)) {
                         this.form.selectedTicketsOther.push(ticket);
                    } else {
                         var index = this.form.selectedTicketsOther.indexOf(ticket);
                         this.form.selectedTicketsOther.splice(index, 1);
                    }

               },
               isCheckedMy: function(ticket) {
                    return this.form.selectedTicketsMy.includes(ticket);
               },
               isCheckedOther: function(ticket) {
                    return this.form.selectedTicketsOther.includes(ticket);
               },
               checkAllMyLocation: function() {
                    this.form.selectedTicketsMy = this.myLocationTickets.slice();
               },
               checkAllOtherLocation: function() {
                    this.form.selectedTicketsOther = this.otherLocationTickets.slice();
               },
               uncheckAllMy: function() {
                    this.form.selectedTicketsMy.length = 0;
                    this.form.selectedTicketsMy.sort();
               },
               uncheckAllOtherLocation: function() {
                    this.form.selectedTicketsOther.length = 0;
                    this.form.selectedTicketsOther.sort();
               },
               myLocation: function(ticket) {
                    if((ticket.subcategory.location_matters && ticket.created_by.location_id == this.user.location_id) || !ticket.subcategory.location_matters) {
                         return true;
                    }
                    return false;
               },
               otherLocations: function(ticket) {
                    if(ticket.subcategory.location_matters && ticket.created_by.location_id != this.user.location_id) {
                         return true;
                    }
                    return false;
               },
               moment: function (date) {
                   return moment(date).utc(date).tz(this.user.timezone).fromNow();
                 },
               assignTickets: function() {
                    var self = this;
                    this.assigning = true;
                    this.$http.post('/tickets/mass-assign', this.form).then(function(response) {

                         swal({   
                               title: "Success",   
                               // text: data.message,   
                               text: "Done",
                               type: "success",
                               timer: 1500,
                               showConfirmButton: false
                           });
                         self.form.selectedTicketsMy.forEach(function(ticket) {
                              self.myLocationTickets.forEach(function(ticket2) {
                                   if(ticket.id == ticket2.id) {
                                        var index = self.myLocationTickets.indexOf(ticket2);
                                        self.myLocationTickets.splice(index, 1);
                                   }
                              });//End foreach inner
                         }); //end Foreach outter

                         self.form.selectedTicketsOther.forEach(function(ticket) {
                              self.otherLocationTickets.forEach(function(ticket2) {
                                   if(ticket.id == ticket2.id) {
                                        var index = self.otherLocationTickets.indexOf(ticket2);
                                        self.otherLocationTickets.splice(index, 1);
                                   }
                              });//End foreach inner
                         }); //end Foreach outter
                         self.assigning = false;
                         self.form.selectedTicketsOther.length = 0;
                         self.form.selectedTicketsMy.length = 0;
                    }); //End post request
               }
          }
     });
</script>
@endsection

{{-- @foreach($tickets as $ticket)
                                   @if($ticket->subcategory->location_matters == 1 
                                        && $ticket->createdBy->location_id == Auth::user()->location_id 
                                        || $ticket->subcategory->location_matters == 0) --}}