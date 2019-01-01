<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertApprisialFormTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->integer('english_communication')->nullable();
            $table->integer('requirement_understanding')->nullable();
            $table->integer('timely_work')->nullable();
            $table->integer('office_on_time')->nullable();
            $table->integer('generate_work')->nullable();
            $table->integer('git_knowledge')->nullable();
            $table->integer('proactive_on_work')->nullable();
            $table->integer('job_profile')->nullable();
            $table->integer('attitude')->nullable();
            $table->integer('work_quality')->nullable();
            $table->integer('Work_independently')->nullable();
            $table->string('form_year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            //
        });
    }
}
