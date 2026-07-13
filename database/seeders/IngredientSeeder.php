<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run()
    {
        $ingredients = [
            ['name' => 'Rum', 'icon' => '🥃', 'category' => 'Spirits'],
            ['name' => 'Vodka', 'icon' => '🥃', 'category' => 'Spirits'],
            ['name' => 'Tequila', 'icon' => '🥃', 'category' => 'Spirits'],
            ['name' => 'Gin', 'icon' => '🥃', 'category' => 'Spirits'],
            ['name' => 'Whiskey', 'icon' => '🥃', 'category' => 'Spirits'],
            ['name' => 'Lime Juice', 'icon' => '🍋', 'category' => 'Mixers'],
            ['name' => 'Lemon Juice', 'icon' => '🍋', 'category' => 'Mixers'],
            ['name' => 'Orange Juice', 'icon' => '🍊', 'category' => 'Mixers'],
            ['name' => 'Cranberry Juice', 'icon' => '🍒', 'category' => 'Mixers'],
            ['name' => 'Pineapple Juice', 'icon' => '🍍', 'category' => 'Mixers'],
            ['name' => 'Simple Syrup', 'icon' => '🍯', 'category' => 'Sweeteners'],
            ['name' => 'Grenadine', 'icon' => '🩸', 'category' => 'Sweeteners'],
            ['name' => 'Soda Water', 'icon' => '💧', 'category' => 'Carbonated'],
            ['name' => 'Tonic Water', 'icon' => '💧', 'category' => 'Carbonated'],
            ['name' => 'Ginger Ale', 'icon' => '🫚', 'category' => 'Carbonated'],
            ['name' => 'Cola', 'icon' => '🥤', 'category' => 'Carbonated'],
            ['name' => 'Mint Leaves', 'icon' => '🌿', 'category' => 'Garnishes'],
            ['name' => 'Lime Wedge', 'icon' => '🍋', 'category' => 'Garnishes'],
            ['name' => 'Cherry', 'icon' => '🍒', 'category' => 'Garnishes'],
            ['name' => 'Olive', 'icon' => '🫒', 'category' => 'Garnishes'],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}