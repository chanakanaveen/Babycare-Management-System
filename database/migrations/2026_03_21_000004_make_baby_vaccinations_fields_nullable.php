<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeBabyVaccinationsFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Using DB::statement to avoid requiring doctrine/dbal for simple column modifications
        DB::statement('ALTER TABLE baby_vaccinations MODIFY administered_date DATE NULL');
        
        // Also ensure others that might be missing from inserts are nullable or have defaults
        try {
            DB::statement('ALTER TABLE baby_vaccinations MODIFY next_dose_date DATE NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE baby_vaccinations MODIFY batch_number VARCHAR(100) NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE baby_vaccinations MODIFY notes TEXT NULL');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE baby_vaccinations MODIFY notification_sent_at TIMESTAMP NULL');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE baby_vaccinations MODIFY parent_notified TINYINT(1) DEFAULT 0');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No down migration required
    }
}
