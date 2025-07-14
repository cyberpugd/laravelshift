<?php

use Illuminate\Database\Seeder;
use App\User;
class AssignUserAdminRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user')->truncate();
        $users = User::all();
        foreach($users as $user) {
               $user->assignRole('admin');
        }
    }
}
