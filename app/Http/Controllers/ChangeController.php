<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\User;
use App\ChangeTicket;
use Auth;
use Input;
use PDF;
use SnappyPDF;
use App\Attachment;
use App\WOTemplateDetail;
use DB;
use Excel;
use App\Revision;
use App\WorkOrder;
use App\ChangeApproval;
use App\p2helpdesk\classes\Email\EmailProvider;
use App\p2helpdesk\classes\Ticket\ChangeControlEloquent;
use Carbon\Carbon;
use App\WorkOrderTemplate;
use App\Urgency;
use App\AuditUnit;
use App\ChangeTicketTemplate;
use App\Http\Requests;
use App\Http\Requests\CreateChangeTicketRequest;
use App\Http\Requests\SaveChangeTicketRequest;
use App\Http\Requests\UploadAttachmentRequest;
use App\p2helpdesk\transformers\ChangeTicketTransformer;

class ChangeController extends HelpdeskController
{
    protected $ticketTransformer;

    public function __construct(ChangeTicketTransformer $ticketTransformer)
    {
        $this->ticketTransformer = $ticketTransformer;
    }

    public function create()
    {
        $user = Auth::user();
        $template = null;
        if (Input::get('template') !== null) {
            if (is_numeric(Input::get('template'))) {
                $template = ChangeTicketTemplate::where('id', Input::get('template'))
                              ->firstOrFail();
            }
        }

        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();

        $data = [
          'user' => $user,
          'users' => User::where('active', 1)->orderBy('first_name')->get(),
          'categories' => $categories,
          'urgencyrows' => Urgency::all(),
          'audit_units' => AuditUnit::where('status', 1)->orderBy('name')->get(),
          'myTemplates' => ChangeTicketTemplate::where('owner_id', Auth::user()->id)->orderBy('name')->get()->merge(Auth::user()->ccTemplates)->sortBy('name'),
          'template' => $template,
          'agents' => User::whereHas('roles.permissions', function ($query) {
              $query->where('name', 'be_assigned_ticket');
          })->where('active', 1)->orderBy('first_name')->get(),
          'approvers' => User::whereHas('roles.permissions', function ($query) {
              $query->where('name', 'approve_change_ticket');
          })->where('active', 1)->orderBy('first_name')->get(),
          ];
        return view('app.change-control.create', $data);
    }

