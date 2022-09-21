<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_role')->insert([
            ['role_name' => "Admin", "created_at" =>  \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now(),],
            ['role_name' => "Editor", "created_at" =>  \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now(),],
            ['role_name' => "Reviewer", "created_at" =>  \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now(),],
            ['role_name' => "Assistant", "created_at" =>  \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now(),],
            ['role_name' => "Collaborator", "created_at" =>  \Carbon\Carbon::now(), "updated_at" => \Carbon\Carbon::now(),],
        ]);
    }
}
