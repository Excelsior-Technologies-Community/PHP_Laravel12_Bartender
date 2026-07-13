<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BartenderController;

Route::get('/', [BartenderController::class, 'index'])->name('home');
Route::post('/find-drinks', [BartenderController::class, 'findDrinks'])->name('find.drinks');
Route::post('/drinks', [BartenderController::class, 'store'])->name('drinks.store');
Route::delete('/drinks/{id}', [BartenderController::class, 'destroy'])->name('drinks.destroy');
Route::post('/favorite/{id}', [BartenderController::class, 'toggleFavorite'])->name('favorite.toggle');
Route::get('/mybar', [BartenderController::class, 'myBar'])->name('mybar');
Route::post('/mybar', [BartenderController::class, 'updateBar'])->name('mybar.update');

// Manual Auth Routes (Simple)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
        return redirect()->intended('/');
    }
    
    return back()->withErrors(['email' => 'Invalid credentials']);
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed'
    ]);
    
    $user = App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password)
    ]);
    
    auth()->login($user);
    return redirect('/');
});

Route::get('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');