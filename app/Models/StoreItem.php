<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_category_id',
        'name',
        'description',
        'sku',
        'unit_of_measure',
        'current_stock',
        'reorder_level',
        'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    public function transactions()
    {
        return $this->hasMany(StoreTransaction::class);
    }
}
