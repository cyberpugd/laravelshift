<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateP2helpdeskTablesAndForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     // Create application Tables
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ad_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('sip');
            $table->string('phone_number')->nullable();
            $table->integer('location_id')->unsigned();
            $table->string('timezone')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('out_of_office')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by')->unsigned();
            $table->integer('agent_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('sub_category_id')->unsigned();
            $table->string('title', 255);
            $table->longtext('description');
            $table->string('status');
            $table->integer('urgency_id')->unsigned();
            $table->longtext('resolution')->nullable();
            $table->datetime('due_date')->nullable();
            $table->datetime('close_date')->nullable();
            $table->timestamps();
        });

     Schema::create('change_tickets', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('audit_unit')->unsigned();
          $table->integer('change_owner_id')->unsigned();
          $table->integer('created_by')->unsigned();
          $table->integer('it_approver_id')->unsigned();
          $table->integer('bus_approver_id')->unsigned();
          $table->string('change_type');
          $table->string('status', 15);
          $table->datetime('start_date');
          $table->datetime('end_date');
          $table->datetime('close_date')->nullable();
          $table->longtext('change_description');
          $table->longtext('roll_out_plan');
          $table->longtext('change_reason');
          $table->longtext('back_out_plan');
          $table->longtext('servers');
          $table->longtext('test_plan');
          $table->longtext('business_impact');
          $table->longtext('affected_groups')->nullable();
          $table->longtext('cancelled_reason')->nullable();
          $table->string('completed_type')->nullable();
          $table->longtext('completed_notes')->nullable();
          $table->string('is_audited')->default('no');
          $table->timestamps();
     });

     Schema::create('change_approvals', function(Blueprint $table) {
          $table->increments('id');
          $table->integer('change_ticket_id');
          $table->string('approval_type', 10);
          $table->integer('approved')->unsigned();
          $table->datetime('date_approved')->nullable();
          $table->integer('approver')->unsigned();
          $table->timestamps();
     });

     Schema::create('audit_units', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name');
          $table->integer('status')->unsigned();
          $table->timestamps();
     });

     Schema::create('urgency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('hours');
            $table->timestamps();
        });        

     Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city');
            $table->string('timezone');
            //Add Day start and end times
            $table->timestamps();
        });  

     Schema::create('holidays', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name', 50);
          $table->datetime('date');
          $table->timestamps();
     });

        Schema::create('work_orders', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('assigned_to')->unsigned();
               $table->integer('created_by')->unsigned();
               $table->integer('ticketable_id')->unsigned();
               $table->string('ticketable_type');
               $table->string('status');
               $table->string('subject');
               $table->text('work_requested')->nullable();
               $table->text('work_completed')->nullable();
               $table->datetime('completed_date')->nullable();
               $table->datetime('due_date')->nullable();
               $table->timestamps();
        });

        Schema::create('work_order_template_details', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('template_id')->unsigned();
               $table->integer('assigned_to')->unsigned();
               $table->string('subject');
               $table->text('work_requested')->nullable();
               $table->integer('due_in')->nullable();
               $table->timestamps();
        });

        Schema::create('work_order_templates', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('owner_id')->unsigned();
               $table->string('name');
               $table->timestamps();
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('ticketable_id')->unsigned();
               $table->string('ticketable_type');
            $table->string('file_name');
            $table->string('file');
            $table->decimal('file_size', 18, 10);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('active')->default(1);
            $table->timestamps();
        });

        Schema::create('subcategories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
            $table->string('tags', 300)->nullable();
            $table->boolean('location_matters');
            $table->integer('created_by')->unsigned();
            $table->integer('active')->default(1);
            $table->timestamps();
        });

        Schema::create('user_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 50);
            $table->string('query_type');
            $table->longtext('select_columns');
            $table->timestamps();
        });

        Schema::create('filter_criteria', function(Blueprint $table) {
               //['view_id', 'column', 'operator', 'criteria1', 'criteria2']
               $table->increments('id');
               $table->integer('view_id')->unsigned();
               $table->string('column');
               $table->string('operator');
               $table->string('criteria1');
               $table->string('criteria2')->nullable();
               $table->timestamps();

        });

        Schema::create('conversations', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('ticket_id')->unsigned();
               $table->string('created_by');
               $table->string('source');
               $table->longtext('message');
               $table->timestamps();
        });

        Schema::create('conversations_private', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('ticket_id')->unsigned();
               $table->string('created_by');
               $table->string('source');
               $table->longtext('message');
               $table->timestamps();
        });

        Schema::create('placeholders', function (Blueprint $table) {
               $table->increments('id');
               $table->string('message');
               $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
               $table->increments('id');
               $table->string('label');
               $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
               $table->increments('id');
               $table->string('name');
               $table->string('label')->nullable();
               $table->timestamps();
        });
        
          Schema::create('admin_settings', function (Blueprint $table) {
               $table->increments('id');
               $table->string('mail_port');
               $table->string('mail_server');
               $table->string('mail_user');
               $table->string('mail_password');
               $table->string('email_address');
               $table->string('mail_folder');
               $table->string('mail_processed_folder');
               $table->string('phone_number');
               $table->timestamps();
          });

          Schema::create('announcements', function (Blueprint $table) {
               $table->increments('id');
               $table->string('type');
               $table->string('title');
               $table->longtext('details');
               $table->datetime('start_date');
               $table->datetime('end_date');
               $table->timestamps();
          });

        Schema::create('permission_role', function (Blueprint $table) {
               $table->integer('permission_id')->unsigned();
               $table->integer('role_id')->unsigned();
        });

         Schema::create('role_user', function (Blueprint $table) {
               $table->integer('role_id')->unsigned();
               $table->integer('user_id')->unsigned();
        });

         Schema::create('holiday_location', function (Blueprint $table) {
               $table->integer('holiday_id')->unsigned();
               $table->integer('location_id')->unsigned();
        });

         Schema::create('teams', function(Blueprint $table) {
               $table->increments('id');
               $table->string('name');
               $table->integer('self_enroll');
               $table->timestamps();
         });

         Schema::create('team_user', function(Blueprint $table) {
               $table->integer('team_id')->unsigned();
               $table->integer('user_id')->unsigned();
         });

         Schema::create('subcategory_team', function(Blueprint $table) {
               $table->integer('team_id')->unsigned();
               $table->integer('subcategory_id')->unsigned();
         });

        Schema::table('permission_role', function (Blueprint $table) {
               $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

               $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
               $table->primary(['permission_id', 'role_id']);
        });

        Schema::table('role_user', function (Blueprint $table) {
               $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

               $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
               $table->primary(['user_id', 'role_id']);
        });

        Schema::table('holiday_location', function (Blueprint $table){
               $table->foreign('holiday_id')
                    ->references('id')
                    ->on('holidays')
                    ->onDelete('cascade');
        });

        Schema::table('work_order_template_details', function (Blueprint $table){
               $table->foreign('template_id')
                    ->references('id')
                    ->on('work_order_templates')
                    ->onDelete('cascade');
        });
        DB::statement("
               CREATE view [dbo].[ticket_work_order] as (
                    Select 
                         a.id as 'ID'
                               , a.ticketable_type as 'Type'
                         , a.status as 'Status'
                         , a.subject as 'Subject'
                         , c.first_name + ' ' + c.last_name as 'Assigned To'
                               , d.first_name + ' ' + d.last_name as 'Created By'
                         , a.work_requested as 'Work Requested'
                         , a.work_completed as 'Work Completed'
                         , a.due_date as 'Due Date'
                         , a.completed_date as 'Date Completed'
                    From work_orders a
                    left join users c on a.assigned_to = c.id
                    left join users d on a.created_by = d.id
                    )
          ");
         DB::statement("
               CREATE view [dbo].[ticket_view] as (
               Select a.id as 'ID'
                    ,  a.description as 'Description'
                    , a.title as 'Subject'
                    , a.status as 'Status'
                    , a.resolution as 'Resolution'
                    , b.name as 'Category'
                    , c.name as 'Subcategory'
                    , d.first_name + ' ' + d.last_name as 'Created By'
                    , Case when e.first_name is null then 'Not Assigned' else e.first_name + ' ' + e.last_name end as 'Assigned To'
                    , f.name as 'Urgency'
                    , g.city as 'Assigned To Location'
                         , h.city as 'Created By Location'
                    , a.due_date as 'Due Date'
                    , a.created_at as 'Date Created'
                         , a.close_date as 'Date Closed'
               from tickets a
               left join categories b on a.category_id = b.id
               left join subcategories c on a.sub_category_id = c.id
               left join users d on a.created_by = d.id
               left join users e on a.agent_id = e.id
               left join urgency f on a.urgency_id = f.id
               left join locations g on e.location_id = g.id
                  left join locations h on d.location_id = h.id
               )
          ");
         DB::statement("
               CREATE view [dbo].[change_ticket_view] as (
               Select a.id
                         , a.change_type
                         , a.status
                         , a.start_date
                         , a.end_date
                         , a.change_description
                         , a.cancelled_reason
                         , a.completed_type
                         , a.is_audited
                         , b.name
                         , c.first_name + ' ' + c.last_name as 'Change Owner'
                         , d.first_name + ' ' + d.last_name as 'Created By'
                         , e.first_name + ' ' + e.last_name as 'IT Approver'
                         , f.first_name + ' ' + f.last_name as 'Bus. Approver'
                    from change_tickets a
                    left join audit_units b on a.audit_unit = b.id
                    left join users c on a.change_owner_id = c.id
                    left join users d on a.created_by = d.id
                    left join users e on a.it_approver_id = e.id
                    left join users f on a.bus_approver_id = f.id

               )
          ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          
        Schema::drop('role_user');
        Schema::drop('users');
        Schema::drop('permission_role');
        Schema::drop('work_orders');
        Schema::drop('work_order_template_details');
        Schema::drop('work_order_templates');
        Schema::drop('tickets');
        DB::statement("Drop view ticket_view");
        DB::statement("Drop view ticket_work_order");
        DB::statement("Drop view change_ticket_view");
        Schema::drop('teams');
        Schema::drop('team_user');
        Schema::drop('subcategory_team');
        Schema::drop('change_tickets');
        Schema::drop('change_approvals');
        Schema::drop('user_views');
        Schema::drop('filter_criteria');
        Schema::drop('audit_units');
        Schema::drop('urgency');
        Schema::drop('holiday_location');
        Schema::drop('announcements');
        Schema::drop('locations');
        Schema::drop('holidays');
        Schema::drop('attachments');
        Schema::drop('categories');
        Schema::drop('subcategories');
        Schema::drop('permissions');
        Schema::drop('roles');
        Schema::drop('placeholders');
        Schema::drop('conversations');
        Schema::drop('conversations_private');
        Schema::drop('admin_settings');

    }
}
