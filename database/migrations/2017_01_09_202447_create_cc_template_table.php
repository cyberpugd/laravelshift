<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_ticket_templates', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('owner_id')->unsigned();
          $table->string('name');
          $table->integer('audit_unit')->unsigned()->nullable();
          $table->integer('change_owner_id')->unsigned()->nullable();
          $table->integer('created_by')->unsigned()->nullable();
          $table->integer('it_approver_id')->unsigned()->nullable();
          $table->integer('bus_approver_id')->unsigned()->nullable();
          $table->string('change_type')->nullable();
          $table->string('status', 15)->nullable();
          $table->datetime('start_date')->nullable();
          $table->datetime('end_date')->nullable();
          $table->datetime('close_date')->nullable();
          $table->longtext('change_description')->nullable();
          $table->longtext('roll_out_plan')->nullable();
          $table->longtext('change_reason')->nullable();
          $table->longtext('back_out_plan')->nullable();
          $table->longtext('servers')->nullable();
          $table->longtext('test_plan')->nullable();
          $table->longtext('business_impact')->nullable();
          $table->longtext('affected_groups')->nullable();
          $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('change_ticket_templates');
    }
}
