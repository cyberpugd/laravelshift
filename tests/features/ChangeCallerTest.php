<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Carbon\Carbon;

class ChangeCallerTest extends TestCase
{
     use DatabaseTransactions;
     /** @test */
     function permission_edit_caller_can_change_a_caller_on_hd_ticket()
     {
          $admin = factory(App\User::class)->create(['email' => 'kreierson2@p2es.com']);
          $admin->assignRole([1,2,3]);
          $this->be($admin);
          $newCaller = factory(App\User::class)->create(['email' => 'kreierson@p2es.com']);

          $ticket = factory(App\Ticket::class)->create(['category_id' => 1, 'sub_category_id' => 2]);

          $this->visit('/tickets/' . $ticket->id)
               ->click('editTicket')
               ->select($newCaller->id, 'caller')
               ->press('Save');

          $this->seeInDatabase('tickets', [
               'id' => $ticket->id,
               'created_by' => $newCaller->id,
          ]);
     }

     /** @test */
     function it_does_not_update_caller_if_permission_edit_caller_does_not_exist()
     {
          $user = factory(App\User::class)->create();
          $user->assignRole([2,3]);
          $this->be($user);
          $newCaller = factory(App\User::class)->create();

          $ticket = factory(App\Ticket::class)->create(['category_id' => 1, 'sub_category_id' => 2]);
          $this->visit('/tickets/' . $ticket->id)
               ->click('editTicket')
               ->press('Save');
     }

     /** @test */
     function email_should_be_sent_to_new_caller()
     {

          $user = factory(App\User::class)->create();
          $user->assignRole([1, 2,3]);
          $this->be($user);
          $newCaller = factory(App\User::class)->create(['email' => 'kreierson@p2es.com']);

          $ticket = factory(App\Ticket::class)->create(['category_id' => 1, 'sub_category_id' => 2]);


          //Verify email was sent to new caller.
          $mock = Mockery::mock('Swift_Mailer');
          $this->app['mailer']->setSwiftMailer($mock);
          $to = null;
          $mock->shouldReceive('send')->once()->andReturnUsing(function($message) use (&$to) {
               $to = $message->getTo();
          });
          $this->call('POST', 'tickets/'.$ticket->id, [
               'caller' => $newCaller->id,
                'sub_category' => $ticket->sub_category_id,
                 'urgency' => $ticket->urgency_id,
                 'agent' => $ticket->agent_id,
                 'due_date' => Carbon::now()->format('m/d/Y g:i a'),
          ]);
          // dd($post);

          $this->assertArrayHasKey($ticket->fresh()->createdBy->email, $to);
     }
     // public function tearDown() {
     //     \Mockery::close();
     // }
}
