<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\Ingredient;
use App\Services\BartenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BartenderController extends Controller
{
    protected $bartenderService;

    public function __construct(BartenderService $bartenderService)
    {
        $this->bartenderService = $bartenderService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $user = Auth::user();

        $ingredients = Ingredient::all();

        $drinks = Drink::with('ingredients', 'favoritedBy')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $recommendations = collect();
        $popularDrinks = $this->bartenderService->getPopularDrinks();

        if ($user) {
            $recommendations = $this->bartenderService->getRecommendationsForUser($user);
        }

        return view('bartender.index', compact(
            'ingredients',
            'drinks',
            'recommendations',
            'popularDrinks',
            'user'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1'
        ]);

        $drink = Drink::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $drink->ingredients()->attach($request->ingredients);

        return redirect('/')->with('success', '🍹 Drink added successfully!');
    }

    public function findDrinks(Request $request)
    {
        $ingredientIds = $request->ingredients ?? [];
        $user = Auth::user();

        $drinks = Drink::with('ingredients', 'favoritedBy')->get();
        
        $results = $drinks->map(function ($drink) use ($ingredientIds, $user) {
            $percentage = $this->bartenderService->calculateMatchPercentage($drink, $ingredientIds);
            $missing = $this->bartenderService->getMissingIngredients($drink, $ingredientIds);
            
            return [
                'drink' => $drink,
                'percentage' => $percentage,
                'missing' => $missing,
                'is_complete' => $percentage === 100,
                'is_favorite' => $user ? $user->isFavorite($drink->id) : false
            ];
        })->sortByDesc('percentage')->values();

        return view('bartender.results', compact('results', 'ingredientIds'));
    }

    public function destroy($id)
    {
        Drink::findOrFail($id)->delete();
        return redirect('/')->with('success', '🗑️ Drink deleted successfully!');
    }

    public function toggleFavorite($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $drink = Drink::findOrFail($id);
        $isFavorite = $user->toggleFavorite($id);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? '❤️ Added to favorites!' : '💔 Removed from favorites'
        ]);
    }

    public function myBar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $ingredients = Ingredient::all();
        $userIngredients = $user->barIngredients()->pluck('ingredient_id')->toArray();

        $recommendations = $this->bartenderService->getRecommendationsForUser($user);
        $favorites = $user->favoriteDrinks()->with('ingredients')->get();

        return view('bartender.mybar', compact(
            'ingredients',
            'userIngredients',
            'recommendations',
            'favorites',
            'user'
        ));
    }

    public function updateBar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $request->validate([
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,id'
        ]);

        $user->barIngredients()->sync($request->ingredients ?? []);

        return back()->with('success', '✅ Your bar updated successfully!');
    }
}