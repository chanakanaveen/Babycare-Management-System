<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBmiToBabyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baby', function (Blueprint $table) {
            $table->decimal('bmi', 5, 2)->nullable()->after('birth_weight'); // Add BMI column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baby', function (Blueprint $table) {
            //
        });
    }
}
