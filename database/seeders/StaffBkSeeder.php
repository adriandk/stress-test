<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffBkSeeder extends Seeder
{
    public function run(): void
    {
        $accountId = DB::table('accounts')->insertGetId([
            'email'         => 'staffbk@example.com',
            'password_hash' => Hash::make('password123'),
            'account_type'  => 'staff_bk',
            'is_active'     => 1,
            'created_by'    => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('staffs')->insert([
            'account_id' => $accountId,
            'name'       => 'Staff BK Utama',
            'phone'      => '081234567890',
            'bio'        => 'Staff BK pertama sistem.',
            'avatar_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}