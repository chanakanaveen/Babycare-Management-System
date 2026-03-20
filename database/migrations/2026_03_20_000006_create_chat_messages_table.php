<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_room_id');
            $table->enum('sender_type', ['parent', 'midwife']);
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->string('attachment_path', 500)->nullable();
            $table->enum('attachment_type', ['image', 'document'])->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('chat_room_id')->references('id')->on('chat_rooms')->onDelete('cascade');
            $table->index(['chat_room_id', 'created_at'], 'idx_room_created');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
