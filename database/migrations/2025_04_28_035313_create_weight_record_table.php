<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weight_record', function (Blueprint $table) {
            $table->id('record_id'); // Primary Key
            $table->unsignedBigInteger('baby_id'); // Foreign Key
            $table->decimal('weight', 10, 2)->nullable(); // Weight with 2 decimal places
            $table->decimal('height', 10, 2)->nullable(); // Height with 2 decimal places
            $table->decimal('head_circumference', 10, 2)->nullable(); // Head circumference with 2 decimal places
            $table->unsignedBigInteger('midwife_id')->nullable(); // Foreign Key
            $table->date('record_date')->nullable(); // Date of the record
            $table->text('notes')->nullable(); // Notes field
            $table->timestamps(); // Includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weight_record');
    }
}
