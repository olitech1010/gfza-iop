<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServedMeal extends Model
{
    protected $fillable = [
        'date',
        'meal_item_id',
        'max_orders',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function mealItem()
    {
        return $this->belongsTo(MealItem::class);
    }
}
