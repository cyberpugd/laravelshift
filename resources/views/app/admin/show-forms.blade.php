@extends('layouts.master')

@section('content')
<section class="content-header">
          <span style="font-size: 24px;">Custom Forms</span>
          <a href="/admin/form-builder" class="btn btn-success form-group" style="float:right;">Create</a>
</section>
<section class="content">
<div class="panel panel-default">
     <div class="panel-body">
<div id="app" v-cloak>
    <div style="overflow-x: auto;">
        <table class="table table-stripped">
            <thead>
                <th>Name</th>
                <th>URL</th>
                <th>Created By</th>
                <th>Active</th>
                <th>Last Modified By</th>
                <th>Last Modified Date</th>
            </thead>
            <tbody>
                <tr v-for="form in forms">
                    <td><a href="/admin/forms/@{{form.id}}">@{{form.name}}</a></td>
                    <td><a href="@{{form.url}}">@{{form.url}}</a></td>
                    <td>@{{form.owner.first_name}} @{{form.owner.last_name}}</td>
                    <td><input type="checkbox" :checked="(form.active == 1 ? true:false)" @click="toggleActive(form)"></td>
                    <td>@{{form.last_modified.first_name}} @{{form.last_modified.last_name}}</td>
                    <td>@{{moment(form.updated_at.date)}}</td>
                    <td><button class="close" @click="removeForm(form)" v-show="form.owner_id == {{Auth::user()->id}}">&times;</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</section>
@endsection

@section('footer')
    <script>
        new Vue({
            el: '#app',
            data: {
                forms: {!! json_encode($forms) !!},
                user: {!!json_encode($currentUser)!!},
            },
            methods: {
               moment: function (date) {
                   return moment(date).tz(this.user.timezone).format('MM/DD/YYYY h:mm a');
                 },
                toggleActive: function(form) {
                    this.$http.post('/admin/forms/toggleActive/'+form.id).then(function(response) {
                        //Sweet Alert 
                        data = JSON.parse(response.body);
                        status = (data.status == 1 ? 'Active':'Inactive');
                        swal({   
                            title: "Success",   
                            text: "Form status set to "+status,   
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                            });

                    }, function(response) {

                    });
                },
                removeForm: function(form) {
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
                            self.$http.post('/admin/forms/remove/'+form.id).then(function(response) {
                                data = JSON.parse(response.body);
                                toRemove = self.forms.indexOf(form);
                                self.forms.splice(toRemove, 1);
                            });
                            swal({   
                                    title: "Success",   
                                    // text: data.message,   
                                    text: "Done",
                                    type: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                        } else {
                            swal("Cancelled", "The form will not be removed.", "error");
                        }
                    });
                }
            }
        });
    </script>
@endsection