<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealOrder extends Model
{
    protected $fillable = [
        'user_id',
        'served_meal_id',
        'ordered_at',
        'status', // ordered, collected, cancelled
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function servedMeal()
    {
        return $this->belongsTo(ServedMeal::class);
    }
}
