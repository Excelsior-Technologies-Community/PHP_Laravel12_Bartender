<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Drink;

class DrinkSeeder extends Seeder
{
    public function run(): void
    {
        $drink = Drink::create([
            'name' => 'Mojito',
            'description' => 'Refreshing mint drink'
        ]);

        $drink->ingredients()->attach([2, 3, 4]); // Mint, Lime, Sugar
    }
}