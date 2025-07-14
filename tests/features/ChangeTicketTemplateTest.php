<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChangeTicketTemplateTest extends TestCase
{
     use DatabaseTransactions;
    /** @test */
    function can_create_a_cc_template()
    {
         $template = factory(App\ChangeTicketTemplate::class)->create(['name' => 'test11']);
         
         $this->assertEquals('test11', $template->name);
    }

    /** @test */
    function can_delete_a_template()
    {
         $template = factory(App\ChangeTicketTemplate::class)->create(['name' => 'test11']);

         $this->seeInDatabase('change_ticket_templates', ['name' => 'test11']);
        
         $template->delete();

         $this->notSeeInDatabase('change_ticket_templates', ['name' => 'test11']);
    }

    /** @test */
    function can_share_a_template()
    {
         $template = factory(App\ChangeTicketTemplate::class)->create(['name' => 'test11']);

         $usersToShare = factory(App\User::class, 5)->create()->lists('id')->toArray();
         
          $template->sharedWith()->sync($usersToShare);

          $this->seeInDatabase('change_ticket_template_user', ['user_id' => $usersToShare[0]]);
          $this->seeInDatabase('change_ticket_template_user', ['user_id' => $usersToShare[1]]);
          $this->seeInDatabase('change_ticket_template_user', ['user_id' => $usersToShare[2]]);
          $this->seeInDatabase('change_ticket_template_user', ['user_id' => $usersToShare[3]]);
          $this->seeInDatabase('change_ticket_template_user', ['user_id' => $usersToShare[4]]);
    }

    /** @test */
    function can_apply_a_template_to_cc_ticket()
    {
         $user = factory(App\User::class)->create([
               'location_id' => 1,
               'timezone' => 'US/Mountain',
               'active' => 1,
               'out_of_office' => 0,
          ]);
         $user->assignRole([1,2,3]);

         $template = factory(App\ChangeTicketTemplate::class)->create(['owner_id' => $user->id]);
         // dd($user->id . ' - ' . $template->owner_id);
         $this->be($user);

         $this->visit('/change-control/create?template='.$template->id)
                    ->see($template->change_description)
                    ->see($template->roll_out_plan);
    }

    /** @test */
    function can_only_see_templates_that_are_shared_with_you_or_you_own()
    {
          $owner = factory(App\User::class)->create();
         $owner->assignRole([1,2,3]);

         $template = factory(App\ChangeTicketTemplate::class)->create([
                         'owner_id' => $owner->id,
                         'name' => 'test11'
                         ]);

         $userToShare = factory(App\User::class)->create();
         $userToShare->assignRole([1,2,3]);
         $template->sharedWith()->sync($userToShare->lists('id')->toArray());

         $userNotShared = factory(App\User::class)->create();
         $userNotShared->assignRole([1,2,3]);

         $this->be($userToShare);
         $this->visit('/change-control/create')
               ->see($template->name);

          $this->be($userNotShared);
          $this->visit('/change-control/create')
               ->dontSee($template->name);

          $this->be($owner);
          $this->visit('/change-control/create')
               ->see($template->name);

    }
}
