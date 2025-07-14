@extends('layouts.master')
@section('content')
@include('app.components.modals.create-category')
@include('app.components.modals.edit-category')
<section class="content-header">
               <span style="font-size: 24px;">Category Management</span>
               <span class="btn btn-success form-group" data-toggle="modal" data-target="#create_category" style="float:right;">Add Category</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
     <div id="categoryManagement" class="row table-responsive" v-cloak>
          <table class="table table-striped">
               <thead>
                    <th>Category Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th></th>
               </thead>
               <tbody>
                    <tr v-for="category in categories">
                         <td><a href="/admin/category-management/@{{category.id}}">@{{category.name}}</a></td>
                         <td><input type="checkbox" v-model="category.active" @click="toggleStatus(category)"></td>
                         <td>@{{moment(category.created_at.date)}}</td>
                         <td style="text-align: right">
                              <span data-toggle="modal" data-target="#edit_category" @click="editCategory(category.id, category.name)"><i class="btn fa fa-pencil"></i></span>
                              <span class="close" v-if="category.tickets_count == 0" @click="removeCategory(category)">&times</span>
                         </td>
                    </tr>
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
                    el: '#categoryManagement',
                    data: {
                         categories: {!!json_encode($categories) !!},
                    },
                    methods: {
                         editCategory: function(id, name) {
                              $(".modal-body #categoryName").val( name );
                              $(".modal-content #editCatForm").attr("action", "/admin/category-management/edit/" + id);
                         },
                         toggleStatus: function(category) {
                              if(category.active == '1') {
                                   this.$http.post('/admin/category-management/inactivate-category/'+category.id).then(function(response) {
                                        this.showSuccess();
                                   }, function(response) {
                                        alert('failed');
                                   });
                              } else {
                                    this.$http.post('/admin/category-management/activate-category/'+category.id).then(function(response) {
                                        this.showSuccess();
                                   }, function(response) {
                                        alert('failed');
                                   });
                              }  
                         },
                         showSuccess: function() {
                              swal({
                                        title: '',
                                        text: 'Status updated successfully.',
                                        type: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                  });
                         },
                         moment: function (date) {
                             return moment(date).format('MM/DD/YYYY h:mm a');
                           },
                           removeCategory: function(category) {
                               self = this;
                              swal({
                                  title: "Are you sure?",
                                  text: "You will not be able to recover this category.",
                                  type: "warning",
                                  showCancelButton: true,
                                  confirmButtonColor: "#DD6B55",
                                  confirmButtonText: "Delete",
                                  cancelButtonText: "Cancel",
                                  closeOnConfirm: false,
                                  closeOnCancel: false
                              },
                              function(isConfirm){
                                  if (isConfirm) {
                                        self.$http.post('/admin/category-management/delete/'+category.id).then(function(response) {
                                              data = JSON.parse(response.body);
                                               toRemove = self.categories.indexOf(category);
                                               self.categories.splice(toRemove, 1);
                                        });
                                        swal({   
                                                   title: "Success",   
                                                   // text: data.message,   
                                                   text: "Done",
                                                   type: "success",
                                                   timer: 1500,
                                                   showConfirmButton: false
                                               });
                                       } else {
                                           swal("Cancelled", "The category will not be removed.", "error");
                                       }
                              });
                           }
                    }
               });
          </script>
     @endsection