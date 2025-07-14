@extends('layouts.master')

@section('content')
@include('app.components.modals.create-survey-group')
<div id="survey-group">
<section class="content-header">
          <span style="font-size: 24px;">Survey Groups</span>
          <button class="btn btn-success form-group" data-toggle="modal" data-target="#create_survey_group" style="float:right;">Add Survey Group</button>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
          <table class="table table-striped">
               <thead>
                    <tr>
                         <td><strong>Name</strong></td>
                         <td><strong>Survey Link</strong></td>
                    </tr>
               </thead>
               <tbody>
                    @foreach($survey_groups as $group)
                        <tr>
                            <td><a href="/admin/survey-groups/{{$group->id}}">{{ $group->name }}</a></td>
                            <td>{{ $group->survey_link}}</td>
                        </tr>
                    @endforeach
               </tbody>
          </table>

     </div>
</div>
</section>
</div>
@endsection
