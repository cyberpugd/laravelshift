<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
               'name' => 'create_ticket',
               'label' => 'Create a ticket for another user',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'clone_change_ticket',
               'label' => 'Clone closed change ticket to a new ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'manage_locations',
               'label' => 'Can manage locations for the company',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'close_ticket',
               'label' => 'Can close a ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'reopen_ticket',
               'label' => 'Can re-open a ticket after it has been closed',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'manage_categories',
               'label' => 'Can manage categories for the system',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'agent_portal',
               'label' => 'Can access the agent portal',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'help_desk_portal',
               'label' => 'Can access the help desk portal',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'manage_users',
               'label' => 'Can manage users for the system',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'manage_imap',
               'label' => 'Manage the IMAP account that the application uses',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
        DB::table('permissions')->insert([
               'name' => 'manage_roles',
               'label' => 'Manage roles that the application uses',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
       DB::table('permissions')->insert([
               'name' => 'update_ticket',
               'label' => 'Can update tickets',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
       DB::table('permissions')->insert([
               'name' => 'view_tickets',
               'label' => 'Can view tickets',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'view_work_orders',
               'label' => 'Can view work orders on a ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'create_work_orders',
               'label' => 'Can create work orders for a ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'create_cc_ticket',
               'label' => 'Can create a change control ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'manage_audit_units',
               'label' => 'Can manage audit units for change tickets',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'change_ticket_auditor',
               'label' => 'Can audit change tickets',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'approve_change_ticket',
               'label' => 'Can approve a change control ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'be_assigned_ticket',
               'label' => ' Can have a helpdesk ticket and work order assigned to them ',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'add_attachment_ticket',
               'label' => 'Can add an attachment to a helpdesk ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'create_portal_ticket',
               'label' => 'Can create a ticket via portal',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'add_conversation_message',
               'label' => 'Can add a message to a conversation thread on a ticket',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'create_view',
               'label' => 'Can create custom views',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);

        DB::table('permissions')->insert([
               'name' => 'create_work_order_templates',
               'label' => 'Can create a work order template',
               'created_at' => \Carbon\Carbon::now(),
               'updated_at' => \Carbon\Carbon::now()
          ]);
    }
}
