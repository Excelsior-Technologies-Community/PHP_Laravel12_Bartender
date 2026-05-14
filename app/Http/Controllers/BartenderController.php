<?php

namespace App\Http\Controllers;

use App\Models\Drink;
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

    public function index(Request $request)
    {
        $search = $request->search;

        $ingredients = Ingredient::all();

        $drinks = Drink::with('ingredients')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                ->orwhere('description', 'like', "%{$search}%");
            })
            ->oldest()
            ->paginate(4);

        return view('bartender.index', compact('ingredients', 'drinks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'ingredients' => 'required|array'
        ]);

        $drink = Drink::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $drink->ingredients()->attach($request->ingredients);

        return redirect('/')->with('success', 'Drink Added Successfully');
    }

    public function findDrinks(Request $request)
    {
        $ingredientIds = $request->ingredients ?? [];

        $drinks = $this->bartenderService
            ->findMatchingDrinks($ingredientIds);

        return view('bartender.results', compact('drinks'));
    }

    public function destroy($id)
    {
        Drink::findOrFail($id)->delete();

        return redirect('/')
            ->with('success', 'Drink Deleted Successfully');
    }
}