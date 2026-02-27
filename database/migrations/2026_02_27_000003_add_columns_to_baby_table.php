<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBabyTable extends Migration
{
    /**
     * Add missing columns to baby table for extended baby profiles.
     */
    public function up()
    {
        Schema::table('baby', function (Blueprint $table) {
            if (!Schema::hasColumn('baby', 'allergies')) {
                $table->text('allergies')->nullable()->after('initial_observations');
            }
            if (!Schema::hasColumn('baby', 'special_conditions')) {
                $table->text('special_conditions')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('baby', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('baby', function (Blueprint $table) {
            $table->dropColumn(['allergies', 'special_conditions']);
            $table->dropTimestamps();
        });
    }
}
