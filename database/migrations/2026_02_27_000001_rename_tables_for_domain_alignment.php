<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameTablesForDomainAlignment extends Migration
{
    /**
     * Run the migrations.
     * Rename legacy tables to match the new domain terminology:
     *   admins  → mohs
     *   clients → parents
     *   sellers → midwives
     */
    public function up()
    {
        Schema::rename('admins', 'mohs');
        Schema::rename('clients', 'parents');
        Schema::rename('sellers', 'midwives');

        // Update guard references in password_resets table
        DB::table('password_resets')->where('guard', 'admin')->update(['guard' => 'moh']);
        DB::table('password_resets')->where('guard', 'client')->update(['guard' => 'parent']);
        DB::table('password_resets')->where('guard', 'seller')->update(['guard' => 'midwife']);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::rename('mohs', 'admins');
        Schema::rename('parents', 'clients');
        Schema::rename('midwives', 'sellers');

        DB::table('password_resets')->where('guard', 'moh')->update(['guard' => 'admin']);
        DB::table('password_resets')->where('guard', 'parent')->update(['guard' => 'parent']);
        DB::table('password_resets')->where('guard', 'midwife')->update(['guard' => 'seller']);
    }
}