    public function myOpen(ChangeControlEloquent $ticket)
    {
        $user = Auth::user();

        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $tickets = ChangeTicket::where('id', $_GET['search']);
            } else {
                $tickets = $ticket->myOpenChangeTickets($_GET['search']);
            }
            $data = [
                 'user' => $user,
                 'change_tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
                 'search' => $_GET['search']
               ];
        } else {
            $tickets = $ticket->myOpenChangeTickets();
            $data = [
                 'user' => $user,
                 'change_tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('My Open Change Tickets', function ($excel) use ($data) {
                $excel->sheet('My Open Change Tickets', function ($sheet) use ($data) {
                    $sheet->fromArray($data['change_tickets']);
                });
            })->export('xlsx');
        }
        // dd($change_tickets);
        return view('app.change-control.my-open', $data);
    }

    public function all(ChangeControlEloquent $ticket)
    {
        $user = Auth::user();
        $sortBy = (isset($_GET['sortBy']) ? $_GET['sortBy'] : 'end_date');
        if (isset($_GET['search'])) {
            if (is_numeric($_GET['search'])) {
                $tickets = ChangeTicket::where('id', $_GET['search']);
            } else {
                $tickets = $ticket->allChangeTickets($_GET['search'], $sortBy);
            }
            $data = [
                 'user' => $user,
                 'change_tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
                 'search' => $_GET['search']
               ];
        } else {
            $tickets = $ticket->allChangeTickets(null, $sortBy);
            $data = [
                 'user' => $user,
                 'change_tickets' => (isset($_GET['print']) ? $this->ticketTransformer->transformCollection($tickets->get()->toArray()) : $tickets->paginate(15)),
               ];
        }

        if (isset($_GET['print'])) {
            Excel::create('All Change Tickets', function ($excel) use ($data) {
                $excel->sheet('All Change Tickets', function ($sheet) use ($data) {
                    $sheet->fromArray($data['change_tickets']);
                });
            })->export('xlsx');
        }
        // dd($change_tickets);
        return view('app.change-control.all', $data);
    }

    public function save(CreateChangeTicketRequest $request, EmailProvider $mailer)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $change_owner = User::find($request->change_owner);
            $start_date = Carbon::createFromFormat('m/d/Y g:ia', $request->start_date, $change_owner->timezone)->timezone('utc');
            $end_date = Carbon::createFromFormat('m/d/Y g:ia', $request->end_date, $change_owner->timezone)->timezone('utc');
            //Create the change ticket in the change_tickets table
            $change_ticket = ChangeTicket::create([
                    'audit_unit' => $request->audit_unit,
                    'it_approver_id' => $request->it_approver,
                    'bus_approver_id' => ($request->bus_approver != '' ? $request->bus_approver : 0),
                    'change_owner_id' => $request->change_owner,
                    'status' => (isset($request->deferred) ? 'deferred' : 'proposed'),
                    'created_by' => Auth::user()->id,
                    'change_type' => $request->change_type,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'change_description' => $request->change_description,
                    'roll_out_plan' => $request->roll_out_plan,
                    'change_reason' => $request->change_reason,
                    'back_out_plan' => $request->back_out_plan,
                    'servers' => $request->servers,
                    'test_plan' => $request->test_plan,
                    'business_impact' => $request->business_impact,
                    'affected_groups' => $request->affected_groups
                    ]);
            if ($request->bus_approver != '') {
                $change_ticket->changeApprovals()->create([
                         'approved' => 0,
                         'approver' => $change_ticket->bus_approver_id,
                         'approval_type' => 'bus',
                         ]);
            }
            $change_ticket->changeApprovals()->create([
                    'approved' => 0,
                    'approver' => $change_ticket->it_approver_id,
                    'approval_type' => 'it',
                    ]);
            if (Auth::user()->id != $change_ticket->change_owner_id) {
                //Notify Owner that the ticket was created.
                $mailer->notifyChangeOwnerAssigned($change_ticket);
            }
            //Notify approvers they need to approve a change ticket
            if ($change_ticket->status != 'deferred') {
                $mailer->notifyApprovers($change_ticket);
                $approvers = User::whereIn('id', $change_ticket->changeApprovals->lists('approver')->toArray())->get();
                foreach ($approvers as $approver) {
                    Revision::create([
                              'revisionable_type' => 'ChangeTicket',
                              'revisionable_id' => $change_ticket->id,
                              'user_id' => Auth::user()->id,
                              'key' => 'change_control',
                              'new_value' => 'Approval request email sent to ' . $approver->first_name . ' ' . $approver->last_name
                         ]);
                }
            }
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
        flash()->success(null, 'Change ticket created.');
        return redirect('/change-control/' . $change_ticket->id);
    }

    public function show(Request $request)
    {
        $change_ticket = ChangeTicket::with('changeApprovals')->where('id', $request->id)->firstOrFail();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
		$sharedWorkOrderTemplates = DB::table('work_order_templates')->join('user_work_order_template', 'work_order_templates.id', '=', 'user_work_order_template.work_order_template_id')->join('users', 'users.id', '=', 'work_order_templates.owner_id')->where('user_work_order_template.user_id', '=', Auth::user()->id)->select(DB::raw("work_order_templates.id, work_order_templates.name, users.first_name + ' ' + users.last_name as 'template_owner', 'Shared With Me' as template_type"));
        $data = [
          'ticket' => $change_ticket,
          'work_orders' => $change_ticket->workOrders,
          'users' => User::where('active', 1)->orderBy('first_name')->get(),
           'agents' => User::whereHas('roles.permissions', function ($query) {
               $query->where('name', 'be_assigned_ticket');
           })->where('active', 1)->orderBy('first_name')->get(),
           'approvers' => User::whereHas('roles.permissions', function ($query) {
               $query->where('name', 'approve_change_ticket');
           })->where('active', 1)->orderBy('first_name')->get(),
		   'templates' => collect(DB::table('work_order_templates')->where('owner_id', '=', Auth::user()->id)->select(DB::raw("work_order_templates.id, work_order_templates.name, '' as 'template_owner', 'My Templates' as template_type"))->union($sharedWorkOrderTemplates)->get())->groupBy('template_type'),
          'histories' => $change_ticket->revisionHistory,
          'categories' => $categories,
          'urgencyrows' => Urgency::all(),
          'audit_units' => AuditUnit::where('status', 1)->get(),
          'attachments' => $change_ticket->attachments,
          'woattachments' => Attachment::whereIn('ticketable_id', $change_ticket->workOrders->lists('id')->toArray())->where('ticketable_type', \App\WorkOrder::class)->get(),
          ];
        return view('app.change-control.show', $data);
    }

    public function approve(Request $request, EmailProvider $mailer)
    {
        $approver = Auth::user();
        // dd(Carbon::now($approver->timezone)->timezone('utc'));
        $change_ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        // if($change_ticket->status != 'proposed') {
        //      flash()->confirm('Sorry', 'Only tickets with a proposed status can be approved.', 'error', 'Okay');
        //      return redirect()->back();
        // }
        $approvals = ChangeApproval::where(['approver' => $approver->id, 'change_ticket_id' => $change_ticket->id])->get();
        try {
            DB::beginTransaction();
            foreach ($approvals as $approval) {
                if ($approver->id == $approval->approver) {
                    $approval->approved = 1;
                    $approval->date_approved = Carbon::now($approver->timezone)->timezone('utc');
                    $approval->save();
                    $approvedApprovals = ChangeApproval::where(['change_ticket_id' => $change_ticket->id, 'approved' => 0, 'approval_type' => 'it'])->get();
                    if ($approvedApprovals->isEmpty()) {
                        $change_ticket->status = 'scheduled';
                        $change_ticket->save();
                    }
                }
            }
            $mailer->notifyChangeOwnerApproval($change_ticket, $approver);
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
        flash()->success(null, 'Change Ticket Approved');
        return redirect()->back();
    }

    public function reject(Request $request, EmailProvider $mailer)
    {
        $approver = Auth::user();
        // dd(Carbon::now($approver->timezone)->timezone('utc'));
        $change_ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $approvals = ChangeApproval::where(['approver' => $approver->id, 'change_ticket_id' => $change_ticket->id])->get();
        try {
            DB::beginTransaction();
            foreach ($approvals as $approval) {
                if ($approver->id == $approval->approver) {
                    $approval->approved = 2;
                    $approval->date_approved = Carbon::now($approver->timezone)->timezone('utc');
                    $approval->save();
                    $approvedApprovals = ChangeApproval::where(['change_ticket_id' => $change_ticket->id, 'approved' => 0])->get();
                    if ($approvedApprovals->isEmpty()) {
                        $change_ticket->status = 'rejected';
                        $change_ticket->save();
                    }
                }
            }
            $mailer->notifyChangeOwnerRejected($change_ticket, $approver);
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
        flash()->confirm('Success', 'Ticket set to rejected status. The ticket owner can amend this decision and re-submit for approval', 'info', 'Got it');
        return redirect()->back();
    }

    public function amend(Request $request)
    {
        // dd(Carbon::now($approver->timezone)->timezone('utc'));
        $mailer = new EmailProvider;
        $change_ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $rejections = ChangeApproval::where(['approved' => 2, 'change_ticket_id' => $change_ticket->id])->get();
        try {
            DB::beginTransaction();
            foreach ($rejections as $rejection) {
                $rejection->approved = 0;
                $rejection->date_approved = null;
                $rejection->save();
                $mailer->notifyChangeApproverAmended($change_ticket, $rejection->approvedBy);
                Revision::create([
                         'revisionable_type' => 'ChangeTicket',
                         'revisionable_id' => $change_ticket->id,
                         'user_id' => Auth::user()->id,
                         'key' => 'change_control',
                         'new_value' => 'Amended approval email sent to ' . $rejection->approvedBy->first_name . ' ' . $rejection->approvedBy->last_name
                    ]);
            }
            $change_ticket->status = 'proposed';
            $change_ticket->save();
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }

    public function createWorkOrder(Request $request, EmailProvider $mailer)
    {
        // dd($request->assigned_to);
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $assigned_to = User::find($request->assigned_to);
        $due_date = Carbon::createFromFormat('m/d/Y h:i A', $request->due_date, $assigned_to->timezone);
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
            Revision::create([
                    'revisionable_type' => 'ChangeTicket',
                    'revisionable_id' => $ticket->id,
                    'user_id' => Auth::user()->id,
                    'key' => 'change_control',
                    'new_value' => 'Work order email sent to ' . $assigned_to->first_name . ' ' . $assigned_to->last_name
               ]);
        }
        flash()->success(null, 'Work Order Created');
        return redirect('/change-control/' . $ticket->id . '/#work-orders');
    }

    public function applyWOTemplate(Request $request, ChangeTicket $ticket, EmailProvider $email)
    {
        $ticket = $ticket->where('id', $request->id)->firstOrFail();
        $work_orders = WOTemplateDetail::where('template_id', $request->template)->get();
        foreach ($work_orders as $work_order) {
            if ($work_order->due_in == -1) {
                $due_date = $ticket->end_date;
            } else {
                $due_date = $ticket->created_at->addDays($work_order->due_in);
            }
            $created_work_order = $ticket->workOrders()->create([
                    'assigned_to' => $work_order->assigned_to,
                    'created_by' => Auth::user()->id,
                    'status' => 'open',
                    'subject' => $work_order->subject,
                    'work_requested' => $work_order->work_requested,
                    'due_date' => $due_date
                    ]);
            //If creating a work order for yourself you don't need to be notified
               //Commenting out
               // if($created_work_order->assigned_to != Auth::user()->id) {
               //      $email->notifyWorkOrderAssigned($created_work_order);
               // }
        }
        flash()->success(null, 'Template Applied');
        return redirect()->back();
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
                $work_orders = WorkOrder::where('assigned_to', $user->id)->where('ticketable_type', 'ChangeTicket')->orderBy('status', 'desc')->orderBy('due_date')->search($_GET['search'], null, true, 1);
            }
            $data = [
                    'user' => $user,
                    'work_orders' => $work_orders->paginate(15),
                    'search' => $_GET['search']
               ];
        } else {
            $work_orders = WorkOrder::where('assigned_to', $user->id)->where('ticketable_type', 'ChangeTicket')->orderBy('status', 'desc')->orderBy('due_date');
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

    public function update(SaveChangeTicketRequest $request, ChangeTicket $ticket, EmailProvider $mailer)
    {
        $ticket = $ticket->where('id', $request->id)->firstOrFail();
        // dd($ticket->it_approver_id . ' - ' . $request->change_owner);

        $oldItApprover = $ticket->it_approver_id;
        $oldBusApprover = $ticket->bus_approver_id;
        $ticket->update([
          'audit_unit' => $request->audit_unit,
          'change_owner_id' => $request->change_owner,
          'it_approver_id' => (isset($request->it_approver) ? $request->it_approver : $ticket->it_approver_id),
          'bus_approver_id' => ($request->bus_approver != '' ? $request->bus_approver : $ticket->bus_approver_id),
          'change_type' => $request->change_type,
          'start_date' => Carbon::createFromFormat('m/d/Y g:ia', $request->start_date, Auth::user()->timezone)->timezone('utc'),
          'end_date' => Carbon::createFromFormat('m/d/Y g:ia', $request->end_date, Auth::user()->timezone)->timezone('utc'),
          'change_description' => $request->change_description,
          'roll_out_plan' => $request->roll_out_plan,
          'change_reason' => $request->change_reason,
          'back_out_plan' => $request->back_out_plan,
          'servers' => $request->servers,
          'test_plan' => $request->test_plan,
          'business_impact' => $request->business_impact,
          'affected_groups' => $request->affected_groups,
          ]);

        $ticket = $ticket->where('id', $request->id)->firstOrFail();
        if ($ticket->it_approver_id == $ticket->change_owner_id || $ticket->bus_approver_id == $ticket->change_owner_id) {
            ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'it', 'approver' => $oldItApprover])->delete();
            ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'bus', 'approver' => $oldBusApprover])->delete();
            $ticket->it_approver_id = 0;
            $ticket->bus_approver_id = 0;
            $ticket->status = 'proposed';
            $ticket->save();
            flash()->success(null, 'Ticket Updated.');
            return redirect()->back();
        }

        // if the approvers have changed, notify the new approver.
        if ($ticket->it_approver_id != $oldItApprover && $ticket->it_approver_id != 0) {
            if ($ticket->status != 'deferred') {
                $mailer->notifySingleApprover($ticket->it_approver_id, $ticket);
                Revision::create([
                         'revisionable_type' => 'ChangeTicket',
                         'revisionable_id' => $ticket->id,
                         'user_id' => Auth::user()->id,
                         'key' => 'change_control',
                         'new_value' => 'Approval email sent to ' . $ticket->itApprover->first_name . ' ' . $ticket->itApprover->last_name
                    ]);
            }
        }
        if ($ticket->bus_approver_id != $oldBusApprover && $ticket->bus_approver_id != 0) {
            if ($ticket->status != 'deferred') {
                $mailer->notifySingleApprover($ticket->bus_approver_id, $ticket);
                Revision::create([
                         'revisionable_type' => 'ChangeTicket',
                         'revisionable_id' => $ticket->id,
                         'user_id' => Auth::user()->id,
                         'key' => 'change_control',
                         'new_value' => 'Approval email sent to ' . $ticket->busApprover->first_name . ' ' . $ticket->busApprover->last_name
                    ]);
            }
        }
        // dd($ticket->it_approver . ' - ' . $oldItApprover);
        if ($ticket->it_approver_id != 0 && $oldItApprover != 0) {
            $approval = ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'it', 'approver' => $oldItApprover])->firstOrFail();
            $approval->approver = $ticket->it_approver_id;
            $approval->save();
        } elseif ($ticket->it_approver_id != 0 && $oldItApprover == 0) {
            $ticket->changeApprovals()->create([
               'approved' => 0,
               'approver' => $ticket->it_approver_id,
               'approval_type' => 'it'
               ]);
            $ticket->status = 'proposed';
            $ticket->save();
        } else {
            ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'it', 'approver' => $oldItApprover])->delete();
        }

        if ($ticket->bus_approver_id != 0 && $oldBusApprover != 0) {
            $approval = ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'bus', 'approver' => $oldBusApprover])->firstOrFail();
            $approval->approver = $ticket->bus_approver_id;
            $approval->save();
        } elseif ($ticket->bus_approver_id != 0 && $oldBusApprover == 0) {
            $ticket->changeApprovals()->create([
               'approved' => 0,
               'approver' => $ticket->bus_approver_id,
               'approval_type' => 'bus'
               ]);
            $ticket->save();
        } else {
            ChangeApproval::where(['change_ticket_id' => $ticket->id, 'approval_type' => 'bus', 'approver' => $oldBusApprover])->delete();
        }
        if ($ticket->status == 'rejected') {
            $this->amend($request);

            flash()->confirm('Changes Submitted', 'Approvers have been notified of your changes.', 'success', 'Okay');
            return redirect()->back();
        }

        flash()->success(null, 'Ticket Updated.');
        return redirect()->back();
    }

    public function startWork(Request $request)
    {
        $change_ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $change_ticket->status = 'in-progress';
        $change_ticket->save();
        flash()->success(null, 'Status set to In-progress.');
        return redirect()->back();
    }

    public function propose(Request $request, EmailProvider $mailer)
    {
        $change_ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $approvers = User::whereIn('id', $change_ticket->changeApprovals->lists('approver')->toArray())->get();
        // dd($approvers);
        $change_ticket->status = 'proposed';
        $change_ticket->save();

        //Notify approvers they need to approve a change ticket
        $mailer->notifyApprovers($change_ticket);
        foreach ($approvers as $approver) {
            Revision::create([
               'revisionable_type' => 'ChangeTicket',
               'revisionable_id' => $change_ticket->id,
               'user_id' => Auth::user()->id,
               'key' => 'change_control',
               'new_value' => 'Approval request email sent to ' . $approver->first_name . ' ' . $approver->last_name
          ]);
        }

        flash()->success(null, 'Status set to Proposed.');
        return redirect()->back();
    }

    public function addAttachment(UploadAttachmentRequest $request, ChangeTicket $ticket)
    {
        // return $request->file('file');
        //Save the file to a variable
        $file = $request->file('file');

        //Create a unique name for the file
        $name = time() . randomString(20) . '.' . $file->getClientOriginalExtension();

        //Get Size of file
        $size = $file->getClientSize()/1024;

        //Move the file to an attachments folder in the public directory
        $file->move('cc/attachments', $name);

        //Get the ticket we are working with
        $ticket = $ticket->where('id', $request->id)->firstOrFail();

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

    public function addAttachmentFromWorkOrder(UploadAttachmentRequest $request, ChangeTicket $ticket)
    {
        // return $request->file('file');
        //Save the file to a variable
        $file = $request->file('file');

        //Create a unique name for the file
        $name = time() . randomString(20) . '.' . $file->getClientOriginalExtension();

        //Get Size of file
        $size = $file->getClientSize()/1024;

        //Move the file to an attachments folder in the public directory
        $file->move('cc/attachments', $name);

        //Get the ticket we are working with
        $ticket = $ticket->where('id', $request->id)->firstOrFail();
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

    public function showCancel(Request $request)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        if (!$ticket->workOrders->where('status', 'open')->isEmpty()) {
            flash()->confirm('Oops', 'All work orders must be closed before cancelling a change ticket.', 'error');
            return redirect()->back();
        }
        $data = [
     'ticket' => $ticket,
     ];
        return view('app.change-control.cancel', $data);
    }

    public function cancelTicket(Request $request)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $ticket->status = 'cancelled';
        $ticket->cancelled_reason = $request->cancelled_reason;
        $ticket->close_date = Carbon::now();
        $ticket->save();
        flash()->success(null, 'Ticket Cancelled');
        return redirect('/change-control/' . $ticket->id);
    }

    public function showClose(Request $request)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        if (!$ticket->workOrders->where('status', 'open')->isEmpty()) {
            flash()->confirm('Oops', 'All work orders must be closed before closing a ticket.', 'error');
            return redirect()->back();
        }
        $data = [
     'ticket' => $ticket,
     ];
        return view('app.change-control.close', $data);
    }

    public function closeTicket(Request $request, ChangeControlEloquent $changeControl)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $workOrders = WorkOrder::where(['ticketable_type' => 'ChangeTicket', 'ticketable_id' => $ticket->id, 'status' => 'open'])->get();
        $changeControl->closeTicket($ticket, $request);
        flash()->success(null, 'Ticket Completed');
        return redirect('/change-control/' . $ticket->id);
    }

    public function cloneTicket(Request $request)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $clonedTicket = new ChangeTicket;
        $newTicket = $clonedTicket->create([
          'audit_unit' => $ticket->audit_unit,
          'it_approver_id' => $ticket->it_approver_id,
          'bus_approver_id' => $ticket->bus_approver_id,
          'change_owner_id' => $ticket->change_owner_id,
          'status' => 'deferred',
          'created_by' => Auth::user()->id,
          'change_type' => $ticket->change_type,
          'start_date' => $ticket->start_date,
          'end_date' => $ticket->end_date,
          'change_description' => $ticket->change_description,
          'roll_out_plan' => $ticket->roll_out_plan,
          'change_reason' => $ticket->change_reason,
          'back_out_plan' => $ticket->back_out_plan,
          'servers' => $ticket->servers,
          'test_plan' => $ticket->test_plan,
          'business_impact' => $ticket->business_impact,
          'affected_groups' => $ticket->affected_groups
     ]);

        if ($newTicket->bus_approver_id != 0) {
            $newTicket->changeApprovals()->create([
               'approved' => 0,
               'approver' => $newTicket->bus_approver_id,
               'approval_type' => 'bus',
               ]);
        }
        if ($newTicket->it_approver_id != 0) {
            $newTicket->changeApprovals()->create([
               'approved' => 0,
               'approver' => $newTicket->it_approver_id,
               'approval_type' => 'it',
               ]);
        }
        flash()->success(null, 'Ticket cloned successfully');
        return redirect('/change-control/' . $newTicket->id);
    }

    public function emailWorkOrders(Request $request, EmailProvider $mailer)
    {
        $work_orders = WorkOrder::where(['ticketable_type' => 'ChangeTicket', 'ticketable_id' => $request->id])
                                        ->whereIn('id', $request->work_order_id)->get();

        if (!$work_orders->isEmpty()) {
            foreach ($work_orders as $work_order) {
                $mailer->notifyWorkOrderAssigned($work_order);
                Revision::create([
                         'revisionable_type' => 'ChangeTicket',
                         'revisionable_id' => $work_order->ticketable->id,
                         'user_id' => Auth::user()->id,
                         'key' => 'change_control',
                         'new_value' => 'Work order email sent to ' . $work_order->assignedTo->first_name . ' ' . $work_order->assignedTo->last_name
                    ]);
            }
        }
        flash()->success(null, 'Work orders emailed successfully.');
        return redirect()->back();
    }

    public function removeWorkOrder(Request $request)
    {
        $work_order = WorkOrder::where('id', $request->id)->firstOrFail();
        $work_order->delete();
        flash()->success(null, 'Work order removed.');
        return redirect('/change-control/' . $work_order->ticketable->id . '/#work-orders');
    }

    public function needsApproval(Request $request)
    {
        $approvals = ChangeApproval::whereHas('changeTicket', function ($query) {
            $query->where('status', '!=', 'deferred')
                         ->where('status', '!=', 'cancelled');
        })->where(['approver' => Auth::user()->id, 'approved' => 0])->get();

        $data = [
               'approvals' => $approvals
          ];

        return view('app.change-control.needs-approval', $data);
    }

    public function printTicket(Request $request)
    {
        $ticket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $data = [
               'ticket' => $ticket,
         ];
        // dd($ticket->revisionHistory);
        // return $data;
        // return view('app.pdf.print-ticket', $data);
        // $pdf = PDF::loadView('app.pdf.print-change-ticket', $data);
        //  return $pdf->stream('Change Ticket #' . $ticket->id . '.pdf');

        $pdf = SnappyPDF::loadView('app.pdf.print-change-ticket', $data);
        return $pdf->inline('Change Ticket #' . $ticket->id . '.pdf');
    }

    public function applyCCTemplate(Request $request)
    {
        $this->validate($request, [
               'template' => 'required',
          ]);

        $template = ChangeTicketTemplate::where('id', $request->template)->firstOrFail();

        flash()->success(null, 'Template Applied');
        return redirect('/change-control/create?template=' . $template->id);
    }

    public function toggleAudit(Request $request)
    {
        $changeTicket = ChangeTicket::where('id', $request->id)->firstOrFail();
        $changeTicket->is_audited = ($changeTicket->is_audited == 'yes' ? 'no' : 'yes');
        $changeTicket->save();

        return response()->json([
            'status' => 'success',
            'is_audited' => $changeTicket->fresh()->is_audited,
        ]);
    }
}
