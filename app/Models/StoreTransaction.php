<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_item_id',
        'type', // receipt, issue, adjustment
        'transaction_date',
        'quantity',
        'unit_price',
        'balance_after',
        // Receipt specifics
        'supplier_id',
        'invoice_number',
        'sra_number',
        // Issue specifics
        'user_id',
        'department_id',
        'requisition_number',
        'siv_number',
        // Overall specifics
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
