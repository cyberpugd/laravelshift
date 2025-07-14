@extends('layouts.master')

@section('content')
<section class="content-header">
          <span style="font-size: 24px;">My Change Control Templates</span>
          <a href="/user-settings/cc-template" class="btn btn-success form-group" style="float:right;">Add Template</a>
</section>
<section class="content">
<div class="panel panel-default">    
    <div class="panel-body">
<div class="col-md-12 row" style="margin-top: 10px;">
     <table class="table table-striped">
          <thead>
               <td><strong>Template Name</strong></td>
               <td align="right"><strong>Modify/Delete</strong></td>
          </thead>
          <tbody>
               @foreach($templates as $template)
                    <tr>
                         <td>{{$template->name}}</td>
                         <td align="right">
                         <form action="/user-settings/cc-template/delete/{{$template->id}}" method="post">
                              <a href="/user-settings/cc-template/{{$template->id}}" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                              <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                         </form>
                         </td>
                    </tr>
               @endforeach
          </tbody>
     </table>
</div>
</div>
</div>
</section>
@endsection

