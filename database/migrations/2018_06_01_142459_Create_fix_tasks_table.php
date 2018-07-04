<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fix_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->date('task_date')->nullable();
            $table->text('ref_link')->nullable();
            $table->string('assigned_by')->nullable();
            $table->float('hour')->nullable();
            $table->float('fix')->nullable();
            $table->float('rate')->nullable();
            $table->integer('invoice_status')->comment('0-Unmap Invoice, 1-Map Invoice');
            $table->timestamps();

            $table->foreign('client_id')->references('id')
                    ->on('clients')
                    ->onUpdate('CASCADE')
                    ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fix_tasks');
    }
}
