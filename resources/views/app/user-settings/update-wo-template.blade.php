@extends('layouts.master')

@section('content')
@include('app.components.modals.add_wo_to_template')
@include('app.components.modals.create_wo_template')
@include('app.components.modals.edit-wo-template-name')
<div id="woTemplate">
	@include('app.components.modals.edit_wo_for_template')
	<section class="content-header">
		<div class="row">
			<div class="col-xs-12">
				<span style="font-size: 24px;">Edit Template</span>
			</div>
		</div>
	</section>
	<section class="content">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
				<div class="col-xs-12" style="margin-top: 10px;">
					<div>
						<form method="POST" action="/user-settings/wo-template/edit/{{$template->id}}">
						<div class="col-md-4">
							<div class="form-group">
								<label for="name">Template Name</label>
								<input type="text" name="name" class="form-control" value="{{ $template->name }}"
									autofocus>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="share_with">Share With</label><br>
								<select name="shared_with[]" class="selectpicker" data-live-search="true" data-size="15"
									title="Share With" autofocus multiple>
									@foreach($users as $user)
									@if($user->can('be_assigned_ticket'))
									<option value="{{$user->id}}"
										data-tokens="{{$user->first_name}} {{$user->last_name}}" @if(collect($template->
										users)->pluck('id')->contains($user->id))
										selected @endif>{{$user->last_name}}, {{$user->first_name}}
									</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>
						<div id="delete" class="col-md-4" style="text-align: right; padding-right: 0px;">
							
							<button type="submit" class="btn btn-success">Save</button>
						

							<button type="button" class="btn btn-danger" @click="deleteTemplate({{$template->id}})" title="Delete Template">Delete</button>
							
						</div>
						</form>
						<form id="deleteForm" action="" method="post">
							{{csrf_field()}}
						</form>
					</div>
				</div>
				</div>

				<div class="row">
					<div class="col-xs-12" style="border-top: 1px solid lightgray;">
						<div class="row" style="margin-top: 10px;">
							<h4 class="col-xs-12 col-lg-6">Work Orders</h4>
							<div class="col-xs-12 col-lg-6">
								<button class="btn btn-success addTemplateDetail pull-right" data-templateid="{{$template->id}}"
									data-toggle="modal" data-target="#add_wo_to_template">Add Work Order</button>
							</div>
						</div>
						<table class="table">
							<thead>
								<td><strong>Subject</strong></td>
								<td><strong>Assigned To</strong></td>
								<td><strong>Due On</strong></td>
								<td></td>
							</thead>
							<tbody>
								@foreach($template->templateDetail as $work_order)
								<form id="WO{{$work_order->id}}" class="form-inline"
									action="/user-settings/wo/{{$work_order->id}}" method="post">
									{{csrf_field()}}</form>
								<tr>
									<td>{{$work_order->subject}}</td>
									<td>{{$work_order->assignedTo->first_name}}
										{{$work_order->assignedTo->last_name}}</td>
									<td>@if($work_order->due_in == 1) {{$work_order->due_in}} day after
										ticket created @elseif($work_order->due_in == 0) Ticket Create
										Date
										@elseif($work_order->due_in == -1) Ticket Due Date @else
										{{$work_order->due_in}} days after ticket created @endif</td>
									<td>
										<button class="btn btn-default btn-xs editWO"
											@click="showModal({{$work_order->id}})" data-toggle="modal"
											data-target="#edit_wo_for_template"><i class="fa fa-pencil"></i></button>
										<button class="btn btn-danger btn-xs" @click="deleteWO({{$work_order->id}})"><i
												class="fa fa-trash"></i></button>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
@section('footer')
<script>
	$('.addTemplateDetail').click(function() {
          var templateid = $(this).data('templateid');
          $("#templateId").val( templateid );
     });

     $('.editTemplate').on("click", function() {
          var templateId = $(this).data('templateid');
          var templateName = $(this).data('name');
          console.log(templateId);
          $("#templateName").val( templateName );
          $("#editTemplateForm").attr("action", "/user-settings/wo-template/edit/" + templateId);
     });

     new Vue({
          el: '#woTemplate',
          data: {
               subject: '',
               workRequested: '',
               assignedTo: '',
               dueOn: '',
               wo_id: '',
          },
          methods: {
               showModal: function(woid) {
                    this.$http.get('/user-settings/wo/'+woid).then(function(response) {
                         wo = response.json();
                         this.subject = wo.subject;
                         this.workRequested = wo.work_requested;
                         this.assignedTo = wo.assigned_to;
                         this.dueOn = wo.due_in;
                         this.wo_id = wo.id;
                    });
               },
               deleteWO: function(woid) {
                    $('#WO'+woid).submit();
               },
			   deleteTemplate: function(id) {
					var confirmed = confirm("Are you sure?");
					if(confirmed) {
						$('#deleteForm').attr('action', '/user-settings/wo-template/'+id);
						$('#deleteForm').submit();
					}
			   }
          }
     });
          
              
</script>
@endsection