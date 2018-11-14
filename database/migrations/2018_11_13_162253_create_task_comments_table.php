<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->integer('assing_task_id')->unsigned(); 
            $table->integer('comment_by_user_id')->unsigned(); 
            $table->text('comments')->nullable();
            $table->integer('task_priority')->default(0)->comment('0-High, 1-Low, 2-Medium');
            $table->date('task_due_date');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT');
            $table->foreign('assing_task_id')->references('id')->on('assign_tasks')->onUpdate('RESTRICT');
            $table->foreign('comment_by_user_id')->references('id')->on('users')->onUpdate('RESTRICT');
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
        Schema::dropIfExists('task_comments');
    }
}
