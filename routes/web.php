<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BartenderController;

Route::get('/', [BartenderController::class, 'index']);
Route::post('/find-drinks', [BartenderController::class, 'findDrinks']);

