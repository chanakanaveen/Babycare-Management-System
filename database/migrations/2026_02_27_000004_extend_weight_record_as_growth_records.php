<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendWeightRecordAsGrowthRecords extends Migration
{
    /**
     * Extend weight_record table with growth tracking and AI prediction fields.
     */
    public function up()
    {
        Schema::table('weight_record', function (Blueprint $table) {
            $table->integer('age_months')->nullable()->after('head_circumference');
            $table->text('milestones')->nullable()->after('age_months');
            $table->json('ai_prediction')->nullable()->after('notes');
            $table->index(['baby_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('weight_record', function (Blueprint $table) {
            $table->dropIndex(['baby_id', 'record_date']);
            $table->dropColumn(['age_months', 'milestones', 'ai_prediction']);
        });
    }
}
