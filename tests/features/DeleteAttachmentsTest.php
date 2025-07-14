<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use Carbon\Carbon;
use App\Attachment;

class DeleteAttachmentsTest extends TestCase
{
     use DatabaseTransactions;

     /** @test */
     function allow_only_change_coordinators_to_delete_attachments()
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

          $changeCoordinator = User::create([
               'ad_id' => 'eaed1\coordinatoruser',
               'first_name' => 'coordinator',
               'last_name' => 'user',
               'email' => 'coordinator@coordinator.com',
               'sip' => 'coordinator@coordinator.com',
               'phone_number' => '1232343455',
               'location_id' => 1,
               'timezone' => 'US/Mountain',
               'active' => 1,
               'out_of_office' => 0,
          ]);

          $changeCoordinator->assignRole([2, 5]);

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

          $attachment = $changeTicket->attachments()->create([
               'file_name' => 'testLogo.jpg',
               'file' => 'testfile.jpg',
               'file_size' => 6.5,
          ]);

          $this->be($changeCoordinator);
          $this->call('POST', 'tickets/attachment/'.$attachment->id, []);
          
          if(!deniedPermission('change_ticket_auditor')) {
               $this->assertResponseStatus(200);
               $this->assertNull(Attachment::find($attachment->id));
          } else {
               $this->assertResponseStatus(302);
               $this->assertNotNull(Attachment::find($attachment->id));
          }
          
          
          // return $perms;
          // dd($perms->flatten());



     }
}