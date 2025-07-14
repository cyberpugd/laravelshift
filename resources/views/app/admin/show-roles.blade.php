@extends('layouts.master')
@section('content')
@include('app.components.modals.create-role')
@include('app.components.modals.edit-role')
     <div id="errors" class="alert alert-danger fade in alert-dismissable alert-center" style="display:none;">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
     </div>
<section class="content-header">
               <span style="font-size: 24px;">Role Management</span>
               <span class="btn btn-success form-group" data-toggle="modal" data-target="#create_role" style="float:right;">Add Role</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
          <div id="accordion" class="panel-group">
               @foreach($roles as $role)
               <div class="panel panel-default">
                    <div id="formdiv{{$role->id}}" class="panel-heading toggle-panel">
                    <div style="text-align: left;" data-toggle="collapse" data-parent="#accordion" href="#panel{{$role->id}}">
                         <h4 class="panel-title pull-left">
                              {{$role->label}}
                         </h4>
                    </div>
                         <div style="text-align: right;">
                         <span style="display: inline;">
                                   <button title="Edit Role Name" class="btn btn-default btn-sm editRole" data-id="{{$role->id}}" data-name="{{$role->label}}" data-toggle="modal" data-target="#edit_role" ><i class="fa fa-pencil"></i></button>
                              </span>
                         </div>
                    </div>
                    <div id="panel{{$role->id}}" class="panel-collapse collapse">
                         <div class="panel-body">
                              <h4>Permissions</h4>
                              <form action="/admin/roles/update-permissions/{{$role->id}}" method="POST">
                              {!! csrf_field() !!}
                                   <table class="table">
                                        <thead>
                                             <td></td>
                                             <td>Permission Name</td>
                                             <td>Description</td>
                                        </thead>
                                        <tbody>
                                             @foreach($permissions as $permission)
                                                  <tr>
                                                       <td><input type="checkbox" name="permissions[]" value="{{$permission->id}}" @if($role->hasPermission($permission->name)) checked @endif></td>
                                                       <td>{{$permission->name}}</td>
                                                       <td>{{$permission->label}}</td>
                                                  </tr>
                                             @endforeach
                                        </tbody>
                                   </table>
                                   <button type="submit" class="btn btn-success">Save</button>
                              </form>
                         </div>
                      </div>
                    </div>
                    @endforeach
               </div>
               </div>
               </div>
               </section>
          </div>
@endsection

@section('footer')
<script>
      $('.editRole').on("click", function() {
          var roleID = $(this).data('id');
          var roleName = $(this).data('name');
          $(".modal-body #roleName").val( roleName );
          $(".modal-content #editRoleForm").attr("action", "/admin/roles/edit/" + roleID);

     });
</script>
@endsection