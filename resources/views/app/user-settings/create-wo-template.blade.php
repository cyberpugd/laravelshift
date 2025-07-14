@extends('layouts.master')

@section('content')
@include('app.components.modals.add_wo_to_template')
@include('app.components.modals.create_wo_template')
@include('app.components.modals.edit-wo-template-name')
<div id="woTemplate">
	@include('app.components.modals.edit_wo_for_template')
	<section class="content-header">
		<div class="row">
			<div class="col-xs-12 col-lg-8 col-lg-offset-2">
				<span style="font-size: 24px;">Work Order Templates</span>
				<span class="btn btn-success form-group" data-toggle="modal" data-target="#create_wo_template"
					style="float:right;">Add Template</span>
			</div>
		</div>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-xs-12 col-lg-8 col-lg-offset-2">
				@if($templates->isEmpty())
					<div class="panel">
						<div class="panel-body">You don't have any templates created. To get started click "Add Template" above.</div>
					</div>
				@else
				<ul class="list-group">
					@foreach($templates as $template)
					<li class="list-group-item">
						<span class="badge">{{ $template->templateDetail->count() }}</span>
						<a href="/user-settings/wo-template/{{ $template->id }}">{{ $template->name }}</a>
					</li>
					@endforeach
				</ul>
				@endif
			</div>
		</div>
</section>
</div>
@endsection
