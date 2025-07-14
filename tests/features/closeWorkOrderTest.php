<?php

use Carbon\Carbon;
use App\User;
use App\WorkOrder;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class closeWorkOrderTest extends TestCase {
     use DatabaseTransactions;


     /** @test */
     function email_change_ticket_owner_when_work_order_status_changes_to_closed_and_change_ticket_status_is_in_progress()
     {
           //Actually create a user instead.
          $user = User::create([
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

          $user->assignRole([2,3]);

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

          $ticket = ChangeTicket::create([
               'audit_unit' => 1,
               'change_owner_id' => $user->id,
               'created_by' => $user->id,
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

          $workOrder = $ticket->workOrders()->create([
               'assigned_to' => $user->id,
               'created_by' => $user->id,
               'subject' => 'test subject',
               'work_requested' => 'test work requested',
               'due_date' => Carbon::now()->addDays(2),
               'status' => 'open',
          ]);

          $this->be($user);

          
          //Verify email was sent to ticket owner.
          $mock = Mockery::mock('Swift_Mailer');
          $this->app['mailer']->setSwiftMailer($mock);
          $to = '';
          $mock->shouldReceive('send')->once()->andReturnUsing(function($message) use (&$to) {
               $to = $message->getTo();
          });

          $this->call('POST', 'tickets/work-order/'.$workOrder->id, [
               'status' => 'closed',
               'work_completed' => 'test completed work',
               'assigned_to' => $user->id,
               'due_date' => Carbon::now()->format('m/d/Y g:i a'),
          ]);

          
          
          $updatedWorkOrder = $workOrder->fresh();
          // dd($to);
          $this->assertEquals('closed', $updatedWorkOrder->status);
          $this->assertArrayHasKey($ticket->changeOwner->email, $to);
          $this->assertEquals('in-progress', $ticket->status);
     }

     public function tearDown() {
         \Mockery::close();
     }
}    