<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;
use PDF;
use SnappyPDF;
use Auth;
use Config;
use App\User;
use Storage;
use Excel;
use App\Userform;
use App\FormColumn;
use App\Subcategory;
use Exception;
use App\Category;
use Carbon\Carbon;
use App\Conversation;
use App\p2helpdesk\transformers\TicketTransformer;
use App\p2helpdesk\transformers\WorkOrderTransformer;
use App\ConversationPrivate;
use App\Placeholder;
use App\Urgency;
use App\WorkOrder;
use App\WorkOrderTemplate;
use App\WOTemplateDetail;
use App\Attachment; //Temporary
use App\Http\Requests;
use App\Http\Requests\SaveTicketRequest;
use App\Http\Requests\CreateWorkOrderRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Requests\ResolutionRequest;
use App\Http\Requests\CloseWorkOrderRequest;
use App\Http\Requests\PostMessageRequest;
use App\Http\Requests\UploadAttachmentRequest;
use App\p2helpdesk\classes\Ticket\TicketEloquent as Ticket;
use App\Ticket as Ticket2;
use App\p2helpdesk\classes\Email\EmailProvider;
use App\AdminSettings;
use File;
use Illuminate\Support\Facades\DB;

class TicketsController extends HelpdeskController
{
    protected $ticketTransformer;
    protected $workOrderTransformer;

