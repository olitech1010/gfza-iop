<?php

namespace App\Services;

use App\Models\Appraisal;

class AppraisalScoringService
{
    /**
     * Calculate and update the final score for an appraisal.
     * 
     * Logic:
     * - Targets (Section 4): 60% Weight
     * - Core Competencies (Section D): 30% Weight
     * - Non-Core Competencies (Section 5): 10% Weight
     */
    public function calculateScore(Appraisal $appraisal): float
    {
        $appraisal->loadMissing(['targets', 'competencyScores']);

        // 1. Work Targets (60%)
        // Formula: (Sum of Scores / (Count * 5)) * 100 * 0.6 ??
        // The prompt said: "Average of all manager_score" ... " (Sum of Scores / Count) * 0.6 "
        // Wait, Avg of 1-5 is max 5. 5 * 0.6 = 3.0.
        // If max score is 5.0, then:
        // Targets Contribution = Average(Targets) * 0.6 (Max 3.0)
        // Core Contribution = Average(Core) * 0.3 (Max 1.5)
        // Non-Core Contribution = Average(Non-Core) * 0.1 (Max 0.5)
        // Total = 3.0 + 1.5 + 0.5 = 5.0. Correct.

        $targets = $appraisal->targets;
        $scoreTargets = $targets->count() > 0 
            ? $targets->avg('manager_score') * 0.6 
            : 0;

        // 2. Core Competencies (30%)
        $coreCompetencies = $appraisal->competencyScores->where('competency_type', 'core');
        $scoreCore = $coreCompetencies->count() > 0
            ? $coreCompetencies->avg('manager_score') * 0.3
            : 0; // Or should we default to some base? No, unrated = 0.

        // 3. Non-Core Competencies (10%)
        // Prompt said: "Average of (manager_score * 0.1)" - Wait.
        // Prompt: "Average of (manager_score * 0.3)"...
        // Let's stick to: Average(Scores) * Weight.
        
        $nonCoreCompetencies = $appraisal->competencyScores->where('competency_type', 'non_core');
        $scoreNonCore = $nonCoreCompetencies->count() > 0
            ? $nonCoreCompetencies->avg('manager_score') * 0.1
            : 0;

        $finalScore = $scoreTargets + $scoreCore + $scoreNonCore;

        return round($finalScore, 2);
    }
}
