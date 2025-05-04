<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->id('notice_id'); // Primary Key
            $table->string('title');
            $table->text('content');
            $table->string('sender_type');
            $table->unsignedBigInteger('sender_id');
            $table->enum('notice_type', ['general', 'urgent', 'reminder'])->nullable();
            $table->enum('target_group', ['parents', 'midwives', 'all'])->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice');
    }
}
