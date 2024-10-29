<?php

namespace Database\Seeders;

use App\Models\Admin\Product;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => '1006',
                'name' => 'Pragmatic Play',
                'short_name' => 'PP',
                'order' => 1,
                'status' => 1,
            ],

            [
                'code' => '1085',
                'name' => 'JDB',
                'short_name' => 'JDB',
                'order' => 2,
                'status' => 1,
            ],

            [
                'code' => '1007',
                'name' => 'PG Soft',
                'short_name' => 'PGSoft',
                'order' => 3,
                'status' => 1,
            ],

            [
                'code' => '1091',
                'name' => 'Jili',
                'short_name' => 'JILI',
                'order' => 4,
                'status' => 1,
            ],

            [
                'code' => '1150',
                'name' => 'Live22SM',
                'short_name' => 'Live22',
                'order' => 5,
                'status' => 1,
            ],

            [
                'code' => '1009',
                'name' => 'CQ9',
                'short_name' => 'CQ9',
                'order' => 6,
                'status' => 0,
            ],

            [
                'code' => '1013',
                'name' => 'Joker',
                'short_name' => 'Joker',
                'order' => 7,
                'status' => 0,
            ],

        ];

        //Product::insert($data);
        foreach ($data as $obj) {
            Product::create($obj);
        }

    }
}
