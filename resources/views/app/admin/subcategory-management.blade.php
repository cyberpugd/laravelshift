@extends('layouts.master')
@section('content')
<div id="categoryManagement" v-cloak>
<section class="content-header">
               <span style="font-size: 24px;">Category: @{{category.name}}</span>
               <a href="{{URL::Previous()}}" class="btn btn-sm btn-default pull-right">Back</a>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
          <table class="table table-responsive">
          <caption><strong>Add Subcategory</strong></caption>
               <tbody>
                    <tr>
                         <td style="vertical-align:top;">
                              <div class="form-group" :class="{'has-error' : errors['name'] }">
                                   <input type="text" class="form-control" v-model="new_subcategory.name" placeholder="Subcategory Name">
                                   <span class="small text-danger" v-text="errors['name']"></span>
                              </div>
                         </td>
                         <td style="vertical-align:top;">
                               <div class="form-group" :class="{'has-error' : errors['tags'] }">
                                   <input type="text" class="form-control" v-model="new_subcategory.tags" placeholder="Enter Search Tags">
                                   <span class="small text-danger" v-text="errors['tags']"></span>
                              </div>
                         </td>
                         <td style="vertical-align:middle;">
                              <div class="form-group" :class="{'has-error' : errors['location_matters'] }">
                                   <label>Location Matters: 
                                   <input type="checkbox" v-model="new_subcategory.location_matters" style="display: inline;"></label>
                                   <span class="small text-danger" v-text="errors['location_matters']"></span>
                              </div>
                         </td>
                         <td style="vertical-align:middle;">
                         </td>
                         <td style="vertical-align:middle;"><button class="btn btn-success btn-sm pull-right" @click="addSubcategory"><i class="fa fa-plus"></i></button></td>
                    </tr>
               </tbody>
          </table>
          <table class="table table-responsive table-striped">
               <thead>
                    <th>Sub-Category Name</th>
                    <th>Search Tags</th>
                    <th>Teams Responsible</th>
                    <th>Location Matters</th>
                    <th>Active</th>
                    <th></th>
               </thead>
               <tbody>
                    <tr v-for="(index, subcategory) in subcategories">
                         <td>
                              <span v-if="editing != subcategory.id">@{{subcategory.name}}</span>
                              <input type="text" class="form-control" v-if="editing == subcategory.id" v-model="subcategory.name" value="@{{subcategory.name}}">
                         </td>
                         <td>
                              <span v-if="editing != subcategory.id">@{{subcategory.tags}}</span>
                              <input type="text" class="form-control" v-if="editing == subcategory.id" v-model="subcategory.tags" value="@{{subcategory.tags}}">
                         </td>
                         <td>
                               <div v-for="team in subcategory.teams"  v-if="editing != subcategory.id"><a href="/admin/teams/@{{team.id}}">@{{team.name}}</a></div>
                               <div  v-show="editing == subcategory.id">
                               <select 
                                   class="selectpicker" 
                                   data-live-search="true" 
                                   data-live-search-placeholder="Just start typing..." 
                                   data-size="15" 
                                   multiple
                                   title="Choose Teams"
                                   v-model="subcategory.teams">
                                   <option v-for="team in teams" :value="team">@{{team.name}}</option>
                              </select>
                              </div>
                         </td>
                         <td>
                              <input type="checkbox"  
                                   :disabled="editing != subcategory.id"
                                   v-model="subcategory.location_matters">
                         </td>
                         <td>
                         <input type="checkbox" 
                              :disabled="editing != subcategory.id"
                              v-model="subcategory.active">
                         </td>
                         <td style="text-align: right;">
                              <span class="btn fa fa-ban text-danger" @click="toggleEditing(subcategory)" v-if="editing == subcategory.id"></span>
                              <span class="btn fa fa-save text-success" @click="saveSubcategory(subcategory)" v-if="editing == subcategory.id"></span>
                              <span class="btn fa fa-pencil" @click="toggleEditing(subcategory)" v-if="editing != subcategory.id"></span>
                              <span v-if="subcategory.tickets_count > 0" style="padding-left: 15px;"></span>
                              <span class="close" v-if="subcategory.tickets_count == 0" @click="removeSubCategory(subcategory)">&times</span>
                         </td>
                    </tr>
               </tbody>
          </table>
     </div>
     </div>
     </section>
     </div>
</div>
     @endsection
     @section('footer')
          <script>
               new Vue({
                    el: '#categoryManagement',
                    data: {
                         user: {!!json_encode(Auth::user())!!},
                         category: {!!json_encode($category) !!},
                         subcategories: {!!json_encode($category->subcategories) !!},
                         subcategoriesReset: {!!json_encode($category->subcategories) !!},
                         errors: [],
                         teams: {!!json_encode($teams)!!},
                         new_subcategory: {
                              name: '',
                              tags: '',
                              location_matters: false,
                         },
                         editing: '',
                    },
                    methods: {
                         addSubcategory: function() {
                              this.$http.post('/admin/category-management/add-subcategory/'+this.category.id, this.new_subcategory).then(function(response) {
                                   data = JSON.parse(response.body);
                                   this.subcategories.push({
                                        'id': data.subcategory_id, 
                                        'category_id': data.category, 
                                        'name': data.name, 
                                        'tags': data.tags, 
                                        'tickets_count': data.ticket_count,
                                        'location_matters': data.location_matters, 
                                        'created_by': data.created_by, 
                                        'active': true
                                   });
                                   this.new_subcategory = {
                                        name: '',
                                        tags: '',
                                        location_matters: false,
                                   };
                              }, function(response) {
                                   this.errors = JSON.parse(response.body);
                              });
                         }, 
                         removeSubCategory: function(subcategory) {
                              self = this;
                              swal({
                                  title: "Are you sure?",
                                  text: "You will not be able to recover this subcategory.",
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
                                        self.$http.post('/admin/subcategory-management/delete/'+subcategory.id).then(function(response) {
                                              data = JSON.parse(response.body);
                                               toRemove = self.subcategories.indexOf(subcategory);
                                               self.subcategories.splice(toRemove, 1);
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
                                           swal("Cancelled", "The subcategory will not be removed.", "error");
                                       }
                              });
                         },
                         toggleEditing: function(subcategory) {
                              this.editing = (this.editing == subcategory.id ? '' : subcategory.id);
                         },     
                         saveSubcategory: function(subcategory) {
                              this.$http.post('/admin/category-management/edit-subcategory/'+subcategory.id, subcategory).then(function(response) {
                                   this.editing = false;
                                    swal({   
                                         title: "Success",   
                                         text: "Update Successful.",   
                                         type: "success",
                                         timer: 1500,
                                         showConfirmButton: false
                                     });
                              }, function(response) {

                              });
                         }           
                    }
               });
          </script>
     @endsection