<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('designation')->nullable();
            $table->text('contact_information')->nullable();
            $table->string('registration_number')->nullable();
            $table->unsignedBigInteger('division_id')->nullable(); // Assuming 'division_id' is a foreign key
            $table->text('operational_jurisdiction')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->enum('account_status', ['active', 'inactive', 'pending'])->default('pending');

            //  Foreign key constraint (assuming there's a 'divisions' table)
             $table->foreign('division_id')
                   ->references('id')
                   ->on('divisions') // Replace 'divisions' with the actual table name if different
                   ->onDelete('SET NULL'); //  Set to null on deletion of the related division.  Choose appropriate action (cascade, restrict, etc.)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            //
        });
    }
}
