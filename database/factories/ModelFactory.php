<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/


$factory->define(App\AdminSettings::class, function (Faker\Generator $faker) {
    return [
        'mail_port' =>  $faker->word ,
        'mail_server' =>  $faker->word ,
        'mail_user' =>  $faker->word ,
        'mail_password' =>  $faker->word ,
        'email_address' =>  $faker->word ,
        'mail_folder' =>  $faker->word ,
        'mail_processed_folder' =>  $faker->word ,
        'phone_number' =>  $faker->word ,
    ];
});

$factory->define(App\Announcement::class, function (Faker\Generator $faker) {
    return [
        'type' =>  $faker->word ,
        'title' =>  $faker->word ,
        'details' =>  $faker->word ,
        'start_date' =>  $faker->dateTimeBetween() ,
        'end_date' =>  $faker->dateTimeBetween() ,
    ];
});

$factory->define(App\AssignedToSearch::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\Attachment::class, function (Faker\Generator $faker) {
    return [
        'ticketable_id' =>  $faker->randomNumber() ,
        'ticketable_type' =>  $faker->word ,
        'file_name' =>  $faker->word ,
        'file' =>  $faker->word ,
        'file_size' =>  $faker->randomFloat() ,
    ];
});

$factory->define(App\AuditUnit::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'status' =>  $faker->randomNumber() ,
    ];
});

$factory->define(App\BaseModel::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'active' =>  $faker->randomNumber() ,
    ];
});

$factory->define(App\ChangeApproval::class, function (Faker\Generator $faker) {
    return [
        'change_ticket_id' =>  function () {
             return factory(App\ChangeTicket::class)->create()->id;
        } ,
        'approval_type' =>  $faker->word ,
        'approved' =>  $faker->randomNumber() ,
        'date_approved' =>  $faker->dateTimeBetween() ,
        'approver' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
    ];
});

$factory->define(App\ChangeTicket::class, function (Faker\Generator $faker) {
    return [
        'audit_unit' =>  function () {
             return factory(App\AuditUnit::class)->create()->id;
        } ,
        'change_owner_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'created_by' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'it_approver_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'bus_approver_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'change_type' =>  $faker->word ,
        'status' =>  $faker->word ,
        'start_date' =>  $faker->dateTimeBetween() ,
        'end_date' =>  $faker->dateTimeBetween() ,
        'close_date' =>  $faker->dateTimeBetween() ,
        'change_description' =>  $faker->word ,
        'roll_out_plan' =>  $faker->word ,
        'change_reason' =>  $faker->word ,
        'back_out_plan' =>  $faker->word ,
        'servers' =>  $faker->word ,
        'test_plan' =>  $faker->word ,
        'business_impact' =>  $faker->word ,
        'affected_groups' =>  $faker->word ,
        'cancelled_reason' =>  $faker->word ,
        'completed_type' =>  $faker->word ,
        'completed_notes' =>  $faker->word ,
        'is_audited' =>  $faker->word ,
    ];
});

$factory->define(App\ChangeTicketTemplate::class, function (Faker\Generator $faker) {
    return [
        'owner_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'name' =>  $faker->name ,
        'audit_unit' =>  $faker->randomNumber() ,
        'change_owner_id' =>  $faker->randomNumber() ,
        'created_by' =>  $faker->randomNumber() ,
        'it_approver_id' =>  $faker->randomNumber() ,
        'bus_approver_id' =>  $faker->randomNumber() ,
        'change_type' =>  $faker->word ,
        'status' =>  $faker->word ,
        'start_date' =>  $faker->dateTimeBetween() ,
        'end_date' =>  $faker->dateTimeBetween() ,
        'close_date' =>  $faker->dateTimeBetween() ,
        'change_description' =>  $faker->word ,
        'roll_out_plan' =>  $faker->word ,
        'change_reason' =>  $faker->word ,
        'back_out_plan' =>  $faker->word ,
        'servers' =>  $faker->word ,
        'test_plan' =>  $faker->word ,
        'business_impact' =>  $faker->word ,
        'affected_groups' =>  $faker->word ,
    ];
});

$factory->define(App\ChangeTicketView::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\Conversation::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' =>  function () {
             return factory(App\Ticket::class)->create()->id;
        } ,
        'created_by' =>  $faker->word ,
        'source' =>  $faker->word ,
        'message' =>  $faker->word ,
    ];
});

$factory->define(App\ConversationPrivate::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' =>  function () {
             return factory(App\Ticket::class)->create()->id;
        } ,
        'created_by' =>  $faker->word ,
        'source' =>  $faker->word ,
        'message' =>  $faker->word ,
    ];
});

