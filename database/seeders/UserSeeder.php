<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = DB::table('roles')->where('name', 'super admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'nalensrin480@gmail.com',
                'role_id' => $superAdminRole->id,
                'password' => Hash::make('password123'),

                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'email' => 'nalensrin2023@gmail.com',
                'role_id' => $adminRole->id,
                'password' => Hash::make('password123'),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
