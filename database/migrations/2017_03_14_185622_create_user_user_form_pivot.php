<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUserFormPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_user_form', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('form_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_user_form', function (Blueprint $table) {
               $table->foreign('form_id')
                    ->references('id')
                    ->on('user_forms')
                    ->onDelete('cascade');

               $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
          Schema::table('user_user_form', function (Blueprint $table) {
               $table->dropForeign('user_user_form_form_id_foreign');
               $table->dropForeign('user_user_form_user_id_foreign');
          });

        Schema::drop('user_user_form');
    }
}
