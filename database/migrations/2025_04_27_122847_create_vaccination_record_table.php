<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaccinationRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaccination_record', function (Blueprint $table) {
            $table->id('record_id'); // Primary Key
            $table->unsignedBigInteger('baby_id'); // Foreign Key
            $table->unsignedBigInteger('vaccine_id'); // Foreign Key
            $table->integer('dose_number');
            $table->date('administered_date');
            $table->unsignedBigInteger('midwife_id'); // Foreign Key
            $table->date('next_dose_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccination_record');
    }
}
