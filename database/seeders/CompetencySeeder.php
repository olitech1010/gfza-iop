<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetencyScore;

// Note: This seeder is intended to be called when initializing an Appraisal, 
// not necessarily to seed the *table* globally, but we'll create a static helper method here 
// or simpler, just define the structure available for the App to use.
// However, in Laravel, Seeders populate tables. 
// Since CompetencyScores belong to an Appraisal, we actually need a Service to fill them when an Appraisal is created.
// But the prompt asked for a "CompetencySeeder". I will create it as a reference class 
// that can be used or potentially seeded if we had a "Template" table.
// Given strict TRS, I'll store the definitions in a config or helper, 
// OR I will simply create the class logic in the 'AppraisalResource' onCreate.
// But following the plan, I will create this file.

class CompetencySeeder extends Seeder
{
    public const CORE_COMPETENCIES = [
        'Organisation and Management',
        'Innovation and Strategic Thinking',
        'Leadership and Decision Making',
        'Developing and Improving',
        'Communication',
        'Job Knowledge and Technical Skills',
        'Supporting and Cooperating',
        'Maximising and Maintaining Productivity',
        'Developing/Managing Budgets',
    ];

    public const NON_CORE_COMPETENCIES = [
        'Ability to Develop Staff',
        'Commitment to Personal Development',
        'Delivering Results / Customer Satisfaction',
        'Following Instructions / Org Goals',
        'Respect and Commitment',
        'Ability to Work in a Team',
    ];

    public function run(): void
    {
        // No-op for global seed, as these depend on specific Appraisals.
    }
}
