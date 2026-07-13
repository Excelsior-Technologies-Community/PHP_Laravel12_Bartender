<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image'
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorites')->withTimestamps();
    }

    public function getFavoriteCountAttribute()
    {
        return $this->favoritedBy()->count();
    }

    public function getIngredientsListAttribute()
    {
        return $this->ingredients->pluck('name')->toArray();
    }
}