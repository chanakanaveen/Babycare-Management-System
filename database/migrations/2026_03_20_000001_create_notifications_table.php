 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('recipient_type', ['parent', 'midwife']);
            $table->unsignedBigInteger('recipient_id');
            $table->string('type', 100);
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id'], 'idx_recipient');
            $table->index('is_read', 'idx_is_read');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
