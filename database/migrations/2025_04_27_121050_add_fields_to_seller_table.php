<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSellerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sellers', function (Blueprint $table) {
            if (!Schema::hasColumn('sellers', 'professional_credentials')) {
                $table->text('professional_credentials')->nullable();
            }
            if (!Schema::hasColumn('sellers', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable();
            }
            if (!Schema::hasColumn('sellers', 'account_status')) {
                $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sellers', function (Blueprint $table) {
            //
        });
    }
}
