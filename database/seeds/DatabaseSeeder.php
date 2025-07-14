<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(AssignPermissionToRole::class);
        $this->call(PopulatePlaceholdersSeeder::class);
        $this->call(PopulateLocationSeeder::class);
        $this->call(PopulateUrgencySeeder::class);
    }
}
