<?php

namespace App\Filament\Pages;

use App\Models\AuditTrip;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use App\Models\VehicleService;
use Filament\Pages\Page;

class TransportDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Transport Dashboard';

    protected static ?string $title = 'Transport Dashboard';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.transport-dashboard';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public function getStats(): array
    {
        return [
            'totalVehicles' => Vehicle::count(),
            'availableVehicles' => Vehicle::where('status', 'available')->count(),
            'inUseVehicles' => Vehicle::where('status', 'in_use')->count(),
            'maintenanceVehicles' => Vehicle::where('status', 'maintenance')->count(),
            'totalDrivers' => Driver::where('status', 'active')->count(),
            'pendingRequisitions' => VehicleRequisition::where('status', 'pending')->count(),
            'activeTrips' => VehicleRequisition::where('status', 'in_progress')->count(),
            'completedTripsMonth' => VehicleRequisition::where('status', 'completed')->whereMonth('updated_at', now()->month)->count(),
            'totalFuelCostMonth' => FuelLog::whereMonth('fuel_date', now()->month)->sum('total_cost'),
            'overdueServices' => VehicleService::where('status', 'scheduled')->where('next_service_date', '<', now())->count(),
            'auditScheduled' => AuditTrip::where('status', 'scheduled')->count(),
            'auditCompleted' => AuditTrip::where('status', 'completed')->count(),
        ];
    }

    public function getRecentRequisitions()
    {
        return VehicleRequisition::with(['requester', 'department', 'vehicle', 'driver.user'])
            ->latest()
            ->limit(8)
            ->get();
    }

    public function getUpcomingServices()
    {
        return VehicleService::with('vehicle')
            ->where('status', '!=', 'completed')
            ->orderBy('service_date')
            ->limit(5)
            ->get();
    }
}
