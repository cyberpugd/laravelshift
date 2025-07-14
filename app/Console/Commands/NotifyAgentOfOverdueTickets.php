<?php

namespace App\Console\Commands;

use App\User;
use App\Ticket;
use App\WorkOrder;
use App\ChangeTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\p2helpdesk\classes\Email\EmailProvider;

class NotifyAgentOfOverdueTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p2helpdesk:notifyAgentOfOverdueTickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email any agent that they have overdue tickets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $agents = User::whereIn(
            'id',
            collect(DB::table('role_user')->where('role_id', 2)->get())->pluck('user_id')->toArray()
        )->get();

        $agents->each(function ($agent) {
            $overdue_tickets = collect();
            $overdue_work_orders = collect();
            $overdue_change_tickets = collect();
            Ticket::where(['agent_id' => $agent->id, 'status' => 'open'])->get()->each(function ($ticket) use ($overdue_tickets) {
                if ($ticket->due_date < Carbon::now()) {
                    $overdue_tickets->push($ticket);
                }
            });
            WorkOrder::where(['assigned_to' => $agent->id, 'status' => 'open'])->get()->each(function ($work_order) use ($overdue_work_orders) {
                if ($work_order->due_date < Carbon::now()) {
                    $overdue_work_orders->push($work_order);
                }
            });
            ChangeTicket::where(['change_owner_id' => $agent->id])
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->get()->each(function ($change_ticket) use ($overdue_change_tickets) {
                    if ($change_ticket->end_date < Carbon::now()) {
                        $overdue_change_tickets->push($change_ticket);
                    }
                });

            // dd($overdue_change_tickets);
            if (!$overdue_tickets->isEmpty() || !$overdue_work_orders->isEmpty() || !$overdue_change_tickets->isEmpty()) {
                $emailProvider = new EmailProvider();
                $emailProvider->notifyAgentOfOverdueTickets($agent, $overdue_tickets, $overdue_work_orders, $overdue_change_tickets);
            }
        });
    }
}
