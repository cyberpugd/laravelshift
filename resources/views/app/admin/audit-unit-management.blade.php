@extends('layouts.master')

@section('content')
@include('app.components.modals.add-audit-unit')
<section class="content-header">
          <span style="font-size: 24px;">Audit Units</span>
          <button class="btn btn-success form-group" data-toggle="modal" data-target="#create_audit_unit" style="float:right;">Add Audit Unit</button>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
          <table class="table table-striped">
               <thead>
                    <tr>
                         <td><strong>Audit Units</strong></td>
                         <td><strong>Status</strong></td>
                    </tr>
               </thead>
               <tbody id="editUnit">
                    <tr is="unit-component" :unit="unit" v-for="unit in auditUnits"></tr>
               </tbody>
          </table>
          
     </div>          
</div>
</section>
<script id="rowTemplate" type="text/x-template">
        <tr id="row@{{unit.id}}">
               <td>
                    <span v-show="!editMode">@{{name}}</span>
                    <input type="text" data-id="@{{unit.id}}" class="form-control" v-show="editMode" v-model="name"></input>
                    <div id="showResult@{{unit.id}}" v-bind:style="{color: resultColor, display: none}">@{{updateResult}}</div>
               </td>
               <td>
                    <span v-show="!editMode">@{{status}}</span>
                    <select class="form-control" v-show="editMode" data-id="@{{unit.id}}" v-model="selected">
                         <option value="0">Inactive</option>
                         <option value="1">Active</option>
                    </select>
               </td>
               <td style="text-align: right;">
                    <button data-id="@{{unit.id}}" class="btn btn-default btn-sm edit" title="Edit" v-on:click="toggleEdit" v-show="!editMode"><i class="fa fa-pencil"></i></button>
                    <button data-id="@{{unit.id}}" class="btn btn-success btn-sm edit" title="Save" v-on:click="saveChanges" v-show="editMode"><i class="fa fa-check"></i></button>
                    <button class="btn btn-danger btn-sm edit" title="Cancel" v-on:click="toggleEdit" v-show="editMode"><i class="fa fa-ban"></i></button>
               </td>
          </tr>
</script>
@endsection
@section('footer')
<script>
     Vue.component('unit-component', {
          props: ['unit'],
          template: '#rowTemplate',
          data: function() {
               return {
                    editMode: false,
                    selected: this.unit.status,
                    name: this.unit.name,
                    updateResult: "",
                    resultColor: ""
               };
          },
          methods: {
               toggleEdit: function() {
                    this.editMode = !this.editMode;
               },
               saveChanges: function() {
                    var values = {
                         'name': this.name,
                         'status': this.selected
                    };

                    $.ajax({
                              type: 'POST',
                              url: '/admin/audit-units/edit/'+this.unit.id,
                              data: values,
                              success: function(data){
                                   this.unit.status= data.status;
                                   this.unit.name = data.name;
                                   this.updateResult = data.result;
                                   this.editMode = !this.editMode;
                                   this.resultColor = data.color;
                                   $('#showResult'+this.unit.id).fadeIn().delay(2000).fadeOut();
                              }.bind(this),
                              error: function(data) {
                                   this.resultColor = data.color;
                                   $('#showResult'+this.unit.id).fadeIn().delay(2000).fadeOut();
                              }.bind(this)
                         });
               },
               handleSuccess: function() {

               }
          },
          computed: {
               status: function() {
                         if(this.unit.status == 0) {
                              return "Inactive";
                         }
                              return "Active";
                    }
          },
     });
     new Vue({
          el: "#editUnit",
          data: {
               auditUnits: {!!$audit_units!!}
          },
     });
</script>
@endsection
