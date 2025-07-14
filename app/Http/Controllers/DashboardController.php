<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\Urgency;
use App\p2helpdesk\classes\Ticket\TicketEloquent;
use App\Announcement;
use App\Ticket;
use App\ChangeTicket;
use App\WorkOrder;
use Carbon\Carbon;


class DashboardController extends HelpdeskController
{
     public function dashboard(TicketEloquent $ticket)
     {
          $user = Auth::user();
          $tickets = $ticket->myOpenTickets();
          $closed_tickets = $ticket->myClosedTickets();
          
          $urgencyTicketsAvailable = 0;
          $ticketsByUrgency = [
          'labels' => Urgency::lists('name')->toArray(),
          'datasets' => [
               [
                    'data' => [
                         Ticket::where(['urgency_id' => 1, 'agent_id' => $user->id, 'status' => 'open'])->count(), 
                         Ticket::where(['urgency_id' => 2, 'agent_id' => $user->id, 'status' => 'open'])->count(), 
                         Ticket::where(['urgency_id' => 3, 'agent_id' => $user->id, 'status' => 'open'])->count(), 
                         Ticket::where(['urgency_id' => 4, 'agent_id' => $user->id, 'status' => 'open'])->count(), 
                         Ticket::where(['urgency_id' => 5, 'agent_id' => $user->id, 'status' => 'open'])->count()],
                         'backgroundColor' => ['#ED4337', '#FF7E47', '#00A1ED', '#82BE5A', '#fdfd96'
                    ],
               ], 
          ],
          ];
          foreach($ticketsByUrgency['datasets'][0]['data'] as $dataPoint) {
               if($dataPoint > 0) {
                    $urgencyTicketsAvailable = 1;
               }
          }
// dd(Carbon::now()->endOfMonth());
          $ticketsDueByDate = [
          'labels' => ['Overdue', 'Due Today', 'Due this week', 'Due after this week'],
          'datasets' => [
          [
          'type' => 'bar',
          'label' => 'Tickets',
          'data' => [
          Ticket::where(['agent_id' => $user->id, 'status' => 'open'])->where('due_date', '<', Carbon::now())->count(), 
          Ticket::where(['agent_id' => $user->id, 'status' => 'open'])->whereBetween('due_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->count(), 
          Ticket::where(['agent_id' => $user->id, 'status' => 'open'])->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(), 
          Ticket::where(['agent_id' => $user->id, 'status' => 'open'])->where('due_date', '>', Carbon::now()->endOfWeek())->count(), 
          ],
          'backgroundColor' => ['#ED4337', '#FF7E47', '#00A1ED', '#82BE5A'],
          ],
          [
          'type' => 'bar',
          'label' => 'Change Tickets',
          'data' => [
          ChangeTicket::where(['change_owner_id' => $user->id])->whereIn('status', ['deferred', 'proposed', 'scheduled', 'in-progress'])->where('end_date', '<', Carbon::now())->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id])->whereIn('status', ['deferred', 'proposed', 'scheduled', 'in-progress'])->whereBetween('end_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id])->whereIn('status', ['deferred', 'proposed', 'scheduled', 'in-progress'])->whereBetween('end_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id])->whereIn('status', ['deferred', 'proposed', 'scheduled', 'in-progress'])->where('end_date', '>', Carbon::now()->endOfWeek())->count(), 
          ],
          'backgroundColor' => ['#ED4337', '#FF7E47', '#00A1ED', '#82BE5A'],
          ]
          ],
          ];

          $workOrdersByDueDate = [
          'labels' => ['Overdue', 'Due Today', 'Due this week', 'Due after this week'],
          'datasets' => [
          [
          'type' => 'bar',
          'label' => 'Tickets',
          'data' => [
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'Ticket'])->where('due_date', '<', Carbon::now())->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'Ticket'])->whereBetween('due_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'Ticket'])->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'Ticket'])->where('due_date', '>', Carbon::now()->endOfWeek())->count(), 
          ],
          'backgroundColor' => ['#ED4337', '#FF7E47', '#00A1ED', '#82BE5A'],
          ],
          [
          'type' => 'bar',
          'label' => 'Change Tickets',
          'data' => [
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'ChangeTicket'])->where('due_date', '<', Carbon::now())->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'ChangeTicket'])->whereBetween('due_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'ChangeTicket'])->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(), 
          WorkOrder::where(['assigned_to' => $user->id, 'status' => 'open', 'ticketable_type' => 'ChangeTicket'])->where('due_date', '>', Carbon::now()->endOfWeek())->count(), 
          ],
          'backgroundColor' => ['#ED4337', '#FF7E47', '#00A1ED', '#82BE5A'],
          ]
          ],

          ];
