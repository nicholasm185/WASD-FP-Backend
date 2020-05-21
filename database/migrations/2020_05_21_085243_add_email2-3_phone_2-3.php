<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmail23Phone23 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('email','email1');
            $table->string('email2')->nullable();
            $table->string('email3')->nullable();
            $table->renameColumn('phone','phone1');
            $table->string('phone2')->nullable();
            $table->string('phone3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('email1','email');
            $table->renameColumn('phone1','phone');
            $table->dropColumn(['email2','email3','phone2','phone3']);
        });
    }
}
