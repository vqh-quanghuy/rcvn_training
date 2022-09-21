<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Master User
        User::create([
            'name' => "Quang Huy Master",
            'email' => "huy_user01@mail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'group_role' => 1,
            'is_active' => 1,
            'is_delete' => 0,
        ]);
        // Fake 50 rows of data
        User::factory()->count(50)->create();
    }
}
