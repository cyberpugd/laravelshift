@extends('layouts.master')
@section('content')
@include('app.components.modals.add-location')
<div id="addLocation">
     <div id="errors" class="alert alert-danger fade in alert-dismissable alert-center" style="display:none;">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
     </div>
<section class="content-header">
               <span style="font-size: 24px;">Locations</span>
               <span class="btn btn-success form-group" data-toggle="modal" data-target="#add_location" style="float:right;">Add Location</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
          <div id="accordion" class="panel-group">
               @foreach($locations as $location)
               <div class="panel panel-default">
                    <div id="formdiv{{$location->id}}" class="panel-heading toggle-panel" @if($location->holidays->isEmpty()) style="background-color: #fcf8e3; @endif>
                         <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#panel{{$location->id}}">
                              {{$location->city}}
                         </h4>
                    </div>
                    <div id="panel{{$location->id}}" class="panel-collapse collapse">
                         <div class="panel-body">
                              <div>
                                   <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="active{{$location->id}}">
                                             <div class="table table-responsive">
                                             <div>
                                                  Timezone: {{$location->timezone}}
                                             </div>
                                             <form data-url="/admin/locations/{{$location->id}}" method="POST">
                                                  {!! csrf_field() !!}
                                                  <table id="activetable{{$location->id}}" class="table table-striped">
                                                       <thead>
                                                            <td><strong>Active</strong></td>
                                                            <td><strong>Holiday</strong></td>
                                                            <td><strong>Date</strong></td>
                                                       </thead>
                                                       <tbody>
                                                            @foreach($holidays as $holiday)
                                                            <tr id="activetr{{ $holiday->id }}">
                                                                 <td>
                                                                      <input id="holidays{{$location->id}}" type="checkbox" name="holidays[]" value="{{$holiday->id}}" @if($location->hasHoliday($holiday->id)) checked @endif>
                                                                 </td>
                                                                 <td id="holiday{{$holiday->id}}">{{ $holiday->name }}</td>
                                                                 <td id="hdate{{$holiday->id}}">{{ $holiday->date->toFormattedDateString() }}</td>
                                                            </tr>
                                                            @endforeach
                                                       </tbody>
                                                  </table>
                                                  <button type="submit" v-on:click="saveLocation" class="btn btn-success">Save</button>
                                                  </form>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                      </div>
                    </div>
                    @endforeach
               </div>
          </div>
          </div>
          </section>
     </div>
     @endsection
     @section('footer')
     <script>
          new Vue({
               el: '#addLocation',
               data: {
                    resultColor: '',
                    updateResult: ''
               },
               methods: {
                    saveLocation: function() {
                          event.preventDefault();
                         var form = $(event.target).closest('form');
                         // console.log(form);
                         // form.find('input').filter(':visible:first').focus();
                         $.ajax({
                              type: 'POST',
                              url: form.data('url'),
                              data: form.serialize(),
                              success: function(data){
                                   console.log(data);
                                   this.updateResult = data.result;
                                   this.resultColor = data.color;
                                   $('.ajax-flash').html(data.result).addClass('alert alert-success');
                                   $('.ajax-flash').fadeIn().delay(2000).fadeOut();
                              }.bind(this),
                              error: function(data) {
                                   var response = JSON.parse(data.responseText);
                                   if(response.error) {
                                        $('.ajax-flash').html(response.error).addClass('alert alert-danger');
                                        $('.ajax-flash').fadeIn().delay(2000).fadeOut();
                                   }
                              }.bind(this)
                         });

                    },
               }
          });
     </script>
     @endsection
