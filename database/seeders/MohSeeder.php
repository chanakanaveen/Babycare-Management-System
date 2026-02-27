<?php

namespace Database\Seeders;

use App\Models\Moh;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MohSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Moh::create([
            'name'     => 'MOH Admin',
            'username' => 'moh',
            'email'    => 'moh@email.com',
            'password' => Hash::make('password'),
        ]);
    }
}
