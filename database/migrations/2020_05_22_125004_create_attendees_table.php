<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 20);
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('paymentMethod');
            $table->integer('numTickets');
            $table->smallInteger('paid')->unsinged()->default(0);
            $table->smallInteger('cancel')->unsinged()->default(0);
            $table->string('paymentProof')->nullable();
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
        Schema::dropIfExists('attendees');
    }
}
