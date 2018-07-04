<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrgKeySendMailUsersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('send_mail_users', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')
                    ->on('clients')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')
                    ->on('users')
                    ->onUpdate('RESTRICT')
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
        Schema::table('send_mail_users', function (Blueprint $table) {
            //
        });
    }
}
