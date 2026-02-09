<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Performance Appraisal Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .letterhead { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #006400; padding-bottom: 15px; }
        .letterhead img { height: 60px; margin-bottom: 5px; }
        .org-name { font-size: 18px; font-weight: bold; color: #006400; margin: 5px 0; }
        .org-full-name { font-size: 14px; color: #333; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 20px; margin-top: 15px; }
        .section-title { background-color: #f0f0f0; padding: 5px; font-weight: bold; margin-top: 15px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f9f9f9; }
        .score-summary { margin-top: 20px; border: 2px solid #000; padding: 10px; }
        .signature { margin-top: 30px; }
    </style>
</head>
<body>
    {{-- GFZA Letterhead --}}
    <div class="letterhead">
        <img src="{{ public_path('images/logo.png') }}" alt="GFZA Logo">
        <div class="org-name">GFZA</div>
        <div class="org-full-name">Ghana Free Zones Authority</div>
    </div>

    <div class="header">
        <h2 style="margin: 5px 0;">PUBLIC SERVICES PERFORMANCE MANAGEMENT SYSTEM</h2>
        <h3 style="margin: 5px 0;">ANNUAL PERFORMANCE APPRAISAL REPORT</h3>
    </div>

    <table>
        <tr>
            <th>Period:</th>
            <td>{{ $appraisal->period->title }}</td>
            <th>Status:</th>
            <td>{{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}</td>
        </tr>
    </table>

    <div class="section-title">SECTION 1: PERSONAL DETAILS</div>
    <table>
        <tr>
            <th>Name:</th>
            <td>{{ $appraisal->user->name }}</td>
            <th>Staff ID:</th>
            <td>{{ $appraisal->user->staff_id }}</td>
        </tr>
        <tr>
            <th>Job Title:</th>
            <td>{{ $appraisal->job_title }}</td>
            <th>Grade:</th>
            <td>{{ $appraisal->current_grade }}</td>
        </tr>
        <tr>
            <th>Appointed Date:</th>
            <td>{{ $appraisal->date_appointed_present_grade ? $appraisal->date_appointed_present_grade->format('d M Y') : '-' }}</td>
            <th>Department:</th>
            <td>{{ $appraisal->user->department?->name }}</td>
        </tr>
    </table>

    <div class="section-title">SECTION 4: WORK TARGETS (60%)</div>
    <table>
        <thead>
            <tr>
                <th width="40%">Objective</th>
                <th width="30%">Criteria</th>
                <th width="10%">Score</th>
                <th width="20%">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisal->targets as $target)
            <tr>
                <td>{{ $target->objective }}</td>
                <td>{{ $target->target_criteria }}</td>
                <td>{{ $target->manager_score }}</td>
                <td>{{ $target->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">SECTION D: CORE COMPETENCIES (30%)</div>
    <table>
        <thead>
            <tr>
                <th width="70%">Competency</th>
                <th width="10%">Score</th>
                <th width="20%">Evidence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisal->competencyScores->where('competency_type', 'core') as $comp)
            <tr>
                <td>{{ $comp->competency_name }}</td>
                <td>{{ $comp->manager_score }}</td>
                <td>{{ $comp->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">SECTION 5: NON-CORE COMPETENCIES (10%)</div>
    <table>
        <thead>
            <tr>
                <th width="70%">Competency</th>
                <th width="10%">Score</th>
                <th width="20%">Evidence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisal->competencyScores->where('competency_type', 'non_core') as $comp)
            <tr>
                <td>{{ $comp->competency_name }}</td>
                <td>{{ $comp->manager_score }}</td>
                <td>{{ $comp->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="score-summary">
        <h3>FINAL ASSESSMENT</h3>
        <p><strong>Total Score: {{ $appraisal->final_score }} / 5.00</strong></p>
        <p><strong>Promotion Verdict:</strong> {{ ucfirst(str_replace('_', ' ', $appraisal->promotion_verdict)) }}</p>
    </div>

    <div class="signature">
        <p><strong>Appraiser (HOD) Comment:</strong> {{ $appraisal->appraiser_comment }}</p>
        <br>
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none;"><strong>Appraiser Signature:</strong> ___________________</td>
                <td style="border: none;"><strong>Date:</strong> {{ now()->format('d M Y') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
