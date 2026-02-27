<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameVaccinationRecordToBabyVaccinations extends Migration
{
    /**
     * Rename vaccination_record → baby_vaccinations and add
     * tracking columns for individual baby vaccination status.
     */
    public function up()
    {
        if (Schema::hasTable('vaccination_record') && !Schema::hasTable('baby_vaccinations')) {
            Schema::rename('vaccination_record', 'baby_vaccinations');
        }

        $tableName = Schema::hasTable('baby_vaccinations') ? 'baby_vaccinations' : 'vaccination_record';

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'vaccination_status')) {
                $table->enum('vaccination_status', ['scheduled', 'administered', 'missed', 'overdue'])
                      ->default('scheduled')
                      ->after('notes');
            }
            if (!Schema::hasColumn($tableName, 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('vaccination_status');
            }
            if (!Schema::hasColumn($tableName, 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('scheduled_date');
            }
            if (!Schema::hasColumn($tableName, 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('baby_vaccinations', function (Blueprint $table) {
            $table->dropColumn(['vaccination_status', 'scheduled_date', 'reminder_sent']);
            $table->dropTimestamps();
        });

        Schema::rename('baby_vaccinations', 'vaccination_record');
    }
}