// return json_encode($workOrdersByDueDate);
          $changeTicketsAvailable = 0;
          $openChangesByStatus = [
          'labels' => ['Deferred', 'Proposed', 'Scheduled', 'In-Progress'],
          'datasets' => [[
          'data' => [
          ChangeTicket::where(['change_owner_id' => $user->id, 'status' => 'deferred'])->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id, 'status' => 'proposed'])->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id, 'status' => 'scheduled'])->count(), 
          ChangeTicket::where(['change_owner_id' => $user->id, 'status' => 'in-progress'])->count(), 
          ],
          'backgroundColor' => ['#FF7E47', '#00A1ED', '#82BE5A', '#fdfd96'],
          ], 
          ],
          ];
          foreach($openChangesByStatus['datasets'][0]['data'] as $dataPoint) {
               if($dataPoint > 0) {
                    $changeTicketsAvailable = 1;
               }
          }

// dd(Auth::user()->teams);
          $teamTicketsAvailable = 0;
          $teamsTickets = Ticket::with('category')->whereHas('subcategory', function($query) {
               return $query->whereHas('teams', function($query2) {
                    return $query2->whereIn('id', Auth::user()->teams->lists('id')->toArray());
               });
          })->where(['agent_id' => 0])->get();
          if(!$teamsTickets->isEmpty()) {
               $teamTicketsAvailable = 1;
          }
          $ticketsGrouped = $teamsTickets->groupBy('category.name');
          $teamLabels = $ticketsGrouped->keys()->toArray();
          $teamData = [];
          $teamColors = [];
          foreach($ticketsGrouped as $ticketGroup) {
               array_push($teamData, $ticketGroup->count());
               array_push($teamColors, rand_color());
          }
          $myTeamsTickets = [
          'labels' => $teamLabels,
          'datasets' => [[
          'data' => $teamData,
          'backgroundColor' => $teamColors,
          ], 
          ],
          ];


// dd(json_encode($ticketsDueByDate));
          $data = [
          'user' => $user,
          'announcements' => Announcement::where('start_date', '<=', Carbon::now())
          ->where('end_date', '>=', Carbon::now())
          ->orderBy('start_date', 'desc')->get(),
          'tickets' => $tickets,
          'closed_tickets' => $closed_tickets,
          'ticketsByUrgency' => json_encode($ticketsByUrgency),
          'ticketsDueByDate' => json_encode($ticketsDueByDate),
          'openChangesByStatus' => json_encode($openChangesByStatus),
          'workOrdersByDueDate' => json_encode($workOrdersByDueDate),
          'myTeamsTickets' => json_encode($myTeamsTickets),
          'teamTickets' => $teamTicketsAvailable,
          'urgencyTickets' => $urgencyTicketsAvailable,
          'changeTicketsAvailable' => $changeTicketsAvailable,
          ];

          return view('app.dashboard', $data);
     }

     public function portalDashboard(TicketEloquent $ticket)
     {
          $user = Auth::user();
          $tickets = Ticket::where(['created_by' => Auth::user()->id, 'status' => 'open'])->orderBy('status', 'desc')->orderBy('created_at', 'desc')->paginate(15);
          $data = [
          'user' => $user,
          'announcements' => Announcement::where('start_date', '<=', Carbon::now())
          ->where('end_date', '>=', Carbon::now())
          ->orderBy('start_date', 'desc')->get(),
          'tickets' => $tickets,
          ];

          return view('app.portal.dashboard', $data);
     }

     public function closedTickets(TicketEloquent $ticket)
     {
          $user = Auth::user();
          $closed_tickets = $ticket->myClosedTickets();
          $data = [
          'user' => $user,
          'closed_tickets' => $closed_tickets,
          ];

          return view('app.portal.closed-tickets', $data);
     }
}
