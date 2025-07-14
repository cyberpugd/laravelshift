<?php

use Illuminate\Database\Seeder;

class PopulateLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
               'city' => 'Denver',
               'timezone' => 'US/Mountain',
          ]);
        DB::table('locations')->insert([
               'city' => 'Adelaide',
               'timezone' => 'Australia/Adelaide',
          ]);
        DB::table('locations')->insert([
               'city' => 'Austin',
               'timezone' => 'US/Central',
          ]);
        DB::table('locations')->insert([
               'city' => 'Bogota',
               'timezone' => 'America/Bogota',
          ]);
        DB::table('locations')->insert([
               'city' => 'Calgary',
               'timezone' => 'US/Mountain',
          ]);
        DB::table('locations')->insert([
               'city' => 'Dubai',
               'timezone' => 'Asia/Dubai',
          ]);
        DB::table('locations')->insert([
               'city' => 'Fort Worth',
               'timezone' => 'US/Central',
          ]);
        DB::table('locations')->insert([
               'city' => 'Houston',
               'timezone' => 'US/Central',
          ]);
        DB::table('locations')->insert([
               'city' => 'Perth',
               'timezone' => 'Australia/Perth',
          ]);
        DB::table('locations')->insert([
               'city' => 'New Jersey',
               'timezone' => 'US/Eastern',
          ]);
        DB::table('locations')->insert([
               'city' => 'San Antonio',
               'timezone' => 'US/Central',
          ]);
        DB::table('locations')->insert([
               'city' => 'Singapore',
               'timezone' => 'Asia/Singapore',
          ]);
        DB::table('locations')->insert([
               'city' => 'Thailand',
               'timezone' => 'Asia/Bangkok',
          ]);
        DB::table('locations')->insert([
               'city' => 'Tunis',
               'timezone' => 'Africa/Tunis',
          ]);
    }
}
