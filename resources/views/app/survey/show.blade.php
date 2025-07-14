@extends('layouts.portal')

@section('content')
<div class="panel panel-default">
          <div class="panel-body">
            <iframe src="{{$survey_link}}" width="100%" height="800" scrolling="no" style="border: none;"></iframe>
            </div>
        </div>
@endsection
