@extends('layouts.master')

@section('content')
<form action="/admin/mail-setup" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <section class="content-header">
               <span style="font-size: 24px;">Settings</span>
     </section>
     <section class="content">
     <div class="panel panel-default">
          <div class="panel-body">
               <div class="col-lg-6">

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail Server</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_server" class="form-control" value="@if($settings){{$settings->mail_server}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail Port</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_port" class="form-control" value="@if($settings){{$settings->mail_port}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail User</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_user" class="form-control" value="@if($settings){{$settings->mail_user}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail Password</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_password" class="form-control" value="@if($settings){{$settings->mail_password}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Email Address</label>
                         <div class="col-lg-9">
                              <input type="text" name="email_address" class="form-control" value="@if($settings){{$settings->email_address}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail Folder</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_folder" class="form-control" value="@if($settings){{$settings->mail_folder}}@endif">
                         </div>
                    </div>

                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Mail Processed Folder</label>
                         <div class="col-lg-9">
                              <input type="text" name="mail_processed_folder" class="form-control" value="@if($settings){{$settings->mail_processed_folder}}@endif">
                         </div>
                    </div>

               </div>
               <div class="col-md-6">
                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Help Desk Phone Number</label>
                         <div class="col-lg-9">
                              <input type="text" name="phone_number" class="form-control" value="@if($settings){{$settings->phone_number}}@endif">
                         </div>
                    </div>
               </div>
          </div>
          <div class="panel-footer">
               <div class="form-group">
                    <div class="col-lg-10">
                         <button id="create-ticket" type="submit" class="btn btn-success">Save</button>
                    </div>
               </div>

          </div>

     </div>
     </section>
</form>

@endsection
