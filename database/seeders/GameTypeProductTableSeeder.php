<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTypeProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'product_id' => 1,  // PPLAY
                'game_type_id' => 2,  // Slots
                'image' => 'pragmatic_play.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 2,  // PPLAYLIVE
                'game_type_id' => 6,  // LiveCasino
                'image' => 'pragmatic_casino.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 3,  // PGSOFT
                'game_type_id' => 2,  // Slots
                'image' => 'pg_soft.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 4,  // JILI
                'game_type_id' => 2,  // Slots
                'image' => 'jili.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 5,  // L22
                'game_type_id' => 2,  // Slots
                'image' => 'live22.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 6,  // JDB
                'game_type_id' => 2,  // Other
                'image' => 'j_d_b.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 7,  // CQ9
                'game_type_id' => 2,  // Arcade
                'image' => 'cq_9.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 7,  // CQ9
                'game_type_id' => 3,  // fish
                'image' => 'cq_9_fish.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 8,  // UUS
                'game_type_id' => 2,  // Slots
                'image' => 'uu_slot.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 9,  // MGH5
                'game_type_id' => 2,  // Other
                'image' => 'mega_h5.jfif',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 10,  // MGH5
                'game_type_id' => 2,  // Other
                'image' => 'epic_win.jpg',
                'rate' => '1.0000',
            ],
             [
                'product_id' => 11,  // MGH5
                'game_type_id' => 2,  // Other
                'image' => 'yellow_bat.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 12,  // EVOPLAY
                'game_type_id' => 2,  // Other
                'image' => 'evoplay.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 13,  // FACHAI
                'game_type_id' => 2,  // Arcade
                'image' => 'fachai.jfif',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 14,  // FACHAI
                'game_type_id' => 2,  // Arcade
                'image' => 'bng.jfif',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 15,  // FACHAI
                'game_type_id' => 2,  // Arcade
                'image' => 'ygr.jpg',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 16,  // FACHAI
                'game_type_id' => 2,  // Arcade
                'image' => 'hack_saw.jfif',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 17,  // FUNTA
                'game_type_id' => 2,  // Arcade
                'image' => 'funta.png',
                'rate' => '1.0000',
            ],
            [
                'product_id' => 18,  // FUNTA
                'game_type_id' => 2,  // Arcade
                'image' => 'simple_play.png',
                'rate' => '1.0000',
            ],
        ];

        DB::table('game_type_product')->insert($data);
    }
}