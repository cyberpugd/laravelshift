<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          DB::table('roles')->insert([
               'label' => 'Helpdesk Administrator',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
          DB::table('roles')->insert([
               'label' => 'Helpdesk Support Agent',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
          DB::table('roles')->insert([
               'label' => 'Employee End User',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

          DB::table('roles')->insert([
               'label' => 'Change Approver',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
    }
}
