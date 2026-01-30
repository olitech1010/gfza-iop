<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AppraisalPdfController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Appraisal $appraisal)
    {
        // Ensure user can view this appraisal
        abort_unless(auth()->user()->can('view', $appraisal), 403);

        $appraisal->load(['user.department', 'period', 'targets', 'competencyScores']);

        $pdf = Pdf::loadView('appraisal-report', ['appraisal' => $appraisal]);
        // Sanitize staff_id for filename (replace / with -)
        $filename = 'Appraisal-' . str_replace('/', '-', $appraisal->user->staff_id ?? 'unknown') . '.pdf';

        return $pdf->stream($filename);
    }
}
