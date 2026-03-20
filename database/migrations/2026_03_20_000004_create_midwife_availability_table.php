<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMidwifeAvailabilityTable extends Migration
{
    public function up()
    {
        Schema::create('midwife_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('midwife_id');
            $table->tinyInteger('day_of_week'); // 0=Sunday ... 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('midwife_id')->references('id')->on('midwives')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('midwife_availability');
    }
}
