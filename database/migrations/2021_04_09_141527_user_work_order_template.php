<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserWorkOrderTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_work_order_template', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('work_order_template_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_work_order_template', function (Blueprint $table) {
            $table->foreign('user_id')
                 ->references('id')
                 ->on('users');

            $table->foreign('work_order_template_id')
                 ->references('id')
                 ->on('work_order_templates');
     });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_work_order_template', function (Blueprint $table) {
            $table->dropForeign('user_work_order_template_work_order_template_id_foreign');
            $table->dropForeign('user_work_order_template_user_id_foreign');
       });

     Schema::drop('user_work_order_template');
    }
}
