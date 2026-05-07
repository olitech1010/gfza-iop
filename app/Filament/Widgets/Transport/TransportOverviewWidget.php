<?php

namespace App\Filament\Widgets\Transport;

use App\Models\AuditTrip;
use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use Filament\Widgets\Widget;

class TransportOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.transport.transport-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    /**
     * Only visible to transport dept_head (isTransportHead) and super_admin.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->isTransportHead());
    }

    public function getStats(): array
    {
        return [
            'totalVehicles' => Vehicle::count(),
            'availableVehicles' => Vehicle::where('status', 'available')->count(),
            'inUseVehicles' => Vehicle::where('status', 'in_use')->count(),
            'pendingRequisitions' => VehicleRequisition::where('status', 'pending')->count(),
            'activeTrips' => VehicleRequisition::where('status', 'in_progress')->count(),
            'totalFuelCostMonth' => FuelLog::whereMonth('fuel_date', now()->month)->sum('total_cost'),
            'auditScheduled' => AuditTrip::where('status', 'scheduled')->count(),
        ];
    }
}
