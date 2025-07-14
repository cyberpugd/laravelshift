@extends('layouts.master')

@section('content')

<section class="content-header">
<h1>Hi {{$user->first_name}},</h1>
<p style="font-size: 14pt;">Welcome to the IFS EnR Help Desk Agent Portal</p>
</section>
<section class="content">
@foreach($announcements as $announcement)
@if($announcement->location == 'agents' || $announcement->location == 'both')
<div class="alert alert-{{$announcement->type}}" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span>{{$announcement->title}}</span>
  <p style="margin-top: 15px;">
   {!! linkify(nl2br(htmlentities($announcement->details))) !!}
  </p>
</div>
@endif
@endforeach
<div class="col-md-12 boxshadow">
     <div class="col-md-4">
          <div style="text-align: center;">
               @if($urgencyTickets != 0)
                    <h4>My Open Tickets by Urgency</h4>
                    <canvas id="myChart"></canvas>
               @else
                    <canvas id="myChart" style="display:none;"></canvas>
                    <h4>My Open Tickets by Urgency</h4>
                    <img src="/images/all_caught_up.png" width="50%" />
               @endif
          </div>
     </div>
     <div class="col-md-4">
          <div style="text-align: center;">
               @if($teamTickets != 0)
               <h4>My Teams' Unassigned by Category</h4>
                    <canvas id="myChart2"></canvas>
               @else
                    <canvas id="myChart2" style="display:none;"></canvas>
                    <h4>My Teams' Unassigned by Category</h4>
                    <img src="/images/all_caught_up.png" width="50%" />
               @endif
          </div>
     </div>
     <div class="col-md-4">
          <div style="text-align: center;">
               @if($changeTicketsAvailable != 0)
                    <h4>My Change Tickets by Status</h4>
                    <canvas id="myChart3"></canvas>
               @else
                    <canvas id="myChart3" style="display:none;"></canvas>
                    <h4>My Change Tickets by Status</h4>
                    <img src="/images/all_caught_up.png" width="50%" />
               @endif
          </div>
     </div>
</div>
<div class="col-md-12 boxshadow" style="margin-top: 15px;">
     <div class="col-md-6">
          <div style="text-align: center;">
               <h4>My Open Tickets by Due Date</h4>
               <canvas id="myChart4" height="100%"></canvas>
          </div>
     </div>
     <div class="col-md-6">
          <div style="text-align: center;">
               <h4>My Open Work Orders</h4>
               <canvas id="myChart5" height="100%"></canvas>
          </div>
     </div>
</div>
</section>
@endsection

@section('footer')
<script>
     new Vue({
          el: 'body',
          methods: {
               showTicket: function(id) {
                    window.location.href = '/tickets/'+id;
               }
          }
     });

//Charts
     var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {!! $ticketsByUrgency !!},
    options: {
          legend: {
                      display: false,
                  },
    }
});





     var ctx = document.getElementById("myChart2");
var myChart2 = new Chart(ctx, {
    type: 'doughnut',
    data: {!! $myTeamsTickets !!},
    options: {
          legend: {
                      display: false,
                  },
    }
});

var ctx = document.getElementById("myChart3");
var myChart3 = new Chart(ctx, {
    type: 'doughnut',
    data: {!! $openChangesByStatus !!},
    options: {
          legend: {
                      display: false,
                  },
    }
});

     var ctx = document.getElementById("myChart4");
var myChart4 = new Chart(ctx, {
    type: 'bar',
    data: {!! $ticketsDueByDate !!},
    options: {
     scales: {
                yAxes: [{
                    ticks: {
                        min: 0,
                        beginAtZero: true,
                        scaleIntegersOnly: true,
                        callback: function(value) {if (value % 1 === 0) {return value;}}
                    }
                }]
            },
          legend: {
                      display: false,
                  },
    }
});

    var ctx = document.getElementById("myChart5");
var myChart5 = new Chart(ctx, {
    type: 'bar',
    data: {!! $workOrdersByDueDate !!},
    options: {
     scales: {
                yAxes: [{
                    ticks: {
                        min: 0,
                        beginAtZero: true,
                        scaleIntegersOnly: true,
                        callback: function(value) {if (value % 1 === 0) {return value;}}
                    }
                }]
            },
          legend: {
                      display: false,
                  },
    }
});
$(document).ready(function() {
     /**
      * Make Charts clickable - not going to implement yet
      */
     // $('#myChart').click(function(evt) {
     //      var activePoints = myChart.getElementsAtEvent(evt);
     //      console.log(activePoints[0]._view.label);
     //      window.location.href = '{{URL::to('/tickets/open-tickets')}}?urgency='+activePoints[0]._view.label;

     // });
     // $('#myChart2').click(function(evt) {
     //      var activePoints = myChart2.getElementsAtEvent(evt);
     //      console.log(activePoints[0]._view.label);
     //      window.location.href = '{{URL::to('/tickets/team-tickets')}}?category='+activePoints[0]._view.label;

     // });
     // $('#myChart3').click(function(evt) {
     //      var activePoints = myChart3.getElementsAtEvent(evt);
     //      console.log(activePoints[0]._view.label);
     //      window.location.href = '{{URL::to('/tickets/open-tickets')}}?status='+activePoints[0]._view.label;

     // });
});
</script>
@endsection

