<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $fillable = [
        'title',
        'body',
        'attachment_path',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'memo_recipients')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
