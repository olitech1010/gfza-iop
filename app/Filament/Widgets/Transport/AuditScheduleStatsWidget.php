<?php

namespace App\Filament\Widgets\Transport;

use App\Models\AuditTrip;
use Filament\Widgets\Widget;

class AuditScheduleStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.transport.audit-schedule-stats-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public function getStats(): array
    {
        $total = AuditTrip::count();
        $scheduled = AuditTrip::where('status', 'scheduled')->count();
        $inProgress = AuditTrip::where('status', 'in_progress')->count();
        $completed = AuditTrip::where('status', 'completed')->count();
        $internalCount = AuditTrip::where('schedule_type', 'internal')->count();
        $externalCount = AuditTrip::where('schedule_type', 'external')->count();
        $complianceCount = AuditTrip::where('audit_type', 'compliance')->count();
        $monitoringCount = AuditTrip::where('audit_type', 'monitoring')->count();
        $completionRate = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'scheduled' => $scheduled,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'internalCount' => $internalCount,
            'externalCount' => $externalCount,
            'complianceCount' => $complianceCount,
            'monitoringCount' => $monitoringCount,
            'completionRate' => $completionRate,
        ];
    }
}
