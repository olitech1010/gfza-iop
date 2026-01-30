<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appraisal extends Model
{
    protected $fillable = [
        'user_id',
        'appraiser_id',
        'hod_id',
        'appraisal_period_id',
        'current_grade',
        'job_title',
        'date_appointed_present_grade',
        'status',
        'final_score',
        'promotion_verdict',
        'appraisee_comment',
        'appraiser_comment',
    ];

    protected $casts = [
        'date_appointed_present_grade' => 'date',
        'final_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appraiser_id');
    }

    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AppraisalPeriod::class, 'appraisal_period_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(AppraisalTarget::class);
    }

    public function competencyScores(): HasMany
    {
        return $this->hasMany(CompetencyScore::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }
}
