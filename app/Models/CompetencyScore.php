<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyScore extends Model
{
    protected $fillable = [
        'appraisal_id',
        'competency_type', // core or non_core
        'competency_name',
        'manager_score',
        'weight_factor',
        'remarks',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal::class);
    }
}
