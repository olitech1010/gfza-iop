<?php

namespace App\Filament\Resources\AppraisalResource\Pages;

use App\Filament\Resources\AppraisalResource;
use App\Models\Appraisal;
use App\Models\CompetencyScore;
use Database\Seeders\CompetencySeeder;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAppraisal extends CreateRecord
{
    protected static string $resource = AppraisalResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Populate Default Competencies for this specific Appraisal
        foreach (CompetencySeeder::CORE_COMPETENCIES as $compName) {
            CompetencyScore::create([
                'appraisal_id' => $record->id,
                'competency_type' => 'core',
                'competency_name' => $compName,
                'manager_score' => 3,
                'weight_factor' => 0.30
            ]);
        }

        foreach (CompetencySeeder::NON_CORE_COMPETENCIES as $compName) {
            CompetencyScore::create([
                'appraisal_id' => $record->id,
                'competency_type' => 'non_core',
                'competency_name' => $compName,
                'manager_score' => 3,
                'weight_factor' => 0.10
            ]);
        }

        return $record;
    }
}
