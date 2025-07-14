@extends('layouts.master')

@section('content')
<section class="content-header">
               <h1>IFS EnR Help Desk Form Builder</h1>
</section>
<section class="content">
<div id="app" class="panel panel-default" v-cloak>
    <div class="panel-body">
    {{-- <div class="alert alert-danger" role="alert" v-if="errors.length || serverErrors != ''" transition="fade">
        <ul>
            <li v-for="error in errors">
                <span v-if="error.ticket_subject">@{{error.ticket_subject}}</span>
                <span v-if="error.ticket_description">@{{error.ticket_description}}</span>
                <span v-if="error.default_value">@{{error.default_value}}</span>
            </li>
            <li v-for="error in serverErrors">@{{error}}</li>
        </ul>
    </div> --}}
        <div class="row">
            <div class="form-group col-md-4" :class="{'has-error' : serverErrors['name'] }">
                <label for="">Form Name</label>
                <input type="text" class="form-control" v-model="form.name" @keyup="serverErrors['name'] = ''" required>
                <span class="small text-danger" v-text="serverErrors['name']"></span>
            </div>     

            <div class="form-group col-md-3" :class="{'has-error' : serverErrors['subcategory_id'] }">
                <label for="category">Category</label>
                    <select class="selectpicker form-control" 
                        data-live-search="true" data-live-search-placeholder="Just start typing..." data-size="15" 
                        title="Choose a Category"
                        v-model="form.subcategory_id" required>
                        
                        <optgroup v-for="category in categories" label="@{{ category.name }}">
                                <option v-for="subcategory in category.subcategories" 
                                    value="@{{ subcategory.id }}" 
                                    data-tokens="@{{ category.name }} @{{subcategory.name}} @{{ subcategory.tags }}" 
                                    >
                                        @{{ subcategory.name }}
                                    </option>
                        </optgroup>
                        
                    </select> 
                    <span class="small text-danger" v-text="serverErrors['subcategory_id']"></span>
            </div>

             <div class="form-group col-lg-2" :class="{'has-error' : serverErrors['urgency'] }">
                <label for="urgency">Urgency</label>
                <select name="urgency" class="form-control" title="Select Severity" v-model="form.urgency" required>
                        <option v-for="urgency in urgencies" value="@{{urgency.id}}">
                            @{{urgency.name}} - @{{urgency.description}}
                        </option>
                </select>
                <span class="small text-danger" v-text="serverErrors['urgency']"></span>
            </div>

            <div class="col-md-2">
                         <label for="share_with" class="control-label">Share With</label>
                              <select name="share_with[]" class="selectpicker form-control" data-live-search="true" data-size="15" title="Select users to share with"
                              v-model="form.share_with" multiple>
                                   @foreach($users as $user)
                                   <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if(old('it_user') == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}}</option>
                                   @endforeach
                              </select> 
                         </div>
        </div>
        
        <hr>
        
        <div class="form-group">
            <button type="button" class="btn btn-large btn-primary" @click="addField"><i class="fa fa-plus"></i> Add Field</button>  
        </div>
        <table class="table table-striped" v-if="form.fields.length">
            <caption><h3>Form Fields</h3></caption>
            <thead>
               <td><strong>Row #</strong></td>
                <td><strong>Field Name</strong></td>
                <td><strong>Field Type</strong></td>
                <td align="center"><strong>Required?</strong></td>
                <td align="center" :class="{'text-danger' : serverErrors['ticket_subject'] }"><strong>Ticket Subject</strong>
                    <span class="fa fa-exclamation-circle" style="cursor: pointer;" :title="serverErrors['ticket_subject']" v-if="serverErrors['ticket_subject']"></span>
               </td>
                <td align="center" :class="{'text-danger' : serverErrors['ticket_description'] }">
                    <strong>Ticket Description</strong>
                    <span class="fa fa-exclamation-circle" style="cursor: pointer;" :title="serverErrors['ticket_description']" v-if="serverErrors['ticket_description']"></span>
               </td>
                <td><strong>Default Value</strong></td>
                <td></td>
            </thead>
            <tbody>
                <tr v-for="(index, field) in form.fields">
                    <td width="5%">
                    @{{$index+1}}
                    <span class="fa fa-arrow-circle-up" @click="sortField(index, 'up')" style="cursor: pointer;" v-show="index > 0"></span>
                    <span class="fa fa-arrow-circle-down" @click="sortField(index, 'down')" style="cursor: pointer;" v-show="index < form.fields.length-1"></span>
                    </td>
                    <td>
                         <div class="form-group" :class="{'has-error' : serverErrors['fields.'+index+'.label'] }">
                              <input type="text" class="form-control" v-model="field.label"
                              v-on:keyup="stopTyping(field)" required 
                              @keydown="serverErrors['fields.'+index+'.label'] = ''">
                         <span id="helpBlock2" class="help-block" v-show="field.name">Database Field Name: @{{field.name}}</span>
                         <span class="small text-danger" v-text="serverErrors['fields.'+index+'.label']"></span>
                         </div>
                    </td>
                    <td>
                         <div class="form-group" :class="{'has-error' : serverErrors['fields.'+index+'.type'] }">
                             <select class="form-control" v-model="field.type" required @change="changeType(field, index)">
                                 <option value="">Choose Type</option>
                                 <option value="text">Text</option>
                                 <option value="textbox">Textbox</option>
                                 <option value="select">Select</option>
                                 <option value="checkbox">Checkbox</option>
                                 <option value="date">Date</option>
                                 <option value="number">Number</option>
                                 <option value="hidden">Hidden</option>
                             </select>
                             <span class="small text-danger" v-text="serverErrors['fields.'+index+'.type']"></span>
                         </div>
                    </td>
                    <td align="center">
                         <div class="form-group" :class="{'has-error' : serverErrors['fields.'+index+'.is_required'] }">
                             <input id="required@{{$index}}" type="checkbox" 
                             @click="serverErrors['fields.'+index+'.is_required'] = ''"
                             v-model="field.is_required" 
                             :disabled="field.type == 'checkbox' ? true : false"
                             :checked="field.type == 'checkbox' ? false : true">
                         </div>
                         <span class="text-danger">
                              <span class="fa fa-exclamation-circle" style="cursor: pointer;" :title="serverErrors['fields.'+index+'.is_required']" v-if="serverErrors['fields.'+index+'.is_required']"></span>
                         </span>
                    </td>
                    <td align="center">
                        <input id="subject@{{$index}}" type="radio" name="ticket_subject"       
                        :disabled="field.type == 'checkbox' ? true : false"
                        :checked="field.type == 'checkbox' ? false"
                        v-model="field.ticket_subject"
                        @change="serverErrors['ticket_subject'] = ''"
                        @click="removeOthersSubject(field)">
                    </td>
                    <td align="center">
                        <input id="description@{{$index}}" type="radio" 
                        :disabled="field.type == 'checkbox' ? true : false"
                        :checked="field.type == 'checkbox' ? false"
                        name="ticket_description" 
                        v-model="field.ticket_description"
                        @change="serverErrors['ticket_description'] = ''"
                        @click="removeOthersDescription(field)">
                    </td>
                    <td align="center">
                         <div class="form-group" :class="{'has-error' : serverErrors['fields.'+index+'.default_value'] }">
                             <input type="text" class="form-control" v-model="field.default_value"
                              v-if="((field.type == 'text' || field.type == 'select' || field.type == 'number' || field.type == 'hidden') && field.ticket_description == '') || field.type == 'select' || field.type == 'number' || field.type == 'text'"
                             @keyup="serverErrors['fields.'+index+'.default_value'] = ''">
                             <textarea rows="5" class="form-control"
                              v-model="field.default_value"
                              v-on:keyup="stopTyping(field)" required
                              @keydown="serverErrors['fields.'+index+'.label'] = ''" 
                              v-if="((field.type == 'hidden' || field.type == 'textbox') && field.ticket_description == 'on') || field.type == 'textbox'"
                              ></textarea>
                             <span class="help-block" v-if="field.type == 'select'">Enter your select options here, separate multiple values with a comma.</span>
                             <span class="help-block" v-if="field.type == 'hidden'">Reference other form fields using "@fieldname" format. <br> E.g. "New Request for @@{{form.fields[0].name}}" could be a hidden field for a Ticket Subject.</span>
                             <input type="checkbox" v-model="field.default_value" v-if="field.type == 'checkbox'"
                             @click="serverErrors['fields.'+index+'.default_value'] = ''">
                             <input 
                                type='text' 
                                class="form-control datetimepicker" 
                                v-show="field.type == 'date'" v-model="field.default_value"
                                @click="serverErrors['fields.'+index+'.default_value'] = ''">
                             <span class="small text-danger" v-text="serverErrors['fields.'+index+'.default_value']"></span>
                         </div>
                    </td>
                    <td align="center">
                        <button class="close" @click="removeField($index)">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <a type="button" class="btn btn-large btn-success"
                    @click="saveForm" 
                    v-bind:class="{disabled: submitting}"
                    v-show="form.fields.length">
                        <span v-if="submitting"><i class="fa fa-cog fa-spin"></i> Saving Please Wait...</span>
                        <span v-if="!submitting">Save Form</span>
                        </a>
    </div><!-- End panel-body -->
