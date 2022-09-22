<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Master customer
        Customer::create([
            'customer_name' => "Quang Huy",
            'email' => "huy_cust01@mail.com",
            'password' => Hash::make('password'), // password
            'address' => '6th Floor EBM Building, 685 Điện Biên Phủ, Phường 25, Bình Thạnh, Thành phố Hồ Chí Minh',
            'tel_num' => '012457878',
            'is_active' => 1,
        ]);
        // Fake 50 rows of data
        Customer::factory()->count(50)->create();
    }
}
