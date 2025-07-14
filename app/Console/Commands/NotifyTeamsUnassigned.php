<?php

namespace App\Console\Commands;

use App\Team;
use Illuminate\Console\Command;
use App\p2helpdesk\classes\Email\EmailProvider;

class NotifyTeamsUnassigned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p2helpdesk:notifyTeamsUnassigned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify all agents on a team when they have unassigned tickets assigned to their team';

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
        //Loop through each Team in the system
        Team::all()->each(function ($team, $teamKey) {
            $tickets = collect();

            //Loop through all the subcategories for each team
            $team->subcategories->each(function ($subcategory, $key) use ($tickets) {
                //For each subcategory, get all open unassigned tickets and loop through each of them
                $subcategory->tickets()
                    ->where('status', 'open')
                    ->where('agent_id', 0)
                    ->each(function ($ticket, $tkey) use ($tickets) {
                        //Push each of the open unassigned tickets to the tickets collection
                        $tickets->push($ticket);
                    });
            });
            //If we have some tickets, send an email
            if (!$tickets->isEmpty()) {
                $emails = $team->users()->lists('email')->toArray();
                if (!empty($emails)) {
                    $emailProvider = new EmailProvider();
                    $emailProvider->notifyTeamOfUnassignedTickets($emails, $tickets, $team);
                }
            }
        });
    }
}
