# PHP_Laravel12_Bartender
 
## Introduction

PHP_Laravel12_Bartender is a web-based application built using Laravel 12 that simulates a bartender-style drink recommendation system.

The application allows users to select available ingredients and dynamically suggests matching drinks based on predefined recipes stored in the database.

It demonstrates the implementation of modern Laravel features such as MVC architecture, Eloquent relationships, service layer design, database seeding, and Blade-based UI rendering.

---

## Project Overview

The system works by allowing users to choose ingredients from a list and then processes the selection to find matching drinks.

###  Workflow:

1. User selects ingredients from the UI
2. Form submits selected ingredient IDs
3. Controller receives request
4. Service layer processes logic
5. Database is queried using Eloquent relationships
6. Matching drinks are returned
7. Results displayed using Blade templates

###  Key Features:

- Ingredient selection system
- Many-to-Many relationship (Drinks ↔ Ingredients)
- Service layer for business logic
- Strict matching algorithm for accurate results
- Seeder-based initial data setup
- Clean and modern UI

---

## Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Bartender "12.*"
cd PHP_Laravel12_Bartender
```

---

## Step 2: Setup Database

Update .env:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bartender_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 3: Create Models & Migrations

```bash
php artisan make:model Drink -m
php artisan make:model Ingredient -m
```
This Creates:

```
app/Models/Drink.php
app/Models/Ingredient.php

xxxx_xx_xx_create_drinks_table.php
xxxx_xx_xx_create_ingredients_table.php
```
---

## Step 4: Migrations

### Drinks Table

File: `database/migrations/xxxx_xx_xx_create_drinks_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drinks');
    }
};
```

### Ingredients Table

File: `database/migrations/xxxx_xx_xx_create_ingredients_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
```

### Pivot Table

Run:

```bash
php artisan make:migration create_drink_ingredient_table
```

File: `database/migrations/xxxx_xx_xx_create_drink_ingredient_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drink_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drink_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drink_ingredient');
    }
};
```
Run Migration Command:

```bash
php artisan migrate
```

---

## Step 5: Models Relationships

### Drink.php

File: `app/Models/Drink.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    protected $fillable = ['name', 'description'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }
}
```

### Ingredient.php

File: `app/Models/Ingredient.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name'];

    public function drinks()
    {
        return $this->belongsToMany(Drink::class);
    }
}
```
---

## Step 6: Seeder

This is required to insert initial data

```bash
php artisan make:seeder IngredientSeeder
php artisan make:seeder DrinkSeeder
```

### IngredientSeeder.php

File: `database/seeders/IngredientSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        Ingredient::create(['name' => 'Vodka']);
        Ingredient::create(['name' => 'Mint']);
        Ingredient::create(['name' => 'Lime']);
        Ingredient::create(['name' => 'Sugar']);
    }
}
```

### DrinkSeeder.php

File: `database/seeders/DrinkSeeder.php`

```php
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
```

Run Seeder:

```bash
php artisan db:seed --class=IngredientSeeder
php artisan db:seed --class=DrinkSeeder
```
---

## Step 7: Service Layer

Create folder:

```
app/Services/
```

Create this file:

File: `app/Services/BartenderService.php`

```php
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
```
---

## Step 8: Controller 

```bash
php artisan make:controller BartenderController
```

File: `app/Http/Controllers/BartenderController.php`

```php
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
```
---

## Step 9: Routes

File: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BartenderController;

Route::get('/', [BartenderController::class, 'index']);
Route::post('/find-drinks', [BartenderController::class, 'findDrinks']);
```

---

## Step 10: Blade UI

### index.blade.php

File: `resources/views/bartender/index.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bartender</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #111827;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .ingredient {
            background: #1f2937;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: 0.2s;
        }

        .ingredient:hover {
            background: #374151;
        }

        input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(90deg, #6366f1, #22c55e);
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
    </style>
</head>

<body>

<div class="container">
    <h2>🍹 Select Ingredients</h2>

    <form method="POST" action="/find-drinks">
        @csrf

        @foreach($ingredients as $ingredient)
            <label class="ingredient">
                <input type="checkbox" name="ingredients[]" value="{{ $ingredient->id }}">
                {{ $ingredient->name }}
            </label>
        @endforeach

        <button type="submit">Find Drinks</button>
    </form>
</div>

</body>
</html>
```

### results.blade.php

File: `resources/views/bartender/results.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a, #020617);
            color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            background: #111827;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0;
            color: #22c55e;
        }

        .card p {
            color: #9ca3af;
        }

        ul {
            padding-left: 20px;
        }

        li {
            color: #e5e7eb;
        }

        .empty {
            text-align: center;
            color: #f87171;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="/" class="back">← Back</a>

    <h2>🍸 Matching Drinks</h2>

    @if($drinks->count())
        @foreach($drinks as $drink)
            <div class="card">
                <h3>{{ $drink->name }}</h3>
                <p>{{ $drink->description }}</p>

                <strong>Ingredients:</strong>
                <ul>
                    @foreach($drink->ingredients as $ingredient)
                        <li>{{ $ingredient->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @else
        <p class="empty">❌ No matching drinks found</p>
    @endif

</div>

</body>
</html>
```

---

## Step 11: Run Development Server

Start Laravel Server

Run this command inside your project folder:

```bash
php artisan serve
```
Open in Browser:

```bash
http://127.0.0.1:8000
```

---

## Output

<img src="screenshots/Screenshot 2026-03-24 143816.png" width="1000">

<img src="screenshots/Screenshot 2026-03-24 143828.png" width="1000">

<img src="screenshots/Screenshot 2026-03-24 143844.png" width="1000">

<img src="screenshots/Screenshot 2026-03-24 143853.png" width="1000">

---

## Project Structure

```
PHP_Laravel12_Bartender/
│
├── app/
│   ├── Models/
│   │   ├── Drink.php
│   │   └── Ingredient.php
│   │
│   ├── Services/
│   │   └── BartenderService.php
│   │
│   └── Http/
│       └── Controllers/
│           └── BartenderController.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── IngredientSeeder.php
│       └── DrinkSeeder.php
│
├── resources/
│   └── views/
│       └── bartender/
│           ├── index.blade.php
│           └── results.blade.php
│
├── routes/
│   └── web.php
│
└── .env
```

---

Your PHP_Laravel12_Bartender Project is now ready!



