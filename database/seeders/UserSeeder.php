<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $User = \App\Models\User::create([
            'name' => 'Atik',
            'email' => 'atikdamayanti@atik.com',
            'password' => bcrypt('Atik0205?'),
        ]);
    }
}
