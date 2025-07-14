@extends('layouts.master')
@section('content')
@include('app.components.modals.create-team')
@include('app.components.modals.edit-team')
<form id="removeUserForm" action="" method="post">
     {{csrf_field()}}
</form>
     <div id="errors" class="alert alert-danger fade in alert-dismissable alert-center" style="display:none;">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
     </div>
     <div class="col-md-12 row">
          <div class="col-md-6 form-group">
               <h2>Team Management</h2>
          </div>
          <div class="col-md-6">
               <button class="btn btn-success form-group" data-toggle="modal" data-target="#create_team" style="float:right;">Add Team</button>
          </div>
     </div>
     <div class="col-md-12 row" style="margin-top: 10px;">
          <div id="accordion" class="panel-group">
               @foreach($teams as $team)
               <div class="panel panel-default">
                    <div id="formdiv{{$team->id}}" class="panel-heading toggle-panel">
                    <div style="text-align: left;" data-toggle="collapse" data-parent="#accordion" href="#panel{{$team->id}}">
                         <h4 class="panel-title pull-left">
                              {{$team->name}}
                         </h4>
                    </div>
                         <div style="text-align: right;">
                         <span style="display: inline;">
                                   <button title="Edit Team Name" class="btn btn-default btn-sm editTeam" data-id="{{$team->id}}" data-name="{{$team->name}}" data-toggle="modal" data-target="#edit_team" ><i class="fa fa-pencil"></i></button>
                              </span>
                         </div>
                    </div>
                    <div id="panel{{$team->id}}" class="panel-collapse collapse">
                         <div class="panel-body">
                              <form id="subcatform_{{$team->id}}" action="/teams/subcategories/sync/{{$team->id}}" method="POST">
                              {{csrf_field()}}
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="columns1">Available Subcategories</label>
                                             <select id="subcat_{{$team->id}}_available_columns" class="form-control" size="10" multiple>
                                                  @foreach($categories as $category)
                                                       @foreach($category->subcategoriesOrdered as $subcategory)
                                                            @if(!in_array($subcategory->id, $team->subcategories->lists('id')->toArray()))
                                                                 <option value="{{$subcategory->id}}">{{$category->name}} - {{$subcategory->name}}</option>
                                                            @endif
                                                       @endforeach
                                                  @endforeach
                                             </select>
                                        </div>
                                   </div>
                                   <div class="col-md-1" style="text-align: center;">
                                        <br><br><br>
                                        <button class="btn btn-success btn-sm subcat_add" data-team="{{$team->id}}"><i class="fa fa-arrow-right"></i></button>
                                        <br><br><br>
                                        <button class="btn btn-danger btn-sm subcat_remove" data-team="{{$team->id}}"><i class="fa fa-arrow-left"></i></button>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="columns">Selected Subcategories</label>
                                             <select id="subcat_{{$team->id}}_selected" name="subcategories[]" class="form-control" size="10" multiple required>
                                                 @foreach($team->subcategoriesOrdered as $subcategory)
                                                            <option value="{{$subcategory->id}}">{{$subcategory->category->name}} - {{$subcategory->name}}</option>
                                                  @endforeach
                                             </select>
                                        </div>
                                   </div>
                                   <div class="col-md-3" style="height: 100%; overflow: auto;">
                                        <table class="table table-striped">
                                             <thead>
                                                  <td><strong>Agents</strong></td>
                                                  <td></td>
                                             </thead>
                                             <tbody>
                                                  @foreach($team->users as $user)
                                                       <tr>
                                                            <td>
                                                                 <a href="/admin/users/{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</a>
                                                            </td>
                                                            <td>
                                                                      <a class="btn btn-xs btn-danger removeUser pull-right" data-team="{{$team->id}}" data-user="{{$user->id}}"><i class="fa fa-trash"></i></a>
                                                            </td>
                                                       </tr>
                                                  @endforeach
                                             </tbody>
                                        </table>
                                   </div>
                              <div class="col-md-2">
                                   <label for="name">Allow Self Enroll</label>
                                   <input type="checkbox" name="self_enroll" @if($team->self_enroll == 1) checked @endif>
                              </div>
                                   <div class="col-md-6">
                                   <button type="submit" class="btn btn-success subcat_save" data-team="{{$team->id}}">Save</button>
                                   </div>
                              </form>
                         <div class="col-md-6">
                              <div class="pull-right">
                              <form class="form-inline" action="/admin/teams/add-user/{{$team->id}}" method="post">
                              {{csrf_field()}}
                                        <select name="agents[]" class="selectpicker" data-live-search="true" data-size="15" title="Select agents to add" multiple>
                                        @foreach($agents as $agent)
                                             @if(!in_array($agent->id, $team->users->lists('id')->toArray()))
                                                  <option value="{{$agent->id}}" data-tokens="{{$agent->first_name}} {{$agent->last_name}}">{{$agent->first_name}} {{$agent->last_name}}</option>
                                             @endif
                                        @endforeach
                                   </select> 
                                   <button type="submit" class="btn btn-default">Add Agents</button>
                              </form>
                                   </div>
                      </div>
                         </div>
                    </div>
               </div>
                    @endforeach
          </div>
@endsection
@section('footer')
<script>
     $('.subcat_add').click(function() {
          event.preventDefault();
          team = $(this).data('team');
          return !$('#subcat_'+team+'_available_columns option:selected').remove().appendTo('#subcat_'+team+'_selected');            
     });
     $('.subcat_remove').click(function() {
          event.preventDefault();
          team = $(this).data('team');
          return !$('#subcat_'+team+'_selected option:selected').remove().appendTo('#subcat_'+team+'_available_columns');            
     });
     $('.subcat_save').click(function() {
          event.preventDefault();
          team = $(this).data('team');
          $("#subcat_"+team+"_selected option").prop("selected",true);
          $('#subcatform_'+team).submit();
     });

      $('.editTeam').on("click", function() {
          var teamID = $(this).data('id');
          var teamName = $(this).data('name');
          $(".modal-body #teamName").val( teamName );
          $(".modal-content #editTeamForm").attr("action", "/admin/teams/edit/" + teamID);
     });

      $('.removeUser').on("click", function() {
          var teamID = $(this).data('team');
          var userID = $(this).data('user');
          $("#removeUserForm").attr("action", "/admin/teams/"+teamID+"/remove-user/"+userID);
          $("#removeUserForm").submit();
      })
</script>
@endsection