<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;
class AssignPermissionToRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::all();
        $adminPermissions = Permission::whereIn('name', [
               'manage_imap',
               'manage_users',
               'manage_categories',
               'manage_roles',
               'manage_companies',
               'manage_audit_units',
               'manage_locations',
               'agent_portal',
               ])->get();
        $agentPermissions = Permission::whereIn('name', [
               'create_ticket', 
               'update_ticket', 
               'view_tickets', 
               'create_work_orders',
               'view_work_orders',
               'close_ticket',
               'reopen_ticket',
               'create_cc_ticket',
               'be_assigned_ticket',
               'add_conversation_message',
               'add_attachment_ticket',
               'create_work_order_templates',
               'create_view',
               'agent_portal',
               ])->get();
        $changePermissions = Permission::whereIn('name', [
               'approve_change_ticket',
               'agent_portal',
               ])->get();
        $userPermissions = Permission::whereIn('name', [
               'create_portal_ticket',
               'add_conversation_message',
               'add_attachment_ticket',
               'view_tickets',
               'help_desk_portal',
               ])->get();
        foreach($roles as $role) {
               if($role->label == 'Helpdesk Administrator') {
                    foreach($adminPermissions as $permission) {
                         $role->givePermissionTo($permission);
                    }
               }
               if($role->label == 'Helpdesk Support Agent') {
                    foreach($agentPermissions as $permission) {
                         $role->givePermissionTo($permission);
                    }
               }
                if($role->label == 'Change Approver') {
                    foreach($changePermissions as $permission) {
                         $role->givePermissionTo($permission);
                    }
               }
               if($role->label == 'Employee End User') {
                    foreach($userPermissions as $permission) {
                         $role->givePermissionTo($permission);
                    }
               }
        }
    }
}
