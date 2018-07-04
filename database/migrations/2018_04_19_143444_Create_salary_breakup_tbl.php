<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryBreakupTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_breakup', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->float('basic_salary')->nullable();
            $table->float('advance')->nullable();
            $table->float('hra')->nullable();
            $table->float('leave_deduction')->nullable();
            $table->float('conveyance_allowance')->nullable();
            $table->float('other_deduction')->nullable();
            $table->float('telephone_allowance')->nullable();
            $table->float('tds')->nullable();
            $table->float('medical_allowance')->nullable();
            $table->float('uniform_allowance')->nullable();
            $table->float('special_allowance')->nullable();
            $table->float('bonus')->nullable();
            $table->float('arrear_salary')->nullable();
            $table->float('advance_given')->nullable();
            $table->float('leave_encashment')->nullable();
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
        Schema::dropIfExists('salary_breakup');
    }
}
