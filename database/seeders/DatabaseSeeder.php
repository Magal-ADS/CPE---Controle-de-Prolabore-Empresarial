<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@weagles.com',
        ], [
            'name' => 'Weagles',
            'password' => Hash::make('123'),
            'is_active' => true,
        ]);
    }
}
