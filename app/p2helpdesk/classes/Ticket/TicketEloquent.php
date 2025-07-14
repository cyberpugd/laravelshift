<?php
namespace App\p2helpdesk\classes\Ticket;

use DB;
use Auth;
use App\Ticket;
use App\Subcategory;
use App\Conversation;
use App\ConversationPrivate;
use Carbon\Carbon;
use App\TicketView;
use App\User;

class TicketEloquent
{
    public function create($request)
    {
        //Get the category the subcategory belongs to
        $subcategory = Subcategory::where('id', $request->sub_category)->firstOrFail();

        try {
            DB::beginTransaction();

            //Create the ticket in the tickets table
            $ticket = Ticket::create([
                    'created_by' => $request->caller,
                    'agent_id' => (isset($request->agent) ? $request->agent : ''),
                    'category_id' => $subcategory->category->id,
                    'sub_category_id' => $request->sub_category,
                    'title' => $request->title,
                    'description' => $request->description,
                    'urgency_id' => $request->urgency,
                    'status' => 'open'
                    ]);
            $dueDate = $this->calculateDueDate($ticket);
            $ticket->due_date = $dueDate;
            $ticket->save();
        } catch (Exception $e) {
            DB::rollback();
        }

        DB::commit();
        return $ticket;
    }

