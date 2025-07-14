@extends('layouts.master')

@section('content')
<div id="survey-group">
    <section class="content-header">
        <span style="font-size: 24px;">{{ $survey_group->name}}</span>
    </section>
    <section class="content">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-md-4">
                <form action="/admin/survey-groups/{{$survey_group->id}}" method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $survey_group->name }}">
                    </div>
                    <div class="form-group">
                        <label>Survey Link</label>
                        <input type="text" class="form-control" name="survey_link" value="{{ $survey_group->survey_link }}">
                    </div>
                    <div class="form-group">
                        <label>Agents</label>
                        <select name="agents[]" class="selectpicker" data-live-search="true" data-size="15" title="Select an Agent" autofocus multiple>
                            <option value="0">None Selected</option>
                            @foreach($agents as $user)
                            <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}"
                                @if($survey_group->users->contains('id', $user->id)) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                            @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" @click.prevent="deleteGroup">Delete</button>
                        </div>
                    </div>
                </form>
                <form id="delete" action="/admin/survey-groups/{{$survey_group->id}}/delete" method="post"></form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer')
    <script>
        new Vue({
            el: '#survey-group',
            methods: {
                deleteGroup: function() {
                        self = this;
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this form.",
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
                            document.getElementById('delete').submit();
                        } else {
                            swal("Cancelled", "The group will not be removed.", "error");
                        }
                    });
                }
            }
        });
    </script>
@endsection
