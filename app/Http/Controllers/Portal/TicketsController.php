<?php

namespace App\Http\Controllers\Portal;

use Auth;
use App\User;
use App\Urgency;
use App\Category;
use App\Attachment;
use App\Placeholder;
use App\Conversation;
use App\AdminSettings;
use App\Http\Requests;
use App\Ticket as Tickets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResolutionRequest;
use App\p2helpdesk\classes\Email\EmailProvider;
use App\Http\Requests\CreatePortalTicketRequest;
use App\p2helpdesk\classes\Ticket\TicketEloquent as Ticket;

class TicketsController extends Controller
{
    public function createTicket(Request $request)
    {
        $user = Auth::user();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
        $data = [
               'user' => $user,
               'callers' => User::where('active', 1)->get(),
               'categories' => $categories,
               'urgencyrows' => Urgency::all()
          ];

        return view('app.portal.create-ticket', $data);
    }

    public function save(CreatePortalTicketRequest $request, Ticket $ticket, EmailProvider $email)
    {
        // dd($request->all());
        $request->request->add(['caller' => Auth::user()->id]);

        $ticket = $ticket->create($request);
        $email->sendTicketCreatedConfirmation($ticket);
        //Check if an agent is assigned
        if (!isset($request->agent) || $request->agent == 0) {
            if ($ticket->subcategory->location_matters) {
                //If location matters, get a collection of all agents on a team assigned to that category but also who are in the same location as the user who created the ticket
                $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
                $createdByCity = $ticket->createdBy->location->city;

                $usersToEmail = User::whereHas('location', function ($query) use ($createdByCity) {
                    $query->where('city', $createdByCity);
                })->whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                    $query->whereIn('name', $teamsAssignedToCat);
                })->where('active', 1)->lists('email');
                if (!$usersToEmail->isEmpty()) {
                    $email->notifyAgentsAssgned($ticket, $usersToEmail->toArray());
                } else {
                    //If we didn't find any users with the same location as created by user, email everyone on the team.
                    $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
                    $createdByCity = $ticket->createdBy->location->city;
                    $usersToEmail = User::whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                        return $query->whereIn('name', $teamsAssignedToCat);
                    })->where('active', 1)->lists('email');
                    if (!$usersToEmail->isEmpty()) {
                        $email->notifyAgentsAssgned($ticket, $usersToEmail->toArray());
                    }
                    if ($usersToEmail->isEmpty()) {
                        // If we don't get any results, there isn't anyone assigned to the category
                        $email->notifyAdminNoTeam($ticket);
                    }
                }
            } else {
                //If location doesn't matter, get a list of all agents on the teams assigned to that category.
                $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
                $createdByCity = $ticket->createdBy->location->city;
                $usersToEmail = User::whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                    return $query->whereIn('name', $teamsAssignedToCat);
                })->where('active', 1)->lists('email');
                if (!$usersToEmail->isEmpty()) {
                    $email->notifyAgentsAssgned($ticket, $usersToEmail->toArray());
                }
                if ($usersToEmail->isEmpty()) {
                    // If we don't get any results, there isn't anyone assigned to the category
                    $email->notifyAdminNoTeam($ticket);
                }
            }
        } else {
            //If an agent is assigned send them the email.
            $email->notifyAgentAssgned($ticket);
        }
        //Check the urgency of the ticket
        if ($ticket->urgency_id == 1) {
            flash()->confirm('Critical Ticket', 'Please call the Help Desk at '. AdminSettings::first()->phone_number . '.', 'info', 'Will Do!');
        } else {
            flash()->success(null, 'Ticket Created');
        }
        return redirect('/helpdesk/tickets/' . $ticket->id);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket = $ticket->findById($request->id);
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
        // dd(Placeholder::orderByRaw('newid()')->take(1)->get());

        $data = [
                           'ticket' => $ticket,
                           // 'attachments' => Attachment::where('ticket_id', $ticket->id)->orderby('created_at')->get(),
                           'attachments' => $ticket->attachments,
                           'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->get(),
                           'placeholder' => Placeholder::orderByRaw('newid()')->take(1)->first(),
                       ];
        // dd($data['ticket']->urgency);
        return view('app.portal.show-ticket', $data);
    }

    public function myTickets()
    {
        // dd(Auth::user()->id);
        $tickets = Tickets::where(['created_by' => Auth::user()->id])->orderBy('status', 'desc')->orderBy('created_at', 'desc');
        $data = [
               'tickets' => $tickets->paginate(15),
         ];
        return view('app.portal.my-tickets', $data);
    }

    public function close(ResolutionRequest $request, Ticket $ticket, EmailProvider $mailer)
    {
        $ticket2 = $ticket->findById($request->id);
        if (!$ticket2->workOrders->where('status', 'open')->isEmpty()) {
            flash()->confirm('Oops', 'All work orders must be closed before closing a ticket.', 'error');
            return redirect()->back();
        }

        $ticket = $ticket->closeTicket($request);
        flash()->success(null, 'Ticket Closed');
        return redirect('/helpdesk/tickets/' . $ticket->id);
    }
}
