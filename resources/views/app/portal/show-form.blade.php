@extends('layouts.portal')

@section('content')

<div id="app" class="container" v-cloak>
<div class="panel panel-default">
          <div class="panel-heading">
               <h3 class="panel-title">{{$form->name}}</h3>
          </div>
          <div class="panel-body">
          <div class="alert alert-danger" role="alert" v-if="serverErrors != ''" transition="fade">
          <ul>
               <li v-for="error in serverErrors">@{{error}}</li>
          </ul>
          </div>

                    <div v-for="field in fields">
                         {{-- <input type='hidden' class="form-control" name="@{{field.name}}" value="@{{field.default_value}}"" v-model="formFields[field.name]"
                                v-if="field.type == 'hidden' && field.ticket_description == false"> --}}

                         <div class="row">
                         <label for="" class="col-md-3 control-label" style="text-align: left; margin-top: 10px;" v-if="field.type != 'hidden'">@{{field.label}}</label>
                         <div class="col-md-8" style="margin-top: 10px;" v-if="field.type != 'hidden'">
                              <input class="form-control" type="text" name="@{{field.name}}" v-if="field.type == 'text'" v-model="formFields[field.name]"
                              value="@{{field.default_value}}">

                              <textarea class="form-control" name="@{{field.name}}" v-if="field.type == 'textbox'" rows="5" v-model="formFields[field.name]">@{{field.default_value}}</textarea>


                              <select class="form-control" name="@{{field.name}}" v-if="field.type == 'select'" v-model="formFields[field.name]">
                                   <option value="" selected>Please Choose</option>
                                   <option v-for="value in field.default_value.split(',')">@{{ value }}</option>
                              </select>

                              <input type="checkbox" name="@{{field.name}}" v-if="field.type == 'checkbox'" v-model="formFields[field.name]"
                              :checked="field.default_value == 1 ? true : false" style="width: 18px;" class="form-control">

                              <input
                                type='text'
                                class="form-control datetimepicker"
                                name="@{{field.name}}"
                                v-if="field.type == 'date'" v-model="formFields[field.name]" value="@{{field.default_value}}">

                                <input type='text' class="form-control" name="@{{field.name}}" value="@{{field.default_value}}"" v-model="formFields[field.name]"
                                v-if="field.type == 'number'">



                         </div>
                         </div>
                    </div>
                    <div class="col-md-11" style="margin-top: 10px;">
                        <button class="btn btn-success pull-right"
                         v-bind:class="{disabled: submitting}"
                         @click="saveForm">
                                  <span v-if="submitting"><i class="fa fa-cog fa-spin"></i> Submitting Please Wait...</span>
                                  <span v-if="!submitting">Submit Form</span></a>
                        </button>
                    </div>
          </div>
</div>
</div>
@endsection

@section('footer')
<script>
    new Vue({
        el: '#app',
        data: {
            fields: {!!json_encode($form->fields)!!},
            formFields: {!!json_encode($formFields)!!},
            submitting: false,
            serverErrors: '',
        },
        methods: {
               saveForm: function() {
                    this.submitting = true;
                    this.$http.post('/forms/{{$form->slug}}', this.formFields).then(function(response) {
                         data = JSON.parse(response.body);
                         window.location.href = '/helpdesk/tickets/'+data.id
                         this.submitting = false;
                    }, function(response) {
                         this.serverErrors = JSON.parse(response.body);
                         $('html, body').animate({scrollTop:0}, 'slow');
                         this.submitting = false;
                    });
               }
        },
    });
     $(document).ready( function() {
          //Initialize datetime pickers
          $('.datetimepicker').datetimepicker({
               timepicker:false,
               format: 'm/d/Y',
          });
     });
</script>
@endsection
