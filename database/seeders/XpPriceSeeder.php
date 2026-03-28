<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\XpPrice;

class XpPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        XpPrice::create([
            'xp_amount' => 100,
            'price' => 10.50,
        ]);
        XpPrice::create([
            'xp_amount' => 200,
            'price' => 20.99,
        ]);
        XpPrice::create([
            'xp_amount' => 300,
            'price' => 30.75,
        ]);
    }
}
