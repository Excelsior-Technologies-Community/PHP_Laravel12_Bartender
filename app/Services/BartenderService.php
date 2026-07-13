<?php

namespace App\Services;

use App\Models\Drink;
use App\Models\Ingredient;
use Illuminate\Support\Collection;

class BartenderService
{
    public function findMatchingDrinks($ingredientIds)
    {
        return Drink::whereHas('ingredients', function ($query) use ($ingredientIds) {
            $query->whereIn('ingredients.id', $ingredientIds);
        })->with('ingredients')->get();
    }

    public function calculateMatchPercentage($drink, $userIngredientIds)
    {
        $drinkIngredientIds = $drink->ingredients->pluck('id')->toArray();
        
        if (empty($drinkIngredientIds)) {
            return 0;
        }

        $matched = count(array_intersect($userIngredientIds, $drinkIngredientIds));
        $total = count($drinkIngredientIds);
        
        return round(($matched / $total) * 100);
    }

    public function getMissingIngredients($drink, $userIngredientIds)
    {
        $drinkIngredientIds = $drink->ingredients->pluck('id')->toArray();
        $missingIds = array_diff($drinkIngredientIds, $userIngredientIds);
        
        return Ingredient::whereIn('id', $missingIds)->get();
    }

    public function getRecommendedDrinks($userIngredientIds, $limit = 10)
    {
        $allDrinks = Drink::with('ingredients')->get();
        
        $recommended = $allDrinks->map(function ($drink) use ($userIngredientIds) {
            $percentage = $this->calculateMatchPercentage($drink, $userIngredientIds);
            $missing = $this->getMissingIngredients($drink, $userIngredientIds);
            
            return [
                'drink' => $drink,
                'percentage' => $percentage,
                'missing' => $missing,
                'is_complete' => $percentage === 100
            ];
        })->sortByDesc('percentage')->take($limit);

        return $recommended;
    }

    public function getRecommendationsForUser($user)
    {
        $userIngredientIds = $user->barIngredients->pluck('id')->toArray();
        
        if (empty($userIngredientIds)) {
            return collect();
        }

        return $this->getRecommendedDrinks($userIngredientIds);
    }

    public function getPopularDrinks($limit = 5)
    {
        return Drink::withCount('favoritedBy')
            ->orderBy('favorited_by_count', 'desc')
            ->limit($limit)
            ->get();
    }
}