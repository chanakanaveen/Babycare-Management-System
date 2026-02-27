<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsApprovedToMidwivesTable extends Migration
{
    /**
     * Add is_approved boolean flag for midwife approval workflow.
     */
    public function up()
    {
        Schema::table('midwives', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('status');
        });

        // Sync existing data: status=1 means already verified/approved
        \Illuminate\Support\Facades\DB::table('midwives')
            ->where('status', 1)
            ->update(['is_approved' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('midwives', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
}
