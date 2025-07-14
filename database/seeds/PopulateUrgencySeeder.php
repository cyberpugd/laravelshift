<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class PopulateUrgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $rows = [
                    ['name'=>'Critical', 'description'=>'Resolved in 4 hrs', 'hours'=>4],
                    ['name'=>'High', 'description'=>'Resolved in 8 hrs', 'hours'=>8],
                    ['name'=>'Medium', 'description'=>'Resolved in 24 hrs', 'hours'=>24],
                    ['name'=>'Low', 'description'=>'Resolved in 64 hrs', 'hours'=>64],
                    ['name'=>'Human Resources', 'description'=>'Human Resources Tickets Only', 'hours'=>160]
               ];
          foreach($rows as $row) {
              DB::table('urgency')->insert([
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'hours' => $row['hours'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
               ]);
          }
    }
}
