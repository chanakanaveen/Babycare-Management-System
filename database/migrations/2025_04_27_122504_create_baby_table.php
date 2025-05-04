<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBabyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baby', function (Blueprint $table) {
            $table->id('baby_id'); // Primary Key
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->decimal('birth_weight', 5, 2); // Example: 99.99 kg
            $table->string('birth_hospital')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('birth_complications')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->text('initial_observations')->nullable();
            $table->unsignedBigInteger('midwife_id'); // Foreign Key
            $table->unsignedBigInteger('parent_id'); // Foreign Key
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
        Schema::dropIfExists('baby');
    }
}