    public function __construct(TicketTransformer $ticketTransformer, WorkOrderTransformer $workOrderTransformer)
    {
        $this->ticketTransformer = $ticketTransformer;
        $this->workOrderTransformer = $workOrderTransformer;
    }
    /**
     * Show open tickets for the current user
     */
    public function myOpenTickets(Request $request, Ticket $ticket)
    {
        $user = Auth::user();


        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $tickets = Ticket2::where('id', $_GET['search']);
            } else {
                // dd('test');
                $tickets = $ticket->openTicketsAssignedToMe($_GET['search']);
            }
            $data = [
                 'user' => $user,
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
                 'search' => $_GET['search']
               ];
        } else {
            $tickets = $ticket->openTicketsAssignedToMe();
            $data = [
                 'user' => $user,
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15))
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('My Open Tickets', function ($excel) use ($data) {
                $excel->sheet('My Open Tickets', function ($sheet) use ($data) {
                    $sheet->fromArray($data['tickets']);
                });
            })->export('xlsx');
        }

        return view('app.tickets.my-open-tickets', $data);
    }

    public function myTeamsTickets(Request $request, Ticket $ticket)
    {
        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $tickets = Ticket2::where('id', $_GET['search']);
            } else {
                $tickets = $ticket->myTeamsTickets($_GET['search']);
            }
            $data = [
                 'user' => Auth::user(),
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->get()),
                 'search' => $_GET['search']
               ];
        } else {
            $tickets = $ticket->myTeamsTickets();
            $data = [
                 'user' => Auth::user(),
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->get())
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('My Teams Tickets', function ($excel) use ($data) {
                $excel->sheet('My Teams Tickets', function ($sheet) use ($data) {
                    $sheet->fromArray($data['tickets']);
                });
            })->export('xlsx');
        }

        return view('app.tickets.my-teams-tickets', $data);
    }

    public function massAssign(Request $request, Ticket $ticket_class)
    {
        foreach ($request->selectedTicketsMy as $ticket) {
            $toUpdate = \App\Ticket::where('id', $ticket['id'])->firstOrFail();
            $toUpdate->agent_id = Auth::user()->id;
            $toUpdate->due_date = $ticket_class->calculateDueDate($toUpdate);
            $toUpdate->save();
        }

        foreach ($request->selectedTicketsOther as $ticket) {
            $toUpdate = \App\Ticket::where('id', $ticket['id'])->firstOrFail();
            $toUpdate->agent_id = Auth::user()->id;
            $toUpdate->due_date = $ticket_class->calculateDueDate($toUpdate);
            $toUpdate->save();
        }
        return ['status' => 'success'];
    }


    /**
     * Show the create ticket page
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
        $data = [
            'user' => $user,
            'callers' => User::where('active', 1)->orderBy('first_name')->get(),
            'agents' => User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'be_assigned_ticket');
            })->where('active', 1)->orderBy('first_name')->get(),
            'categories' => $categories,
            'urgencyrows' => Urgency::all()
        ];

        return view('app.tickets.create', $data);
    }


    /**
     * Save a new ticket to the database
     *
     * @param  SaveTicketRequest
     * @param  New up the Ticket class
     * @return Redirect to the created ticket
     */
    public function save(SaveTicketRequest $request, Ticket $ticket, EmailProvider $email)
    {
        // dd($request->all());
        $ticket = $ticket->create($request);
        $email->sendTicketCreatedConfirmation($ticket);
        //Check if an agent is assigned
        if (!isset($request->agent) || $request->agent == 0) {
            $this->getTeamMembersToEmail($ticket);
        } else {
            //If an agent is assigned send them the email.
            if (Auth::user()->id != $request->agent) {
                $email->notifyAgentAssgned($ticket);
            }
        }
        //Check the urgency of the ticket
        if ($ticket->urgency_id == 1) {
            flash()->confirm('Critical Ticket', 'Please call the Help Desk at '. AdminSettings::first()->phone_number . '.', 'info', 'Will Do!');
        } else {
            flash()->success(null, 'Ticket Created');
        }
        return redirect('/tickets/' . $ticket->id);
    }


    public function getTeamMembersToEmail($ticket)
    {
        $email = new EmailProvider;
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
    }

    /**
     * Show a specific ticket
     *
     * @param  Request
     * @param  Ticket
     * @return View
     */
    public function show(Request $request, Ticket $ticket)
    {
        $ticket = Ticket2::with(['workOrders', 'attachments', 'revisionHistory', 'conversations', 'conversationsPrivate'])->where('id', $request->id)->firstOrFail();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();

		if (!deniedPermission('agent_portal')) {
			$sharedWorkOrderTemplates = DB::table('work_order_templates')->join('user_work_order_template', 'work_order_templates.id', '=', 'user_work_order_template.work_order_template_id')->join('users', 'users.id', '=', 'work_order_templates.owner_id')->where('user_work_order_template.user_id', '=', Auth::user()->id)->select(DB::raw("work_order_templates.id, work_order_templates.name, users.first_name + ' ' + users.last_name as 'template_owner', 'Shared With Me' as template_type"));
            $data = [
                           'ticket_details' => json_encode($ticket),
                           'ticket' => $ticket,
                           // 'attachments' => Attachment::where('ticket_id', $ticket->id)->orderby('created_at')->get(),
                           'attachments' => $ticket->attachments,
                           'woattachments' => Attachment::whereIn('ticketable_id', $ticket->workOrders->lists('id')->toArray())->where('ticketable_type', \App\WorkOrder::class)->get(),
                           'conversations' => $ticket->conversations,
                           'conversations_private' => $ticket->conversationsPrivate,
                           'placeholder' => Placeholder::orderByRaw('newid()')->take(1)->first(),
                           'users' => User::where('active', 1)->orderBy('first_name')->get(),
                           'agents' => User::whereHas('roles.permissions', function ($query) {
                               $query->where('name', 'be_assigned_ticket');
                           })->where('active', 1)->orderBy('first_name')->get(),
                           'categories' => $categories,
                           'urgencyrows' => Urgency::all(),
                           'work_orders' => $ticket->workOrders,
                           'templates' => collect(DB::table('work_order_templates')->where('owner_id', '=', Auth::user()->id)->select(DB::raw("work_order_templates.id, work_order_templates.name, '' as 'template_owner', 'My Templates' as template_type"))->union($sharedWorkOrderTemplates)->get())->sortBy('template_type')->groupBy('template_type'),
                           'histories' => $ticket->revisionHistory,
                       ];
            // dd($data['agents']);
            return view('app.tickets.show', $data);
        } else {
            $data = [
                           'ticket' => $ticket,
                           'attachments' => $ticket->attachments,
                           'conversations' => Conversation::where('ticket_id', $ticket->id)->orderBy('created_at', 'desc')->get(),
                           'placeholder' => Placeholder::orderByRaw('newid()')->take(1)->first(),
                       ];
            // dd($data['ticket']->urgency);
            return view('app.portal.show-ticket', $data);
        }
    }

    public function showAll(Ticket $ticket)
    {
        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $tickets = Ticket2::where('id', $_GET['search']);
            } else {
                $tickets = $ticket->getAll($_GET['search']);
            }
            $data = [
                 'user' => Auth::user(),
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
                 'search' => $_GET['search']
               ];
        } else {
            $tickets = $ticket->getAll();
            $data = [
                 'user' => Auth::user(),
                 'tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15))
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('All Tickets', function ($excel) use ($data) {
                $excel->sheet('All Tickets', function ($sheet) use ($data) {
                    $sheet->fromArray($data['tickets']);
                });
            })->export('xlsx');
        }
        return view('app.tickets.all-tickets', $data);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, EmailProvider $email)
    {
        // dd($request);
        $currentTicketValues = $ticket->findById($request->id);
        //Update the ticket
        $newTicketValues = $ticket->update($request, $request->id);
        if ($currentTicketValues->sub_category_id != $newTicketValues->sub_category_id) {
            if ($newTicketValues->agent_id == 0) {
                $this->getTeamMembersToEmail($newTicketValues);
            }
        }
        // echo $currentTicketValues;
        // echo $newTicketValues;
        if ($currentTicketValues->agent_id != $newTicketValues->agent_id) {
            if ($newTicketValues->agent_id != Auth::user()->id) {
                $email->notifyAgentAssgned($newTicketValues);
            }
        }

        if ($currentTicketValues->created_by != $newTicketValues->created_by) {
            $email->sendTicketCreatedConfirmation($newTicketValues);
        }
        // dd($newTicketValues->sub_category_id . ' - ' . $currentTicketValues->sub_category_id);
        if ($currentTicketValues->sub_category_id != $newTicketValues->sub_category_id) {
            // dd('here');
            $email->notifyIncorrectCategory($newTicketValues, $currentTicketValues);
        }

        flash()->success(null, 'Ticket updated successfully.');
        return redirect()->back();
    }

    public function closeTicket(ResolutionRequest $request, Ticket $ticket, EmailProvider $mailer)
    {
        $ticket = $ticket->closeTicket($request);
        $mailer->sendCloseTicketNotification($ticket);
        flash()->success(null, 'Ticket Closed');
        return redirect('/tickets/' . $ticket->id);
    }

    public function provideResolution(Request $request, Ticket $ticket)
    {
        $ticket = $ticket->findById($request->id);
        if (!$ticket->assignedTo) {
            flash()->confirm(null, 'Ticket must be assigned to someone before it can be closed.', 'error');
            return redirect()->back();
        }
        if (!$ticket->workOrders->where('status', 'open')->isEmpty()) {
            flash()->confirm('Oops', 'All work orders must be closed before closing a ticket.', 'error');
            return redirect()->back();
        }

        $data = [
               'ticket' => $ticket
         ];
        return view('app.tickets.resolution', $data);
    }

    public function addAttachment(UploadAttachmentRequest $request, Ticket $ticket)
    {
        // return $request->file('file');
        //Save the file to a variable
        $file = $request->file('file');

        //Create a unique name for the file
        $name = time() . randomString(20) . '.' . $file->getClientOriginalExtension();

        //Get Size of file
        $size = $file->getClientSize()/1024;

        //Move the file to an attachments folder in the public directory
        $file->move('attachments', $name);

        //Get the ticket we are working with
        $ticket = $ticket->findById($request->id);

        //Save the attachment to the database
        $attachment = $ticket->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file' => $name,
            'file_size' => $size
            ]);

        //Get the count of attachments on the ticket
        $attachment['count'] = $ticket->attachments()->count();

        //Return the attachment to the ajax callback
        return $attachment;
    }

    public function addAttachmentFromWorkOrder(UploadAttachmentRequest $request, Ticket $ticket)
    {
        // return $request->file('file');
        //Save the file to a variable
        $file = $request->file('file');

        //Create a unique name for the file
        $name = time() . randomString(20) . '.' . $file->getClientOriginalExtension();

        //Get Size of file
        $size = $file->getClientSize()/1024;

        //Move the file to an attachments folder in the public directory
        $file->move('attachments', $name);

        //Get the ticket we are working with
        $ticket = $ticket->findById($request->id);
        $work_order = WorkOrder::where('id', $request->woid)->firstOrFail();

        //Save the attachment to the database
        $attachment = $work_order->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file' => $name,
            'file_size' => $size
            ]);

        //Get the count of attachments on the ticket
        $attachment['count'] = $work_order->attachments()->count();

        //Return the attachment to the ajax callback
        return $attachment;
    }


    public function deleteAttachment(Request $request, Attachment $attachment)
    {
        $attachment = $attachment->find($request->id);
        $type = $attachment->ticketable_type;
        $attachment->delete();
        if ($attachment->ticketable_type == 'Ticket') {
            File::delete('attachments\\' .$attachment->file);
        } else {
            File::delete('cc\\attachments\\' .$attachment->file);
        }
        $attachments = $attachment->where(['ticketable_id' => $request->ticket_id, 'ticketable_type' => $type])->get();
        // return $attachments->toJson();

        return response()->json(['status' => 'success', 'statusText' => 'Attachment successfully removed', 'count' => $attachments->count()], 200);
    }

    public function postMessage(PostMessageRequest $request, Ticket $ticket, EmailProvider $email)
    {
        $ticket->postMessage($request, 'Web');
        $ticket = $ticket->findById($request->id);
        // if($ticket->assignedTo) {
        $email->sendConversationUpdatedEmail($ticket);
        // }
        flash()->success(null, 'Message Posted Successfully');
        return redirect()->back();
    }

    public function postPrivateMessage(PostMessageRequest $request, Ticket $ticket)
    {
        $ticket->postPrivateMessage($request, 'Web');
        flash()->success(null, 'Message Posted Successfully');
        return redirect()->back();
    }

    public function postPrivateMessageNotify(PostMessageRequest $request, Ticket $ticket)
    {
        $ticket->postPrivateMessage($request, 'Web');
        $currentTicket = Ticket2::where('id', $request->id)->firstOrFail();
        $mailer = new EmailProvider;
        $mailer->notifyTicketOwnerOfPrivateMessage($currentTicket, Auth::user(), $request->message);
        flash()->success(null, 'Message Posted Successfully');
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function createWorkOrder(CreateWorkOrderRequest $request, Ticket $ticket, EmailProvider $mailer)
    {
        $ticket = $ticket->findById($request->id);
        $assigned_to = User::find($request->assigned_to);
        $due_date = Carbon::createFromFormat('m/d/Y g:ia', $request->due_date, $assigned_to->timezone);
        $work_order = $ticket->workOrders()->create([
               'assigned_to' => $request->assigned_to,
               'created_by' => Auth::user()->id,
               'status' => 'open',
               'subject' => $request->subject,
               'work_requested' => $request->work_requested,
               'due_date' => $due_date->timezone('utc')
          ]);
        //If creating a work order for yourself you don't need to be notified
        if (isset($request->send_email)) {
            $mailer->notifyWorkOrderAssigned($work_order);
        }
        flash()->success(null, 'Work Order Created');
        return redirect('tickets/' . $ticket->id . '/#workorders');
    }

    public function applyWOTemplate(Request $request, Ticket $ticket, EmailProvider $email)
    {
        $ticket = $ticket->findById($request->id);
        $work_orders = WOTemplateDetail::where('template_id', $request->template)->get();
        foreach ($work_orders as $work_order) {
            if ($work_order->due_in == -1) {
                $due_date = $ticket->due_date;
            } else {
                $due_date = $ticket->created_at->addDays($work_order->due_in);
            }
            $ticket->workOrders()->create([
				'assigned_to' => $work_order->assigned_to,
				'created_by' => Auth::user()->id,
				'status' => 'open',
				'subject' => $work_order->subject,
				'work_requested' => $work_order->work_requested,
				'due_date' => $due_date
			]);
			// Commenting out 4/22/2021
            //If creating a work order for yourself you don't need to be notified
            // if ($created_work_order->assigned_to != Auth::user()->id) {
            //     $email->notifyWorkOrderAssigned($created_work_order);
            // }
        }
        flash()->success(null, 'Template Applied');
        return redirect('tickets/' . $ticket->id . '/#workorders');
    }

    public function showWorkOrder(Request $request)
    {
        $work_order = WorkOrder::where('id', $request->id)->firstOrFail();
        $data = [
               'work_order' => $work_order,
               'users' => User::whereHas('roles.permissions', function ($query) {
                   $query->where('name', 'be_assigned_ticket');
               })->where('active', 1)->orderBy('first_name')->get(),
               'attachments' => $work_order->attachments,
               'histories' => $work_order->revisionHistory,
         ];
        if ($work_order->ticketable_type == 'ChangeTicket') {
            if (!$work_order->ticketable->workStarted()) {
                return view('app.tickets.work-order-not-ready');
            }
        }
        return view('app.tickets.show-work-order', $data);
    }

    public function updateWorkOrder(CloseWorkOrderRequest $request, EmailProvider $mailer)
    {
        // dd($request);
        $assigned_to = User::find($request->assigned_to);
        $due_date = Carbon::createFromFormat('m/d/Y g:i a', $request->due_date, $assigned_to->timezone);
        $work_order = WorkOrder::where('id', $request->id)->firstOrFail();
        $current_assigned_to = $work_order->assigned_to;
        $currentStatus = $work_order->status;
        $work_order->update([
               'assigned_to' => $request->assigned_to,
               'status' => $request->status,
               'work_completed' => $request->work_completed,
               'completed_date' => ($request->status == 'closed' ? Carbon::now() : null),
               'due_date' => $due_date->timezone('utc'),
          ]);
        if (isset($request->subject)) {
            $work_order->subject = $request->subject;
            $work_order->work_requested = $request->work_requested;
            $work_order->save();
        }
        // If owner of work order changes, notify them the work order has been assigned to them
        if ($current_assigned_to != $request->assigned_to) {
            $mailer->notifyWorkOrderAssigned($work_order->fresh());
        }

        if ($currentStatus !== $work_order->status) {
            if ($work_order->ticketable_type == 'Ticket') {
                if ($work_order->ticketable->agent != null) {
                    if ($work_order->assigned_to != $work_order->ticketable->agent_id) {
                        $mailer->notifyAgentWorkOrderClosed($work_order->fresh());
                    }
                }
            }
            if ($work_order->ticketable_type == 'ChangeTicket') {
                if ($work_order->ticketable->status == 'in-progress') {
                    //Send email lettig the change ticket owner know of the status change.
                    $mailer->notifyChangeOwnerWorkOrderClosed($work_order->fresh());
                }
            }
        }

        flash()->success(null, 'Work Order Updated');
        return redirect()->back();
    }

    public function openWorkOrder(Request $request)
    {
        $work_order = WorkOrder::where('id', $request->id)->firstOrFail();
        $work_order->update([
               'status' => 'open',
               'completed_date' => null,
          ]);
        flash()->success(null, 'Work Order Opened');
        return response()->json(['status' => 'success', 'statusText' => 'Work order opened'], 200);
    }

    /**
     * Show open tickets for the current user
     */
    public function myOpenWorkOrders(Request $request)
    {
        $user = Auth::user();

        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $work_orders = WorkOrder::where('id', $_GET['search']);
            } else {
                $work_orders = WorkOrder::where('assigned_to', $user->id)->where('ticketable_type', 'Ticket')->orderBy('status', 'desc')->orderBy('due_date')->search($_GET['search'], null, true, 1);
            }
            $data = [
                    'user' => $user,
                    'work_orders' => $work_orders->paginate(15),
                    'search' => $_GET['search']
               ];
        } else {
            $work_orders = WorkOrder::where('assigned_to', $user->id)->where('ticketable_type', 'Ticket')->orderBy('status', 'desc')->orderBy('due_date');
            $data = [
                 'user' => $user,
                 'work_orders' => (isset($_GET['print']) ? $this->workOrderTransformer->transformCollection($work_orders->get()->toArray()) : $work_orders->paginate(15))
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('My Work Orders', function ($excel) use ($data) {
                $excel->sheet('My Work Orders', function ($sheet) use ($data) {
                    $sheet->fromArray($data['work_orders']);
                });
            })->export('xlsx');
        }

        return view('app.tickets.my-open-work-orders', $data);
    }

    public function emailWorkOrders(Request $request, Ticket $ticket, EmailProvider $mailer)
    {
        $work_orders = WorkOrder::where(['ticketable_type' => 'Ticket', 'ticketable_id' => $request->id])
                                        ->whereIn('id', $request->work_order_id)->get();

        if (!$work_orders->isEmpty()) {
            foreach ($work_orders as $work_order) {
                $mailer->notifyWorkOrderAssigned($work_order);
            }
        }
        flash()->success(null, 'Work orders emailed successfully.');
        return redirect()->back();
    }

    public function reopenTicket(Request $request, Ticket $ticket)
    {
        $ticket = $ticket->findById($request->id);

        $ticket->status = 'open';
        $ticket->save();
        flash()->success(null, 'Ticket status set to open.');
        return redirect()->back();
    }

    public function printTicket(Request $request, Ticket $ticket)
    {
        $ticket = $ticket->findById($request->id);
        $data = [
               'ticket' => $ticket,
               'histories' => $ticket->revisionHistory,
         ];
        // dd(url(elixir('css/adminlte.css')));
        // return view('app.pdf.print-ticket', $data);
        $pdf = SnappyPDF::loadView('app.pdf.print-ticket', $data);
        return $pdf->inline('Ticket #' . $ticket->id . '.pdf');
    }

    public function assignToMe(Request $request, Ticket $ticket)
    {
        $currentTicket = $ticket->findById($request->id);

        $currentTicket->agent_id = Auth::user()->id;
        $currentTicket->due_date = $ticket->calculateDueDate($currentTicket);
        $currentTicket->save();
        return redirect()->back();
    }

    public function postUserForm(Request $request, EmailProvider $email, Ticket $ticket_class)
    {
        $form = UserForm::with('fields')->where('active', 1)->where('slug', $request->slug)->firstOrFail();
        //Still need to do form validation here
        $validationArray = [];
        foreach ($request->all() as $key => $value) {
            foreach ($form->fields as $field) {
                if ($key == $field->name) {
                    $validationArray[$field->name] = rtrim(($field->is_required ? 'required|' : '') .
                                    ($field->type == 'date' ? 'date|date_format:m/d/Y|' : '') .
                                    ($field->type == 'number' ? 'numeric|' : '') .
                                    ($field->ticket_subject == 1 ? 'max:255|' : '') .
                                    ($field->type == 'select' ? 'in:'.$field->default_value.'|':''), '|');
                }
            }
        }
        // dd($validationArray);
        $this->validate($request, $validationArray);

        $subcategory = Subcategory::where('id', $form->subcategory_id)->firstOrFail();
        $ticketSubjectField = $form->fields()->where('ticket_subject', 1)->first();
        $ticketDescriptionField = $form->fields()->where('ticket_description', 1)->first();
        $ticketSubject = '';
        $ticketDescription = '';
        $currentValue = '';
        $formFields = collect();
        foreach ($request->all() as $key => $value) {
            $currentValue = $value;
            foreach ($form->fields as $field) {
                if (str_contains($value, '@'.$field->name)) {
                    // $request->offsetSet($key, str_replace('@'.$field->name, $request->{$field->name}, $value));
                    $currentValue = str_replace('@'.$field->name, $request->{$field->name}, $currentValue);
                    // echo $currentValue . '<br>';
                }
                if ($field->name == $key) {
                    $formFields->put($field->label, $value);
                }
            }

            if ($key == $ticketSubjectField->name) {
                $ticketSubject = $currentValue;
            }
            if ($key == $ticketDescriptionField->name) {
                $ticketDescription = $currentValue;
                // dd($currentValue);
            }
        }


        $ticket = Ticket2::create([
                  'created_by' => Auth::user()->id,
                  'agent_id' => 0,
                  'category_id' => $subcategory->category->id,
                  'sub_category_id' => $subcategory->id,
                  'title' => $ticketSubject,
                  'description' => $ticketDescription,
                  'urgency_id' => $form->urgency,
                  'status' => 'open'
            ]);

        $ticket->due_date = $ticket_class->calculateDueDate($ticket);
        $ticket->save();

        //Generate PDF and attach to ticket
        //Exclude hidden fields and csrf token from pdf
        $excludeFields = $form->fields()->where('type', 'hidden')->pluck('label')->toArray();
        array_push($excludeFields, '_token');

        $data = [
                  'formFields' => $formFields->except($excludeFields),
                  // 'formFields' => $request->except($excludeFields),
                  'form' => $form
            ];

        // dd($data['formFields']);

        //Save form data as pdf variable
        $pdf = PDF::loadView('app.pdf.user-form', $data);

        //Create a unique name for the file
        $name = time() . randomString(20) . '.pdf';

        //Store the pdf in the attachments folder
        Storage::disk('attachments')->put($name, $pdf->output());
        $size = Storage::disk('attachments')->size($name);

        //Save the attachment to the database and attache to ticket
        $attachment = $ticket->attachments()->create([
                  'file_name' => $form->name.'.pdf',
                  'file' => $name,
                  'file_size' => $size/1000
            ]);

        $email->sendTicketCreatedConfirmation($ticket);
        $this->getTeamMembersToEmail($ticket);
        flash()->success(null, 'Form submitted successfully');
        return $ticket;
    }
}
