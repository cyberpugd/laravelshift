@extends('layouts.master')
@section('content')
<div id="addLocation">
@include('app.components.modals.add-location')
<section class="content-header">
               <span style="font-size: 24px;">Locations</span>
               <span class="btn btn-success form-group" data-toggle="modal" data-target="#add_location" style="float:right;">Add Location</span>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
     <div id="categoryManagement" class="row table-responsive" v-cloak>
          <table class="table table-striped">
               <thead>
                    <th>Name</th>
                    <th>Timezone</th>
                    <th>Holidays</th>
               </thead>
               <tbody>
                    <tr v-for="location in locations">
                        <td>@{{location.city}}</td>
                        <td>@{{location.timezone}}</td>
                        <td>
                            <div v-show="editing == location.id">
                                <select name="holiday"
                                    v-model="location.holidays"
                                    class="selectpicker form-control"
                                    data-live-search="true"
                                    data-size="15"
                                    data-selected-text-format="count"
                                    multiple
                                    title="Choose Holidays">
                                    <option v-for="holiday in holidays"
                                        :data-tokens="holiday.name"
                                        :value="holiday">
                                            @{{moment(holiday.date)}} - (@{{holiday.name}})
                                    </option>
                                </select>
                            </div>
                                <a v-show="editing !== location.id"
                                    data-toggle="tooltip"
                                    data-html="true"
                                    :title="locationHolidays(location)"
                                    data-placement="left"
                                    style="cursor: pointer;">
                                    @{{location.holidays.length}} holidays selected
                                </a>
                        </td>
                         <td style="text-align: right">
                            <span style="cursor: pointer;" @click="editLocation(location)" v-show="editing !== location.id"
                                title="Edit">
                                <i class="btn fa fa-pencil"></i>
                            </span>
                            <span style="cursor: pointer;" @click="updateLocation(location)" v-show="editing == location.id"
                                title="Save">
                                <i class="btn fa fa-save" v-show="updatingLocation !== location.id"></i>
                                <i class="btn fa fa-cog fa-spin" v-show="updatingLocation == location.id"></i>
                            </span>
                            <span style="cursor: pointer;" @click="stopEditLocation(location)" v-show="editing == location.id"
                                title="Cancel Edit">
                                <i class="btn fa fa-ban"></i>
                            </span>
                         </td>
                    </tr>
               </tbody>
          </table>
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
                    updateResult: '',
                    locations: {!! json_encode($locations) !!},
                    holidays: {!! json_encode($holidays) !!},
                    editing: 0,
                    updatingLocation: 0,
               },
               methods: {
                    saveLocation: function() {
                          event.preventDefault();
                         var form = $(event.target).closest('form');
                         $.ajax({
                              type: 'POST',
                              url: form.data('url'),
                              data: form.serialize(),
                              success: function(data){
                              }.bind(this),
                              error: function(data) {
                                   var response = JSON.parse(data.responseText);
                                   if(response.error) {
                                        $('.ajax-flash').html(response.error).addClass('alert alert-danger');
                                        $('.ajax-flash').fadeIn().delay(2000).fadeOut();
                                   }
                              }.bind(this),
                         });
                    },
                    updateLocation: function(location) {
                        this.updatingLocation = location.id;
                        this.$http.post('/admin/locations/'+location.id, location).then(function(response) {
                            this.editing = 0;
                            this.updatingLocation = 0;
                            swal({
                                title: "",
                                text: "Update Successful!",
                                timer: 1500,
                                showConfirmButton: false
                              });
                        });
                    },
                    locationHolidays: function(location) {
                        var returnString;
                        location.holidays.forEach(function(holiday) {
                            var holidayName = holiday.name +'<br>';
                            returnString = (returnString ? returnString + holidayName : holidayName);
                        });
                        return returnString;
                    },
                    editLocation: function(location) {
                        this.editing = location.id;
                    },
                    stopEditLocation: function(location) {
                        this.editing = 0;
                    },
                    moment: function(date) {
                        return moment(date).format('M/D/YYYY');
                    }
               }
          });
     </script>
     @endsection
