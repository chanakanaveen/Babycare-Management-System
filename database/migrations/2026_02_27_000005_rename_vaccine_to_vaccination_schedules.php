<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameVaccineToVaccinationSchedules extends Migration
{
    /**
     * Rename vaccine → vaccination_schedules and add new columns
     * for the MOH master vaccination schedule list.
     */
    public function up()
    {
        Schema::rename('vaccine', 'vaccination_schedules');

        Schema::table('vaccination_schedules', function (Blueprint $table) {
            $table->json('dose_schedule')->nullable()->after('doses_required');
            $table->boolean('is_mandatory')->default(true)->after('dose_schedule');
            $table->tinyInteger('status')->default(1)->after('is_mandatory'); // 1=active, 0=inactive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('vaccination_schedules', function (Blueprint $table) {
            $table->dropColumn(['dose_schedule', 'is_mandatory', 'status']);
        });

        Schema::rename('vaccination_schedules', 'vaccine');
    }
}
