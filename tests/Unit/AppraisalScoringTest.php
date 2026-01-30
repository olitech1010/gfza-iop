<?php

namespace Tests\Unit;

use App\Models\Appraisal;
use App\Models\AppraisalTarget;
use App\Models\CompetencyScore;
use App\Services\AppraisalScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppraisalScoringTest extends TestCase
{
    // use RefreshDatabase; 
    // We can use RefreshDatabase if we want to hit DB, simpler given the models.
    
    /** @test */
    public function it_calculates_appraisal_score_correctly()
    {
        // 1. Setup Data
        // We will mock or create in memory. Since we are in Laravel, using SQLite memory is easiest.
        // Assuming TestCase uses RefreshDatabase or similar.
        
        $appraisal = new Appraisal();
        $appraisal->setRelation('targets', collect([
            new AppraisalTarget(['manager_score' => 5]), // 5
            new AppraisalTarget(['manager_score' => 5]), // 5
        ]));
        // Target Avg = 5.   60% of 5 = 3.0.
        
        $appraisal->setRelation('competencyScores', collect([
            // Core (Weight 0.3)
            new CompetencyScore(['competency_type' => 'core', 'manager_score' => 5]), 
            new CompetencyScore(['competency_type' => 'core', 'manager_score' => 5]),
            // Core Avg = 5.   30% of 5 = 1.5.
            
            // Non-Core (Weight 0.1)
            new CompetencyScore(['competency_type' => 'non_core', 'manager_score' => 5]),
            new CompetencyScore(['competency_type' => 'non_core', 'manager_score' => 5]),
            // Non-Core Avg = 5. 10% of 5 = 0.5.
        ]));
        
        // Expected Total = 3.0 + 1.5 + 0.5 = 5.0.
        
        $service = new AppraisalScoringService();
        $score = $service->calculateScore($appraisal);
        
        $this->assertEquals(5.0, $score);
    }
    
    /** @test */
    public function it_calculates_mixed_scores_correctly()
    {
        $appraisal = new Appraisal();
        
        // Targets: Avg 3. (3 * 0.6 = 1.8)
        $appraisal->setRelation('targets', collect([
            new AppraisalTarget(['manager_score' => 3]),
            new AppraisalTarget(['manager_score' => 3]), 
        ]));
        
        // Core: Avg 4. (4 * 0.3 = 1.2)
        $appraisal->setRelation('competencyScores', collect([
            new CompetencyScore(['competency_type' => 'core', 'manager_score' => 4]), 
            new CompetencyScore(['competency_type' => 'core', 'manager_score' => 4]),
        ]));

        // Non-Core: Avg 2. (2 * 0.1 = 0.2)
        $appraisal->setRelation('competencyScores', $appraisal->competencyScores->merge([
            new CompetencyScore(['competency_type' => 'non_core', 'manager_score' => 2]),
            new CompetencyScore(['competency_type' => 'non_core', 'manager_score' => 2]),
        ]));

        // Expected Total = 1.8 + 1.2 + 0.2 = 3.2.
        
        $service = new AppraisalScoringService();
        $score = $service->calculateScore($appraisal);
        
        $this->assertEquals(3.2, $score);
    }
}
