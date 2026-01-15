<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoRecipient extends Model
{
    protected $fillable = [
        'memo_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
