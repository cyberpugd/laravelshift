<?php
use App\User;
use Carbon\Carbon;
use App\ChangeTicket;
use App\WorkOrder;
use App\p2helpdesk\classes\Ticket\ChangeControlEloquent;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CancelChangeTicketTest extends TestCase
{
     use DatabaseTransactions;
    /** @test */
    public function all_work_orders_must_be_closed_when_closing_change_tickets()
    {

          $changeOwner = User::create([
               'ad_id' => 'eaed1\testuser',
               'first_name' => 'test',
               'last_name' => 'user',
               'email' => 'test@test.com',
               'sip' => 'test@test.com',
               'phone_number' => '1232343455',
               'location_id' => 1,
               'timezone' => 'US/Mountain',
               'active' => 1,
               'out_of_office' => 0,
          ]);

          $changeOwner->assignRole([2,3]);

          $changeApprover = User::create([
               'ad_id' => 'eaed1\approver',
               'first_name' => 'change',
               'last_name' => 'approver',
               'email' => 'approver@test.com',
               'sip' => 'approver@test.com',
               'phone_number' => '1232343455',
               'location_id' => 1,
               'timezone' => 'US/Mountain',
               'active' => 1,
               'out_of_office' => 0,
          ]);
          
          $changeTicket = ChangeTicket::create([
               'audit_unit' => 1,
               'change_owner_id' => $changeOwner->id,
               'created_by' => $changeOwner->id,
               'it_approver_id' => $changeApprover->id,
               'bus_approver_id' => 0,
               'change_type' => 'planned',
               'status' => 'in-progress',
               'start_date' => Carbon::now(),
               'end_date' => Carbon::now()->addDays(3),
               'change_description' => 'test',
               'roll_out_plan' => 'test',
               'change_reason' => 'test',
               'back_out_plan' => 'test',
               'servers' => 'test',
               'test_plan' => 'test',
               'business_impact' => 'test',
               'affected_groups' => 'test',              
               'is_audited' => 'no', 
          ]);

          $workOrder = $changeTicket->workOrders()->create([
               'assigned_to' => $changeOwner->id,
               'created_by' => $changeOwner->id,
               'subject' => 'test subject',
               'work_requested' => 'test work requested',
               'due_date' => Carbon::now()->addDays(2),
               'status' => 'open',
          ]);
          // dd($changeOwner->toArray());
          $this->be($changeOwner);
          $this->visit('/change-control/all');
          $this->visit('/change-control/cancel/'.$changeTicket->id);
          // $this->click('Close Change Ticket');
          $this->see('All work orders must be closed before cancelling a change ticket.');
          
          

    }
}
