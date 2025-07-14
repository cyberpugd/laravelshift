@extends('layouts.master')
@section('content')
<div id="app" v-cloak>
@include('app.components.modals.edit-team')
     <section class="content-header">
               <span style="font-size: 24px;"><span style="color: #337ab7">Team:</span> @{{team.name}} <span class="btn text-primary" data-toggle="modal" data-target="#edit_team"><i class="fa fa-pencil fa-2x"></i></span></span>
          <a href="{{URL::Previous()}}" class="btn btn-sm btn-default pull-right">Back</a>
     </section>

     <section class="content">
     <div class="panel panel-default">
          <div class="panel-body">
          <div class="col-md-4">
               <div class="form-group">
                    <label>Available Subcategories</label>
                    <select class="form-control" size="10" multiple>
                         <option v-for="subcategory in availableSubcategories" value="@{{subcategory.id}}" @click="selectSubcategory(subcategory)">@{{subcategory.category_name}} - @{{subcategory.name}}</option>
                    </select>
               </div>
          </div>

          <div class="col-md-4">
               <div class="form-group">
                    <label>Selected Subcategories</label>
                    <select class="form-control" size="10" multiple>
                         <option v-for="subcategory in team.subcategories" value="@{{subcategory.id}}" @click="removeSubcategory(subcategory)">@{{subcategory.category.name}} - @{{subcategory.name}}</option>
                    </select>
               </div>
          </div>

          <div class="col-md-4">
               <label for="name">Allow Self Enroll</label>
               <input type="checkbox" name="self_enroll" v-model="team.self_enroll">
          </div>

          <div class="col-md-12" style="margin-bottom: 25px;">
               <button class="btn btn-success" @click="saveTeam">Save</button>
          </div>
     </div>
     </div>

     <div class="panel panel-default">
          <div class="panel-body">
     <div class="col-md-12 row" style="height: 400px; overflow: auto;">
          <table class="table table-striped">
          <caption>Team Members</caption>
               <thead>
                    <th v-show="addingUser"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th style="text-align: right;">
                         <button class="btn btn-sm btn-default" @click="addingUser = true" v-show="!addingUser"><i class="fa fa-pencil"></i></button>
                         <button class="btn btn-sm btn-default" @click="cancelEditUsers" v-show="addingUser"><i class="fa fa-ban"></i></button>
                         <button class="btn btn-sm btn-success" @click="syncUsers" v-show="addingUser"><i class="fa fa-save"></i></button>
                    </th>
               </thead>
               <tbody>
                    <tr v-for="member in team.users">
                         <td v-show="addingUser"><input type="checkbox" @click="toggleAgents(member)" checked></td></td>
                         <td><a href="/admin/users/@{{member.id}}">@{{member.first_name}} @{{member.last_name}}</a></td>
                         <td><a href="mailto:@{{member.email}}">@{{member.email}}</a></td>
                         <td>@{{member.phone_number}}</td>
                         <td>@{{member.location.city}}</td>
                         <td></td>
                    </tr>
                    <tr v-show="addingUser" v-for="agent in agents">
                         <td><input type="checkbox" @click="toggleAgents(agent)"></td></td>
                         <td>@{{agent.first_name}} @{{agent.last_name}}</td>
                         <td>@{{agent.email}}</td>
                         <td>@{{agent.phone_number}}</td>
                         <td>@{{agent.location.city}}</td>
                         <td></td>
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
               el: '#app',
               data: {
                    team: {!! json_encode($team) !!},
                    teamAgentsReset: {!! json_encode($team->users) !!},
                    availableSubcategories: {!! json_encode($availableSubcategories) !!},
                    addingUser: false,
                    agents: {!!json_encode($agents)!!},
               },
               methods: {
                    cancelEditUsers: function() {
                         this.team.users = this.teamAgentsReset;
                         this.addingUser = false;
                    },
                    toggleAgents: function(agent) {
                         index = this.team.users.indexOf(agent);
                         if(index > -1) {
                              this.team.users.splice(index, 1);
                              this.agents.unshift(agent);
                         } else {
                              index2 = this.agents.indexOf(agent);
                              this.team.users.push(agent);
                              this.agents.splice(index2, 1);
                         }

                    },
                    saveTeamName: function() {
                         this.$http.post('/admin/teams/edit/'+this.team.id, this.team).then(function(response) {
                               swal({
                                        title: '',
                                        text: 'Team Name updated successfully.',
                                        type: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                  });
                         });
                    },
                    saveTeam: function() {
                         this.$http.post('/admin/teams/subcategories/sync/'+this.team.id, this.team).then(function(response) {
                              swal({
                                        title: '',
                                        text: 'Team updated successfully.',
                                        type: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                  });
                         });
                    },
                    syncUsers: function() {
                         this.$http.post('/admin/teams/'+this.team.id+'/sync-users', {agentsToAdd: this.team.users}).then(function(response) {
                              swal({
                                        title: '',
                                        text: 'Members updated successfully.',
                                        type: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                  });
                              this.addingUser = false;
                         });
                    },
                    selectSubcategory: function(subcategory) {
                         toRemove = this.availableSubcategories.indexOf(subcategory);
                         this.availableSubcategories.splice(toRemove, 1);
                         this.team.subcategories.push({
                            active: true,
                            category: {
                                name: subcategory.category_name
                            },
                            category_id: '',
                            created_at: '',
                            created_by: '',
                            description: null,
                            id: subcategory.id,
                            location_matters: false,
                            name: subcategory.name,
                            pivot: {},
                            tags: '',
                            updated_at: ''
                         });
                    },
                    removeSubcategory: function(subcategory) {
                         toRemove = this.team.subcategories.indexOf(subcategory);
                         this.team.subcategories.splice(toRemove, 1);
                         this.availableSubcategories.push({
                            id: subcategory.id,
                            name: subcategory.name,
                            category_name: subcategory.category.name
                         });
                    }
               }
          });
     </script>
@endsection
