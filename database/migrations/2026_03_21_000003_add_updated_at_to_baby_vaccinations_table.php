<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtToBabyVaccinationsTable extends Migration
{
    /**
     * Add the missing updated_at column to the baby_vaccinations table.
     */
    public function up()
    {
        Schema::table('baby_vaccinations', function (Blueprint $table) {
            if (!Schema::hasColumn('baby_vaccinations', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('baby_vaccinations', function (Blueprint $table) {
            if (Schema::hasColumn('baby_vaccinations', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
}
