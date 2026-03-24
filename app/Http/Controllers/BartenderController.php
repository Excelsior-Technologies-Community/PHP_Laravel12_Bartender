<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Services\BartenderService;
use Illuminate\Http\Request;

class BartenderController extends Controller
{
    protected $bartenderService;

    public function __construct(BartenderService $bartenderService)
    {
        $this->bartenderService = $bartenderService;
    }

    public function index()
    {
        $ingredients = Ingredient::all();
        return view('bartender.index', compact('ingredients'));
    }

    public function findDrinks(Request $request)
    {
        $ingredientIds = $request->ingredients ?? [];

        $drinks = $this->bartenderService->findMatchingDrinks($ingredientIds);

        return view('bartender.results', compact('drinks'));
    }
}