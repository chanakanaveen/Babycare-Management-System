<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationFieldsToBabyVaccinations extends Migration
{
    public function up()
    {
        Schema::table('baby_vaccinations', function (Blueprint $table) {
            $table->timestamp('notification_sent_at')->nullable()->after('reminder_sent');
            $table->boolean('parent_notified')->default(false)->after('notification_sent_at');
        });
    }

    public function down()
    {
        Schema::table('baby_vaccinations', function (Blueprint $table) {
            $table->dropColumn(['notification_sent_at', 'parent_notified']);
        });
    }
}
