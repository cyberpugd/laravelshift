@extends('layouts.master')
@section('content')
@include('app.components.modals.create-category')
@include('app.components.modals.edit-category')
<div id="addSubcategory">
     <div id="errors" class="alert alert-danger fade in alert-dismissable alert-center" style="display:none;">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
     </div>
     <div class="col-md-12 row">
          <div class="col-md-6 form-group">
               <h2>Category Management</h2>
          </div>
          <div class="col-md-6">
               <button class="btn btn-success form-group" data-toggle="modal" data-target="#create_category" style="float:right;">Add Category</button>
          </div>
     </div>
     <div class="col-md-12 row" style="margin-top: 10px;">
          <div id="accordion" class="panel-group">
               @foreach($categories as $category)
               <div class="panel panel-default">
                    <div id="formdiv{{$category->id}}" class="panel-heading toggle-panel">
                         <div style="text-align: left;" data-toggle="collapse" data-parent="#accordion" href="#panel{{$category->id}}">
                         <h4 class="panel-title pull-left">
                              {{$category->name}}
                         </h4>
                         </div>
                         <div style="text-align: right;">
                         <span style="display: inline;">
                                   <button class="btn btn-default btn-sm editCategory" data-id="{{$category->id}}" data-name="{{$category->name}}" data-toggle="modal" data-target="#edit_category" ><i class="fa fa-pencil"></i></button>
                                   <form data-url="/admin/category-management/inactivate-category/{{$category->id}}" method="POST" style="display: inline;">
                                        <button id="activeButton{{$category->id}}" type="submit" class="btn btn-success btn-sm" onclick="inactivateCategory(event)" 
                                        @if($category->active == 0) style="display:none" @endif>Active</button>
                                   </form>
                                   <form data-url="/admin/category-management/activate-category/{{$category->id}}" method="POST" style="display: inline;">
                                        <button id="inactiveButton{{$category->id}}" type="submit" class="btn btn-danger btn-sm" onclick="activateCategory(event)"
                                        @if($category->active == 1) style="display:none" @endif>Inactive</button>
                                   </form>
                              </span>
                         </div>
                    </div>
                    <div id="panel{{$category->id}}" class="panel-collapse collapse">
                         <div class="panel-body">
                              <form data-url="/admin/category-management/add-subcategory/{{$category->id}}" method="POST">
                                   {!! csrf_field() !!}
                                   <newsubcategory></newsubcategory>
                              </form>
                              <div>
                                   <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#active{{$category->id}}" aria-controls="active" role="tab" data-toggle="tab">Active 
                                        <span id="activeBadge{{$category->id}}" class="badge">{{$category->subcategories->filter(function($item) { return $item->active == 1;})->count()}}</span></a></li>
                                        <li role="presentation"><a href="#inactive{{$category->id}}" aria-controls="inactive" role="tab" data-toggle="tab">Inactive 
                                        <span id="inactiveBadge{{$category->id}}" class="badge">{{$category->subcategories->filter(function($item) { return $item->active == 0;})->count()}}</span></a></li>
                                   </ul>
                                   <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="active{{$category->id}}">
                                             <div class="table table-responsive">
                                                  <table id="activetable{{$category->id}}" class="table table-striped">
                                                       <thead>
                                                            <td><strong>Subcategory Name</strong></td>
                                                            <td><strong>Search Tags</strong></td>
                                                            <td><strong>Location Matters</strong></td>
                                                            <td><strong>Created By</strong></td>
                                                            <td><strong>Created At</strong></td>
                                                            <td># of Tickets</td>
                                                            <td></td>
                                                       </thead>
                                                       <tbody>
                                                            @foreach($category->subcategories as $subcategory)
                                                            @if($subcategory->active == 1)
                                                            <tr id="activetr{{ $subcategory->id }}">
                                                                 <td id="scname{{$subcategory->id}}">{{ $subcategory->name }}</td>
                                                                 <td id="sctags{{$subcategory->id}}">{{ $subcategory->tags}}</td>
                                                                 <td id="scloc{{$subcategory->id}}">{{ ($subcategory->location_matters == 1 ? 'Yes' : 'No') }}</td>
                                                                 <td>{{ $subcategory->createdBy->last_name }}, {{ $subcategory->createdBy->first_name }}</td>
                                                                 <td>{{ $subcategory->created_at->toDayDateTimeString()}}</td>
                                                                 <td><strong>{{$subcategory->tickets->count()}}</strong></td>
                                                                 <td><div class="form-inline">
                                                                           <form data-url="/admin/category-management/inactivate-subcategory/{{$subcategory->id}}" method="POST">
                                                                                <a id="editMode{{$subcategory->id}}" class="btn btn-default btn-sm" onclick="editMode({{$subcategory->id}})"><i class="fa fa-pencil"></i></a>
                                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="inactivateSubcategory(event)"><i class="fa fa-minus"></i></button> 
                                                                           </form>
                                                                           </div>
                                                                 </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach

                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>
                                        <!-- Inactive Subcategories -->
                                        <div role="tabpanel" class="tab-pane active inactive" id="inactive{{$category->id}}">
                                             <div class="table table-responsive">
                                                  <table id="inactivetable{{$category->id}}" class="table table-striped">
                                                       <thead>
                                                            <td><strong>Subcategory Name</strong></td>
                                                            <td><strong>Search Tags</strong></td>
                                                            <td><strong>Location Matters</strong></td>
                                                            <td><strong>Created By</strong></td>
                                                            <td><strong>Created At</strong></td>
                                                            <td># of Tickets</td>
                                                            <td></td>
                                                       </thead>
                                                       <tbody>
                                                            @foreach($category->subcategories as $subcategory)
                                                            @if($subcategory->active == 0)
                                                            <tr id="inactivetr{{ $subcategory->id }}">
                                                                 <td>{{ $subcategory->name }}</td>
                                                                 <td>{{ $subcategory->tags}}</td>
                                                                 <td>{{ $subcategory->location_matters }}</td>
                                                                 <td>{{ $subcategory->createdBy->last_name }}, {{ $subcategory->createdBy->first_name }}</td>
                                                                 <td>{{ $subcategory->created_at->toDayDateTimeString()}}</td>
                                                                 <td><strong>{{ $subcategory->tickets->count() }}</strong></td>
                                                                 <td>
                                                                      <form data-url="/admin/category-management/activate-subcategory/{{$subcategory->id}}" method="POST">
                                                                           <button type="submit" class="btn btn-primary btn-sm" onclick="activateSubcategory(event)"><i class="fa fa-plus"></i></button>
                                                                      </form>
                                                                 </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach

                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                      </div>
                    </div>
                    @endforeach
               </div>
          </div>
     </div>
     <template id="rowTemplate">
          <div>
               <div class="col-md-3 form-group">
                    <input type="text" name="name" v-el="inputField" class="form-control subcategoryname" value="" placeholder="Subcategory Name">
               </div>
               <div class="col-md-4 form-group">
                    <input type="text" name="tags" class="form-control" value="" placeholder="Search Tags">
               </div>
               <div class="col-md-2 form-group">
                    <label for="location_matters">Location Matters</label>
                    <input id="location_matters" type="checkbox" name="location_matters">
               </div>
               <div class="col-md-1 form-group">
                    <button type="submit" class="btn btn-success" v-on:click="saveSubcategory" v-show="!savingSubcategory">Save</button>
                    <button type="submit" class="btn btn-success" v-show="savingSubcategory" disabled>Saving</button>
               </div>
          </div>
     </template>
     @endsection
     @section('footer')
     <script src="/js/add-subcategory.js"></script>
     <script>
      $('.editCategory').on("click", function() {
          var categoryID = $(this).data('id');
          var categoryName = $(this).data('name');
          $(".modal-body #categoryName").val( categoryName );
          $(".modal-content #editCatForm").attr("action", "/admin/category-management/edit/" + categoryID);

     });
</script>
     @endsection