</div>
</section>
@endsection

@section('footer')
<script>
    new Vue({
        el: '#app',
        data: {
            form: {
                name: '',
                subcategory_id: '',
                share_with: [],
                urgency: '3',
                fields: [],
            },
            categories: {!!json_encode($categories)!!},
            urgencies: {!!json_encode($urgencies)!!},
            errors: [],
            serverErrors: '',
            submitting: false,
            timeout: null,
            doneTypingInterval: 500,
        },

        methods: {
            addField: function() {
                this.form.fields.push({label: '', type: '', is_required: '', ticket_subject: '', ticket_description: '', default_value: '', name: ''});
            },
            removeField: function(field) {
                this.form.fields.splice(field, 1);
            },
            changeType: function(field, index) {
                    if(field.type == 'date') {
                         $('.datetimepicker').datetimepicker({
                              timepicker:false,
                              format: 'm/d/Y',
                         });
                    }
                    field.default_value = '';
                    field.is_required = true;
                    if(field.type == 'checkbox') {
                         field.is_required = false;
                         field.ticket_subject = '';
                         field.ticket_description = '';
                    }

                    if(this.serverErrors['fields.'+index+'.type']) {
                         this.serverErrors['fields.'+index+'.type'] = '';
                    }
            },
            sortField: function(index, direction) {
                    if(direction == 'up') {
                         // move the item in the underlying array
                         this.form.fields.splice(index-1, 0, this.form.fields.splice(index, 1)[0]);
                         // update order property based on position in array
                         this.form.fields.forEach(function(item, index){
                         item.order = index + 1;
                         });
                    }
                    if(direction == 'down') {
                         // move the item in the underlying array
                         this.form.fields.splice(index+1, 0, this.form.fields.splice(index, 1)[0]);
                         // update order property based on position in array
                         this.form.fields.forEach(function(item, index){
                         item.order = index + 1;
                         });
                    }
            },
            stopTyping: function(field) {
                clearTimeout(this.timeout);
                self = this;
                this.timeout = setTimeout(function() {
                        if(field) {
                            self.getSlug(field)
                        }
                    }, this.doneTypingInterval);
            },
            getSlug: function(field) {
                this.$http.get('/admin/get-slug/'+field.label).then(function(response) {
                    slug = JSON.parse(response.body);
                    field.name = slug.slug;
                }, function(response) {

                });
            },
            saveForm: function() {
                this.submitting = true;
                if(!this.errors.length) {
                    this.$http.post('/admin/form-builder', this.form).then(function(response) {
                        data = JSON.parse(response.body);
                        window.location.href = '/admin/forms';
                    }, function(response) {
                        this.serverErrors = JSON.parse(response.body);
                        swal({
                              title: 'Oops...',
                              text: 'We had an issue processing your request, please fix any items in red.',
                              type: 'error',
                              showConfirmButton: true
                        });
                        this.submitting = false;
                    });
                } else {
                    this.submitting = false;
                }
            },
            removeOthersSubject: function(currentField) {
                this.form.fields.forEach(function(field) {
                    field.ticket_subject = '';
                });
                currentField.ticket_subject = true;
            },
            removeOthersDescription: function(currentField) {
                this.form.fields.forEach(function(field) {
                    field.ticket_description = '';
                });
                currentField.ticket_description = true;
            },

        }
    });


</script>
@endsection