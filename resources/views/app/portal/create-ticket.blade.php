@extends('layouts.portal')

@section('content')

<div style="margin-bottom: 5px;">
<a href="/helpdesk/dashboard" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Home</a>
</div>
<form action="/tickets/create-ticket" method="POST" class="form-horizontal">
     {{csrf_field()}}
     <div class="panel panel-default">
          <div class="panel-heading">
               <h3 class="panel-title">Create Ticket</h3>
          </div>
          <div class="panel-body">
               <div class="col-lg-6">
                    <div class="form-group">
                         <label for="category" class="col-lg-3 control-label">Category</label>
                         <div class="col-lg-9">
                              <select id="opt" name="sub_category" class="selectpicker" data-live-search="true" data-live-search-placeholder="Just start typing..." data-size="15" title="Choose a Category">
                                   @foreach($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                             @foreach($category->subcategories as $subcategory)
                                                  <option value="{{ $subcategory->id }}" data-tokens="{{ $category->name }} {{$subcategory->name}} {{ $subcategory->tags }}" @if(old('sub_category') == $subcategory->id) selected @endif>{{ $subcategory->name }}</option>
                                             @endforeach
                                        </optgroup>
                                   @endforeach
                                  
                              </select> 
                    <span id="category" style="font-weight: bold;"></span>
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
                              <textarea id="focus" name="description" class="form-control" rows="6" placeholder="Please describe the issue you are having.">{{old('description')}}</textarea>
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

     $('#opt').change(function() {
          var str = "";
          $('#opt option:selected').each(function() {
               str = $(this).parent().attr('label');
          });
          $('#category').text(' in '+str);
     });


</script>
@endsection