$factory->define(App\FilterCriteria::class, function (Faker\Generator $faker) {
    return [
        'view_id' =>  function () {
             return factory(App\UserView::class)->create()->id;
        } ,
        'column' =>  $faker->word ,
        'operator' =>  $faker->word ,
        'criteria1' =>  $faker->word ,
        'criteria2' =>  $faker->word ,
        'operator2' =>  $faker->word ,
    ];
});

$factory->define(App\Holiday::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'date' =>  $faker->dateTimeBetween() ,
    ];
});

$factory->define(App\Location::class, function (Faker\Generator $faker) {
    return [
        'city' =>  $faker->city ,
        'timezone' =>  'US/Mountain' ,
    ];
});

$factory->define(App\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'label' =>  $faker->word ,
    ];
});

$factory->define(App\Placeholder::class, function (Faker\Generator $faker) {
    return [
        'message' =>  $faker->word ,
    ];
});

$factory->define(App\Revision::class, function (Faker\Generator $faker) {
    return [
        'revisionable_type' =>  $faker->word ,
        'revisionable_id' =>  $faker->randomNumber() ,
        'user_id' =>  $faker->randomNumber() ,
        'key' =>  $faker->word ,
        'old_value' =>  $faker->word ,
        'new_value' =>  $faker->word ,
    ];
});

$factory->define(App\Role::class, function (Faker\Generator $faker) {
    return [
        'label' =>  $faker->word ,
    ];
});

$factory->define(App\Subcategory::class, function (Faker\Generator $faker) {
    return [
        'category_id' =>  function () {
             return factory(App\Category::class)->create()->id;
        } ,
        'name' =>  $faker->name ,
        'description' =>  $faker->word ,
        'tags' =>  $faker->word ,
        'location_matters' =>  $faker->boolean ,
        'created_by' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'active' =>  $faker->randomNumber() ,
    ];
});

$factory->define(App\Team::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'self_enroll' =>  $faker->randomNumber() ,
    ];
});

$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    return [
        'created_by' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'agent_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'category_id' =>  function () {
             return factory(App\Category::class)->create()->id;
        } ,
        'sub_category_id' =>  function () {
             return factory(App\Subcategory::class)->create()->id;
        } ,
        'title' =>  $faker->word ,
        'description' =>  $faker->word ,
        'status' =>  'open' ,
        'urgency_id' =>  function () {
             return factory(App\Urgency::class)->create()->id;
        } ,
        'resolution' =>  null,
        'due_date' =>  $faker->dateTimeBetween() ,
        'close_date' =>  $faker->dateTimeBetween() ,
    ];
});

$factory->define(App\TicketView::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\TicketWorkOrderView::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\Urgency::class, function (Faker\Generator $faker) {
    return [
        'name' =>  $faker->name ,
        'description' =>  $faker->word ,
        'hours' =>  $faker->randomNumber() ,
    ];
});

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'ad_id' =>  'eaed1\\' . $faker->word ,
        'first_name' =>  $faker->firstName ,
        'last_name' =>  $faker->lastName ,
        'email' =>  $faker->safeEmail ,
        'sip' =>  $faker->word ,
        'phone_number' =>  $faker->word ,
        'location_id' =>  function () {
             return factory(App\Location::class)->create()->id;
        } ,
        'timezone' =>  'US/Mountain' ,
        'active' =>  1 ,
        'out_of_office' =>  0 ,
        'remember_token' =>  str_random(10) ,
    ];
});

$factory->define(App\UserView::class, function (Faker\Generator $faker) {
    return [
        'user_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'name' =>  $faker->name ,
        'query_type' =>  $faker->word ,
        'select_columns' =>  $faker->word ,
    ];
});

$factory->define(App\WorkOrder::class, function (Faker\Generator $faker) {
    return [
        'assigned_to' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'created_by' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'ticketable_id' =>  $faker->randomNumber() ,
        'ticketable_type' =>  $faker->word ,
        'status' =>  $faker->word ,
        'subject' =>  $faker->word ,
        'work_requested' =>  $faker->word ,
        'work_completed' =>  $faker->word ,
        'completed_date' =>  $faker->dateTimeBetween() ,
        'due_date' =>  $faker->dateTimeBetween() ,
    ];
});

$factory->define(App\WorkOrderTemplate::class, function (Faker\Generator $faker) {
    return [
        'owner_id' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'name' =>  $faker->name ,
    ];
});

$factory->define(App\WOTemplateDetail::class, function (Faker\Generator $faker) {
    return [
        'template_id' =>  function () {
             return factory(App\WorkOrderTemplate::class)->create()->id;
        } ,
        'assigned_to' =>  function () {
             return factory(App\User::class)->create()->id;
        } ,
        'subject' =>  $faker->word ,
        'work_requested' =>  $faker->word ,
        'due_in' =>  $faker->randomNumber() ,
    ];
});

