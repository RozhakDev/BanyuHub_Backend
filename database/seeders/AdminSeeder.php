<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Putri Ayaka Kirana',
            'email' => '0xPutri@banyuhub.space',
            'password' => Hash::make('Putri123#'),
            'role' => 'admin',
        ]);
    }
}
