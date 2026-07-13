<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'category',
        'icon'
    ];

    public function drinks()
    {
        return $this->belongsToMany(Drink::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_bar')->withTimestamps();
    }
}