<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignTasksTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('priority')->default(0)->comment('0-High, 1-Low, 2-Medium');
            $table->date('due_date');
            $table->integer('status')->default(0)->comment('0-Pending, 1-Done');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT');
            $table->foreign('project_id')->references('id')->on('projects')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_tasks');
    }
}
