@extends('layouts.master')

@section('content')
@include('app.components.modals.add-holiday')
<section class="content-header">
          <span style="font-size: 24px;">P2 Holidays</span>
          <span class="btn btn-success form-group" data-toggle="modal" data-target="#add_holiday" style="float:right;">Add Holiday</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body table-responsive">
          <table class="table table-striped">
               <thead>
                    <tr>
                         <td><strong>Name</strong></td>
                         <td><strong>Date</strong></td>
                    </tr>
               </thead>
               <tbody id="editHoliday">
                    <tr is="holiday-component" :holiday="holiday" v-for="holiday in holidays"></tr>
               </tbody>
          </table>
          
     </div>          
</div>
</section>
<script id="rowTemplate" type="text/x-template">
        <tr id="row@{{holiday.id}}">
               <td>
                    <span v-show="!editMode">@{{name}}</span>
                    <input type="text" data-id="@{{holiday.id}}" class="form-control" v-show="editMode" v-model="name"></input>
                    <div id="showResult@{{holiday.id}}" v-bind:style="{color: resultColor, display: none}">@{{updateResult}}</div>
               </td>
               <td>
                    <span v-show="!editMode">@{{date}}</span>
                                        <input type='text' class="form-control holiday-date" name="date" v-model="date" v-show="editMode" required />
               </td>
               <td style="text-align: right;">
                    <button class="btn btn-default btn-sm" title="Edit" v-on:click="toggleEdit" v-show="!editMode"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm" title="Remove" v-on:click="removeHoliday" v-show="!editMode"><i class="fa fa-trash"></i></button>
                    <button class="btn btn-default btn-sm" title="Cancel" v-on:click="toggleEdit" v-show="editMode"><i class="fa fa-ban"></i></button>
                    <button type="submit" class="btn btn-success btn-sm" title="Save" v-on:click="saveHoliday" v-show="editMode"><i class="fa fa-check"></i></button>
               </td>
          </tr>
</script>
@endsection
@section('footer')
<script>
     $(document).ready( function() {
          $('.holiday-date').datetimepicker({
               format: 'M d, Y',
               timepicker: false,
          });
     });
</script>
<script>
     Vue.component('holiday-component', {
          props: ['holiday'],
          template: '#rowTemplate',
          data: function() {
               return {
                    editMode: false,
                    name: this.holiday.name,
                    date: moment(this.holiday.date).format('MMM D, YYYY'),
                    id: this.holiday.id,
                    updateResult: "",
                    resultColor: ""
               };
          },
          methods: {
               toggleEdit: function() {
                    this.editMode = !this.editMode;
               },
               removeHoliday: function() {
                    $.ajax({
                              type: 'POST',
                              url: '/admin/holiday/remove/'+this.holiday.id,
                              success: function(data){
                                   swal({   
                                        title: "",   
                                        text: "Holiday Removed.",   
                                        type: "success",
                                        timer: 1500,
                                        showConfirmButton: false
                                      });
                                   $('#row'+this.holiday.id).slideUp();
                              }.bind(this),
                              error: function(data) {
                                   this.resultColor = data.color;
                                   $('#showResult'+this.holiday.id).fadeIn().delay(2000).fadeOut();
                              }.bind(this)
                         });
               },
               saveHoliday: function() {
                     var values = {
                         'name': this.name,
                         'date': this.date
                    };
                     $.ajax({
                              type: 'POST',
                              data: values,
                              url: '/admin/holiday/update/'+this.holiday.id,
                              success: function(data){
                                   swal({   
                                        title: "",   
                                        text: "Holiday Updated.",   
                                        type: "success",
                                        timer: 1500,
                                        showConfirmButton: false
                                      });
                                   this.editMode = !this.editMode;
                              }.bind(this),
                              error: function(data) {
                                   this.resultColor = data.color;
                                   $('#showResult'+this.holiday.id).fadeIn().delay(2000).fadeOut();
                              }.bind(this)
                         });
               },
          },
     });
     new Vue({
          el: "#editHoliday",
          data: {
               holidays: {!!$holidays!!}
          },
     });
</script>
@endsection
