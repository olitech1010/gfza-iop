<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppraisalTarget extends Model
{
    protected $fillable = [
        'appraisal_id',
        'objective',
        'target_criteria',
        'self_score',
        'manager_score',
        'remarks',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal::class);
    }
}
