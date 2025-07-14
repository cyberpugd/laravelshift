<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('url', 200);
            $table->string('slug', 100)->unique();
            $table->integer('subcategory_id')->unsigned();
            $table->integer('urgency')->unsigned();
            $table->integer('active')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->integer('last_modified_by')->unsigned();
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
        Schema::drop('user_forms');
    }
}
