<?php

use App\User;
use App\Location;
use App\Holiday;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LocationsTest extends TestCase
{
    /** @test */
    public function a_user_can_create_a_location()
    {
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
        $user->assignRole([1,2,3,4]);
        $this->actingAs($user);
        $currentLocationCount = Location::all()->count();
        $this->call('POST', '/admin/locations/add', [
            'city' => 'Denver',
            'timezone' => 'US/Mountain'
        ]);

        $this->assertResponseStatus(302);
        $this->assertEquals($currentLocationCount+1, Location::all()->count());
    }

    /** @test */
    public function a_user_can_update_a_location()
    {
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
        $user->assignRole([1,2,3,4]);
        $this->actingAs($user);

        $location = Location::first();
        $holiday = Holiday::first();

        $this->call('POST', '/admin/locations/' . $location->id, [
            'city' => 'Denver',
            'holidays' => [
                ['id' => $holiday->id, 'name' => $holiday->name, 'date' => $holiday->date]
            ],
            'id' => $location->id,
            'timezone' => 'US/Mountain',
        ]);

        $this->assertResponseStatus(200);
    }
}
