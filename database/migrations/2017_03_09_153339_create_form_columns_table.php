<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            $table->string('name', 50);
            $table->string('label', 50);
            $table->string('type');
            $table->integer('is_required')->unsigned();
            $table->integer('ticket_subject')->unsigned();
            $table->integer('ticket_description')->unsigned();
            $table->text('default_value')->nullable();
            $table->timestamps();
        });

        Schema::table('form_columns', function (Blueprint $table) {
               $table->foreign('form_id')
                    ->references('id')
                    ->on('user_forms')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_columns', function (Blueprint $table) {
               $table->dropForeign('form_columns_form_id_foreign');
        });
        Schema::drop('form_columns');
    }
}
