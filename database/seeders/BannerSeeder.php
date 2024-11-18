<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            ['image' => '1.png', 'admin_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['image' => '2.png', 'admin_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['image' => '3.png', 'admin_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            // Add more banners here if needed
        ];

        DB::table('banners')->insert($banners);
    }
}