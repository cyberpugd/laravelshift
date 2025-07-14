@extends('layouts.master')

@section('content')
@include('app.components.modals.add_user')
<section class="content-header">
     <span style="font-size: 24px;">Create Ticket for User</span>
     <div class="btn-group pull-right">
          <span class="btn btn-primary" data-toggle="modal" data-target="#add_user">Add New Caller</span>
     </div>
</section>
<section class="content">
@include('app.partials.errors')
<form action="/tickets/create" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <div class="panel panel-default">
          <div class="panel-body">
               <div class="post">
                    <p>This form is for creating a ticket for a user that calls you with an issue. If you would like to create a ticket for yourself, please use the <a href="/helpdesk/tickets/create-ticket">Help Desk Portal</a></p>
               </div>
               <div class="col-lg-6">
                    <div class="form-group">
                         <label for="caller" class="col-lg-3 control-label">Caller</label>
                         <div class="col-lg-4">
                              <select name="caller" class="selectpicker" data-live-search="true" data-live-search-placeholder="Search" data-size="15" title="Select a Caller" autofocus>
                                   @foreach($callers as $caller)
                                        <option value="{{$caller->id}}" data-tokens="{{$caller->first_name}} {{$caller->last_name}}" @if(old('caller') == $caller->id) selected @endif @if(session()->has('new_user') && session('new_user') == $caller->id) selected @endif  @if($caller->out_of_office == 1) style="color: #CD5555;" @endif>{{$caller->first_name}} {{$caller->last_name}} @if($caller->out_of_office == 1) (Out of Office) @endif</option>
                                   @endforeach
                              </select>

                         </div>
                         </div>
                         <div class="form-group">
                              <label for="caller" class="col-lg-3 control-label">Agent</label>
                         <div class="col-lg-3">
                             <select name="agent" class="selectpicker" data-live-search="true" data-size="15" title="Select an Agent" autofocus>
                                        <option value="0">None Selected</option>
                                        @foreach($agents as $user)
                                                  <option value="{{$user->id}}" data-tokens="{{$user->first_name}} {{$user->last_name}}" @if(Auth::user()->id == $user->id) selected @endif @if($user->out_of_office == 1) style="color: #CD5555;" @endif>{{$user->first_name}} {{$user->last_name}} @if($user->out_of_office == 1) (Out of Office) @endif</option>
                                        @endforeach
                                   </select>
                         </div>
                    </div>
                    <div class="form-group">
                         <label for="category" class="col-lg-3 control-label">Category</label>
                         <div class="col-lg-9">
                              <select name="sub_category" class="selectpicker" data-live-search="true" data-live-search-placeholder="Just start typing..." data-size="15" title="Choose a Category">
                                   @foreach($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                             @foreach($category->subcategories as $subcategory)
                                                  <option value="{{ $subcategory->id }}" data-tokens="{{ $category->name }} {{$subcategory->name}} {{ $subcategory->tags }}" @if(old('sub_category') == $subcategory->id) selected @endif>{{ $subcategory->name }}</option>
                                             @endforeach
                                        </optgroup>
                                   @endforeach

                              </select>
                         </div>
                    </div>
                    <div class="form-group">
                              <label for="urgency" class="col-lg-3 control-label">Urgency</label>
                              <div class="col-lg-9">
                                  <select name="urgency" class="selectpicker" title="Select Severity" data-width="fit">
                                   @foreach($urgencyrows as $urgency)
                                        <option value="{{$urgency->id}}" @if($urgency->id == 3)  selected @endif>
                                        {{$urgency->name}} - {{$urgency->description}}
                                        </option>
                                   @endforeach
                              </select>
                              </div>
                    </div>
                    <div class="form-group">
                         <label for="title" class="col-lg-3 control-label">Subject</label>
                         <div class="col-lg-9">
                              <input type="text" name="title" class="form-control" placeholder="Subject" value="{{old('title')}}">
                         </div>
                    </div>
                    <div class="form-group">
                         <label for="description" class="col-lg-3 control-label">Description</label>
                         <div class="col-lg-9">
                              <textarea name="description" class="form-control" rows="6" placeholder="Please describe the issue you are having.">{{old('description')}}</textarea>
                              <em>*Add attachments on next step</em>
                         </div>

                    </div>


               </div>
          </div>
          <div id="app" class="panel-footer">
               <div class="form-group">
                    <div class="col-lg-10">
                         <button v-show="!creating" id="create-ticket" type="submit" class="btn btn-success" v-on:click="disableButton">Create Ticket</button>
                         <a v-show="creating" class="btn btn-default" v-on:click="disableButton" disabled><i class="fa fa-spin fa-cog"></i> Creating ticket please wait</a>
                    </div>
               </div>

          </div>

     </div>
</form>
</section>
@endsection
@section('footer')
<script>
     new Vue({
     el: '#app',
     data: {
          creating: false
     },
     methods: {
          disableButton: function() {
               this.creating = true;
          }
     }
});
</script>
@endsection