    public function update($request, $ticket_id)
    {
        try {
            DB::beginTransaction();
            $dueDate = Carbon::createFromFormat('m/d/Y h:i A', $request->due_date, Auth::user()->timezone);

            // dd($dueDate);
            $ticket = $this->findById($ticket_id);
            $subcategory = Subcategory::where('id', $request->sub_category)->firstOrFail();
            $ticket->sub_category_id = $request->sub_category;
            $ticket->category_id = $subcategory->category->id;
            if ($ticket->due_date->format('m/d/Y h:i A') != $dueDate->format('m/d/Y h:i A')) {
                $ticket->due_date = $dueDate->timezone('utc');
            }
            $ticket->save();

            $ticket = $this->findById($request->id);
            //If the urgency has changed, update the due date
            if ($ticket->urgency_id != $request->urgency) {
                $ticket->urgency_id = $request->urgency;
                $ticket->due_date = $this->calculateDueDate($ticket);
                $ticket->save();
            }
            //If the agent has changed, update the due date
            if ($ticket->agent_id != $request->agent) {
                $ticket->agent_id = $request->agent;
                $ticket->due_date = $this->calculateDueDate($ticket);
                $ticket->save();
            }
            if (isset($request->caller)) {
                if ($ticket->created_by != $request->caller) {
                    $ticket->created_by = $request->caller;
                    $ticket->save();
                }
            }
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
        return $ticket;
    }

    public function openTicketsAssignedToMe($search = null, $numResults = 25)
    {
        return Ticket::where(['agent_id' => Auth::user()->id, 'status' => 'open'])->orderBy('due_date', 'asc')->search($search, null, true, 1);
    }

    public function findById($id)
    {
        return Ticket::where(['id' => $id])->firstOrFail();
    }

    public function closeTicket($request)
    {
        $ticket = $this->findById($request->id);
        $ticket->status = 'closed';
        $ticket->resolution = $request->resolution;
        $ticket->close_date = Carbon::now();
        $ticket->save();
        return $ticket;
    }

    public function getAll($search = null, $numResults = 25)
    {
        $tickets = Ticket::with(['category', 'subcategory'])->orderBy('status', 'desc')->orderBy('due_date', 'asc')->search($search, null, true, 1);
        return $tickets;
    }

    public function postMessage($request, $source)
    {
        return Conversation::create([
               'ticket_id' => $request->id,
               'created_by' =>  Auth::user()->last_name . ',  ' . Auth::user()->first_name,
               'source' => $source,
               'message' => $request->message
          ]);
    }

    public function postPrivateMessage($request, $source)
    {
        return ConversationPrivate::create([
               'ticket_id' => $request->id,
               'created_by' =>  Auth::user()->last_name . ',  ' . Auth::user()->first_name,
               'source' => $source,
               'message' => $request->message
          ]);
    }

    public function calculateDueDate($ticket)
    {
        // dd($ticket);
        if ($ticket->agent_id != 0) {
            $beginTime = Carbon::create($ticket->created_at->year, $ticket->created_at->month, $ticket->created_at->day, 8, 0, 0, $ticket->assignedTo->timezone);
            $endTime = Carbon::create($ticket->created_at->year, $ticket->created_at->month, $ticket->created_at->day, 17, 0, 0, $ticket->assignedTo->timezone);
            $tempDate = $ticket->created_at->timezone($ticket->assignedTo->timezone);
            $holidays = $ticket->assignedTo->location->holidays;
        } else {
            $beginTime = Carbon::create($ticket->created_at->year, $ticket->created_at->month, $ticket->created_at->day, 8, 0, 0, $ticket->createdBy->timezone);
            $endTime = Carbon::create($ticket->created_at->year, $ticket->created_at->month, $ticket->created_at->day, 17, 0, 0, $ticket->createdBy->timezone);
            $tempDate = $ticket->created_at->timezone($ticket->createdBy->timezone);
            $holidays = $ticket->createdBy->location->holidays;
        }



        // dd($beginTime);
        //Loop through each hour
        for ($i = 1; $i <= $ticket->urgency->hours; $i++) {

                  // if the time is less than the start of business day
            if ($tempDate->lt($beginTime)) {
                $tempDate = $beginTime->addHour();
                $i = $i-1;
            }

            // If the time doesn't fall inside business hours add a day
            // dd($endTime);
            if ($tempDate->gt($endTime)) {
                $tempDate->hour = 8;
                $tempDate->minute = $beginTime->minute + ($tempDate->minute - $endTime->minute);
                $tempDate->second = $beginTime->second + ($tempDate->second - $endTime->second);
                $tempDate = $tempDate->addDay();
                $endTime = $endTime->addDay();
                // $i = $i-1;
            }

            //Check for weekends and holidays
            if (in_array($tempDate->dayOfWeek, [Carbon::SUNDAY, Carbon::SATURDAY])) {
                if ($tempDate->toDateString() != Carbon::now()->toDateString()) {
                    $tempDate->hour = 8;
                    $tempDate->minute = $beginTime->minute + ($tempDate->minute - $endTime->minute);
                    $tempDate->second = $beginTime->second + ($tempDate->second - $endTime->second);
                    $i = $i-1;
                } else {
                    $tempDate = $beginTime;
                    $i = $i-1;
                }
                $tempDate = $tempDate->addDay();
                $endTime = $endTime->addDay();
                continue;
            }
            // dd($tempDate);
            //Check for holidays
            foreach ($holidays as $holiday) {
                if ($tempDate->toDateString() == $holiday->date->toDateString()) {
                    if ($tempDate->toDateString() != Carbon::now()->toDateString()) {
                        $tempDate->hour = 8;
                        $tempDate->minute = $beginTime->minute + ($tempDate->minute - $endTime->minute);
                        $tempDate->second = $beginTime->second + ($tempDate->second - $endTime->second);
                        $i = $i-1;
                    } else {
                        $tempDate = $beginTime;
                        $i = $i-1;
                    }
                    $tempDate = $tempDate->addDay();
                    $endTime = $endTime->addDay();

                    continue;
                }
            }
            //Add an hour
            $tempDate = $tempDate->addHour(1);
            // echo $tempDate->toDayDateTimeString() . ' - ' . $i . '<br>';
        }
        // dd('done');
        return $tempDate->timezone('utc');
    }

    public function myOpenTickets()
    {
        return Ticket::where(['agent_id' => Auth::user()->id, 'status' => 'open'])->orderBy('status', 'desc')->orderBy('created_at', 'desc')->paginate(25);
    }

    public function myTeamsTickets($search = null)
    {
        $teamsTickets = Ticket::with(['category', 'subcategory', 'createdBy', 'createdBy.location', 'urgency'])->whereHas('subcategory', function ($query) {
            return $query->whereHas('teams', function ($query2) {
                return $query2->whereIn('id', Auth::user()->teams->lists('id')->toArray());
            });
        })->where('agent_id', 0)->where('status', 'open')->orderBy('due_date', 'asc')->search($search, null, true, 1);

        return $teamsTickets;
    }

    public function myClosedTickets()
    {
        return Ticket::where(['created_by' => Auth::user()->id, 'status' => 'closed'])->orderBy('status', 'desc')->orderBy('created_at', 'desc')->paginate(15);
    }

    public function search($search)
    {
        $tickets = Ticket::search($search, null, true, 1);

        return $tickets;
    }

    public function getTeamMembersToEmail($ticket, $emailToExclude)
    {
        if ($ticket->subcategory->location_matters) {
            //If location matters, get a collection of all agents on a team assigned to that category but also who are in the same location as the user who created the ticket
            $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
            $createdByCity = $ticket->createdBy->location->city;

            $usersToEmail = User::whereHas('location', function ($query) use ($createdByCity) {
                $query->where('city', $createdByCity);
            })->whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                $query->whereIn('name', $teamsAssignedToCat);
            })->where('active', 1)->where('email', '!=', $emailToExclude)->lists('email');
            if (!$usersToEmail->isEmpty()) {
                return $usersToEmail;
            } else {
                //If we didn't find any users with the same location as created by user, email everyone on the team.
                $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
                $createdByCity = $ticket->createdBy->location->city;
                $usersToEmail = User::whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                    return $query->whereIn('name', $teamsAssignedToCat);
                })->where('active', 1)->where('email', '!=', $emailToExclude)->lists('email');
                if (!$usersToEmail->isEmpty()) {
                    return $usersToEmail;
                }
                if ($usersToEmail->isEmpty()) {
                    // If we don't get any results, there isn't anyone assigned to the category
                }
            }
        } else {
            //If location doesn't matter, get a list of all agents on the teams assigned to that category.
            $teamsAssignedToCat = $ticket->subcategory->teams->lists('name');
            $createdByCity = $ticket->createdBy->location->city;
            $usersToEmail = User::whereHas('teams', function ($query) use ($teamsAssignedToCat) {
                return $query->whereIn('name', $teamsAssignedToCat);
            })->where('active', 1)->where('email', '!=', $emailToExclude)->lists('email');
            if (!$usersToEmail->isEmpty()) {
                return $usersToEmail;
            }
            if ($usersToEmail->isEmpty()) {
                // If we don't get any results, there isn't anyone assigned to the category
            }
        }
    }
}
