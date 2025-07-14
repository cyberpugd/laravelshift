@extends('layouts.master')
@section('content')
@include('app.components.modals.create-team')
<div id="app" v-cloak>
     <section class="content-header">
               <span style="font-size: 24px;">Team Management</span>
               <span class="btn btn-success form-group" data-toggle="modal" data-target="#create_team" style="float:right;">Add Team</span>
     </section>

     <section class="content">
     <div class="panel panel-default">
          <div class="panel-body">
     <div class="table-responsive">
          <table class="table table-striped">
               <thead>
                    <th>Name</th>
                    <th>Created At</th>
                    <th></th>
               </thead>
               <tbody>
                    <tr v-for="team in teams">
                         <td><a href="/admin/teams/@{{team.id}}">@{{team.name}}</a></td>
                         <td>@{{moment(team.created_at.date)}}</td>
                         <td></td>
                    </tr>
               </tbody>
          </table>
     </div>
</div>
</section>
</div>
@endsection
@section('footer')
     <script>
          new Vue({
               el: '#app',
               data: {
                    teams: {!! json_encode($teams) !!}
               },
               methods: {
                    moment: function (date) {
                        return moment(date).format('MM/DD/YYYY h:mm a');
                      },
               }
          });
     </script>
@endsection