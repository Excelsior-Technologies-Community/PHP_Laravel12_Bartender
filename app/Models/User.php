<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function favoriteDrinks()
    {
        return $this->belongsToMany(Drink::class, 'user_favorites')->withTimestamps();
    }

    public function barIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'user_bar')->withTimestamps();
    }

    public function isFavorite($drinkId)
    {
        return $this->favoriteDrinks()->where('drink_id', $drinkId)->exists();
    }

    public function toggleFavorite($drinkId)
    {
        if ($this->isFavorite($drinkId)) {
            $this->favoriteDrinks()->detach($drinkId);
            return false;
        } else {
            $this->favoriteDrinks()->attach($drinkId);
            return true;
        }
    }
}