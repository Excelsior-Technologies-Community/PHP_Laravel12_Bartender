<?php

namespace App\Services;

use App\Models\Drink;

class BartenderService
{
    public function findMatchingDrinks($ingredientIds)
    {
        return Drink::whereDoesntHave('ingredients', function ($query) use ($ingredientIds) {
            $query->whereNotIn('ingredients.id', $ingredientIds);
        })->with('ingredients')->get();
    }
}
